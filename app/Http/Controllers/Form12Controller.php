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
use App\Models\KegiatanDaftarTilik;
use App\Models\DaftarUser;
use App\Models\ElemenForm3;
use App\Models\JawabanForm4d;
use App\Models\DaftarTilik;

use App\Models\JawabanDaftarTilik;
use App\Models\Form10;
use App\Models\PertanyaanForm4d;
use App\Models\PertanyaanForm4c;
use App\Models\IukModel;
use App\Models\JawabanForm4b;
use App\Models\KukModel;
use App\Models\JawabanForm4a;
use App\Models\BidangModel;
use App\Models\UserRole;
use App\Models\KompetensiTrack;
use App\Models\KompetensiProgres;
use App\Models\Notification;
use App\Models\SoalForm7;
use App\Models\Form9;
use App\Models\Form9Answer;
use App\Models\Form9Question;
use App\Models\Form9SubQuestion;
use App\Models\Form12;
use App\Models\JawabanForm7;
use App\Models\PoinForm4;
use Carbon\Carbon;

class Form12Controller extends BaseController
{

	protected $formService;

    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
    }

    // public function getByPkId(Request $request)
    // {
    //     // ✅ Validasi request
    //     $validator = Validator::make($request->all(), [
    //         'pk_id'     => 'required|integer|min:1',
    //         'asesi_id'  => 'required|integer|min:1', // tambahkan validasi asesi_id
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     $pkId     = $request->input('pk_id');
    //     $asesiId  = $request->input('asesi_id');

    //     // ✅ Query ambil data nested dengan filter asesi_id
    //     $data = ElemenForm3::with([
    //         'kukForm3' => function ($q) use ($asesiId) {
    //             $q->orderBy('no_kuk', 'asc')
    //             ->with([
    //                 'iukForm3' => function ($q2) use ($asesiId) {
    //                     $q2->orderBy('no_iuk', 'asc')
    //                         ->with([
    //                             'soalForm7' => function ($q3) use ($asesiId) {
    //                                 $q3->select('id', 'iuk_form3_id')
    //                                     ->with([
    //                                         'jawabanForm7' => function ($q4) use ($asesiId) {
    //                                             $q4->select('id', 'soal_form7_id', 'keputusan', 'asesi_id')
    //                                                 ->where('asesi_id', $asesiId);
    //                                         }
    //                                     ]);
    //                             }
    //                         ]);
    //                 }
    //             ]);
    //         }
    //     ])
    //     ->where('pk_id', $pkId)
    //     // ⬇️ Tambahkan ini supaya hanya ambil Elemen yang punya jawaban sesuai asesi
    //     ->whereHas('kukForm3.iukForm3.soalForm7.jawabanForm7', function ($q) use ($asesiId) {
    //         $q->where('asesi_id', $asesiId);
    //     })
    //     ->orderBy('no_elemen_form_3', 'asc')
    //     ->get();

    //     if ($data->isEmpty()) {
    //         return response()->json([
    //             'status'  => 'not_found',
    //             'message' => "Data tidak ditemukan untuk pk_id: $pkId dan asesi_id: $asesiId",
    //         ], 404);
    //     }

    //     // ✅ Hitung nilai final per level
    //     $data->transform(function ($elemen) {
    //         $elemen->kukForm3->transform(function ($kuk) {
    //             $kuk->iukForm3->transform(function ($iuk) {
    //                 $totalSoal = $iuk->soalForm7->count();
    //                 $jumlahK   = 0;

    //                 foreach ($iuk->soalForm7 as $soal) {
    //                     foreach ($soal->jawabanForm7 as $jawaban) {
    //                         if ($jawaban->keputusan === 'K') {
    //                             $jumlahK++;
    //                         }
    //                     }
    //                 }

    //                 $iuk->final = ($totalSoal > 0 && ($jumlahK / $totalSoal) >= 0.5) ? 'K' : 'BK';
    //                 return $iuk;
    //             });

    //             // Hitung nilai KUK dari seluruh IUK
    //             $totalIuk = $kuk->iukForm3->count();
    //             $jumlahK  = $kuk->iukForm3->where('final', 'K')->count();

    //             $kuk->final = ($totalIuk > 0 && ($jumlahK / $totalIuk) >= 0.5) ? 'K' : 'BK';
    //             return $kuk;
    //         });

    //         // Hitung nilai Elemen dari seluruh KUK
    //         $totalKuk = $elemen->kukForm3->count();
    //         $jumlahK  = $elemen->kukForm3->where('final', 'K')->count();

    //         $elemen->final = ($totalKuk > 0 && ($jumlahK / $totalKuk) >= 0.5) ? 'K' : 'BK';
    //         return $elemen;
    //     });

    //     return response()->json([
    //         'status' => 'success',
    //         'data'   => $data,
    //     ]);
    // }

    public function getByPkId(Request $request)
    {
        // ✅ Validasi request
        $validator = Validator::make($request->all(), [
            'pk_id'     => 'required|integer|min:1',
            'asesi_id'  => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pkId     = $request->input('pk_id');
        $asesiId  = $request->input('asesi_id');

        // Tentukan ekspresi casting sesuai driver
        $driver = DB::getDriverName();
        $orderExpr = $driver === 'mysql'
            ? 'CAST(no_elemen_form_3 AS UNSIGNED)'
            : 'CAST(no_elemen_form_3 AS INTEGER)';

        // ✅ Query ambil data nested dengan filter asesi_id
        $data = ElemenForm3::with([
            'kukForm3.iukForm3.soalForm7.jawabanForm7' => function ($q) use ($asesiId) {
                $q->where('asesi_id', $asesiId);
            }
        ])
        ->where('pk_id', $pkId)
        ->whereHas('kukForm3.iukForm3.soalForm7.jawabanForm7', function ($q) use ($asesiId) {
            $q->where('asesi_id', $asesiId);
        })
        ->orderByRaw("$orderExpr ASC")   // ⬅️ numeric sorting
        ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'status'  => 'not_found',
                'message' => "Data tidak ditemukan untuk pk_id: $pkId dan asesi_id: $asesiId",
            ], 404);
        }

        // ✅ Hitung nilai final hanya di level Elemen
        $elemenFinal = $data->map(function ($elemen) {
            $jumlahKuk = $elemen->kukForm3->count();
            $jumlahK   = 0;

            foreach ($elemen->kukForm3 as $kuk) {
                $totalIuk = $kuk->iukForm3->count();
                $jumlahKIuk = 0;

                foreach ($kuk->iukForm3 as $iuk) {
                    $totalSoal = $iuk->soalForm7->count();
                    $jumlahKSoal = 0;

                    foreach ($iuk->soalForm7 as $soal) {
                        foreach ($soal->jawabanForm7 as $jawaban) {
                            if ($jawaban->keputusan === 'K') {
                                $jumlahKSoal++;
                            }
                        }
                    }

                    $iukFinal = ($totalSoal > 0 && ($jumlahKSoal / $totalSoal) >= 0.5) ? 'K' : 'BK';
                    if ($iukFinal === 'K') $jumlahKIuk++;
                }

                $kukFinal = ($totalIuk > 0 && ($jumlahKIuk / $totalIuk) >= 0.5) ? 'K' : 'BK';
                if ($kukFinal === 'K') $jumlahK++;
            }

            $elemenFinal = ($jumlahKuk > 0 && ($jumlahK / $jumlahKuk) >= 0.5) ? 'K' : 'BK';

            return [
                'id'               => $elemen->id,
                'no_elemen_form_3' => $elemen->no_elemen_form_3,
                'nama_elemen'      => $elemen->isi_elemen, // sesuai kode kamu
                'final'            => $elemenFinal,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => $elemenFinal,
        ]);
    }




    public function ApproveForm12ByAsesi(Request $request, $form12Id)
    {
        // 🔥 NORMALISASI action JADI LOWERCASE
        $action = strtolower($request->action);

        // Validasi parameter
        $validator = Validator::make([
            'form_12_id' => $form12Id,
            'action'     => $action
        ], [
            'form_12_id' => 'required|integer|exists:form_12,form_12_id',
            'action'     => 'required|in:approved,rejected'
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

            // Ambil data form_12
            $form12 = Form12::find($form12Id);
            if (!$form12) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Form 12 tidak ditemukan.'
                ], 404);
            }

            // Ambil parent form_1
            $form1Id = $this->formService->getParentFormIdByFormIdAndAsesiId(
                $form12Id,
                $form12->asesi_id,
                'form_12'
            );
            $form1 = $this->formService->getParentDataByFormId($form1Id);

            // Cek status existing
            $form12Status = $this->formService
                ->getStatusByParentFormIdAndType($form1Id, 'form_12')
                ->first();

            // ===========================================
            // 🔥 PROSES JIKA rejected
            // ===========================================
            if ($action === 'rejected') {

                $this->formService->updateForm12(
                    $form12Id,
                    null,
                    null,
                    'form_12',
                    null,
                    null,
                    null,
                    null,
                    'Rejected'
                );

                $this->formService->updateProgresDanTrack(
                    $form12Id,
                    'form_12',
                    'Rejected',
                    $form12->asesi_id,
                    'Form form_12 telah DITOLAK oleh Asesi'
                );

                // Notifikasi ke asesor
                $this->formService->kirimNotifikasiKeUser(
                    DaftarUser::find($form1->asesor_id),
                    'Form 12 Ditolak',
                    'Form form_12 telah ditolak oleh Asesi.'
                );

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Form 12 berhasil DITOLAK oleh Asesi',
                ]);
            }

            // ===========================================
            // 🔥 PROSES APPROVE
            // ===========================================
            if ($form12Status === 'InAssessment') {

                // Update status form 12
                $this->formService->updateForm12(
                    $form12Id,
                    null,
                    null,
                    'form_12',
                    null,
                    null,
                    null,
                    null,
                    'Completed'
                );

                $this->formService->updateProgresDanTrack(
                    $form12Id,
                    'form_12',
                    'Completed',
                    $form12->asesi_id,
                    'Form form_12 telah di-approve oleh Asesi'
                );

                // Notifikasi ke asesor
                $this->formService->kirimNotifikasiKeUser(
                    DaftarUser::find($form1->asesor_id),
                    'Form 12 Completed',
                    'Form form_12 telah di-approve oleh Asesi.'
                );
            }

            // ===========================================
            // 🔥 INISIALISASI FORM 9 (HANYA APPROVE)
            // ===========================================
            if ($action === 'approved') {

                $isForm9Exist = $this->formService->isFormExistSingle(
                    $form1->asesi_id,
                    $form1->pk_id,
                    'form_9'
                );

                if (!$isForm9Exist) {

                    $form9 = $this->formService->inputForm9(
                        $form1->pk_id,
                        $form1->asesi_id,
                        $form1->asesi_name,
                        $form1->asesor_id,
                        $form1->asesor_name,
                        $form1->no_reg
                    );

                    $this->formService->createProgresDanTrack(
                        $form9->form_9_id,
                        'form_9',
                        'InAssessment',
                        $form1->asesi_id,
                        $form1->form_1_id,
                        'Form 9 sudah dapat diisi Asesi.'
                    );

                    // Inisialisasi jawaban form 9
                    $isAnswerExist = Form9Answer::where('form_9_id', $form9->form_9_id)->exists();

                    if (!$isAnswerExist) {
                        $questions = Form9Question::orderBy('order_no', 'asc')->get();

                        foreach ($questions as $question) {
                            $userId = $question->subject === 'asesi'
                                ? $form1->asesi_id
                                : ($question->subject === 'asesor'
                                    ? $form1->asesor_id
                                    : null);

                            if ($question->has_sub_questions) {

                                $subQuestions = $question->subQuestions()->orderBy('order_no')->get();

                                foreach ($subQuestions as $subQuestion) {
                                    Form9Answer::create([
                                        'form_9_id'       => $form9->form_9_id,
                                        'question_id'     => $question->question_id,
                                        'sub_question_id' => $subQuestion->sub_question_id,
                                        'answer_text'     => null,
                                        'is_checked'      => false,
                                        'user_id'         => $userId
                                    ]);
                                }

                            } else {
                                Form9Answer::create([
                                    'form_9_id'       => $form9->form_9_id,
                                    'question_id'     => $question->question_id,
                                    'sub_question_id' => null,
                                    'answer_text'     => null,
                                    'is_checked'      => null,
                                    'user_id'         => $userId
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $action === 'approved'
                    ? 'Form 12 berhasil di-approve oleh Asesi'
                    : 'Form 12 berhasil ditolak oleh Asesi',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }




}
