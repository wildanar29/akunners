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

            $form4d = Form4d::find($form4dId);
            if (!$form4d) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Form 4d tidak ditemukan.'
                ], 404);
            }

            $form1Id = $this->formService->getParentFormIdByFormId($form4dId);
            $form1   = $this->formService->getParentDataByFormId($form1Id);

            $form4dStatus = $this->formService
                ->getStatusByParentFormIdAndType($form1Id, 'form_4d')
                ->first();

            if ($form4dStatus === 'Submitted') {
                $this->formService->updateForm4d(
                    $form4dId,
                    null, null,
                    'form_4d',
                    null, null,
                    null, null,
                    'Approved'
                );

                $this->formService->updateProgresDanTrack(
                    $form4dId,
                    'form_4d',
                    'Approved',
                    Auth::id(),
                    'Form 4D telah di-approve oleh Asesi'
                );

                $this->formService->kirimNotifikasiKeUser(
                    DaftarUser::find($form1->asesor_id),
                    'Form 4D sudah di Approved',
                    'Form 4D telah di-approve oleh Asesi.'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form 4D berhasil di-approve oleh Asesi',
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
