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
use App\Models\Form7;
use App\Models\Form12;
use App\Models\JawabanForm7;
use App\Models\PoinForm4;
use Carbon\Carbon;

class Form7Controller extends BaseController
{

	protected $formService;

    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
    }

    public function getSoalForm7($pkId)
    {
        $validator = Validator::make(['pk_id' => $pkId], [
            'pk_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $elemen = ElemenForm3::with([
                'kukForm3.iukForm3.soalForm7' => function($query) use ($pkId) {
                    $query->where('pk_id', $pkId)
                        ->orderBy('id', 'asc');
                }
            ])
            ->orderBy('no_elemen_form_3', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $elemen
        ]);
    }

    public function simpanBanyakJawabanForm7(Request $request)
    {
        // Validasi request utama
        $validator = Validator::make($request->all(), [
            'asesi_id' => 'required|integer',
            'asesor_id' => 'required|integer',
            'jawaban' => 'required|array', // array jawaban
            'jawaban.*.soal_form7_id' => 'required|integer|exists:soal_form7,id',
            'jawaban.*.keputusan' => 'nullable|in:K,BK',
            'jawaban.*.catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $asesiId = $request->asesi_id;
        $asesorId = $request->asesor_id;
        $jawabanData = $request->jawaban;

        $saved = [];

        foreach ($jawabanData as $data) {
            $saved[] = \App\Models\JawabanForm7::updateOrCreate(
                [
                    'asesi_id'      => $asesiId,
                    'asesor_id'     => $asesorId,
                    'soal_form7_id' => $data['soal_form7_id']
                ],
                [
                    'keputusan'  => $data['keputusan'] ?? null,
                    'catatan'    => $data['catatan'] ?? null,
                    'updated_at' => Carbon::now(),
                    'created_at' => Carbon::now()
                ]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Semua jawaban berhasil disimpan.',
            'data' => $saved
        ]);
    }

    public function getSoalDanJawabanForm7($pkId, $asesiId)
    {
        // Validasi parameter
        $validator = Validator::make(
            [
                'pk_id'    => $pkId,
                'asesi_id' => $asesiId
            ],
            [
                'pk_id'    => 'required|integer',
                'asesi_id' => 'required|integer'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Ambil data dengan relasi
        $elemen = ElemenForm3::with([
            'kukForm3.iukForm3.soalForm7' => function ($query) use ($pkId) {
                $query->where('pk_id', $pkId)
                    ->orderBy('id', 'asc')
                    ->select('id', 'pk_id', 'iuk_form3_id', 'sumber_form');
            },
            'kukForm3.iukForm3.soalForm7.jawabanForm7' => function ($query) use ($asesiId) {
                $query->where('asesi_id', $asesiId)
                    ->select('id', 'asesi_id', 'asesor_id', 'soal_form7_id', 'keputusan', 'created_at', 'updated_at');
            }
        ])
        ->orderBy('no_elemen_form_3', 'asc')
        ->get();

        // 🔹 Ubah struktur jawaban_form7 jadi single object
        $elemen->each(function ($el) {
            $el->kukForm3->each(function ($kuk) {
                $kuk->iukForm3->each(function ($iuk) {
                    $iuk->soalForm7->each(function ($soal) {
                        if ($soal->jawabanForm7 && $soal->jawabanForm7->count() > 0) {
                            // Simpan sebagai single object
                            $soal->jawaban_form_7 = $soal->jawabanForm7->first();
                        } else {
                            $soal->jawaban_form_7 = null;
                        }

                        // 🔥 Hapus relasi aslinya agar tidak muncul lagi di JSON
                        $soal->setRelation('jawabanForm7', null); // hapus relasi lama
                        unset($soal->jawabanForm7);              // hapus output JSON lama

                    });
                });
            });
        });

        return response()->json([
            'status' => 'success',
            'data'   => $elemen
        ]);
    }




    public function getIukForm3IdFromForm7($pkId)
    {
        $validator = Validator::make(['pk_id' => $pkId], [
            'pk_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Ambil iuk_form3_id dan sumber_form langsung dari model SoalForm7
        $data = SoalForm7::where('pk_id', $pkId)
            ->select('iuk_form3_id', 'sumber_form') // ambil kedua kolom
            ->get()
            ->unique('iuk_form3_id') // hilangkan duplikat berdasarkan iuk_form3_id
            ->values();               // reset indeks array

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function getKeputusanForm4cPerIuk($pkId, $form1Id)
    {
        // 1. Ambil asesi_id dari form 1
        $form1Data = $this->formService->getParentDataByFormId($form1Id);
        $asesiId = $form1Data->asesi_id ?? null;
        $asesorId = $form1Data->asesor_id ?? null;

        if (!$asesiId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Asesi ID tidak ditemukan dari Form 1.'
            ], 404);
        }

        // 2. Ambil semua soal form 7 untuk pkId (hanya sumber_form mengandung 4C)
        $soalForm7List = SoalForm7::where('pk_id', $pkId)
            ->whereRaw("LOWER(sumber_form) LIKE ?", ['%4c%'])
            ->get();

        if ($soalForm7List->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada Soal Form7 dengan sumber_form 4C untuk pkId ini.'
            ], 404);
        }

        $hasil = [];

        foreach ($soalForm7List as $soal) {
            $iukId = $soal->iuk_form3_id;

            // 3. Ambil id pertanyaan_form4c
            $pertanyaanForm4cIds = PertanyaanForm4c::where('iuk_form_3_id', $iukId)
                ->pluck('id')
                ->unique()
                ->values()
                ->toArray();

            if (empty($pertanyaanForm4cIds)) {
                $keputusan = 'BK';
                $hasil[] = [
                    'soal_form7_id' => $soal->id,
                    'iuk_form3_id' => $iukId,
                    'total' => 0,
                    'benar' => 0,
                    'persentase_benar' => 0,
                    'keputusan' => $keputusan
                ];

                JawabanForm7::updateOrCreate(
                    [
                        'asesi_id' => $asesiId,
                        'asesor_id' => $asesorId,
                        'soal_form7_id' => $soal->id
                    ],
                    [
                        'keputusan' => $keputusan
                    ]
                );

                continue;
            }

            $lastAttempt = JawabanForm4c::where('form_1_id', $form1Id)
                ->whereIn('pertanyaan_form4c_id', $pertanyaanForm4cIds)
                ->max('attempt');
            // 4. Hitung total & benar
            // Hitung total berdasarkan attempt terakhir
            $total = JawabanForm4c::whereIn('pertanyaan_form4c_id', $pertanyaanForm4cIds)
                ->where('form_1_id', $form1Id)
                ->where('attempt', $lastAttempt) // 🔴 DITAMBAHKAN
                ->count();

            // Hitung jawaban benar berdasarkan attempt terakhir
            $benar = JawabanForm4c::whereIn('pertanyaan_form4c_id', $pertanyaanForm4cIds)
                ->where('form_1_id', $form1Id)
                ->where('attempt', $lastAttempt) // 🔴 DITAMBAHKAN
                ->where('is_correct', 1)
                ->count();

            $persentase = $total > 0 ? round(($benar / $total) * 100, 2) : 0;

            // Tentukan keputusan K / BK
            $keputusan = $persentase >= 80 ? 'K' : 'BK';

            $hasil[] = [
                'soal_form7_id' => $soal->id,
                'iuk_form3_id' => $iukId,
                'total' => $total,
                'benar' => $benar,
                'persentase_benar' => $persentase,
                'keputusan' => $keputusan
            ];

            // 5. Simpan ke JawabanForm7
            JawabanForm7::updateOrCreate(
                [
                    'asesi_id' => $asesiId,
                    'asesor_id' => $asesorId,
                    'soal_form7_id' => $soal->id
                ],
                [
                    'keputusan' => $keputusan
                ]
            );
        }

        return response()->json([
            'status' => 'success',
            'data' => $hasil
        ]);
    }

    public function getKeputusanForm4aPerIuk($pkId, $form1Id)
    {
        // 1. Ambil asesi_id dari Form 1
        $form1Data = $this->formService->getParentDataByFormId($form1Id);
        $asesiId = $form1Data->asesi_id ?? null;
        $asesorId = $form1Data->asesor_id ?? null;

        if (!$asesiId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Asesi ID tidak ditemukan dari Form 1.'
            ], 404);
        }

        // 2. Ambil semua soal form 7 untuk pkId (hanya sumber_form mengandung 4A)
        $soalForm7List = SoalForm7::where('pk_id', $pkId)
            ->whereRaw("LOWER(sumber_form) LIKE ?", ['%4a%'])
            ->get();

        if ($soalForm7List->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada Soal Form7 dengan sumber_form 4A untuk pkId ini.'
            ], 404);
        }

        $hasil = [];

        foreach ($soalForm7List as $soal) {
            $iukId = $soal->iuk_form3_id;

            // 3. Ambil jawaban form 4A untuk IUK ini
            $jawaban = JawabanForm4a::where('form_1_id', $form1Id)
                ->where('iuk_form3_id', $iukId)
                ->first();

            if (!$jawaban) {
                $keputusan = 'BK';
            } else {
                $keputusan = $jawaban->pencapaian == 1 ? 'K' : 'BK';
            }

            $hasil[] = [
                'soal_form7_id' => $soal->id,
                'iuk_form3_id' => $iukId,
                'pencapaian' => $jawaban->pencapaian ?? null,
                'keputusan' => $keputusan
            ];

            // 4. Simpan ke JawabanForm7
            JawabanForm7::updateOrCreate(
                [
                    'asesi_id' => $asesiId,
                    'asesor_id' => $asesorId,
                    'soal_form7_id' => $soal->id
                ],
                [
                    'keputusan' => $keputusan
                ]
            );
        }

        return response()->json([
            'status' => 'success',
            'data' => $hasil
        ]);
    }

    public function getKeputusanForm4bPerIuk($pkId, $form1Id)
    {
        // 1. Ambil asesi_id dari Form 1
        $form1Data = $this->formService->getParentDataByFormId($form1Id);
        $asesiId = $form1Data->asesi_id ?? null;
        $asesorId = $form1Data->asesor_id ?? null;

        if (!$asesiId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Asesi ID tidak ditemukan dari Form 1.'
            ], 404);
        }

        // 2. Ambil semua soal form 7 untuk pkId (hanya sumber_form mengandung 4B)
        $soalForm7List = SoalForm7::where('pk_id', $pkId)
            ->whereRaw("LOWER(sumber_form) LIKE ?", ['%4b%'])
            ->get();

        if ($soalForm7List->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada Soal Form7 dengan sumber_form 4B untuk pkId ini.'
            ], 404);
        }

        $hasil = [];

        foreach ($soalForm7List as $soal) {
            $iukId = $soal->iuk_form3_id;

            // 3. Ambil jawaban form 4B untuk IUK ini
            $jawaban = JawabanForm4b::where('form_1_id', $form1Id)
                ->where('iuk_form3_id', $iukId)
                ->first();

            if (!$jawaban) {
                $keputusan = 'BK';
            } else {
                $keputusan = $jawaban->pencapaian == 1 ? 'K' : 'BK';
            }

            $hasil[] = [
                'soal_form7_id' => $soal->id,
                'iuk_form3_id' => $iukId,
                'pencapaian' => $jawaban->pencapaian ?? null,
                'keputusan' => $keputusan
            ];

            // 4. Simpan ke JawabanForm7
            JawabanForm7::updateOrCreate(
                [
                    'asesi_id' => $asesiId,
                    'asesor_id' => $asesorId,
                    'soal_form7_id' => $soal->id
                ],
                [
                    'keputusan' => $keputusan
                ]
            );
        }

        return response()->json([
            'status' => 'success',
            'data' => $hasil
        ]);
    }

    public function getAllKeputusanForm7($pkId, $form1Id)
    {
        $form4a = $this->getKeputusanForm4aPerIuk($pkId, $form1Id);
        $form4b = $this->getKeputusanForm4bPerIuk($pkId, $form1Id);
        $form4c = $this->getKeputusanForm4cPerIuk($pkId, $form1Id);

        // ambil data form1 agar dapat asesi_id
        $form1 = BidangModel::find($form1Id);
        $asesiId = $form1->asesi_id ?? null;

        $form7Id = $this->formService->getFormIdsByParentFormIdAndType(
            $form1Id,
            'form_7'
        );

        if ($form7Id && $asesiId) {
            DB::beginTransaction();
            try {
                // update progres dan track
                $this->formService->updateProgresDanTrack(
                    $form7Id,
                    $form7->form_type ?? 'form_7',
                    'Submitted',
                    $asesiId,
                    'Form 7 telah selesai diproses oleh asesor'
                );

                // kirim notifikasi ke asesi
                $user = DaftarUser::find($asesiId);
                if ($user) {
                    $this->formService->kirimNotifikasiKeUser(
                        $user,
                        'Form ' . ($form7->form_type ?? 'form_7') . ' Selesai',
                        'Form ' . ($form7->form_type ?? 'form_7') . ' dengan ID ' . $form7Id . ' telah berhasil diselesaikan.'
                    );
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal memproses Form 7: ' . $e->getMessage(),
                ], 500);
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'form4a' => $form4a->getData()->data ?? [],
                'form4b' => $form4b->getData()->data ?? [],
                'form4c' => $form4c->getData()->data ?? []
            ]
        ]);
    }

    public function ApproveForm7ByAsesi(Request $request, $form7Id)
    {
        // Validasi ID Form 7
        $validator = Validator::make(['form_7_id' => $form7Id], [
            'form_7_id' => 'required|integer|exists:form_7,form_7_id',
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

            // Ambil data form_7
            $form7 = Form7::find($form7Id);
            if (!$form7) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Form 7 tidak ditemukan.'
                ], 404);
            }

            // Ambil form induk (form_1) berdasarkan relasi
            // $form1Id = $this->formService->getParentFormIdByFormId($form7Id);
            $form1Id = $this->formService->getParentFormIdByFormIdAndAsesiId($form7Id, $form7->asesi_id);
            $form1   = $this->formService->getParentDataByFormId($form1Id);

            // Ambil status form 7 sesuai form_type yang dimiliki
            $form7Status = $this->formService
                ->getStatusByParentFormIdAndType($form1Id, 'form_7')
                ->first();

            if ($form7Status === 'Submitted') {
                // Update status form 7
                $updatedForm7 = $this->formService->updateForm7(
                    $form7Id,
                    null, // pkId
                    null, // daftarTilikId
                    'form_7', // form_type tetap dari DB
                    null, // asesiId
                    null, // asesiName
                    null, // asesorId
                    null, // asesorName
                    'Completed' // status
                );

                // Update progres & track sesuai form_type turunan
                $this->formService->updateProgresDanTrack(
                    $form7Id,
                    'form_7',
                    'Completed',
                    $form7->asesi_id,
                    'Form form_7 telah di-approve oleh Asesi'
                );

                // Kirim notifikasi ke asesor
                $this->formService->kirimNotifikasiKeUser(
                    DaftarUser::find($form1->asesor_id),
                    'Form form_7 Approved',
                    'Form form_7 telah di-approve oleh Asesi.'
                );
            }

            // ===== FORM 12 =====
            $isForm12Exist = $this->formService->isFormExistSingle(
                $form1->asesi_id,
                $form1->pk_id,
                'form_12'
            );

            Log::info("Cek keberadaan Form 12 untuk Asesi ID: {$form1->asesi_id}, PK ID: {$form1->pk_id} - Ada: " . ($isForm12Exist ? 'Ya' : 'Tidak'));

            if (!$isForm12Exist) {
                Log::info("Form 12 belum ada, membuat form 12...");
                $form12 = $this->formService->inputForm12(
                    $form1->pk_id,
                    $form1->asesi_id,
                    $form1->asesi_name,
                    $form1->asesor_id,
                    $form1->asesor_name,
                    $form1->no_reg
                );

                $this->formService->createProgresDanTrack(
                    $form12->form_12_id,
                    'form_12',
                    'InAssessment',
                    $form1->asesi_id,
                    $form1->form_1_id,
                    'Form 12 sudah dapat diisi.'
                );
            } else {
                Log::info("Form 12 sudah ada, tidak membuat ulang.");
            }
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form form_7 berhasil di-approve oleh Asesi',
                'data'    => $form7Status
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
