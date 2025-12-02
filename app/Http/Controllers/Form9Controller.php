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
use App\Models\FormBandingAsesmen;
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
            // Ambil input subject dan pk_id, lalu normalisasi ke lowercase
            $subject = strtolower($request->input('subject'));
            $pkId = $request->input('pk_id');

            // Query ke model dengan relasi subQuestions
            $questions = Form9Question::with([
                    'subQuestions' => function ($query) {
                        $query->orderBy('order_no', 'asc');
                    }
                ])
                ->when($subject, function ($query, $subject) {
                    // Bandingkan dalam lowercase supaya konsisten
                    $query->whereRaw('LOWER(subject) = ?', [$subject]);
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

            // Transform hasil agar formatnya mirip dengan fungsi 2 (tanpa answers)
            $result = $questions->map(function ($q) {
                $hasSub = $q->subQuestions->isNotEmpty();

                return [
                    'question_id' => $q->question_id,
                    'pk_id'       => $q->pk_id,
                    'section'     => $q->section,
                    'sub_section' => $q->sub_section,
                    'question_text' => $q->question_text,
                    'criteria'    => $q->criteria,
                    'order_no'    => $q->order_no,
                    'subject'     => $q->subject,
                    'has_sub_questions' => $hasSub,

                    'sub_questions' => $q->subQuestions->map(function ($sq) {
                        return [
                            'sub_question_id' => $sq->sub_question_id,
                            'sub_label'       => $sq->sub_label,
                            'order_no'        => $sq->order_no,
                        ];
                    })->values(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $result
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error ambil pertanyaan Form 9: ' . $e->getMessage());

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
                    'has_sub_questions' => (bool) $hasSub,
                    
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
        /**
         * 🔥 NORMALISASI INPUT SUBJECT MENJADI LOWERCASE
         */
        if ($request->has('subject')) {
            $request->merge([
                'subject' => strtolower($request->input('subject'))
            ]);
        }

        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|in:asesor,asesi',
            'answers' => 'required|array',

            // Validasi pertanyaan utama
            'answers.*.question_id' => 'required|integer|exists:form9_questions,question_id',
            'answers.*.answer_text' => 'nullable|string',

            // Opsional: checkbox khusus asesi
            'answers.*.is_checked' => 'nullable|boolean',

            // Validasi sub pertanyaan
            'answers.*.sub_questions' => 'nullable|array',
            'answers.*.sub_questions.*.sub_question_id' => 'required|integer|exists:form9_sub_questions,sub_question_id',
            'answers.*.sub_questions.*.answer_text' => 'nullable|string',
            'answers.*.sub_questions.*.notes' => 'nullable|string',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $subject = $data['subject'];

        try {

            // 🔍 CEK apakah subjek ini sudah mengisi sebelumnya
            $alreadyFilled = \DB::table('form9_answers as a')
                ->join('form9_questions as q', 'a.question_id', '=', 'q.question_id')
                ->where('a.form_9_id', $form9Id)
                ->where('q.subject', $subject)
                ->exists();

            /**
             * 🔥 Jika sudah ada → HAPUS jawaban lama (update mode)
             */
            if ($alreadyFilled) {
                \DB::table('form9_answers as a')
                    ->join('form9_questions as q', 'a.question_id', '=', 'q.question_id')
                    ->where('a.form_9_id', $form9Id)
                    ->where('q.subject', $subject)
                    ->delete();
            }

            /**
             * 💾 INSERT jawaban baru (baik insert awal atau update)
             */
            $this->processAnswersBySubject($form9Id, $data['answers'], $subject);
            
            /**
             * 🔔 Trigger event/notifikasi setelah simpan
             */
            $this->afterAnswerSaved($form9Id, $subject);

            return response()->json([
                'success' => true,
                'message' => $alreadyFilled
                    ? 'Jawaban berhasil diperbarui'
                    : 'Jawaban berhasil disimpan'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error simpan jawaban Form 9: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan jawaban',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Proses jawaban sesuai subject (asesor / asesi)
     */
    private function processAnswersBySubject($form9Id, array $answers, $subject)
    {
        $form9 = Form9::with(['asesi', 'asesor', 'answers'])
            ->where('form_9_id', $form9Id)
            ->first();
        Log::info($form9);

        // Jika tidak ditemukan
        if (!$form9) {
            \Log::error("Form9 dengan ID {$form9Id} tidak ditemukan.");
            return null;
        }

        // Ambil data form1 berdasarkan form9
        Log::info("Mengambil form1Id dari form9Id: {$form9Id} dan asesi_id: {$form9->asesi_id}");
        $form1Id = $this->formService->getParentFormIdByFormIdAndAsesiId($form9Id, $form9->asesi_id, 'form_9');
        $form1   = $this->formService->getParentDataByFormId($form1Id);
        Log::info("Ditemukan form1:", ['form1' => $form1]);

        // Tentukan user_id berdasarkan subject
        $userId = null;
        if ($subject === 'asesi') {
            $userId = $form1->asesi_id ?? null;
        } elseif ($subject === 'asesor') {
            $userId = $form1->asesor_id ?? null;
        }

        foreach ($answers as $q) {
            $question = \App\Models\Form9Question::find($q['question_id']);
            if (!$question || $question->subject !== $subject) {
                continue;
            }

            // 🔹 Data dasar untuk disimpan
            $baseData = [
                'user_id' => $userId, // ✅ gunakan user_id sesuai subject
            ];

            // 🔹 Tambahkan answer_text kalau ada
            if (isset($q['answer_text'])) {
                $baseData['answer_text'] = $q['answer_text'];
            }

            // 🔹 Tambahkan is_checked hanya untuk asesi
            if ($subject === 'asesi' && array_key_exists('is_checked', $q)) {
                $baseData['is_checked'] = $q['is_checked'] ? 1 : 0;
            }

            // ✅ Simpan jawaban utama
            if (!isset($q['sub_questions']) || empty($q['sub_questions'])) {
                Form9Answer::updateOrCreate(
                    [
                        'form_9_id' => $form9Id,
                        'question_id' => $q['question_id'],
                        'sub_question_id' => null,
                    ],
                    $baseData
                );
            }

            // ✅ Simpan jawaban sub-question (jika ada)
            if (!empty($q['sub_questions'])) {
                foreach ($q['sub_questions'] as $sq) {
                    Form9Answer::updateOrCreate(
                        [
                            'form_9_id' => $form9Id,
                            'question_id' => $q['question_id'],
                            'sub_question_id' => $sq['sub_question_id'],
                        ],
                        [
                            'answer_text' => $sq['answer_text'] ?? null,
                            'user_id' => $userId,

                            // hanya asesor yang boleh isi notes
                            'notes' => ($subject === 'asesor')
                                ? ($sq['notes'] ?? null)
                                : null,
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
        $form9 = Form9::with(['asesi', 'asesor', 'answers'])
            ->where('form_9_id', $form9Id)
            ->first();

        if (!$this->formService) {
            \Log::error("formService NULL di afterAnswerSaved");
            return;
        }

        $form1Id = $this->formService->getParentFormIdByFormIdAndAsesiId($form9Id, $form9->asesi_id, 'form_9');
        $form1   = $this->formService->getParentDataByFormId($form1Id);

        // Ambil status Form 9
        $form9Status = $this->formService
            ->getStatusByParentFormIdAndType($form1Id, 'form_9')
            ->first();

        // Tentukan status baru
        $newStatus = $subject === 'asesi' ? 'Submitted' : 'Completed';

        // Update Form 9
        $this->formService->updateForm9(
            $form9Id,
            null, null, null, null,
            null, null, null,
            null,
            $newStatus
        );

        // Update progres dan track
        $this->formService->updateProgresDanTrack(
            $form9Id,
            'form_9',
            $newStatus,
            $form1->asesi_id,
            "Form 9 telah diisi oleh {$subject} dengan status {$newStatus}"
        );

        // Jika yang submit adalah asesi → cek kondisi banding
        if ($subject === 'asesi') {

            \Log::info("Cek otomatis inisialisasi Form Banding untuk form1_id = {$form1Id}");

            // 🔥 PANGGIL FUNCTION BARU
            $result = $this->checkAndInitBandingAsesmen($form1Id);

            \Log::info("Hasil cek banding otomatis:", $result);
        }

        // Notifikasi
        if ($subject === 'asesi') {
            $this->formService->kirimNotifikasiKeUser(
                DaftarUser::find($form1->asesor_id),
                'Form 9 Submitted',
                'Form 9 telah diisi oleh Asesi dan menunggu persetujuan Asesor.'
            );
        } else {
            $this->formService->kirimNotifikasiKeUser(
                DaftarUser::find($form1->asesi_id),
                'Form 9 Approved',
                'Form 9 telah disetujui oleh Asesor.'
            );
        }
    }



    /**
     * Mengecek status Form 12 & Form 9.
     * Jika Form 12 = Rejected dan Form 9 = Submitted → otomatis inisialisasi Form Banding Asesmen (Form 8)
     */
    public function checkAndInitBandingAsesmen(int $form1Id)
    {
        Log::info("=== [AUTO-BANDING] Mulai proses checkAndInitBandingAsesmen ===", [
            'form1_id' => $form1Id
        ]);

        try {
            // ======================================================
            // 1. Ambil data Form 1
            // ======================================================
            $form1 = $this->formService->getParentDataByFormId($form1Id);

            if (!$form1) {
                Log::warning("[AUTO-BANDING] Form 1 tidak ditemukan.", [
                    'form1_id' => $form1Id
                ]);

                return [
                    'success' => false,
                    'message' => "Data Form 1 tidak ditemukan"
                ];
            }
            // ======================================================
            // 2. Ambil status Form 12 & Form 9
            // ======================================================
            $form12Status = $this->formService
                ->getStatusByParentFormIdAndType($form1Id, 'form_12')
                ->first();

            $form9Status = $this->formService
                ->getStatusByParentFormIdAndType($form1Id, 'form_9')
                ->first();


            // ======================================================
            // 3. Validasi kondisi
            // ======================================================
            if (!$form12Status) {
                Log::warning("[AUTO-BANDING] Form 12 tidak ditemukan.");
            }
            if (!$form9Status) {
                Log::warning("[AUTO-BANDING] Form 9 tidak ditemukan.");
            }

            if (
                !$form12Status || !$form9Status ||
                $form12Status !== 'Rejected' ||
                $form9Status !== 'Submitted'
            ) {

                return [
                    'success' => false,
                    'message' => 'Syarat inisialisasi Form Banding tidak terpenuhi.'
                ];
            }

            Log::info("[AUTO-BANDING] Syarat inisialisasi terpenuhi (Form 12 Rejected & Form 9 Submitted)");


            // ======================================================
            // 4. Cek apakah Form 8 sudah ada
            // ======================================================
            $existing = FormBandingAsesmen::where('form_1_id', $form1Id)->first();

            if ($existing) {
                Log::info("[AUTO-BANDING] Form 8 sudah ada, tidak perlu membuat baru.", [
                    'banding_id' => $existing->banding_id
                ]);

                return [
                    'success' => true,
                    'message' => 'Form Banding sudah ada sebelumnya.',
                    'data'    => $existing
                ];
            }


            // ======================================================
            // 5. CREATE FORM 8
            // ======================================================
            Log::info("[AUTO-BANDING] Membuat FORM 8 baru...");

            DB::beginTransaction();

            $formBanding = FormBandingAsesmen::create([
                'form_1_id'          => $form1Id,
                'asesi_id'           => $form1->asesi_id,
                'asesor_id'          => $form1->asesor_id,
                'tanggal_asesmen'    => $form1->asesor_date,
                'alasan_banding' => 'Belum diisi',
                'persetujuan_asesi'  => false,
                'persetujuan_asesor' => false,
            ]);

            Log::info("[AUTO-BANDING] Form 8 berhasil dibuat", [
                'banding_id' => $formBanding->banding_id
            ]);


            // ======================================================
            // 6. Track progress
            // ======================================================
            $this->formService->createProgresDanTrack(
                $formBanding->banding_id,
                'form_8',
                'InAssessment',
                $form1->asesi_id,
                $form1->form_1_id,
                'Form 8 diinisialisasi otomatis (Form 12 Rejected & Form 9 Submitted).'
            );

            Log::info("[AUTO-BANDING] Track progres Form 8 berhasil dibuat.");

            DB::commit();

            Log::info("=== [AUTO-BANDING] SELESAI: Form 8 terinisialisasi dengan sukses ===");

            return [
                'success' => true,
                'message' => 'Form 8 berhasil diinisialisasi.',
                'data'    => $formBanding
            ];


        } catch (\Exception $e) {

            DB::rollBack();

            Log::error("[AUTO-BANDING] ERROR: Gagal inisialisasi otomatis Form 8", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan.',
                'error'   => $e->getMessage()
            ];
        }
    }



}
