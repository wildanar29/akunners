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
use App\Models\Form4cAttemptSummary;
use App\Models\JawabanForm4c;
use App\Models\DaftarUser;
use App\Models\ElemenForm3;
use App\Models\JawabanForm4d;
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
use App\Models\Form4c;
use App\Models\Notification;
use Carbon\Carbon;

class Form4cController extends BaseController
{

	protected $formService;

    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
    }

    public function getAllPertanyaanForm4c(Request $request)
    {
        // Validasi agar pk_id wajib diisi dan harus berupa integer
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

        // Query dengan relasi
        $query = PertanyaanForm4c::with([
            'question.questionChoices.choice'
        ])->orderBy('urutan');

        // Filter berdasarkan pk_id
        $query->whereHas('iuk', function ($q) use ($request) {
            $q->where('pk_id', $request->pk_id);
        });

        $data = $query->get();

        // Format ulang data agar hanya field yang dibutuhkan saja yang tampil
        $filtered = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'iuk_form_3_id' => $item->iuk_form_3_id,
                'urutan' => $item->urutan,
                'question' => [
                    'id' => $item->question->id,
                    'question_text' => $item->question->question_text,
                    'question_choices' => $item->question->questionChoices->map(function ($qc) {
                        return [
                            'id' => $qc->id,
                            'is_correct' => $qc->is_correct,
                            'choice' => [
                                'id' => $qc->choice->id,
                                'choice_label' => $qc->choice->choice_label,
                                'choice_text' => $qc->choice->choice_text,
                            ]
                        ];
                    })
                ]
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Data pertanyaan berhasil diambil',
            'data' => $filtered,
        ]);
    }

    public function storeJawabanForm4c(Request $request)
    {
        // Validasi data
        $validator = Validator::make($request->all(), [
            'form_1_id' => 'required|integer|exists:form_1,form_1_id',
            'asesi_id' => 'required|integer|exists:users,user_id',
            'jawaban' => 'required|array',
            'jawaban.*.pertanyaan_form4c_id' => 'required|integer|exists:pertanyaan_form4c,id',
            'jawaban.*.question_choice_id' => 'required|integer|exists:question_choice,id',
            'jawaban.*.catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'data' => [
                    'is_passed' => false,
                    'errors' => $validator->errors(),
                ],
            ], 422);
        }

        // Ambil attempt terakhir
        $lastAttempt = JawabanForm4c::where('form_1_id', $request->form_1_id)
            ->where('user_id', $request->asesi_id)
            ->max('attempt');

        $currentAttempt = $lastAttempt ? $lastAttempt + 1 : 1;

        // Batasi maksimum attempt 3 kali
        if ($currentAttempt > 3) {
            return response()->json([
                'status' => false,
                'message' => 'Anda sudah mencapai batas maksimum 3 kali attempt. Jawaban tidak dapat disimpan lagi.',
                'data' => [
                    'is_passed' => false,
                    'attempt_terakhir' => $lastAttempt,
                ],
            ], 200);
        }

        DB::beginTransaction();

        try {
            $duplikatIds = [];

            foreach ($request->jawaban as $item) {

                $exists = JawabanForm4c::where('form_1_id', $request->form_1_id)
                    ->where('user_id', $request->asesi_id)
                    ->where('pertanyaan_form4c_id', $item['pertanyaan_form4c_id'])
                    ->where('attempt', $currentAttempt)
                    ->exists();

                if ($exists) {
                    $duplikatIds[] = $item['pertanyaan_form4c_id'];
                    continue;
                }

                $questionChoice = \App\Models\QuestionChoice::with('choice')->find($item['question_choice_id']);

                if (!$questionChoice || !$questionChoice->choice) {
                    throw new \Exception("Choice data not found for question_choice_id: {$item['question_choice_id']}");
                }

                JawabanForm4c::create([
                    'form_1_id' => $request->form_1_id,
                    'user_id' => $request->asesi_id,
                    'pertanyaan_form4c_id' => $item['pertanyaan_form4c_id'],
                    'question_choice_id' => $item['question_choice_id'],
                    'catatan' => $item['catatan'] ?? null,
                    'choice_label' => $questionChoice->choice->choice_label,
                    'is_correct' => $questionChoice->is_correct,
                    'attempt' => $currentAttempt,
                ]);
            }

            DB::commit();

            $summary = $this->hitungNilai4c($request->form_1_id, $request->asesi_id);

            // Simpan summary attempt
            Form4cAttemptSummary::create([
                'form_1_id' => $request->form_1_id,
                'user_id' => $request->asesi_id,
                'attempt' => $currentAttempt,
                'tanggal_attempt' => Carbon::now(),
                'total_jawaban' => $summary['total_jawaban'],
                'jawaban_benar' => $summary['jawaban_benar'],
                'jawaban_salah' => $summary['jawaban_salah'],
                'nilai' => $summary['nilai'],
                'skor' => $summary['skor'],
            ]);

            // Jika ada duplikat (masih attempt yang sama)
            if (!empty($duplikatIds)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Beberapa pertanyaan pada attempt ini sudah ada dan tidak disimpan ulang.',
                    'data' => [
                        'is_passed' => $summary['is_passed'] ?? true,
                        'duplikat_pertanyaan_ids' => $duplikatIds,
                        'summary' => $summary,
                        'attempt' => $currentAttempt,
                    ],
                ], 409);
            }

            // Update progres dan kirim notifikasi
            $form_4c_id = $this->formService->getFormIdsByParentFormIdAndType($request->form_1_id, 'Form_4c');

            $this->formService->updateProgresDanTrack(
                $form_4c_id,
                'form_4c',
                'Submitted',
                $request->asesi_id,
                'Form 4C telah di isi oleh Asesi'
            );

            $form1 = $this->formService->getParentDataByFormId($request->form_1_id);

            $this->formService->kirimNotifikasiKeUser(
                DaftarUser::find($form1->asesor_id),
                'Form 4C Submitted',
                'Form 4C telah di-submit oleh Asesi.'
            );

            return response()->json([
                'status' => true,
                'message' => 'Semua jawaban berhasil disimpan.',
                'data' => [
                    'is_passed' => $summary['is_passed'] ?? false,
                    'summary' => $summary,
                    'attempt' => $currentAttempt,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan jawaban.',
                'data' => [
                    'is_passed' => false,
                    'error' => $e->getMessage(),
                ],
            ], 500);
        }
    }


    public function getRiwayatAttempt4c($form1Id, $asesiId)
    {
        $riwayat = Form4cAttemptSummary::where('form_1_id', $form1Id)
            ->where('user_id', $asesiId)
            ->orderBy('attempt', 'asc')
            ->get();

        if ($riwayat->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Belum ada riwayat attempt untuk Form 4C ini.',
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Riwayat attempt Form 4C berhasil diambil.',
            'data' => $riwayat
        ]);
    }

    /**
     * 🔥 Fungsi tambahan untuk menghitung skor 4C
     * Tidak mengubah query existing
     */
    private function hitungNilai4c($form1Id, $userId)
    {
        // Ambil attempt terbesar (default 1, jika re-attempt akan >1)
        $lastAttempt = \App\Models\JawabanForm4c::where('form_1_id', $form1Id)
            ->where('user_id', $userId)
            ->max('attempt');

        // Ambil jawaban berdasarkan attempt terbesar
        $jawaban = \App\Models\JawabanForm4c::where('form_1_id', $form1Id)
            ->where('user_id', $userId)
            ->where('attempt', $lastAttempt)
            ->get();

        $total = $jawaban->count();
        $benar = $jawaban->where('is_correct', 1)->count();

        return [
            'total_jawaban' => $total,
            'jawaban_benar' => $benar,
            'jawaban_salah' => $total - $benar,
            // 'skor' => $benar,
            'nilai' => $total > 0 ? round(($benar / $total) * 100, 2) . '' : '0',
            'attempt' => $lastAttempt, // opsional, jika ingin tahu attempt mana yang dinilai
        ];
    }



    public function getSoalDanJawabanForm4c(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pk_id' => 'required|integer',
            'group_no' => 'required|string',
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

        $pkId      = $request->input('pk_id');
        $groupNo   = $request->input('group_no');
        $form1Id   = $request->input('form_1_id');
        $userId    = $request->input('user_id');

        // Ambil IUK + pertanyaan
        $iukList = \App\Models\IukModel::whereRaw('FIND_IN_SET(?, group_no)', [$groupNo])
            ->orderByRaw('CAST(no_iuk AS UNSIGNED)')
            ->with([
                'pertanyaanForm4c' => function ($query) {
                    $query->orderBy('urutan')->with([
                        'question.questionChoices.choice'
                    ]);
                }
            ])
            ->where('pk_id', $pkId)
            ->get(['iuk_form3_id', 'no_iuk', 'group_no', 'iuk_name']);

        // Ambil jawaban user
        $lastAttempt = \App\Models\JawabanForm4c::where('form_1_id', $form1Id)
            ->where('user_id', $userId)
            ->max('attempt'); // nilai terbesar, default = 1

        $jawabanMap = \App\Models\JawabanForm4c::where('form_1_id', $form1Id)
            ->where('user_id', $userId)
            ->where('attempt', $lastAttempt)
            ->get()
            ->keyBy('pertanyaan_form4c_id');

        // Susun response data
        $data = $iukList->map(function ($iuk) use ($jawabanMap) {
            return [
                'iuk_form3_id' => $iuk->iuk_form3_id,
                'no_iuk' => $iuk->no_iuk,
                'iuk_desc' => $iuk->iuk_name,
                'pertanyaan_form4c' => $iuk->pertanyaanForm4c->map(function ($pertanyaan) use ($jawabanMap) {
                    $jawaban = $jawabanMap->get($pertanyaan->id);

                    return [
                        'id' => $pertanyaan->id,
                        'urutan' => $pertanyaan->urutan,
                        'question' => [
                            'id' => $pertanyaan->question->id,
                            'question_text' => $pertanyaan->question->question_text,
                            'question_choices' => $pertanyaan->question->questionChoices->map(function ($qc) {
                                return [
                                    'id' => $qc->id,
                                    'is_correct' => (bool) $qc->is_correct,
                                    'choice' => [
                                        'id' => $qc->choice->id,
                                        'choice_label' => $qc->choice->choice_label,
                                        'choice_text' => $qc->choice->choice_text,
                                    ]
                                ];
                            }),
                        ],
                        'jawaban' => $jawaban ? [
                            'question_choice_id' => $jawaban->question_choice_id,
                            'choice_label' => $jawaban->choice_label,
                            'is_correct' => (bool) $jawaban->is_correct,
                            'catatan' => $jawaban->catatan,
                        ] : null,
                    ];
                })
            ];
        });

        // ============================
        //   HITUNG SCORE FORM 4C
        // ============================

        // Hitung total pertanyaan dari hasil iukList
        $totalPertanyaan = $data->sum(function ($iuk) {
            return $iuk['pertanyaan_form4c']->count();
        });

        // Hitung jawaban benar di database
        $jawabanBenar = $jawabanMap->where('is_correct', 1)->count();
        $jawabanSalah = $jawabanMap->where('is_correct', 0)->count();

        // Hitung persentase
        $persentase = $totalPertanyaan > 0
            ? round(($jawabanBenar / $totalPertanyaan) * 100, 2)
            : 0;

        // Siapkan score
        $score = [
            'total_pertanyaan' => $totalPertanyaan,
            'jawaban_benar' => $jawabanBenar,
            'jawaban_salah' => $jawabanSalah,
            'skor' => $jawabanBenar,
            'persentase' => $persentase
        ];

        // RETURN
        return response()->json([
            'status' => true,
            'message' => 'Data soal dan jawaban berhasil diambil',
            'data' => [
                'items' => $data,
                'score' => $score
            ]
        ]);
    }

    public function ApproveForm4cByAsesi(Request $request, $form4cId)
    {
        $validator = Validator::make(['form_4c_id' => $form4cId], [
            'form_4c_id' => 'required|integer|exists:form_4c,form_4c_id',
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

            // Pastikan $form4c adalah model tunggal
            $form4c = Form4c::find($form4cId);
            if (!$form4c) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Form 4c tidak ditemukan.'
                ], 404);
            }

            $form1Id = $this->formService->getParentFormIdByFormIdAndAsesiId($form4cId, $form4c->asesi_id);

            // Normalisasi $dataForm4c -> ambil single row jika service mengembalikan Collection
            $dataForm4c = $this->formService->getForm4cDataFromForm4cId($form4cId);
            if ($dataForm4c instanceof \Illuminate\Support\Collection) {
                $dataForm4c = $dataForm4c->first();
            }
            if (!$dataForm4c) {
                throw new \RuntimeException('Data Form 4C (detail) tidak ditemukan.');
            }

            $form1 = $this->formService->getParentDataByFormId($form1Id);
            Log::info("Form 4C ID: {$form4cId}, Form 1 ID: {$form1Id}");

            $form4cStatus = $this->formService
                ->getStatusByParentFormIdAndType($form1Id, 'form_4c')
                ->first();

            if ($form4c->status === 'Submitted' || $form4c->status === 'InAssessment') {
                $this->formService->updateForm4c(
                    $form4cId,
                    null, null,
                    'form_4c',
                    null, null,
                    null, null,
                    'Completed'
                );

                $this->formService->updateProgresDanTrack(
                    $form4cId,
                    'form_4c',
                    'Completed',
                    $dataForm4c->asesi_id,
                    'Form 4C telah di-approve oleh Asesor'
                );

                Log::info('Form 4C approved successfully.');

                $this->formService->kirimNotifikasiKeUser(
                    DaftarUser::find($form1->asesor_id),
                    'Form 4C sudah di Approved',
                    'Form 4C telah di-approve oleh Asesor.'
                );
            }

            // ✅ Cek kelengkapan Form 4
            $isAllForm4Completed = $this->formService->checkForm4Completion(
                $form4c->pk_id,
                $form4c->asesi_id,
                $form4c->asesor_id
            );

            if ($isAllForm4Completed) {
                Log::info("Semua Form 4 (A–D) telah lengkap untuk PK ID: {$form4c->pk_id}");

                $form7Status = $this->formService
                    ->getStatusByParentFormIdAndType($form1Id, 'form_7') // konsisten lowercase
                    ->first();

                // Ambil 1 ID Form 7 (bukan Collection)
                $form7Ids = $this->formService->getFormIdsByParentFormIdAndTypeNew(
                    $form1Id,
                    'form_7', // konsisten lowercase
                    $dataForm4c->asesi_id
                );

                if ($form7Ids instanceof \Illuminate\Support\Collection) {
                    $form7Id = $form7Ids->first();
                } else {
                    $form7Id = $form7Ids; // jika service sudah mengembalikan scalar
                }

                Log::info("Form 7 ID for Form 1 ID {$form1Id}: " . json_encode($form7Id));

                if ($form7Id && $form7Status === 'Submitted') {
                    // Update status form 7
                    $updatedForm7 = $this->formService->updateForm7(
                        $form7Id,
                        null, // pkId
                        null, // daftarTilikId
                        'form_7', // form_type
                        null, // asesiId
                        null, // asesiName
                        null, // asesorId
                        null, // asesorName
                        'Process' // status
                    );

                    // Update progres & track
                    $this->formService->updateProgresDanTrack(
                        $form7Id,
                        'form_7',
                        'Process',
                        $dataForm4c->asesi_id,
                        'Form form_7 telah di-approve oleh Asesi'
                    );

                    // Notifikasi
                    $this->formService->kirimNotifikasiKeUser(
                        DaftarUser::find($form1->asesor_id),
                        'Form form_7 Process',
                        'Form form_7 telah di-InAssessment oleh Asesi.'
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form 4C berhasil di-approve oleh Asesor',
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
