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
use App\Models\Form9Question;
use App\Models\Form9Answer;
use App\Models\Form9;
use App\Models\KompetensiTrack;
use App\Models\KompetensiProgres;
use App\Models\Notification;
use App\Models\SoalForm7;
use App\Models\Form12;
use App\Models\JawabanForm7;
use App\Models\PoinForm4;
use Carbon\Carbon;

class Form9Controller extends BaseController
{

	protected $formService;

    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
    }

    public function getQuestionsBySubject(Request $request)
    {
        try {
            // Ambil input subject (section) dan pk_id
            $subject = $request->input('subject');
            $pkId = $request->input('pk_id');

            // Query ke model
            $questions = Form9Question::query()
                ->when($subject, function ($query, $subject) {
                    $query->where('subject', $subject);
                })
                ->when($pkId, function ($query, $pkId) {
                    $query->where('pk_id', $pkId);
                })
                ->orderBy('order_no', 'asc')
                ->get();

            // Kalau tidak ada hasil
            if ($questions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => ($subject || $pkId) 
                        ? "Tidak ada pertanyaan ditemukan untuk filter yang diberikan"
                        : "Tidak ada pertanyaan ditemukan",
                    'data' => []
                ], 404);
            }

            // Kalau berhasil
            return response()->json([
                'success' => true,
                'data' => $questions
            ], 200);

        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Error ambil pertanyaan Form 9: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil pertanyaan',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function getQuestionsAndAnswersByFormId($form9Id)
    {
        try {
            $questions = Form9Question::with([
                'answers' => function ($query) use ($form9Id) {
                    $query->where('form_9_id', $form9Id);
                },
                'subQuestions' => function ($q) {
                    $q->orderBy('order_no', 'asc');
                },
                'subQuestions.answers' => function ($query) use ($form9Id) {
                    $query->where('form_9_id', $form9Id);
                }
            ])
            // urutkan berdasarkan subject: asesi dulu, lalu asesor
            ->orderByRaw("CASE WHEN subject = 'asesi' THEN 1 WHEN subject = 'asesor' THEN 2 ELSE 3 END")
            ->orderBy('order_no', 'asc')
            ->get();

            if ($questions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak ada pertanyaan untuk form_9_id: {$form9Id}",
                    'data' => []
                ], 404);
            }

            $result = $questions->map(function ($q) {
                $hasSub = $q->subQuestions->isNotEmpty();

                return [
                    'question_id' => $q->question_id,
                    'section' => $q->section,
                    'sub_section' => $q->sub_section,
                    'question_text' => $q->question_text,
                    'criteria' => $q->criteria,
                    'order_no' => $q->order_no,
                    'subject' => $q->subject,
                    'has_sub_questions' => $hasSub ? 1 : 0,
                    
                    // Jika tidak punya sub question, konversi answer_text ke boolean
                    'answers' => $hasSub ? [] : $q->answers->map(function ($a) {
                        return [
                            'answer_id' => $a->answer_id,
                            'question_id' => $a->question_id,
                            'form_9_id' => $a->form_9_id,
                            'answer_text' => $a->answer_text === '1' ? true : ($a->answer_text === '0' ? false : $a->answer_text),
                        ];
                    }),

                    // Untuk sub_questions
                    'sub_questions' => $q->subQuestions->map(function ($sq) {
                        return [
                            'sub_question_id' => $sq->sub_question_id,
                            'sub_label' => $sq->sub_label,
                            'order_no' => $sq->order_no,
                            'answers' => $sq->answers->map(function ($sa) {
                                return [
                                    'answer_id' => $sa->answer_id,
                                    'sub_question_id' => $sa->sub_question_id,
                                    'form_9_id' => $sa->form_9_id,
                                    'answer_text' => $sa->answer_text === '1' ? true : ($sa->answer_text === '0' ? false : $sa->answer_text),
                                ];
                            })
                        ];
                    })->values(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $result
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error ambil pertanyaan & jawaban Form 9: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil pertanyaan & jawaban',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function saveOrUpdateAnswers(Request $request, $form9Id)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|in:asesor,asesi',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:form9_questions,question_id',
            'answers.*.answer_text' => 'nullable|string',
            'answers.*.sub_questions' => 'nullable|array',
            'answers.*.sub_questions.*.sub_question_id' => 'required|integer|exists:form9_sub_questions,sub_question_id',
            'answers.*.sub_questions.*.answer_text' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $subject = $data['subject'];

        try {
            // Simpan jawaban sesuai subject
            $this->processAnswersBySubject($form9Id, $data['answers'], $subject);

            // Setelah berhasil simpan, update status & kirim notifikasi
            $this->afterAnswerSaved($form9Id, $subject);

            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil disimpan/diperbarui'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error simpan jawaban Form 9: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan jawaban',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Proses jawaban sesuai subject (asesor / asesi)
     */
    private function processAnswersBySubject($form9Id, array $answers, $subject)
    {
        foreach ($answers as $q) {
            $question = \App\Models\Form9Question::find($q['question_id']);
            if (!$question || $question->subject !== $subject) {
                continue;
            }

            // Jawaban utama
            if (!isset($q['sub_questions']) || empty($q['sub_questions'])) {
                Form9Answer::updateOrCreate(
                    [
                        'form_9_id' => $form9Id,
                        'question_id' => $q['question_id'],
                        'sub_question_id' => null,
                    ],
                    [
                        'answer_text' => $q['answer_text'] ?? null,
                        'user_id' => auth()->id() ?? null,
                    ]
                );
            }

            // Jawaban sub-question
            if (isset($q['sub_questions']) && !empty($q['sub_questions'])) {
                foreach ($q['sub_questions'] as $sq) {
                    Form9Answer::updateOrCreate(
                        [
                            'form_9_id' => $form9Id,
                            'question_id' => $q['question_id'],
                            'sub_question_id' => $sq['sub_question_id'],
                        ],
                        [
                            'answer_text' => $sq['answer_text'] ?? null,
                            'user_id' => auth()->id() ?? null,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Setelah jawaban tersimpan → update status form & kirim notifikasi ke pihak lawan
     */
    private function afterAnswerSaved($form9Id, $subject)
    {
        \Log::info("afterAnswerSaved dipanggil", [
            'form9Id' => $form9Id,
            'subject' => $subject
        ]);

        // cek apakah formService ada
        if (!$this->formService) {
            \Log::error("formService NULL di afterAnswerSaved");
            return; // stop biar ga error fatal
        }

        // ambil form induk dari form_9
        $form1Id = $this->formService->getParentFormIdByFormId($form9Id);
        \Log::info("Hasil getParentFormIdByFormId", ['form1Id' => $form1Id]);

        $form1 = $this->formService->getParentDataByFormId($form1Id);
        \Log::info("Hasil getParentDataByFormId", ['form1' => $form1]);

        // cek status form_9
        $form9Status = $this->formService
            ->getStatusByParentFormIdAndType($form1Id, 'form_9')
            ->first();

        \Log::info("Status form_9", ['status' => $form9Status]);

        if ($form9Status === 'Submitted' || $form9Status === 'InAssessment') {
            // update status jadi Approved
            $this->formService->updateForm9(
                $form9Id,
                null,   // pk_id
                null,   // asesi_id
                null,   // asesi_name
                null,   // asesi_date
                null,   // asesor_id
                null,   // asesor_name
                null,   // asesor_date
                null,   // no_reg
                'Approved' // status
            );



            $this->formService->updateProgresDanTrack(
                $form9Id,
                'form_9',
                'Approved',
                Auth::id(),
                "Form 9 telah di-approve oleh {$subject}"
            );

            // kirim notifikasi ke pihak lawan
            if ($subject === 'asesi') {
                \Log::info("Kirim notifikasi ke Asesor", [
                    'asesor_id' => $form1->asesor_id ?? null
                ]);

                $this->formService->kirimNotifikasiKeUser(
                    DaftarUser::find($form1->asesor_id),
                    'Form 9 Approved',
                    'Form 9 telah diisi & disetujui oleh Asesi.'
                );
            } else {
                \Log::info("Kirim notifikasi ke Asesi", [
                    'asesi_id' => $form1->asesi_id ?? null
                ]);

                $this->formService->kirimNotifikasiKeUser(
                    DaftarUser::find($form1->asesi_id),
                    'Form 9 Approved',
                    'Form 9 telah diisi & disetujui oleh Asesor.'
                );
            }
        }
    }



}
