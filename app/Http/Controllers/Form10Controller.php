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
use Carbon\Carbon;

class Form10Controller extends BaseController
{

	protected $formService;

    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
    }

    public function getAll(Request $request)
    {
        try {
            $query = DaftarTilik::query();

            // Filter berdasarkan pk_id jika dikirim
            if ($request->has('pk_id')) {
                $query->where('pk_id', $request->input('pk_id'));
            }

            // Filter berdasarkan form_number jika dikirim
            if ($request->has('form_number')) {
                $query->where('form_number', 'like', '%' . $request->input('form_number') . '%');
            }

            // Urutkan data jika parameter sort_by dikirim
            if ($request->has('sort_by')) {
                $sortOrder = $request->input('sort_order', 'asc');
                $query->orderBy($request->input('sort_by'), $sortOrder);
            }

            $data = $query->get();

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data daftar_tilik.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSoalList($form10Id)
    {
        try {
            $form10 = Form10::find($form10Id);

            if (!$form10) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Form 10 tidak ditemukan.'
                ], 404);
            }

            $pkId = $form10->pk_id;
            $daftarTilikId = $form10->daftar_tilik_id;
            $asesiId = $form10->asesi_id;
            $asesorId = $form10->asesor_id;

            // Cek apakah jawaban sudah diinisialisasi
            $exists = JawabanDaftarTilik::where('form_10_id', $form10Id)
                ->where('daftar_tilik_id', $daftarTilikId)
                ->where('asesi_id', $asesiId)
                ->exists();

            // Jika belum, inisialisasi dari kegiatan_daftar_tilik
            if (!$exists) {
                $kegiatanList = KegiatanDaftarTilik::where('pk_id', $pkId)
                    ->where('daftar_tilik_id', $daftarTilikId)
                    ->where(function ($query) {
                        $query->where('isTitle', 0)
                            ->orWhereNull('isTitle');
                    })
                    ->get();

                $insertData = [];
                foreach ($kegiatanList as $kegiatan) {
                    $insertData[] = [
                        'form_10_id' => $form10Id,
                        'daftar_tilik_id' => $daftarTilikId,
                        'kegiatan_daftar_tilik_id' => $kegiatan->id,
                        'asesi_id' => $asesiId,
                        'asesor_id' => $asesorId,
                        'dilakukan' => 0,
                        'catatan' => null,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }

                if (!empty($insertData)) {
                    JawabanDaftarTilik::insert($insertData);
                }
            }

            // Ambil hanya struktur soal (tanpa jawaban)
            $soal = KegiatanDaftarTilik::with('children')
                ->whereNull('parent_id')
                ->where('pk_id', $pkId)
                ->where('daftar_tilik_id', $daftarTilikId)
                ->orderBy('urutan')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $soal,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil list soal.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // public function getSoalList($form10Id)
    // {
    //     try {
    //         $form10 = Form10::find($form10Id);

    //         if (!$form10) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Data Form 10 tidak ditemukan.'
    //             ], 404);
    //         }

    //         $pkId = $form10->pk_id;
    //         $daftarTilikId = $form10->daftar_tilik_id;
    //         $asesiId = $form10->asesi_id;
    //         $asesorId = $form10->asesor_id;

    //         // Cek apakah jawaban sudah diinisialisasi
    //         $exists = JawabanDaftarTilik::where('form_10_id', $form10Id)
    //             ->where('daftar_tilik_id', $daftarTilikId)
    //             ->where('asesi_id', $asesiId)
    //             ->exists();

    //         // Jika belum, inisialisasi dari kegiatan_daftar_tilik
    //         if (!$exists) {
    //             $kegiatanList = KegiatanDaftarTilik::where('pk_id', $pkId)
    //                 ->where('daftar_tilik_id', $daftarTilikId)
    //                 ->where(function ($query) {
    //                     $query->where('isTitle', 0)
    //                         ->orWhereNull('isTitle');
    //                 })
    //                 ->get();

    //             $insertData = [];
    //             foreach ($kegiatanList as $kegiatan) {
    //                 $insertData[] = [
    //                     'form_10_id' => $form10Id,
    //                     'daftar_tilik_id' => $daftarTilikId,
    //                     'kegiatan_daftar_tilik_id' => $kegiatan->id,
    //                     'asesi_id' => $asesiId,
    //                     'asesor_id' => $asesorId,
    //                     'dilakukan' => 0,
    //                     'catatan' => null,
    //                     'created_at' => now(),
    //                     'updated_at' => now(),
    //                 ];
    //             }

    //             if (!empty($insertData)) {
    //                 JawabanDaftarTilik::insert($insertData);
    //             }
    //         }

    //         // Ambil seluruh kegiatan (tanpa nested children)
    //         $soal = KegiatanDaftarTilik::where('pk_id', $pkId)
    //             ->where('daftar_tilik_id', $daftarTilikId)
    //             ->orderBy('parent_id')
    //             ->orderBy('urutan')
    //             ->get();


    //         return response()->json([
    //             'success' => true,
    //             'data' => $soal,
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal mengambil list soal.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }


    
    public function getForm10WithAnswersById($form10Id)
    {
        try {
            $form10 = Form10::with([
                'daftarTilik.kegiatanDaftarTilik' => function ($q) use ($form10Id) {
                    $q->whereNull('parent_id')
                        ->orderBy('urutan')
                        ->with([
                            'jawaban' => function ($jq) use ($form10Id) {
                                $jq->where('form_10_id', $form10Id);
                            },
                            'children.children.jawaban' => function ($jq) use ($form10Id) {
                                $jq->where('form_10_id', $form10Id);
                            },
                            'children.children.children.jawaban' => function ($jq) use ($form10Id) {
                                $jq->where('form_10_id', $form10Id);
                            },
                        ]);
                },
            ])->find($form10Id);

            if (!$form10) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Form 10 tidak ditemukan',
                    'data'    => null
                ], 404);
            }

            // 🧩 Fungsi rekursif untuk format kegiatan beserta anak-anaknya
            $formatKegiatan = function ($kegiatan) use (&$formatKegiatan) {
                $jawaban = $kegiatan->jawaban->first();

                $jawabanData = $kegiatan->isTitle
                    ? null
                    : [
                        'dilakukan' => (bool) ($jawaban->dilakukan ?? false),
                        'catatan'   => $jawaban->catatan ?? null,
                    ];

                return [
                    'id'       => $kegiatan->id,
                    'kegiatan' => $kegiatan->kegiatan,
                    'isTitle'  => (bool) $kegiatan->isTitle,
                    'jawaban'  => $jawabanData,
                    // 🔁 Rekursif: panggil lagi untuk setiap child
                    'children' => $kegiatan->children->map(fn($child) => $formatKegiatan($child))->values(),
                ];
            };

            // Gunakan fungsi rekursif untuk memetakan semua kegiatan
            $data = [
                'form_10_id' => $form10->form_10_id,
                'pk_id'      => $form10->pk_id,
                'asesi_id'   => $form10->asesi_id,
                'asesor_id'  => $form10->asesor_id,
                'soal'       => $form10->daftarTilik->kegiatanDaftarTilik->map(fn($kegiatan) => $formatKegiatan($kegiatan)),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Data Form 10 berhasil diambil',
                'data'    => $data
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Gagal mengambil Form 10: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data Form 10',
                'error'   => $e->getMessage(),
                'data'    => null
            ], 500);
        }
    }






    public function submitSoalList(Request $request, $form10Id)
    {
        DB::beginTransaction();

        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'jawaban'   => 'required|array',
                'jawaban.*.kegiatan_daftar_tilik_id' => 'required|integer|exists:kegiatan_daftar_tilik,id',
                'jawaban.*.dilakukan' => 'required|boolean',
                'jawaban.*.catatan'   => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'errors'  => $validator->errors()
                ], 422);
            }

            // Pastikan Form10 ada
            $form10 = Form10::find($form10Id);
            if (!$form10) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Form 10 tidak ditemukan.'
                ], 404);
            }

            $daftarTilikId = $form10->daftar_tilik_id;
            $asesiId       = $form10->asesi_id;
            $asesorId      = $form10->asesor_id;

            foreach ($request->jawaban as $item) {
                JawabanDaftarTilik::updateOrCreate(
                    [
                        'form_10_id'              => $form10Id,
                        'daftar_tilik_id'         => $daftarTilikId,
                        'kegiatan_daftar_tilik_id'=> $item['kegiatan_daftar_tilik_id'],
                        'asesi_id'                => $asesiId,
                    ],
                    [
                        'asesor_id' => $asesorId,
                        'dilakukan' => $item['dilakukan'],
                        'catatan'   => $item['catatan'] ?? null,
                        'updated_at'=> Carbon::now(),
                    ]
                );
            }

            $this->formService->updateProgresDanTrack(
                $form10Id,
                $form10->form_type, // asumsi form_type-nya "form_10", sesuaikan jika beda
                'Submitted', // contoh status selesai, sesuaikan sesuai kebutuhan
                $asesiId, 
                'Form 10 telah selesai diisi oleh asesor'
            );

            $user = DaftarUser::find($asesiId); // ambil data user sesuai asesinya
            if ($user) {
                $this->formService->kirimNotifikasiKeUser(
                    $user,
                    'Form ' . $form10->form_type . ' Selesai',
                    'Form ' . $form10->form_type . ' dengan ID ' . $form10Id . ' telah berhasil diselesaikan.'
                );
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Jawaban berhasil disimpan.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan jawaban.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function ApproveForm10ByAsesi(Request $request, $form10Id)
    {
        // Validasi ID Form 10
        $validator = Validator::make(['form_10_id' => $form10Id], [
            'form_10_id' => 'required|integer|exists:form_10,form_10_id',
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

            // Ambil data form_10
            $form10 = Form10::find($form10Id);
            if (!$form10) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Form 10 tidak ditemukan.'
                ], 404);
            }

            // Ambil form induk (form_1) berdasarkan relasi
            $form1Id = $this->formService->getParentFormIdByFormId($form10Id);
            $form1   = $this->formService->getParentDataByFormId($form1Id);

            // Ambil status form 10 sesuai form_type yang dimiliki
            $form10Status = $this->formService
                ->getStatusByParentFormIdAndType($form1Id, $form10->form_type)
                ->first();

            if ($form10Status === 'Submitted') {
                // Update status form 10
                $updatedForm10 = $this->formService->updateForm10(
                    $form10Id,
                    null, // pkId
                    null, // daftarTilikId
                    $form10->form_type, // form_type tetap dari DB
                    null, // asesiId
                    null, // asesiName
                    null, // asesorId
                    null, // asesorName
                    'Approved' // status
                );

                // Update progres & track sesuai form_type turunan
                $this->formService->updateProgresDanTrack(
                    $form10Id,
                    $form10->form_type,
                    'Approved',
                    Auth::id(),
                    'Form ' . $form10->form_type . ' telah di-approve oleh Asesi'
                );

                // Kirim notifikasi ke asesor
                $this->formService->kirimNotifikasiKeUser(
                    DaftarUser::find($form1->asesor_id),
                    'Form ' . $form10->form_type . ' Approved',
                    'Form ' . $form10->form_type . ' telah di-approve oleh Asesi.'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form ' . $form10->form_type . ' berhasil di-approve oleh Asesi',
                'data'    => $form10Status
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
