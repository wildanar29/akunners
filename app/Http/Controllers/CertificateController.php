<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf; // alias dari dompdf
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\KompetensiPk;
use App\Models\BidangModel;
use App\Models\Form6;
use App\Models\SertifikatPk;
use App\Models\TranskripNilaiPk;
use App\Models\ElemenForm3;
use App\Service\FormService;
use Carbon\Carbon;

Carbon::setLocale('id'); // set global
class CertificateController extends Controller
{
    
    protected $formService;

    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
    }

    public function generate(Request $request)
    {
        Log::info("=== START GENERATE SERTIFIKAT ===");
        Log::debug("Input request:", $request->all());

        $form_1_id = $request->input('form_1_id', null);

        Log::debug("Form 1 ID diterima:", ['form_1_id' => $form_1_id]);

        // Cek sertifikat existing
        $existing = SertifikatPk::where('form_1_id', $form_1_id)->first();
        if ($existing) {
            Log::warning("Sertifikat sudah ada!", $existing->toArray());

            return response()->json([
                'message' => 'Sertifikat untuk form ini sudah dibuat sebelumnya',
                'data'    => $existing,
            ], 400);
        }

        $data = ['form_1_id' => $form_1_id];

        // Ambil Form 1
        Log::info("Mengambil data Form1...");
        $form1 = $this->formService->getParentDataByFormId($data['form_1_id']);

        if (!$form1) {
            Log::error("Form1 tidak ditemukan!");
            return response()->json(['message' => 'Form1 tidak ditemukan'], 404);
        }

        Log::debug("Data Form1:", (array) $form1);

        // Ambil progress
        Log::info("Mengambil data progress Form1...");
        $progress = $this->formService->getProgresSingleByParentFormId($data['form_1_id']);
        Log::debug("Progress Form1:", (array) $progress);

        $form6Progress = collect($progress)->firstWhere('form_type', 'form_6');
        Log::debug("Progress Form6 ditemukan:", ['form6Progress' => $form6Progress]);

        if (!$form6Progress) {
            Log::warning("FORM 6 belum selesai → form6Progress NULL");
        }

        // Tanggal selesai Form 6
        try {
            $form6EndedAt = $form6Progress && isset($form6Progress['updated_at'])
                ? Carbon::parse($form6Progress['updated_at'])->translatedFormat('d F Y')
                : null;
        } catch (\Exception $e) {
            Log::error("Error parsing tanggal Form6:", ['error' => $e->getMessage()]);
            $form6EndedAt = null;
        }

        Log::debug("Tanggal selesai Form6:", ['tanggal_selesai' => $form6EndedAt]);

        // Hitung final result
        Log::info("Menghitung final result...");
        $finalResult = $this->formService->getFinalResultByPkIdAndAsesiId($form1['pk_id'], $form1->asesi_id ?? null);
        Log::debug("Final result:", (array) $finalResult);

        $finalOnly = collect($finalResult)->pluck('final')->all();
        $counts    = collect($finalOnly)->countBy();

        $jumlahK = $counts->get('K', 0);
        $total   = count($finalOnly);
        $persenK = $total > 0 ? ($jumlahK / $total) * 100 : 0;

        Log::debug("Statistik final:", [
            'jumlah_K' => $jumlahK,
            'total'    => $total,
            'persen_K' => $persenK
        ]);

        $overallFinal = $persenK >= 80 ? 'KOMPETEN' : 'BELUM KOMPETEN';
        Log::info("Status akhir keseluruhan: {$overallFinal}");

        // Bangun data sertifikat
        $kompetensi = KompetensiPk::find($form1['pk_id']);
        Log::debug("Kompetensi PK:", optional($kompetensi)->toArray() ?? []);

        if (!$kompetensi) {
            Log::error("Kompetensi PK dengan pk_id {$form1['pk_id']} tidak ditemukan!");
        }

        $data['nama']            = strtoupper($form1->asesi_name);
        $data['tanggal_mulai']   = Carbon::parse($form1->updated_at)->translatedFormat('d F Y');
        $data['tanggal_selesai'] = $form6EndedAt;
        $data['status']          = $overallFinal;
        $data['gelar']           = $kompetensi->nama_level ?? null;
        $data['nomor_surat']     = '-';

        Log::debug("DATA SERTIFIKAT:", $data);


        // ======== TRANSAKSI =============
        DB::beginTransaction();
        try {
            Log::info("Generate nomor surat...");
            [$nomorUrut, $nomorSurat] = SertifikatPk::generateNomorSurat();
            $data['nomor_surat'] = $nomorSurat;

            Log::debug("Nomor urut / surat:", [
                'nomor_urut'  => $nomorUrut,
                'nomor_surat' => $nomorSurat
            ]);

            // Buat nama file aman
            $safeNama  = preg_replace('/[^A-Za-z0-9\-]/', '_', $data['nama']);
            $safeNomor = preg_replace('/[^A-Za-z0-9\-]/', '_', $nomorSurat);
            $fileName  = "sertifikat_{$safeNama}_{$safeNomor}.pdf";

            Log::debug("Nama file PDF:", ['fileName' => $fileName]);

            $year = Carbon::now()->year;
            Log::debug("Tahun saat ini:", ['year' => $year]);
            $path = "sertifikat/{$year}/{$fileName}";

            // Generate PDF
            Log::info("Membuat file PDF...");
            $pdf = Pdf::loadView('sertifikat.keperawatan', $data);

            // Simpan PDF
            Storage::disk('public')->put($path, $pdf->output());
            Log::info("PDF berhasil disimpan!", ['path' => $path]);


            // Simpan DB
            Log::info("Menyimpan metadata sertifikat ke DB...");
            $sertifikat = SertifikatPk::create([
                'asesi_id'        => $form1->asesi_id,
                'form_1_id'       => $data['form_1_id'],
                'pk_id'           => $form1->pk_id,
                'nomor_urut'      => $nomorUrut,
                'nomor_surat'     => $nomorSurat,
                'nama'            => $data['nama'],
                'gelar'           => $data['gelar'],
                'status'          => $data['status'],
                'tanggal_mulai'   => $form1->updated_at,
                'tanggal_selesai' => $form6Progress['updated_at'] ?? null,
                'file_path'       => $path,
            ]);

            Log::debug("Data sertifikat tersimpan:", $sertifikat->toArray());


            // Update Form1
            if ($form1->status === 'Approved') {
                Log::info("Update progress Form1 jadi Completed...");

                $this->formService->updateForm1($form1->form_1_id, 'Completed');
                $this->formService->updateProgresDanTrack(
                    $form1->form_1_id,
                    'form_1',
                    'Completed',
                    Auth::id(),
                    'Sertifikat Asesmen dibuat dan dikirim ke Asesi'
                );

                $this->formService->KirimNotifikasiKeUser(
                    $this->formService->findUser($form1->asesi_id),
                    'Sertifikat Asesmen',
                    'Sertifikat sudah dapat diunduh.'
                );
            }

            DB::commit();
            Log::info("=== BERHASIL GENERATE SERTIFIKAT ===");

            return response()->json([
                'message'     => 'Sertifikat berhasil disimpan',
                'preview_url' => url("storage/{$path}"),
                'data'        => $sertifikat,
                'nomor_surat' => $nomorSurat,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("ERROR SAAT GENERATE SERTIFIKAT:", [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                Log::warning("File PDF sudah dihapus karena gagal.");
            }

            return response()->json([
                'message' => 'Gagal menyimpan sertifikat',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function downloadSertifikatByFormId($form_1_id)
    {
        // Cari sertifikat berdasarkan form_1_id
        $sertifikat = SertifikatPk::where('form_1_id', $form_1_id)->first();

        if (!$sertifikat) {
            return response()->json([
                'message' => 'Sertifikat untuk form ini tidak ditemukan'
            ], 404);
        }

        // Pastikan file ada
        if (!Storage::disk('public')->exists($sertifikat->file_path)) {
            return response()->json([
                'message' => 'File sertifikat tidak ditemukan'
            ], 404);
        }

        // Ambil konten file
        $file = Storage::disk('public')->get($sertifikat->file_path);
        $mime = Storage::disk('public')->mimeType($sertifikat->file_path);

        // Download file
        return response($file, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'attachment; filename="' . basename($sertifikat->file_path) . '"');
    }

    public function viewSertifikatByFormId($form_1_id)
    {
        // Cari sertifikat berdasarkan form_1_id
        $sertifikat = SertifikatPk::where('form_1_id', $form_1_id)->first();

        if (!$sertifikat) {
            return response()->json([
                'message' => 'Sertifikat untuk form ini tidak ditemukan'
            ], 404);
        }

        // Pastikan file ada
        if (!Storage::disk('public')->exists($sertifikat->file_path)) {
            return response()->json([
                'message' => 'File sertifikat tidak ditemukan'
            ], 404);
        }

        // Ambil konten file
        $file = Storage::disk('public')->get($sertifikat->file_path);
        $mime = Storage::disk('public')->mimeType($sertifikat->file_path);

        // Tampilkan file sebagai response
        return response($file, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . basename($sertifikat->file_path) . '"');
    }

    public function getSertifikatByUserId($user_id)
    {
        // Ambil semua sertifikat milik user
        $sertifikatList = SertifikatPk::where('asesi_id', $user_id)->get();

        if ($sertifikatList->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada sertifikat untuk user ini'
            ], 404);
        }

        // Buat struktur data dengan preview_url pakai endpoint API
        $data = $sertifikatList->map(function ($sertifikat) {
            return [
                'id'              => $sertifikat->id,
                'form_1_id'       => $sertifikat->form_1_id,
                'pk_id'           => $sertifikat->pk_id,
                'nomor_urut'      => $sertifikat->nomor_urut,
                'nomor_surat'     => $sertifikat->nomor_surat,
                'nama'            => $sertifikat->nama,
                'gelar'           => $sertifikat->gelar,
                'status'          => $sertifikat->status,
                'tanggal_mulai'   => $sertifikat->tanggal_mulai,
                'tanggal_selesai' => $sertifikat->tanggal_selesai,
                // preview lewat API, bukan langsung ke storage
                'preview_url'     => url("sertifikat/view/{$sertifikat->form_1_id}"),
            ];
        });

        return response()->json([
            'message' => 'Daftar sertifikat berhasil diambil',
            'data'    => $data,
        ]);
    }

    public function getSertifikatByPkId($pk_id)
    {
        // Ambil user yang sedang login
        $userId = auth()->id();

        // Validasi pk_id
        $validator = Validator::make(
            ['pk_id' => $pk_id],
            ['pk_id' => 'required|integer|exists:kompetensi_pk,pk_id']
        );

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Ambil 1 data sertifikat (bukan array)
        $sertifikat = SertifikatPk::where('asesi_id', $userId)
            ->where('pk_id', $pk_id)
            ->first();

        if (!$sertifikat) {
            return response()->json([
                'message' => 'Tidak ada sertifikat untuk pk_id ini'
            ], 404);
        }

        // Format object data
        $previewUrl = url('storage/' . $sertifikat->file_path);

        $data = [
            'id'              => $sertifikat->id,
            'form_1_id'       => $sertifikat->form_1_id,
            'pk_id'           => $sertifikat->pk_id,
            'nomor_urut'      => $sertifikat->nomor_urut,
            'nomor_surat'     => $sertifikat->nomor_surat,
            'nama'            => $sertifikat->nama,
            'gelar'           => $sertifikat->gelar,
            'status'          => $sertifikat->status,
            'tanggal_mulai'   => $sertifikat->tanggal_mulai,
            'tanggal_selesai' => $sertifikat->tanggal_selesai,
            'preview_url'     => $previewUrl,
        ];


        return response()->json([
            'message' => 'Detail sertifikat berhasil diambil',
            'data'    => $data,
        ]);
    }


    public function getListSertifikat(Request $request)
    {
        try {
            // Ambil parameter status dari request
            $status = $request->input('status');
            $hasCertificate = $request->boolean('has_certificate'); // true/false/null

            // Query data form 6 berdasarkan status
            $query = Form6::query();

            if (!empty($status)) {
                $query->where('status', $status);
            }

            $form6List = $query->get();

            // Tambahkan flag sertifikat + form_1_id
            $result = $form6List->map(function ($form6) {
                // $form6 = Form6::find($request->form_6_id);
                $form_1_id = $this->formService->getParentFormIdByFormIdAndAsesiId($form6->form_6_id, $form6->asesi_id, 'form_6');
                // $form_1_id = $this->formService->getParentFormIdByFormId($form6->form_6_id);

                $existingSertifikat = SertifikatPk::where('form_1_id', $form_1_id)->first();

                return [
                    'form_6_id'        => $form6->form_6_id,
                    'form_1_id'        => $form_1_id, // <-- ditambahkan
                    'pk_id'            => $form6->pk_id,
                    'asesi_id'         => $form6->asesi_id,
                    'asesi_name'       => $form6->asesi_name,
                    'status'           => $form6->status,
                    'created_at'       => $form6->created_at,
                    'updated_at'       => $form6->updated_at,
                    'sertifikat'       => $existingSertifikat ? [
                        'id'          => $existingSertifikat->id,
                        'nomor_surat' => $existingSertifikat->nomor_surat,
                        'preview_url' => url("storage/{$existingSertifikat->file_path}")
                    ] : null,
                    'sudah_sertifikat' => $existingSertifikat ? true : false,
                ];
            });

            // Filter hasil berdasarkan has_certificate
            if ($request->has('has_certificate')) {
                $result = $result->filter(function ($item) use ($hasCertificate) {
                    return $hasCertificate
                        ? $item['sudah_sertifikat'] === true
                        : $item['sudah_sertifikat'] === false;
                })->values(); // reset index
            }

            return response()->json([
                'status'  => 200,
                'message' => 'List Form 6 berhasil diambil',
                'data'    => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Terjadi kesalahan saat mengambil data Form 6',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function getTranskripNilai(Request $request)
    {
        // ✅ Validasi
        $validator = Validator::make($request->all(), [
            'pk_id'    => 'required|integer|min:1',
            'asesi_id' => 'required|integer|min:1',
            'form_1_id'=> 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pkId    = $request->input('pk_id');
        $asesiId = $request->input('asesi_id');
        $form1Id = $request->input('form_1_id');

        // Cek apakah sudah ada transkrip
        $existing = TranskripNilaiPk::where('pk_id', $pkId)
            ->where('asesi_id', $asesiId)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Transkrip nilai sudah dibuat sebelumnya',
                'data'    => $existing,
            ], 400);
        }

        // Tentukan ekspresi casting sesuai driver DB
        $driver = DB::getDriverName();
        $orderExpr = $driver === 'mysql'
            ? 'CAST(no_elemen_form_3 AS UNSIGNED)'
            : 'CAST(no_elemen_form_3 AS INTEGER)';

        // ✅ Query ambil data nested
        $data = ElemenForm3::with([
            'kukForm3.iukForm3.soalForm7.jawabanForm7' => function ($q) use ($asesiId) {
                $q->where('asesi_id', $asesiId);
            }
        ])
        ->where('pk_id', $pkId)
        ->whereHas('kukForm3.iukForm3.soalForm7.jawabanForm7', function ($q) use ($asesiId) {
            $q->where('asesi_id', $asesiId);
        })
        ->orderByRaw("$orderExpr ASC")
        ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'status'  => 'not_found',
                'message' => "Data tidak ditemukan untuk pk_id: $pkId dan asesi_id: $asesiId",
            ], 404);
        }

        // ✅ Hitung nilai final hanya di level Elemen
        $elemenFinal = $data->map(function ($elemen) {
            $jumlahKuk = $elemen->kukForm3->count();
            $jumlahK   = 0;

            foreach ($elemen->kukForm3 as $kuk) {
                $totalIuk = $kuk->iukForm3->count();
                $jumlahKIuk = 0;

                foreach ($kuk->iukForm3 as $iuk) {
                    $totalSoal = $iuk->soalForm7->count();
                    $jumlahKSoal = 0;

                    foreach ($iuk->soalForm7 as $soal) {
                        foreach ($soal->jawabanForm7 as $jawaban) {
                            if ($jawaban->keputusan === 'K') {
                                $jumlahKSoal++;
                            }
                        }
                    }

                    $iukFinal = ($totalSoal > 0 && ($jumlahKSoal / $totalSoal) >= 0.5) ? 'K' : 'BK';
                    if ($iukFinal === 'K') $jumlahKIuk++;
                }

                $kukFinal = ($totalIuk > 0 && ($jumlahKIuk / $totalIuk) >= 0.5) ? 'K' : 'BK';
                if ($kukFinal === 'K') $jumlahK++;
            }

            $elemenFinal = ($jumlahKuk > 0 && ($jumlahK / $jumlahKuk) >= 0.5) ? 'K' : 'BK';

            return [
                'no_elemen_form_3' => $elemen->no_elemen_form_3,
                'nama_elemen'      => $elemen->isi_elemen,
                'final'            => $elemenFinal,
            ];
        });

        // Ambil info tambahan
        $form1 = BidangModel::find($form1Id);
        $asesiName = $form1 ? strtoupper($form1->asesi_name) : '-';
        $kompetensi = KompetensiPk::find($pkId);
        // Generate nomor dokumen
        [$nomorUrut, $nomorDokumen] = TranskripNilaiPk::generateNomorDokumen();

        // Buat nama file aman
        $safeNama  = preg_replace('/[^A-Za-z0-9\-]/', '_', $asesiName);
        $safeNomor = preg_replace('/[^A-Za-z0-9\-]/', '_', $nomorDokumen);
        $fileName  = "transkrip_{$safeNama}_{$safeNomor}.pdf";
        $path = 'transkrip/' . $fileName;

        // ✅ Pastikan data dilempar sebagai array
        $pdf = Pdf::loadView('transkrip.nilai', [
            'nama'        => $asesiName,
            'gelar'       => $kompetensi->nama_level ?? '-',
            'data'        => $elemenFinal->toArray(),
            'nomor'       => $nomorDokumen,
            'asesor_name' => $form1->asesor_name ?? '-', // ✅ kirim ke view
        ]);


        DB::beginTransaction();
        try {
            // Simpan file PDF
            Storage::disk('public')->put($path, $pdf->output());

            // Simpan metadata
            $transkrip = TranskripNilaiPk::create([
                'asesi_id'        => $asesiId,
                'form_1_id'       => $form1->form_1_id,
                'pk_id'           => $pkId,
                'nomor_urut'      => $nomorUrut,
                'nomor_dokumen'   => $nomorDokumen,
                'nama'            => $asesiName,
                'gelar'           => $kompetensi->nama_level ?? '-',
                'status'          => 'Selesai',
                'tanggal_mulai'   => $form1 ? $form1->updated_at : Carbon::now(),
                'tanggal_selesai' => Carbon::now(),
                'file_path'       => $path,
            ]);

            DB::commit();

            return response()->json([
                'message'      => 'Transkrip nilai berhasil disimpan',
                'preview_url'  => url("storage/{$path}"),
                'data'         => $transkrip,
                'nomor_dokumen'=> $nomorDokumen,
                'asesor_name' => $form1->asesor_name ?? '-', // tambah ini
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            return response()->json([
                'message' => 'Gagal menyimpan transkrip nilai',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }






}
