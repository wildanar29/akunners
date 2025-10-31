<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Service\OneSignalService;
use App\Service\FormService;
use App\Models\DataAsesorModel;
use App\Models\JawabanForm4c;
use App\Models\DaftarUser;
use App\Models\ElemenForm3;
use App\Models\JawabanForm4d;
use App\Models\PertanyaanForm4d;
use App\Models\PertanyaanForm4c;
use App\Models\IukModel;
use App\Models\JawabanForm4b;
use App\Models\Form4d;
use App\Models\KukModel;
use App\Models\JawabanForm4a;
use App\Models\BidangModel;
use App\Models\UserRole;
use App\Models\KompetensiTrack;
use App\Models\KompetensiProgres;
use App\Models\Notification;
use Carbon\Carbon;

class Form4dController extends BaseController
{

	protected $formService;

    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
    }

    public function getSoalForm4dByPkId(Request $request)
    {
        // Validasi input manual menggunakan Validator
        $validator = Validator::make($request->all(), [
            'pk_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $pkId = $request->input('pk_id');

            $soal = PertanyaanForm4d::whereHas('kuk', function ($query) use ($pkId) {
                    $query->where('pk_id', $pkId);
                })
                ->with(['kuk', 'children']) // load relasi jika perlu
                ->orderBy('urutan')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Soal Form 4D berhasil diambil',
                'data' => $soal,
            ]);
        } catch (\Exception $e) {
            // Optional: log error-nya
            Log::error('Gagal mengambil soal Form 4D: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage(), // Untuk debugging, bisa dihilangkan di production
            ], 500);
        }
    }

    public function simpanJawabanForm4d(Request $request)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'form_1_id' => 'required|integer',
            'asesi_id' => 'required|integer',
            'jawaban' => 'required|array',
            'jawaban.*.pertanyaan_form4d_id' => 'required|integer|exists:pertanyaan_form4d,id',
            'jawaban.*.pencapaian' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($request->jawaban as $item) {
                JawabanForm4d::updateOrCreate(
                    [
                        'form_1_id' => $request->form_1_id,
                        'user_id' => $request->asesi_id,
                        'pertanyaan_form4d_id' => $item['pertanyaan_form4d_id'],
                    ],
                    [
                        'pencapaian' => $item['pencapaian'],
                    ]
                );
            }

             $form_4d_id = $this->formService->getFormIdsByParentFormIdAndType($request->form_1_id, 'Form_4d');

            $this->formService->updateProgresDanTrack(
                $form_4d_id,
                'form_4d',
                'Submitted',
                $request->asesi_id,
                'Form 4D telah di isi oleh Asesor'
            );

            $form1 = $this->formService->getParentDataByFormId($request->form_1_id);

            // Kirim notifikasi ke asesor
            $this->formService->kirimNotifikasiKeUser(
                DaftarUser::find($form1->asesor_id),
                'Form 4D Submitted',
                'Form 4D telah di-submit oleh Asesor.'
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Jawaban Form 4D berhasil disimpan',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan jawaban Form 4D: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSoalDanJawabanForm4d(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pk_id' => 'required|integer',
            'form_1_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $pkId = $request->pk_id;
            $form1Id = $request->form_1_id;
            $userId = $request->user_id;

            // Ambil soal Form 4D berdasarkan pk_id
            $pertanyaanList = \App\Models\PertanyaanForm4d::whereHas('kuk', function ($query) use ($pkId) {
                    $query->where('pk_id', $pkId);
                })
                ->with(['kuk'])
                ->orderBy('urutan')
                ->get();

            // Ambil jawaban user terkait
            $jawabanMap = \App\Models\JawabanForm4d::where('form_1_id', $form1Id)
                ->where('user_id', $userId)
                ->get()
                ->keyBy('pertanyaan_form4d_id');

            // Susun data respons
            $data = $pertanyaanList->map(function ($pertanyaan) use ($jawabanMap) {
                $jawaban = $jawabanMap->get($pertanyaan->id);

                return [
                    'id' => $pertanyaan->id,
                    'urutan' => $pertanyaan->urutan,
                    'dokumen' => $pertanyaan->dokumen,
                    'kuk' => [
                        'kuk_form3_id' => $pertanyaan->kuk->kuk_form3_id,
                        'no_elemen_form_3' => $pertanyaan->kuk->no_elemen_form_3,
                        'no_kuk' => $pertanyaan->kuk->no_kuk,
                        'kuk_name' => $pertanyaan->kuk->kuk_name,
                        'pk_id' => $pertanyaan->kuk->pk_id,
                    ],
                    'jawaban' => $jawaban ? [
                        'pencapaian' => (bool) $jawaban->pencapaian,
                    ] : null,
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Data soal dan jawaban Form 4D berhasil diambil',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil soal dan jawaban Form 4D: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function ApproveForm4dByAsesi(Request $request, $form4dId)
    {
        $validator = Validator::make(['form_4d_id' => $form4dId], [
            'form_4d_id' => 'required|integer|exists:form_4d,form_4d_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Pastikan $form4d adalah model tunggal
            $form4d = Form4d::find($form4dId);
            if (!$form4d) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Form 4D tidak ditemukan.'
                ], 404);
            }

            // Ambil Form 1 ID dari service (berdasarkan form4d + asesi_id)
            $form1Id = $this->formService->getParentFormIdByFormIdAndAsesiId($form4dId, $form4d->asesi_id);

            // Normalisasi $dataForm4d jika service mengembalikan Collection
            $dataForm4d = $this->formService->getForm4dDataFromForm4dId($form4dId);
            if ($dataForm4d instanceof \Illuminate\Support\Collection) {
                $dataForm4d = $dataForm4d->first();
            }
            if (!$dataForm4d) {
                throw new \RuntimeException('Data Form 4D (detail) tidak ditemukan.');
            }

            $form1 = $this->formService->getParentDataByFormId($form1Id);
            Log::info("Form 4D ID: {$form4dId}, Form 1 ID: {$form1Id}");

            // Ambil status Form 4D
            $form4dStatus = $this->formService
                ->getStatusByParentFormIdAndType($form1Id, 'form_4d')
                ->first();

            // Jika Form 4D statusnya Submitted atau InAssessment → ubah jadi Completed
            if ($form4d->status === 'Submitted' || $form4d->status === 'InAssessment') {
                $this->formService->updateForm4d(
                    $form4dId,
                    null, null,
                    'form_4d',
                    null, null,
                    null, null,
                    'Completed'
                );

                $this->formService->updateProgresDanTrack(
                    $form4dId,
                    'form_4d',
                    'Completed',
                    $dataForm4d->asesi_id,
                    'Form 4D telah di-approve oleh Asesor'
                );

                Log::info('Form 4D approved successfully.');

                // Kirim notifikasi ke asesor
                $this->formService->kirimNotifikasiKeUser(
                    DaftarUser::find($form1->asesor_id),
                    'Form 4D sudah di Approved',
                    'Form 4D telah di-approve oleh Asesor.'
                );
            }

            /**
             * ✅ Tambahan: cek apakah semua Form 4 (A–D) sudah lengkap.
             * Jika semua lengkap, maka ubah status Form 7 → InAssessment
             */
            $isAllForm4Completed = $this->formService->checkForm4Completion(
                $form4d->pk_id,
                $form4d->asesi_id,
                $form4d->asesor_id
            );

            if ($isAllForm4Completed) {
                Log::info("Semua Form 4 (A–D) telah lengkap untuk PK ID: {$form4d->pk_id}");

                // Ambil status Form 7
                $form7Status = $this->formService
                    ->getStatusByParentFormIdAndType($form1Id, 'form_7')
                    ->first();

                // Ambil ID Form 7 (pastikan bukan collection)
                $form7Ids = $this->formService->getFormIdsByParentFormIdAndTypeNew(
                    $form1Id,
                    'form_7',
                    $dataForm4d->asesi_id
                );

                if ($form7Ids instanceof \Illuminate\Support\Collection) {
                    $form7Id = $form7Ids->first();
                } else {
                    $form7Id = $form7Ids;
                }

                Log::info("Form 7 ID for Form 1 ID {$form1Id}: " . json_encode($form7Id));

                if ($form7Id && $form7Status === 'Submitted') {
                    // Update status form 7 → InAssessment
                    $this->formService->updateForm7(
                        $form7Id,
                        null, // pkId
                        null, // daftarTilikId
                        'form_7',
                        null, // asesiId
                        null, // asesiName
                        null, // asesorId
                        null, // asesorName
                        'Process'
                    );

                    // Update progres & track
                    $this->formService->updateProgresDanTrack(
                        $form7Id,
                        'form_7',
                        'Process',
                        $dataForm4d->asesi_id,
                        'Form form_7 telah di-approve oleh Asesi'
                    );

                    // Kirim notifikasi ke asesor
                    $this->formService->kirimNotifikasiKeUser(
                        DaftarUser::find($form1->asesor_id),
                        'Form form_7 InAssessment',
                        'Form form_7 telah di-InAssessment oleh Asesi.'
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form 4D berhasil di-approve oleh Asesor',
                'data'    => []
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }


}
