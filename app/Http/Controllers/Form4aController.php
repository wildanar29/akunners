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
use App\Models\Form4a;
use App\Models\UserRole;
use App\Models\KompetensiTrack;
use App\Models\KompetensiProgres;
use App\Models\Notification;
use Carbon\Carbon;

class Form4aController extends BaseController
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
            // 🔹 Bersihkan isi_elemen dari karakter tab, newline, dan spasi berlebih
            $elemen->isi_elemen = preg_replace('/\s+/', ' ', trim(str_replace(["\t", "\n", "\r"], ' ', $elemen->isi_elemen)));

            $elemen->kuk_form3 = $elemen->kukForm3->map(function ($kuk) {
                unset($kuk->kuk_name, $kuk->pk_id);

                $kuk->iuk_form3 = $kuk->iukForm3->map(function ($iuk) {
                    $poinGrouped = $iuk->poinForm4->groupBy('parent_id');

                    $buildTree = function ($parentId) use (&$buildTree, $poinGrouped) {
                        return ($poinGrouped[$parentId] ?? collect())->map(function ($poin) use (&$buildTree) {
                            // 🔹 Bersihkan teks isi_poin dari tab/newline/spasi berlebih
                            $cleanIsi = preg_replace('/\s+/', ' ', trim(str_replace(["\t", "\n", "\r"], ' ', $poin->isi_poin)));

                            return [
                                'id' => $poin->id,
                                'isi_poin' => $cleanIsi,
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

        // 🔹 Bersihkan spasi berlebih di seluruh struktur JSON (jaga agar clean)
        $cleanedData = json_decode(
            preg_replace('/\s+/', ' ', json_encode($elemenList, JSON_UNESCAPED_UNICODE)),
            true
        );

        return response()->json([
            'status' => true,
            'message' => 'Data elemen berhasil diambil',
            'data' => $cleanedData,
        ]);
    }


    public function simpanJawabanForm4a(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'form_1_id' => 'required|integer',
            'asesi_id' => 'required|integer',
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
        $userId = $request->input('asesi_id');
        $jawabanData = $request->input('jawaban');

        DB::beginTransaction();
        try {
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

            $form_4a_id = $this->formService->getFormIdsByParentFormIdAndType($form1Id, 'form_4a');

            $this->formService->updateProgresDanTrack(
                $form_4a_id,
                'Form_4a',
                'Submitted',
                $userId,
                'Form 4A telah di isi oleh Asesor'
            );

            $form1 = $this->formService->getParentDataByFormId($form1Id);

            // Kirim notifikasi ke asesor
            $this->formService->kirimNotifikasiKeUser(
                DaftarUser::find($form1->asesor_id),
                'Form 4A Submitted',
                'Form telah di-submit oleh Asesor.'
            );
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Jawaban berhasil disimpan',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan jawaban',
                'error' => $e->getMessage(),
            ], 500);
        }
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

    public function ApproveForm4aByAsesi(Request $request, $form4aId)
    {
        // Validasi ID Form 4a
        $validator = Validator::make(['form_4a_id' => $form4aId], [
            'form_4a_id' => 'required|integer|exists:form_4a,form_4a_id',
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

            // Ambil data form_4a
            $form4a = Form4a::find($form4aId);
            if (!$form4a) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Form 4a tidak ditemukan.'
                ], 404);
            }

            // Ambil form induk (form_1) berdasarkan relasi
            $form1Id = $this->formService->getParentFormIdByFormId($form4aId);
            $form1   = $this->formService->getParentDataByFormId($form1Id);

            // Ambil status form 10 sesuai form_type yang dimiliki
            $form4aStatus = $this->formService
                ->getStatusByParentFormIdAndType($form1Id, 'form_4a')
                ->first();

            if ($form4aStatus === 'Submitted') {
                // Update status form 4a
                $updatedForm4a = $this->formService->updateForm4a(
                    $form4aId,
                    null, // pkId
                    null, // daftarTilikId
                    'form_4a', // form_type tetap dari DB
                    null, // asesiId
                    null, // asesiName
                    null, // asesorId
                    null, // asesorName
                    'Approved' // status
                );

                // Update progres & track sesuai form_type turunan
                $this->formService->updateProgresDanTrack(
                    $form4aId,
                    'form_4a',
                    'Approved',
                    Auth::id(),
                    'Form 4A telah di-approve oleh Asesi'
                );

                // Kirim notifikasi ke asesor
                $this->formService->kirimNotifikasiKeUser(
                    DaftarUser::find($form1->asesor_id),
                    'Form 4A sudah di Approved',
                    'Form 4A telah di-approve oleh Asesi.'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form 4A berhasil di-approve oleh Asesi',
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
