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
            'user_id' => 'required|integer|exists:users,user_id',
            'jawaban' => 'required|array',
            'jawaban.*.pertanyaan_form4c_id' => 'required|integer|exists:pertanyaan_form4c,id',
            'jawaban.*.question_choice_id' => 'required|integer|exists:question_choice,id',
            'jawaban.*.catatan' => 'nullable|string',
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
            $duplikatIds = [];

            foreach ($request->jawaban as $item) {
                $exists = JawabanForm4c::where('form_1_id', $request->form_1_id)
                    ->where('user_id', $request->user_id)
                    ->where('pertanyaan_form4c_id', $item['pertanyaan_form4c_id'])
                    ->exists();

                if ($exists) {
                    $duplikatIds[] = $item['pertanyaan_form4c_id'];
                    continue;
                }

                // Ambil data relasi
                $questionChoice = \App\Models\QuestionChoice::with('choice')->find($item['question_choice_id']);

                if (!$questionChoice || !$questionChoice->choice) {
                    throw new \Exception("Choice data not found for question_choice_id: {$item['question_choice_id']}");
                }

                // Simpan
                JawabanForm4c::create([
                    'form_1_id' => $request->form_1_id,
                    'user_id' => $request->user_id,
                    'pertanyaan_form4c_id' => $item['pertanyaan_form4c_id'],
                    'question_choice_id' => $item['question_choice_id'],
                    'catatan' => $item['catatan'] ?? null,
                    'choice_label' => $questionChoice->choice->choice_label,
                    'is_correct' => $questionChoice->is_correct,
                ]);
            }

            

            DB::commit();

            if (!empty($duplikatIds)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Beberapa pertanyaan sudah pernah dijawab dan tidak disimpan ulang.',
                    'duplikat_pertanyaan_ids' => $duplikatIds,
                ], 409);
            }

            $form_4c_id = $this->formService->getFormIdsByParentFormIdAndType($request->form_1_id, 'Form_4c');

            $this->formService->updateProgresDanTrack(
                $form_4c_id,
                'form_4c',
                'Submitted',
                $request->user_id,
                'Form 4C telah di isi oleh Asesi'
            );

            $form1 = $this->formService->getParentDataByFormId($request->form_1_id);

            // Kirim notifikasi ke asesor
            $this->formService->kirimNotifikasiKeUser(
                DaftarUser::find($form1->asesor_id),
                'Form 4C Submitted',
                'Form 4C telah di-submit oleh Asesi.'
            );

            return response()->json([
                'status' => true,
                'message' => 'Semua jawaban berhasil disimpan.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan jawaban',
                'error' => $e->getMessage(),
            ], 500);
        }
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

        $groupNo = $request->input('group_no');
        $form1Id = $request->input('form_1_id');
        $userId = $request->input('user_id');

        // Ambil data IUK beserta pertanyaan Form 4C dan question + pilihan
        $iukList = \App\Models\IukModel::whereRaw('FIND_IN_SET(?, group_no)', [$groupNo])
            ->orderByRaw('CAST(no_iuk AS UNSIGNED)')
            ->with([
                'pertanyaanForm4c' => function ($query) {
                    $query->orderBy('urutan')->with([
                        'question.questionChoices.choice'
                    ]);
                }
            ])
            ->where('pk_id', $request->pk_id)
            ->get(['iuk_form3_id', 'no_iuk', 'group_no']);

        // Ambil jawaban dari user
        $jawabanMap = \App\Models\JawabanForm4c::where('form_1_id', $form1Id)
            ->where('user_id', $userId)
            ->get()
            ->keyBy('pertanyaan_form4c_id');

        // Susun response
        $data = $iukList->map(function ($iuk) use ($jawabanMap) {
            return [
                'iuk_form3_id' => $iuk->iuk_form3_id,
                'no_iuk' => $iuk->no_iuk,
                'group_no' => $iuk->group_no,
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

        return response()->json([
            'status' => true,
            'message' => 'Data soal dan jawaban berhasil diambil',
            'data' => $data,
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

            $form4c = Form4c::find($form4cId);
            if (!$form4c) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Form 4c tidak ditemukan.'
                ], 404);
            }

            $form1Id = $this->formService->getParentFormIdByFormId($form4cId);
            $form1   = $this->formService->getParentDataByFormId($form1Id);

            $form4cStatus = $this->formService
                ->getStatusByParentFormIdAndType($form1Id, 'form_4c')
                ->first();

            if ($form4cStatus === 'Submitted') {
                $this->formService->updateForm4c(
                    $form4cId,
                    null, null,
                    'form_4c',
                    null, null,
                    null, null,
                    'Approved'
                );

                $this->formService->updateProgresDanTrack(
                    $form4cId,
                    'form_4c',
                    'Approved',
                    Auth::id(),
                    'Form 4C telah di-approve oleh Asesor'
                );

                $this->formService->kirimNotifikasiKeUser(
                    DaftarUser::find($form1->asesor_id),
                    'Form 4C sudah di Approved',
                    'Form 4C telah di-approve oleh Asesor.'
                );
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
