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

    public function getByPkId(Request $request)
    {
        // ✅ Validasi request
        $validator = Validator::make($request->all(), [
            'pk_id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pkId = $request->input('pk_id');

        // ✅ Query ambil data nested
        $data = ElemenForm3::with([
                'kukForm3' => function ($q) {
                    $q->orderBy('no_kuk', 'asc')
                    ->with([
                        'iukForm3' => function ($q2) {
                            $q2->orderBy('no_iuk', 'asc')
                                ->with([
                                    'soalForm7' => function ($q3) {
                                        $q3->select('id', 'iuk_form3_id')
                                            ->with([
                                                'jawabanForm7' => function ($q4) {
                                                    $q4->select('id', 'soal_form7_id', 'keputusan');
                                                }
                                            ]);
                                    }
                                ]);
                        }
                    ]);
                }
            ])
            ->where('pk_id', $pkId)
            ->orderBy('no_elemen_form_3', 'asc')
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'status'  => 'not_found',
                'message' => "Data tidak ditemukan untuk pk_id: $pkId",
            ], 404);
        }

        // ✅ Hitung nilai final per level
        $data->transform(function ($elemen) {
            $elemen->kukForm3->transform(function ($kuk) {
                $kuk->iukForm3->transform(function ($iuk) {
                    $totalSoal = $iuk->soalForm7->count();
                    $jumlahK   = 0;

                    foreach ($iuk->soalForm7 as $soal) {
                        foreach ($soal->jawabanForm7 as $jawaban) {
                            if ($jawaban->keputusan === 'K') {
                                $jumlahK++;
                            }
                        }
                    }

                    $iuk->final = ($totalSoal > 0 && ($jumlahK / $totalSoal) >= 0.5) ? 'K' : 'BK';
                    return $iuk;
                });

                // Hitung nilai KUK dari seluruh IUK
                $totalIuk = $kuk->iukForm3->count();
                $jumlahK  = $kuk->iukForm3->where('final', 'K')->count();

                $kuk->final = ($totalIuk > 0 && ($jumlahK / $totalIuk) >= 0.5) ? 'K' : 'BK';
                return $kuk;
            });

            // Hitung nilai Elemen dari seluruh KUK
            $totalKuk = $elemen->kukForm3->count();
            $jumlahK  = $elemen->kukForm3->where('final', 'K')->count();

            $elemen->final = ($totalKuk > 0 && ($jumlahK / $totalKuk) >= 0.5) ? 'K' : 'BK';
            return $elemen;
        });

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    public function ApproveForm12ByAsesi(Request $request, $form12Id)
    {
        // Validasi ID Form 12
        $validator = Validator::make(['form_12_id' => $form12Id], [
            'form_12_id' => 'required|integer|exists:form_12,form_12_id',
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

            // Ambil form induk (form_1) berdasarkan relasi
            $form1Id = $this->formService->getParentFormIdByFormId($form12Id);
            $form1   = $this->formService->getParentDataByFormId($form1Id);

            // Ambil status form 12 sesuai form_type
            $form12Status = $this->formService
                ->getStatusByParentFormIdAndType($form1Id, 'form_12')
                ->first();

            if ($form12Status === 'InAssessment') {
                // Update status form 12
                $updatedForm12 = $this->formService->updateForm12(
                    $form12Id,
                    null, // pkId
                    null, // daftarTilikId
                    'form_12', // form_type
                    null, // asesiId
                    null, // asesiName
                    null, // asesorId
                    null, // asesorName
                    'Approved' // status
                );

                // Update progres & track
                $this->formService->updateProgresDanTrack(
                    $form12Id,
                    'form_12',
                    'Approved',
                    Auth::id(),
                    'Form form_12 telah di-approve oleh Asesi'
                );

                // Kirim notifikasi ke asesor
                $this->formService->kirimNotifikasiKeUser(
                    DaftarUser::find($form1->asesor_id),
                    'Form form_12 Approved',
                    'Form form_12 telah di-approve oleh Asesi.'
                );
            }

            // ===== INISIALISASI FORM 9 =====
            $isForm9Exist = $this->formService->isFormExistSingle(
                $form1->asesi_id,
                $form1->pk_id,
                'form_9'
            );

            if (!$isForm9Exist) {
                Log::info("Form 9 belum ada, membuat form 9...");
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
                    'Form 9 sudah dapat diisi.'
                );

                // ===== INIT JAWABAN FORM 9 =====
                $isAnswerExist = Form9Answer::where('form_9_id', $form9->form_9_id)->exists();

                if (!$isAnswerExist) {
                    $questions = Form9Question::orderBy('order_no', 'asc')->get();

                    foreach ($questions as $question) {
                        // Tentukan siapa user yang menjawab berdasarkan subject
                        $userId = null;
                        if ($question->subject === 'asesi') {
                            $userId = $form1->asesi_id;
                        } elseif ($question->subject === 'asesor') {
                            $userId = $form1->asesor_id;
                        }

                        // Cek apakah pertanyaan punya sub pertanyaan
                        if ($question->has_sub_questions) {
                            $subQuestions = $question->subQuestions()->orderBy('order_no', 'asc')->get();

                            foreach ($subQuestions as $subQuestion) {
                                Form9Answer::create([
                                    'form_9_id'       => $form9->form_9_id,
                                    'question_id'     => $question->question_id,
                                    'sub_question_id' => $subQuestion->sub_question_id,
                                    'answer_text'     => null, // default kosong
                                    'is_checked'      => false, // default tidak dicentang
                                    'user_id'         => $userId,
                                ]);
                            }
                        } else {
                            // Jika tidak punya sub pertanyaan, buat jawaban normal
                            Form9Answer::create([
                                'form_9_id'       => $form9->form_9_id,
                                'question_id'     => $question->question_id,
                                'sub_question_id' => null,
                                'answer_text'     => null,
                                'is_checked'      => null,
                                'user_id'         => $userId,
                            ]);
                        }
                    }

                    Log::info("Jawaban form 9 berhasil diinisialisasi untuk asesi {$form1->asesi_id} dan asesor {$form1->asesor_id}");
                } else {
                    Log::info("Jawaban untuk form_9_id {$form9->form_9_id} sudah ada, tidak dibuat ulang.");
                }



            } else {
                Log::info("Form 9 sudah ada, tidak membuat ulang.");
            }


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form form_12 berhasil di-approve oleh Asesi',
                'data'    => $form12Status
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
