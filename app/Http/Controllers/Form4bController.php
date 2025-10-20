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
use App\Models\Form4b;
use Carbon\Carbon;

class Form4bController extends BaseController
{

	protected $formService;

    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
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
            'asesi_id' => 'required|integer',
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
                    ->where('user_id', $request->asesi_id)
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
                    'user_id' => $request->asesi_id,
                    'iuk_form3_id' => $data['iuk_form3_id'],
                    'jawaban_asesi' => $data['jawaban_asesi'] ?? null,
                    'pencapaian' => $data['pencapaian'],
                    'nilai' => $data['nilai'] ?? null,
                    'catatan' => $data['catatan'] ?? null,
                ]);
            }

            $form_4b_id = $this->formService->getFormIdsByParentFormIdAndType($request->form_1_id, 'Form_4b');

            $this->formService->updateProgresDanTrack(
                $form_4b_id,
                'Form_4b',
                'Submitted',
                $request->asesi_id,
                'Form 4B telah di isi oleh Asesor'
            );

            $form1 = $this->formService->getParentDataByFormId($request->form_1_id);

            // Kirim notifikasi ke asesor
            $this->formService->kirimNotifikasiKeUser(
                DaftarUser::find($form1->asesor_id),
                'Form 4B Submitted',
                'Form 4B telah di-submit oleh Asesor.'
            );
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

    public function ApproveForm4bByAsesi(Request $request, $form4bId)
    {
        $validator = Validator::make(['form_4b_id' => $form4bId], [
            'form_4b_id' => 'required|integer|exists:form_4b,form_4b_id',
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

            $form4b = Form4b::find($form4bId);
            if (!$form4b) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Form 4b tidak ditemukan.'
                ], 404);
            }

            $form1Id = $this->formService->getParentFormIdByFormId($form4bId);
            $form1   = $this->formService->getParentDataByFormId($form1Id);

            $form4bStatus = $this->formService
                ->getStatusByParentFormIdAndType($form1Id, 'form_4b')
                ->first();

            if ($form4bStatus === 'Submitted') {
                $this->formService->updateForm4b(
                    $form4bId,
                    null, null,
                    'form_4b',
                    null, null,
                    null, null,
                    'Approved'
                );

                $this->formService->updateProgresDanTrack(
                    $form4bId,
                    'form_4b',
                    'Approved',
                    Auth::id(),
                    'Form 4B telah di-approve oleh Asesi'
                );

                $this->formService->kirimNotifikasiKeUser(
                    DaftarUser::find($form1->asesor_id),
                    'Form 4B sudah di Approved',
                    'Form 4B telah di-approve oleh Asesi.'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form 4B berhasil di-approve oleh Asesi',
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
