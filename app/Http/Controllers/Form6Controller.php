<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Form6Controller;
use App\Service\OneSignalService;
use App\Service\FormService;
use App\Models\DataAsesorModel;
use App\Models\LangkahForm6;
use App\Models\KegiatanForm6;
use App\Models\PoinForm6;
use App\Models\JawabanForm6;
use App\Models\DaftarUser;
use App\Models\BidangModel;
use App\Models\UserRole;
use App\Models\KompetensiTrack;
use App\Models\KompetensiProgres;
use App\Models\Notification;
use App\Models\Form6;
use Carbon\Carbon;

class Form6Controller extends BaseController
{

	protected $formService;

    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
    }

    public function SoalForm6($pkId)
    {
        try {
            // Ambil data langkah beserta relasi nested-nya
            $langkah = LangkahForm6::with([
                'kegiatan.poin.subPoin'
            ])
            ->where('pk_id', $pkId)
            ->orderBy('nomor_langkah')
            ->get();

            // Jika data kosong
            if ($langkah->isEmpty()) {
                return response()->json([
                    'message' => 'Data soal tidak ditemukan untuk pk_id: ' . $pkId,
                    'data' => []
                ], 404);
            }

            // Sembunyikan kolom pencapaian (jika diperlukan)
            $langkah->each(function ($item) {
                $item->kegiatan->each(function ($kegiatan) {
                    $kegiatan->makeHidden('pencapaian');
                });
            });

            return response()->json([
                'message' => 'Data soal berhasil diambil.',
                'data' => $langkah
            ], 200);

        } catch (\Exception $e) {
            // Catat error ke file log
            Log::error('Gagal mengambil soal Form6', [
                'pk_id' => $pkId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Berikan respon error ke client
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data soal.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function simpanJawabanForm6(Request $request)
    {
        try {
            // 🔹 Validasi input termasuk asesi_id
            $validator = Validator::make($request->all(), [
                'asesi_id' => 'required|integer|exists:users,user_id',
                'pk_id' => 'required|integer',
                'jawaban' => 'required|array|min:1',
                'jawaban.*.kegiatan_id' => 'required|integer|exists:kegiatan_form6,id',
                'jawaban.*.pencapaian' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validated = $validator->validated();
            $asesiId = $validated['asesi_id']; // 🔹 Ambil dari request body
            $asesorId = Auth::id(); // 🔹 Asesor tetap diambil dari user login (opsional)

            DB::beginTransaction();

            foreach ($validated['jawaban'] as $item) {
                $sudahAda = JawabanForm6::where('pk_id', $validated['pk_id'])
                    ->where('kegiatan_id', $item['kegiatan_id'])
                    ->where('user_id', $asesiId)
                    ->exists();

                if ($sudahAda) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Jawaban untuk kegiatan ID ' . $item['kegiatan_id'] . ' sudah pernah disimpan.',
                    ], 409);
                }

                JawabanForm6::create([
                    'pk_id' => $validated['pk_id'],
                    'kegiatan_id' => $item['kegiatan_id'],
                    'user_id' => $asesiId, // 🔹 asesi_id dipakai sebagai user_id penyimpan
                    'pencapaian' => $item['pencapaian'],
                ]);
            }

            // 🔹 Ambil Form 1 berdasarkan asesi (bukan user login)
            $form1 = $this->formService->getForm1ByAsesiIdAndPkId($asesiId, $validated['pk_id']);
            Log::info('Form 1 yang ditemukan untuk asesi_id ' . $asesiId . ' dan pk_id ' . $validated['pk_id'], ['form1' => $form1]);
            if (!$form1) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Data Form 1 tidak ditemukan untuk asesi ini dan PK ID tersebut.',
                ], 404);
            }

            // 🔹 Kirim notifikasi ke asesor dari Form 1
            $userAsesor = $this->formService->findUser($form1->asesor_id);
            $this->formService->KirimNotifikasiKeUser(
                $userAsesor,
                'Jawaban Form 6 Tersimpan',
                'Jawaban Form 6 telah disimpan oleh asesor.'
            );

            // 🔹 Cek apakah form6 ada, lalu update progres dan track
            $isFormExist = $this->formService->isFormExistSingle($asesiId, $validated['pk_id'], 'form_6');
            if ($isFormExist) {
                $form6 = $this->formService->getFormIdsByParentFormIdAndType($form1->form_1_id, 'form_6');
                $this->formService->updateProgresDanTrack(
                    $form6,
                    'form_6',
                    'Submitted',
                    $form1->asesi_id,
                    'Jawaban Form 6 telah diisi oleh Asesor'
                );
            }

            DB::commit();

            return response()->json([
                'message' => 'Jawaban berhasil disimpan.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal menyimpan Jawaban Form 6', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan jawaban.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getSoalDanJawabanForm6(Request $request, $pkId)
    {
        try {
            // Validasi manual user_id (jika dikirim)
            $validator = Validator::make($request->all(), [
                'user_id' => 'nullable|integer|exists:users,user_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Ambil user_id dari input atau fallback ke Auth
            $userId = $request->input('user_id', Auth::id());

            $langkah = LangkahForm6::with([
                'kegiatan.poin.subPoin',
                'kegiatan.jawabanForm6' => function ($query) use ($pkId, $userId) {
                    $query->where('pk_id', $pkId)
                        ->where('user_id', $userId);
                }
            ])
            ->where('pk_id', $pkId)
            ->orderBy('nomor_langkah')
            ->get();

            // Jika data kosong
            if ($langkah->isEmpty()) {
                return response()->json([
                    'message' => 'Data soal tidak ditemukan untuk pk_id: ' . $pkId,
                    'data' => []
                ], 404);
            }

            // Tambahkan 'pencapaian' ke dalam kegiatan
            $langkah->each(function ($item) {
                $item->kegiatan->each(function ($kegiatan) {
                    $jawaban = $kegiatan->jawabanForm6->first();
                    $kegiatan->pencapaian = $jawaban ? $jawaban->pencapaian : null;
                    $kegiatan->makeHidden('jawabanForm6');
                });
            });

            return response()->json([
                'message' => 'Data soal dan jawaban berhasil diambil.',
                'data' => $langkah
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data soal.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function ApproveForm6ByAsesi(Request $request)
    {
        Log::info("=== START ApproveForm6ByAsesi ===");
        Log::debug("Request data:", $request->all());

        // Validasi input
        $validator = Validator::make($request->all(), [
            'form_6_id' => 'required|integer|exists:form_6,form_6_id',
        ]);

        if ($validator->fails()) {
            Log::warning("Validasi gagal", $validator->errors()->toArray());

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            Log::info("Mencari Form6 berdasarkan form_6_id...");
            $form6 = Form6::find($request->form_6_id);

            if (!$form6) {
                Log::error("Form6 tidak ditemukan!");
            } else {
                Log::debug("Data Form6:", $form6->toArray());
            }

            Log::info("Memproses approval Form 6 ID: {$request->form_6_id}");

            // FORM 1 ID
            Log::info("Mencari Form1 ID berdasarkan Form6...");
            $form1Id = $this->formService->getParentFormIdByFormIdAndAsesiId(
                $request->form_6_id,
                $form6->asesi_id,
                'form_6'
            );

            Log::info("Form1 ID ditemukan: " . json_encode($form1Id));

            $form1 = $this->formService->getParentDataByFormId($form1Id);

            if (!$form1) {
                Log::error("DATA FORM1 TIDAK DITEMUKAN!");
            } else {
                Log::debug("Data Form1:", (array) $form1);
            }

            // DATA ASESOR
            $userAsesor = $this->formService->findUser($form1->asesor_id);
            Log::debug("Data User Asesor:", optional($userAsesor)->toArray() ?? []);

            // STATUS FORM 6
            $form6Status = $this->formService
                ->getStatusByParentFormIdAndType($form1Id, 'form_6')
                ->first();

            Log::info("Status Form 6 saat ini: " . json_encode($form6Status));

            // UPDATE STATUS
            if ($form6Status === 'Submitted') {
                Log::info("Status Submitted → melakukan update ke Completed");

                $updatedForm6 = $this->formService->updateForm6(
                    $request->form_6_id,
                    null, null, null, null, null, null,
                    Carbon::now(),
                    null,
                    'Completed'
                );

                Log::debug("Result updateForm6:", (array) $updatedForm6);

                $updateProgres = $this->formService->updateProgresDanTrack(
                    $request->form_6_id,
                    'form_6',
                    'Completed',
                    Auth::id(),
                    'Form 6 telah di-approve oleh Asesi'
                );

                Log::debug("Hasil update progres & track:", (array) $updateProgres);

                $this->formService->KirimNotifikasiKeUser(
                    $userAsesor,
                    'Form 6 Approved',
                    'Form 6 telah di-approve oleh Asesi.'
                );

                Log::info("Notifikasi dikirim ke asesor.");
            } else {
                Log::warning("Status Form6 bukan 'Submitted'. Tidak dilakukan update.");
            }

            DB::commit();
            Log::info("=== END ApproveForm6ByAsesi SUCCESS ===");

            return response()->json([
                'success' => true,
                'message' => 'Form 6 berhasil di-approve oleh Asesi',
                'data' => $form6Status
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("ERROR ApproveForm6ByAsesi: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


}
