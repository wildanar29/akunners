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

    public function getQuestionsBySubject(Request $request)
    {
        try {
            // Ambil input subject (section) dan pk_id
            $subject = $request->input('subject');
            $pkId = $request->input('pk_id');

            // Query ke model
            $questions = Form9Question::query()
                ->when($subject, function ($query, $subject) {
                    $query->where('section', $subject);
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
                    // Jika ada sub question, jawaban pertanyaan utama dikosongkan
                    'answers' => $hasSub ? [] : $q->answers,
                    'sub_questions' => $q->subQuestions->map(function ($sq) {
                        return [
                            'sub_question_id' => $sq->sub_question_id,
                            'sub_label' => $sq->sub_label,
                            'order_no' => $sq->order_no,
                            'answers' => $sq->answers
                        ];
                    })->values(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $result
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error ambil pertanyaan & jawaban Form 9: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil pertanyaan & jawaban',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function saveOrUpdateAnswers(Request $request, $form9Id)
    {
        // Validasi manual menggunakan Validator
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|in:asesor,asesi', // filter subject
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
        $subject = $data['subject']; // subject dari request (asesor / asesi)

        try {
            foreach ($data['answers'] as $q) {
                // Ambil pertanyaan untuk cek subject
                $question = \App\Models\Form9Question::find($q['question_id']);
                if (!$question || $question->subject !== $subject) {
                    // Skip jika subject tidak sesuai
                    continue;
                }

                // Jawaban untuk pertanyaan utama (tanpa sub question)
                if (!isset($q['sub_questions']) || empty($q['sub_questions'])) {
                    Form9Answer::updateOrCreate(
                        [
                            'form_9_id' => $form9Id,
                            'question_id' => $q['question_id'],
                            'sub_question_id' => null,
                        ],
                        [
                            'answer_text' => $q['answer_text'] ?? null,
                            'user_id' => auth()->id() ?? null, // sesuaikan jika pakai auth
                        ]
                    );
                }

                // Jawaban untuk sub question
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

            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil disimpan/diperbarui'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error simpan jawaban Form 9: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan jawaban',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
