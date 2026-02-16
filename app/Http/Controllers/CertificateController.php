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
use App\Models\UserRole;
use App\Models\Form6;
use App\Models\KompetensiProgres;
use App\Models\User;
use App\Models\DataAsesorModel;
use App\Models\SertifikatPk;
use App\Models\TranskripNilaiPk;
use App\Models\ElemenForm3;
use App\Service\FormService;
use Carbon\Carbon;
use Milon\Barcode\DNS2D;
// use Milon\Barcode\Facades\DNS2DFacade as DNS2D;


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
        $form_1_id = $request->input('form_1_id', null);
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

        $form1 = $this->formService->getParentDataByFormId($data['form_1_id']);

        if (!$form1) {
            Log::error("Form1 tidak ditemukan!");
            return response()->json(['message' => 'Form1 tidak ditemukan'], 404);
        }

        $progress = $this->formService->getProgresSingleByParentFormId($data['form_1_id']);

        $form6Progress = collect($progress)->firstWhere('form_type', 'form_6');

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
        $finalResult = $this->formService
            ->getFinalResultByPkIdAndAsesiId($form1['pk_id'], $form1->asesi_id ?? null);

        Log::debug("Final result:", (array) $finalResult);

        $finalOnly = collect($finalResult)->pluck('final')->all();
        $counts    = collect($finalOnly)->countBy();

        $jumlahK = $counts->get('K', 0);
        $total   = count($finalOnly);
        $persenK = $total > 0 ? ($jumlahK / $total) * 100 : 0;


        $overallFinal = $persenK >= 80 ? 'KOMPETEN' : 'BELUM KOMPETEN';

        // Bangun data sertifikat
        $kompetensi = KompetensiPk::find($form1['pk_id']);


        if (!$kompetensi) {
            Log::error("Kompetensi PK dengan pk_id {$form1['pk_id']} tidak ditemukan!");
        }

        $data['nama']            = strtoupper($form1->asesi_name);
        $data['nama_asesor']     = strtoupper($form1->asesor_name ?? 'ASESOR');
        $data['tanggal_mulai']   = Carbon::parse($form1->updated_at)->translatedFormat('d F Y');
        $data['tanggal_selesai'] = $form6EndedAt;
        $data['status']          = $overallFinal;
        $data['gelar']           = $kompetensi->nama_level ?? null;
        $data['nomor_surat']     = '-';

        Log::debug("DATA SERTIFIKAT:", $data);

        

        // ======== TRANSAKSI =============
        DB::beginTransaction();
        try {

            Log::info("Membuat transkrip nilai terlebih dahulu...");
            $this->createTranskripNilai(
                $form1->pk_id,
                $form1->asesi_id,
                $form1->form_1_id
            );

            Log::info("Generate nomor surat...");
            [$nomorUrut, $nomorSurat] = SertifikatPk::generateNomorSurat();
            $data['nomor_surat'] = $nomorSurat;

            $barcodePayload = [
                'nomor_surat' => $data['nomor_surat'],
                'nama'        => $data['nama'],
                'gelar'       => $data['gelar'],
                'status'      => $data['status'],
            ];

            $data['barcode_data'] = json_encode($barcodePayload, JSON_UNESCAPED_UNICODE);

            // Buat nama file aman
            $safeNama  = preg_replace('/[^A-Za-z0-9\-]/', '_', $data['nama']);
            $safeNomor = preg_replace('/[^A-Za-z0-9\-]/', '_', $nomorSurat);
            $fileName  = "sertifikat_{$safeNama}_{$safeNomor}.pdf";

            $year = Carbon::now()->year;
            $path = "sertifikat/{$year}/{$fileName}";

            // ================== QR CODE ==================
            $dns2d = new DNS2D();
            $dns2d->setStorPath(storage_path('framework/barcodes'));

            // QR Direktur
            $barcodeDirekturPayload = [
                'nomor_surat' => $data['nomor_surat'],
                'nama'        => $data['nama'],
                'gelar'       => $data['gelar'],
                'status'      => $data['status'],
                'penandatangan' => 'Direktur Utama RS Immanuel',
            ];

            $data['barcode_direktur'] = $dns2d->getBarcodePNG(
                json_encode($barcodeDirekturPayload, JSON_UNESCAPED_UNICODE),
                'QRCODE',
                5,
                5
            );

            // QR Asesor
            $barcodeAsesorPayload = [
                'nomor_surat' => $data['nomor_surat'],
                'nama'        => $data['nama'],
                'asesor'      => $data['nama_asesor'],
                'pk'          => $data['gelar'],
                'penandatangan' => 'Asesor Kompetensi',
            ];

            $data['barcode_asesor'] = $dns2d->getBarcodePNG(
                json_encode($barcodeAsesorPayload, JSON_UNESCAPED_UNICODE),
                'QRCODE',
                5,
                5
            );
            // =====================================================


            // =====================================================

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
            // $statusForm1 = $this->formService
            //     ->getStatusByParentFormIdAndType($data['form_1_id'], 'form_1')
            //     ->first();

            $progres = KompetensiProgres::where('form_id', $data['form_1_id'])
				->where('user_id', $form1->asesi_id)
				->whereNull('parent_form_id')
				->first();
            
            Log::info($progres);
            // Update Form1
            if ($progres->status === 'Approved') {
                Log::info("Update progress Form1 jadi Completed...");

                // ini nnti dipindahin ke HUMAS
                // $this->formService->updateForm1($form1->form_1_id, 'Completed');
                // $this->formService->updateProgresDanTrack(
                //     $form1->form_1_id,
                //     'form_1',
                //     'Completed',
                //     Auth::id(),
                //     'Sertifikat Asesmen dibuat dan dikirim ke Asesi'
                // );

                // $this->formService->KirimNotifikasiKeUser(
                //     $this->formService->findUser($form1->asesi_id),
                //     'Sertifikat Asesmen',
                //     'Sertifikat sudah dapat diunduh.'
                // );
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

    // public function approveHumasRSI(Request $request, $form_1_id)
    // {
    //     return response()->json([
    //         'message' => 'Endpoint hidup',
    //         'form_1_id' => $form_1_id
    //     ]);
    // }
    public function approveHumasRSI(Request $request, $form_1_id)
    {
        Log::info("HUMAS RSI approve sertifikat form_1_id: {$form_1_id}");

        DB::beginTransaction();

        try {

            // ==========================
            // Ambil Form1
            // ==========================
            $form1 = $this->formService->getParentDataByFormId($form_1_id);

            if (!$form1) {
                return response()->json(['message' => 'Form1 tidak ditemukan'], 404);
            }

            // ==========================
            // Ambil Data Sertifikat
            // ==========================
            $sertifikat = SertifikatPk::where('form_1_id', $form_1_id)->first();

            if (!$sertifikat) {
                return response()->json(['message' => 'Sertifikat belum digenerate'], 400);
            }

            // ==========================
            // Ambil Transkrip Nilai
            // ==========================
            $transkrip = TranskripNilaiPk::where('form_1_id', $form_1_id)->first();

            if (!$transkrip) {
                return response()->json(['message' => 'Transkrip nilai belum dibuat'], 400);
            }

            // ==========================
            // Siapkan Data QR
            // ==========================
            $dns2d = new DNS2D();
            $dns2d->setStorPath(storage_path('framework/barcodes'));

            $barcodeDirekturPayload = [
                'nomor_surat'  => $sertifikat->nomor_surat,
                'nama'         => $sertifikat->nama,
                'gelar'        => $sertifikat->gelar,
                'status'       => $sertifikat->status,
                'penandatangan'=> 'Direktur Utama RS Immanuel',
                'approved_at'  => Carbon::now()->toDateTimeString(),
            ];

            $barcodeAsesorPayload = [
                'nomor_surat'  => $sertifikat->nomor_surat,
                'nama'         => $sertifikat->nama,
                'asesor'       => strtoupper($form1->asesor_name ?? 'ASESOR'),
                'pk'           => $sertifikat->gelar,
                'penandatangan'=> 'Asesor Kompetensi',
                'approved_at'  => Carbon::now()->toDateTimeString(),
            ];

            $data = [
                'nama'            => $sertifikat->nama,
                'nama_asesor'     => strtoupper($form1->asesor_name ?? 'ASESOR'),
                'tanggal_mulai'   => Carbon::parse($sertifikat->tanggal_mulai)->translatedFormat('d F Y'),
                'tanggal_selesai' => $sertifikat->tanggal_selesai
                                        ? Carbon::parse($sertifikat->tanggal_selesai)->translatedFormat('d F Y')
                                        : null,
                'status'          => $sertifikat->status,
                'gelar'           => $sertifikat->gelar,
                'nomor_surat'     => $sertifikat->nomor_surat,

                // QR CODE
                'barcode_direktur' => $dns2d->getBarcodePNG(
                    json_encode($barcodeDirekturPayload, JSON_UNESCAPED_UNICODE),
                    'QRCODE',
                    5,
                    5
                ),
                'barcode_asesor' => $dns2d->getBarcodePNG(
                    json_encode($barcodeAsesorPayload, JSON_UNESCAPED_UNICODE),
                    'QRCODE',
                    5,
                    5
                ),
            ];

            // ==========================
            // Regenerate PDF dengan QR
            // ==========================
            $year = Carbon::now()->year;
            $safeNama  = preg_replace('/[^A-Za-z0-9\-]/', '_', $sertifikat->nama);
            $safeNomor = preg_replace('/[^A-Za-z0-9\-]/', '_', $sertifikat->nomor_surat);
            $fileName  = "sertifikat_{$safeNama}_{$safeNomor}.pdf";
            $path      = "sertifikat/{$year}/{$fileName}";

            $pdf = Pdf::loadView('sertifikat.keperawatan', $data);

            Storage::disk('public')->put($path, $pdf->output());

            // ==========================
            // Update Database
            // ==========================
            $sertifikat->update([
                'file_path'    => $path,
                'updated_at'  => Carbon::now(),
            ]);


            // ==========================
            // Update Status Form & Progress
            // ==========================
            $this->formService->updateForm1($form_1_id, 'Completed');

            $this->formService->updateProgresDanTrack(
                $form_1_id,
                'form_1',
                'Completed',
                $form1->asesi_id,
                'Sertifikat & Transkrip telah disetujui HUMAS dan ditandatangani QR Code'
            );

            // ==========================
            // Kirim Notifikasi
            // ==========================
            $this->formService->KirimNotifikasiKeUser(
                $this->formService->findUser($form1->asesi_id),
                'Sertifikat Asesmen',
                'Sertifikat sudah disetujui dan dapat diunduh.'
            );

            DB::commit();

            Log::info("=== APPROVE HUMAS BERHASIL ===");

            return response()->json([
                'message'     => 'Sertifikat & Transkrip berhasil di-approve dan ditandatangani QR',
                'preview_url' => url("storage/{$path}")
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error("ERROR APPROVE HUMAS:", [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Gagal approve sertifikat',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

        
    public function getSertifikatByPkId($pk_id)
    {
        $userId = auth()->id();

        // ================= VALIDASI PK_ID =================
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


        // ================= AMBIL FORM 1 =================
        // $form1 = $this->formService
        //     ->getForm1ByAsesiIdAndPkId($userId,$pk_id);

        // if (!$form1) {
        //     return response()->json([
        //         'message' => 'Form 1 tidak ditemukan'
        //     ], 404);
        // }
        $form1 = BidangModel::where('asesi_id', $userId)
                    ->where('pk_id', $pk_id)
                    ->first();

        $progres = KompetensiProgres::where('form_id', $form1->form_1_id)
            ->where('user_id', $form1->asesi_id)
            ->whereNull('parent_form_id')
            ->first();

        // ================= CEK STATUS FORM 1 =================
        if (!in_array($progres->status, ['Completed'])) {
            return response()->json([
                'message' => 'Sertifikat belum tersedia karena proses asesmen belum selesai',
                'status_form_1' => $form1->status
            ], 400);
        }

        // ================= AMBIL SERTIFIKAT =================
        $sertifikat = SertifikatPk::where('asesi_id', $userId)
            ->where('pk_id', $pk_id)
            ->first();

        if (!$sertifikat) {
            return response()->json([
                'message' => 'Tidak ada sertifikat untuk pk_id ini'
            ], 404);
        }

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

    // public function getTranskripNilai(Request $request)
    // {
    //     // ✅ Validasi
    //     $validator = Validator::make($request->all(), [
    //         'pk_id'    => 'required|integer|min:1',
    //         'asesi_id' => 'required|integer|min:1',
    //         'form_1_id'=> 'required|integer|min:1',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     $pkId    = $request->input('pk_id');
    //     $asesiId = $request->input('asesi_id');
    //     $form1Id = $request->input('form_1_id');

    //     // Cek apakah sudah ada transkrip
    //     $existing = TranskripNilaiPk::where('pk_id', $pkId)
    //         ->where('asesi_id', $asesiId)
    //         ->first();

    //     if ($existing) {
    //         return response()->json([
    //             'message' => 'Transkrip nilai sudah dibuat sebelumnya',
    //             'data'    => $existing,
    //         ], 400);
    //     }

    //     // Tentukan ekspresi casting sesuai driver DB
    //     $driver = DB::getDriverName();
    //     $orderExpr = $driver === 'mysql'
    //         ? 'CAST(no_elemen_form_3 AS UNSIGNED)'
    //         : 'CAST(no_elemen_form_3 AS INTEGER)';

    //     // ✅ Query ambil data nested
    //     $data = ElemenForm3::with([
    //         'kukForm3.iukForm3.soalForm7.jawabanForm7' => function ($q) use ($asesiId) {
    //             $q->where('asesi_id', $asesiId);
    //         }
    //     ])
    //     ->where('pk_id', $pkId)
    //     ->whereHas('kukForm3.iukForm3.soalForm7.jawabanForm7', function ($q) use ($asesiId) {
    //         $q->where('asesi_id', $asesiId);
    //     })
    //     ->orderByRaw("$orderExpr ASC")
    //     ->get();

    //     if ($data->isEmpty()) {
    //         return response()->json([
    //             'status'  => 'not_found',
    //             'message' => "Data tidak ditemukan untuk pk_id: $pkId dan asesi_id: $asesiId",
    //         ], 404);
    //     }

    //     // ✅ Hitung nilai final hanya di level Elemen
    //     $elemenFinal = $data->map(function ($elemen) {
    //         $jumlahKuk = $elemen->kukForm3->count();
    //         $jumlahK   = 0;

    //         foreach ($elemen->kukForm3 as $kuk) {
    //             $totalIuk = $kuk->iukForm3->count();
    //             $jumlahKIuk = 0;

    //             foreach ($kuk->iukForm3 as $iuk) {
    //                 $totalSoal = $iuk->soalForm7->count();
    //                 $jumlahKSoal = 0;

    //                 foreach ($iuk->soalForm7 as $soal) {
    //                     foreach ($soal->jawabanForm7 as $jawaban) {
    //                         if ($jawaban->keputusan === 'K') {
    //                             $jumlahKSoal++;
    //                         }
    //                     }
    //                 }

    //                 $iukFinal = ($totalSoal > 0 && ($jumlahKSoal / $totalSoal) >= 0.5) ? 'K' : 'BK';
    //                 if ($iukFinal === 'K') $jumlahKIuk++;
    //             }

    //             $kukFinal = ($totalIuk > 0 && ($jumlahKIuk / $totalIuk) >= 0.5) ? 'K' : 'BK';
    //             if ($kukFinal === 'K') $jumlahK++;
    //         }

    //         $elemenFinal = ($jumlahKuk > 0 && ($jumlahK / $jumlahKuk) >= 0.5) ? 'K' : 'BK';

    //         return [
    //             'no_elemen_form_3' => $elemen->no_elemen_form_3,
    //             'nama_elemen'      => $elemen->isi_elemen,
    //             'final'            => $elemenFinal,
    //         ];
    //     });

    //     // Ambil info tambahan
    //     $form1 = BidangModel::find($form1Id);
    //     $asesiName = $form1 ? strtoupper($form1->asesi_name) : '-';
    //     $kompetensi = KompetensiPk::find($pkId);
    //     // Generate nomor dokumen
    //     [$nomorUrut, $nomorDokumen] = TranskripNilaiPk::generateNomorDokumen();

    //     // Buat nama file aman
    //     $safeNama  = preg_replace('/[^A-Za-z0-9\-]/', '_', $asesiName);
    //     $safeNomor = preg_replace('/[^A-Za-z0-9\-]/', '_', $nomorDokumen);
    //     $fileName  = "transkrip_{$safeNama}_{$safeNomor}.pdf";
    //     $path = 'transkrip/' . $fileName;

    //     // ✅ Pastikan data dilempar sebagai array
    //     $pdf = Pdf::loadView('transkrip.nilai', [
    //         'nama'        => $asesiName,
    //         'gelar'       => $kompetensi->nama_level ?? '-',
    //         'data'        => $elemenFinal->toArray(),
    //         'nomor'       => $nomorDokumen,
    //         'asesor_name' => $form1->asesor_name ?? '-', // ✅ kirim ke view
    //     ]);


    //     DB::beginTransaction();
    //     try {
    //         // Simpan file PDF
    //         Storage::disk('public')->put($path, $pdf->output());

    //         // Simpan metadata
    //         $transkrip = TranskripNilaiPk::create([
    //             'asesi_id'        => $asesiId,
    //             'form_1_id'       => $form1->form_1_id,
    //             'pk_id'           => $pkId,
    //             'nomor_urut'      => $nomorUrut,
    //             'nomor_dokumen'   => $nomorDokumen,
    //             'nama'            => $asesiName,
    //             'gelar'           => $kompetensi->nama_level ?? '-',
    //             'status'          => 'Selesai',
    //             'tanggal_mulai'   => $form1 ? $form1->updated_at : Carbon::now(),
    //             'tanggal_selesai' => Carbon::now(),
    //             'file_path'       => $path,
    //         ]);

    //         DB::commit();

    //         return response()->json([
    //             'message'      => 'Transkrip nilai berhasil disimpan',
    //             'preview_url'  => url("storage/{$path}"),
    //             'data'         => $transkrip,
    //             'nomor_dokumen'=> $nomorDokumen,
    //             'asesor_name' => $form1->asesor_name ?? '-', // tambah ini
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         if (Storage::disk('public')->exists($path)) {
    //             Storage::disk('public')->delete($path);
    //         }
    //         return response()->json([
    //             'message' => 'Gagal menyimpan transkrip nilai',
    //             'error'   => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    private function createTranskripNilai(int $pkId, int $asesiId, int $form1Id)
    {
        $existing = TranskripNilaiPk::where('pk_id', $pkId)
            ->where('asesi_id', $asesiId)
            ->first();

        if ($existing) {
            throw new \Exception('Transkrip nilai sudah dibuat sebelumnya');
        }

        $driver = DB::getDriverName();
        $orderExpr = $driver === 'mysql'
            ? 'CAST(no_elemen_form_3 AS UNSIGNED)'
            : 'CAST(no_elemen_form_3 AS INTEGER)';

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
            throw new \Exception("Data elemen tidak ditemukan");
        }

        // ===========================
        // HITUNG NILAI FINAL
        // ===========================

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

            $status = ($jumlahKuk > 0 && ($jumlahK / $jumlahKuk) >= 0.5)
                ? 'KOMPETEN'
                : 'BELUM KOMPETEN';


            return [
                'no_elemen_form_3' => $elemen->no_elemen_form_3,
                'nama_elemen'      => $elemen->isi_elemen,
                'final'            => $status,
            ];
        });

        $form1 = BidangModel::find($form1Id);
        $asesiName  = strtoupper($form1->asesi_name ?? '-');
        $asesorName = strtoupper($form1->asesor_name ?? 'ASESOR');
        Log::info($form1);
        $userAsesor = DataAsesorModel::where('user_id', $form1->asesor_id)->first();
        Log::info('ini asesor');
        Log::info($userAsesor);
        $asesorReg = $userAsesor->no_reg;
        $kompetensi = KompetensiPk::find($pkId);

        [$nomorUrut, $nomorDokumen] = TranskripNilaiPk::generateNomorDokumen();

        $dns2d = new DNS2D();
        $dns2d->setStorPath(storage_path('framework/barcodes'));

        // ===============================
        // QR ASESOR
        // ===============================
        $barcodeAsesor = $dns2d->getBarcodePNG(
            json_encode([
                'nomor_dokumen' => $nomorDokumen,
                'nama_asesi'    => $asesiName,
                'asesor'        => $asesorName,
                'kompetensi'    => $kompetensi->nama_level ?? '-',
                'jenis'         => 'Transkrip Nilai',
                'penandatangan' => 'Asesor Kompetensi'
            ], JSON_UNESCAPED_UNICODE),
            'QRCODE',
            5,
            5
        );

        // ===============================
        // BIDANG (DUMMY DATA SEMENTARA)
        // ===============================

        // 🔥 Data dummy sementara
        // 🔥 Data dummy sementara
        $bidangName       = 'SRI WAHYUNI, S.Kep., Ns., M.Kep.';
        $bidangJabatan    = 'Kepala Bidang Keperawatan';
        $bidangReg        = 'REG-KEP-001-2025';

        // $asesorReg        = 'REG-ASESOR-015-2025';


        $barcodeBidang = $dns2d->getBarcodePNG(
            json_encode([
                'nomor_dokumen' => $nomorDokumen,
                'nama_asesi'    => $asesiName,
                'kompetensi'    => $kompetensi->nama_level ?? '-',
                'bidang'        => $bidangName,
                'jabatan'       => $bidangJabatan,
                'jenis'         => 'Transkrip Nilai',
                'penandatangan' => 'Bidang Keperawatan'
            ], JSON_UNESCAPED_UNICODE),
            'QRCODE',
            5,
            5
        );

        // ===============================

        $safeNama  = preg_replace('/[^A-Za-z0-9\-]/', '_', $asesiName);
        $safeNomor = preg_replace('/[^A-Za-z0-9\-]/', '_', $nomorDokumen);
        $fileName  = "transkrip_{$safeNama}_{$safeNomor}.pdf";
        $path = 'transkrip/' . $fileName;

        $pdf = Pdf::loadView('transkrip.nilai', [
            'nama'            => $asesiName,
            'gelar'           => $kompetensi->nama_level ?? '-',
            'data'            => $elemenFinal->toArray(),
            'nomor'           => $nomorDokumen,
            'asesor_name'     => $asesorName,
            'asesor_reg'      => $asesorReg,
            'bidang_name'     => $bidangName,
            'bidang_jabatan'  => $bidangJabatan,
            'bidang_reg'      => $bidangReg,
            'barcode_asesor'  => $barcodeAsesor,
            'barcode_bidang'  => $barcodeBidang,
        ]);


        Storage::disk('public')->put($path, $pdf->output());

        return TranskripNilaiPk::create([
            'asesi_id'        => $asesiId,
            'form_1_id'       => $form1Id,
            'pk_id'           => $pkId,
            'nomor_urut'      => $nomorUrut,
            'nomor_dokumen'   => $nomorDokumen,
            'nama'            => $asesiName,
            'gelar'           => $kompetensi->nama_level ?? '-',
            'status'          => 'Selesai',
            'tanggal_mulai'   => $form1->updated_at ?? Carbon::now(),
            'tanggal_selesai' => Carbon::now(),
            'file_path'       => $path,
        ]);
    }



}
