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
use App\Models\Notification;
use Carbon\Carbon;

class Form4Controller extends BaseController
{

	protected $formService;

    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
    }

    public function getSoalForm4a(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pk_id' => 'required|integer',
            'group_no' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pkId = $request->input('pk_id');
        $groupNo = $request->input('group_no');

        $elemenList = ElemenForm3::with(['kukForm3' => function ($query) use ($pkId, $groupNo) {
            $query->where('pk_id', $pkId)
                ->whereHas('iukForm3', function ($q) use ($groupNo) {
                    $q->whereRaw('FIND_IN_SET(?, group_no)', [$groupNo]);
                })
                ->with(['iukForm3' => function ($q) use ($groupNo) {
                    $q->whereRaw('FIND_IN_SET(?, group_no)', [$groupNo])
                        ->with('poinForm4');
                }]);
        }])
        ->where('pk_id', $pkId)
        ->orderByRaw('CAST(no_elemen_form_3 AS UNSIGNED)')
        ->get()
        ->filter(fn($elemen) => $elemen->kukForm3->isNotEmpty())
        ->values()
        ->map(function ($elemen) {
            $elemen->kuk_form3 = $elemen->kukForm3->map(function ($kuk) {
                unset($kuk->kuk_name, $kuk->pk_id);

                $kuk->iuk_form3 = $kuk->iukForm3->map(function ($iuk) {
                    $poinGrouped = $iuk->poinForm4->groupBy('parent_id');

                    $buildTree = function ($parentId) use (&$buildTree, $poinGrouped) {
                        return ($poinGrouped[$parentId] ?? collect())->map(function ($poin) use (&$buildTree) {
                            return [
                                'id' => $poin->id,
                                'isi_poin' => $poin->isi_poin,
                                'urutan' => $poin->urutan,
                                'parent_id' => $poin->parent_id,
                                'children' => $buildTree($poin->id),
                            ];
                        })->values();
                    };

                    return [
                        'iuk_form3_id' => $iuk->iuk_form3_id,
                        'no_iuk' => $iuk->no_iuk,
                        'group_no' => $iuk->group_no,
                        'poin_form4' => $buildTree(null),
                    ];
                });

                unset($kuk->iukForm3);
                return $kuk;
            });

            unset($elemen->kukForm3);
            return $elemen;
        });

        return response()->json([
            'status' => true,
            'message' => 'Data elemen berhasil diambil',
            'data' => $elemenList,
        ]);
    }

    public function simpanJawabanForm4a(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'form_1_id' => 'required|integer',
            'user_id' => 'required|integer',
            'jawaban' => 'required|array|min:1',
            'jawaban.*.iuk_form3_id' => 'required|integer|exists:iuk_form3,iuk_form3_id',
            'jawaban.*.pencapaian' => 'required|boolean',
            'jawaban.*.nilai' => 'nullable|integer',
            'jawaban.*.catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $form1Id = $request->input('form_1_id');
        $userId = $request->input('user_id');
        $jawabanData = $request->input('jawaban');

        foreach ($jawabanData as $data) {
            JawabanForm4a::updateOrCreate(
                [
                    'form_1_id' => $form1Id,
                    'user_id' => $userId,
                    'iuk_form3_id' => $data['iuk_form3_id'],
                ],
                [
                    'pencapaian' => $data['pencapaian'],
                    'nilai' => $data['nilai'] ?? null,
                    'catatan' => $data['catatan'] ?? null,
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Jawaban berhasil disimpan',
        ]);
    }

    public function getSoalDanJawabanForm4a(Request $request)
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

        $pkId = $request->input('pk_id');
        $groupNo = $request->input('group_no');
        $form1Id = $request->input('form_1_id');
        $userId = $request->input('user_id');

        // Ambil semua jawaban yang sudah diinput
        $jawabanMap = JawabanForm4a::where('form_1_id', $form1Id)
            ->where('user_id', $userId)
            ->get()
            ->keyBy('iuk_form3_id');

        $elemenList = ElemenForm3::with(['kukForm3' => function ($query) use ($pkId, $groupNo) {
            $query->where('pk_id', $pkId)
                ->whereHas('iukForm3', function ($q) use ($groupNo) {
                    $q->whereRaw('FIND_IN_SET(?, group_no)', [$groupNo]);
                })
                ->with(['iukForm3' => function ($q) use ($groupNo) {
                    $q->whereRaw('FIND_IN_SET(?, group_no)', [$groupNo])
                        ->with('poinForm4');
                }]);
        }])
        ->where('pk_id', $pkId)
        ->orderByRaw('CAST(no_elemen_form_3 AS UNSIGNED)')
        ->get()
        ->filter(fn($elemen) => $elemen->kukForm3->isNotEmpty())
        ->values()
        ->map(function ($elemen) use ($jawabanMap) {
            $elemen->kuk_form3 = $elemen->kukForm3->map(function ($kuk) use ($jawabanMap) {
                unset($kuk->kuk_name, $kuk->pk_id);

                $kuk->iuk_form3 = $kuk->iukForm3->map(function ($iuk) use ($jawabanMap) {
                    $poinGrouped = $iuk->poinForm4->groupBy('parent_id');

                    $buildTree = function ($parentId) use (&$buildTree, $poinGrouped) {
                        return ($poinGrouped[$parentId] ?? collect())->map(function ($poin) use (&$buildTree) {
                            return [
                                'id' => $poin->id,
                                'isi_poin' => $poin->isi_poin,
                                'urutan' => $poin->urutan,
                                'parent_id' => $poin->parent_id,
                                'children' => $buildTree($poin->id),
                            ];
                        })->values();
                    };

                    $jawaban = $jawabanMap[$iuk->iuk_form3_id] ?? null;

                    return [
                        'iuk_form3_id' => $iuk->iuk_form3_id,
                        'no_iuk' => $iuk->no_iuk,
                        'group_no' => $iuk->group_no,
                        'poin_form4' => $buildTree(null),
                        'jawaban' => [
                            'pencapaian' => isset($jawaban) ? (bool) $jawaban->pencapaian : null,
                            'nilai' => $jawaban->nilai ?? null,
                            'catatan' => $jawaban->catatan ?? null,
                        ],
                    ];
                });

                unset($kuk->iukForm3);
                return $kuk;
            });

            unset($elemen->kukForm3);
            return $elemen;
        });

        return response()->json([
            'status' => true,
            'message' => 'Data soal dan jawaban berhasil diambil',
            'data' => $elemenList,
        ]);
    }

    public function getSoalForm4b(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_no' => 'required|string',
            'pk_id' => 'required|integer', // Tambahkan validasi pk_id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $groupNo = $request->input('group_no');
        $pkId = $request->input('pk_id'); // Ambil nilai pk_id dari request

        // Query IUK dengan filter group_no dan pk_id jika diberikan
        $iukQuery = \App\Models\IukModel::whereRaw('FIND_IN_SET(?, group_no)', [$groupNo]);

        if (!is_null($pkId)) {
            $iukQuery->where('pk_id', $pkId);
        }

        $iukList = $iukQuery->orderByRaw('CAST(no_iuk AS UNSIGNED)')
            ->with([
                'pertanyaanForm4b' => function ($query) {
                    $query->orderBy('urutan')->with([
                        'poinPertanyaan' => function ($q) {
                            $q->orderBy('urutan');
                        },
                        'children.poinPertanyaan' => function ($q) {
                            $q->orderBy('urutan');
                        }
                    ]);
                }
            ])
            ->get(['iuk_form3_id', 'no_iuk', 'group_no', 'pk_id']);

        // Bersihkan dan susun ulang data untuk response
        $cleanedData = $iukList->map(function ($iuk) {
            return [
                'iuk_form3_id' => $iuk->iuk_form3_id,
                'no_iuk' => $iuk->no_iuk,
                'group_no' => $iuk->group_no,
                'pk_id' => $iuk->pk_id,
                'pertanyaan_form4b' => $iuk->pertanyaanForm4b->map(function ($pertanyaan) {
                    return [
                        'id' => $pertanyaan->id,
                        'parent_id' => $pertanyaan->parent_id,
                        'pertanyaan' => $pertanyaan->pertanyaan,
                        'urutan' => $pertanyaan->urutan,
                        'poin_pertanyaan' => $pertanyaan->poinPertanyaan->map(function ($poin) {
                            return [
                                'id' => $poin->id,
                                'pertanyaan_form4b_id' => $poin->pertanyaan_form4b_id,
                                'isi_poin' => $poin->isi_poin,
                                'urutan' => $poin->urutan,
                            ];
                        }),
                        'children' => $pertanyaan->children->map(function ($child) {
                            return [
                                'id' => $child->id,
                                'parent_id' => $child->parent_id,
                                'pertanyaan' => $child->pertanyaan,
                                'urutan' => $child->urutan,
                                'poin_pertanyaan' => $child->poinPertanyaan->map(function ($poin) {
                                    return [
                                        'id' => $poin->id,
                                        'pertanyaan_form4b_id' => $poin->pertanyaan_form4b_id,
                                        'isi_poin' => $poin->isi_poin,
                                        'urutan' => $poin->urutan,
                                    ];
                                }),
                            ];
                        }),
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Data IUK dan pertanyaan berhasil diambil',
            'data' => $cleanedData,
        ]);
    }

    public function storeJawabanForm4b(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'form_1_id' => 'required|integer',
            'user_id' => 'required|integer',
            'jawaban' => 'required|array',
            'jawaban.*.iuk_form3_id' => 'required|integer',
            'jawaban.*.jawaban_asesi' => 'nullable|string',
            'jawaban.*.pencapaian' => 'required|boolean',
            'jawaban.*.nilai' => 'nullable|integer',
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
            foreach ($request->jawaban as $data) {
                // Cek apakah jawaban sudah ada
                $existing = JawabanForm4b::where('form_1_id', $request->form_1_id)
                    ->where('user_id', $request->user_id)
                    ->where('iuk_form3_id', $data['iuk_form3_id'])
                    ->first();

                if ($existing) {
                    DB::rollBack(); // batalkan semua jika salah satu sudah ada
                    return response()->json([
                        'status' => false,
                        'message' => 'Jawaban untuk IUK ' . $data['iuk_form3_id'] . ' sudah ada dan tidak dapat disimpan ulang.',
                    ], 409); // 409 Conflict
                }

                // Jika belum ada, simpan
                JawabanForm4b::create([
                    'form_1_id' => $request->form_1_id,
                    'user_id' => $request->user_id,
                    'iuk_form3_id' => $data['iuk_form3_id'],
                    'jawaban_asesi' => $data['jawaban_asesi'] ?? null,
                    'pencapaian' => $data['pencapaian'],
                    'nilai' => $data['nilai'] ?? null,
                    'catatan' => $data['catatan'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Jawaban berhasil disimpan',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSoalDanJawabanForm4b(Request $request)
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

        // Ambil data IUK + pertanyaan + poin
        $iukList = IukModel::whereRaw('FIND_IN_SET(?, group_no)', [$groupNo])
            ->orderByRaw('CAST(no_iuk AS UNSIGNED)')
            ->with([
                'pertanyaanForm4b' => function ($query) {
                    $query->orderBy('urutan')->with([
                        'poinPertanyaan' => function ($q) {
                            $q->orderBy('urutan');
                        },
                        'children.poinPertanyaan' => function ($q) {
                            $q->orderBy('urutan');
                        }
                    ]);
                }
            ])
            ->get(['iuk_form3_id', 'no_iuk', 'group_no']);

        // Ambil jawaban yang sudah tersimpan
        $jawabanMap = JawabanForm4b::where('form_1_id', $form1Id)
            ->where('user_id', $userId)
            ->get()
            ->keyBy('iuk_form3_id');

        // Susun response
        $data = $iukList->map(function ($iuk) use ($jawabanMap) {
            $jawaban = $jawabanMap->get($iuk->iuk_form3_id);

            return [
                'iuk_form3_id' => $iuk->iuk_form3_id,
                'no_iuk' => $iuk->no_iuk,
                'group_no' => $iuk->group_no,
                'jawaban' => $jawaban ? [
                    'jawaban_asesi' => $jawaban->jawaban_asesi,
                    'pencapaian' => (bool) $jawaban->pencapaian,
                    'nilai' => $jawaban->nilai,
                    'catatan' => $jawaban->catatan,
                ] : null,
                'pertanyaan_form4b' => $iuk->pertanyaanForm4b->map(function ($pertanyaan) {
                    return [
                        'id' => $pertanyaan->id,
                        'parent_id' => $pertanyaan->parent_id,
                        'pertanyaan' => $pertanyaan->pertanyaan,
                        'urutan' => $pertanyaan->urutan,
                        'poin_pertanyaan' => $pertanyaan->poinPertanyaan->map(function ($poin) {
                            return [
                                'id' => $poin->id,
                                'pertanyaan_form4b_id' => $poin->pertanyaan_form4b_id,
                                'isi_poin' => $poin->isi_poin,
                                'urutan' => $poin->urutan,
                            ];
                        }),
                        'children' => $pertanyaan->children->map(function ($child) {
                            return [
                                'id' => $child->id,
                                'parent_id' => $child->parent_id,
                                'pertanyaan' => $child->pertanyaan,
                                'urutan' => $child->urutan,
                                'poin_pertanyaan' => $child->poinPertanyaan->map(function ($poin) {
                                    return [
                                        'id' => $poin->id,
                                        'pertanyaan_form4b_id' => $poin->pertanyaan_form4b_id,
                                        'isi_poin' => $poin->isi_poin,
                                        'urutan' => $poin->urutan,
                                    ];
                                }),
                            ];
                        }),
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Data soal dan jawaban berhasil diambil',
            'data' => $data,
        ]);
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
            'user_id' => 'required|integer',
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

        try {
            foreach ($request->jawaban as $item) {
                JawabanForm4d::updateOrCreate(
                    [
                        'form_1_id' => $request->form_1_id,
                        'user_id' => $request->user_id,
                        'pertanyaan_form4d_id' => $item['pertanyaan_form4d_id'],
                    ],
                    [
                        'pencapaian' => $item['pencapaian'],
                    ]
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'Jawaban Form 4D berhasil disimpan',
            ]);
        } catch (\Exception $e) {
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


}
