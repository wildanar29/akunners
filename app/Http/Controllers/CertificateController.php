<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf; // alias dari dompdf
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\KompetensiPk;
use App\Models\Form6;
use App\Models\SertifikatPk;
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
        $form_1_id = $request->input('form_1_id', null);

        // Cek apakah sudah ada sertifikat untuk form_1_id ini
        $existing = SertifikatPk::where('form_1_id', $form_1_id)->first();
        if ($existing) {
            return response()->json([
                'message' => 'Sertifikat untuk form ini sudah dibuat sebelumnya',
                'data'    => $existing,
            ], 400);
        }

        $data = [
            'form_1_id' => $form_1_id,
        ];

        $form1 = $this->formService->getParentDataByFormId($data['form_1_id']);
        $progress = $this->formService->getProgresSingleByParentFormId($data['form_1_id']);
        $form6Progress = collect($progress)->firstWhere('form_type', 'form_6');
        $form6EndedAt  = $form6Progress ? Carbon::parse($form6Progress['updated_at'])->translatedFormat('d F Y') : null;

        // Hitung final result
        $finalResult = $this->formService->getFinalResultByPkIdAndAsesiId($form1['pk_id'], $form1->asesi_id ?? null);
        $finalOnly   = collect($finalResult)->pluck('final')->all();

        $counts   = collect($finalOnly)->countBy();
        $jumlahK  = $counts->get('K', 0);
        $total    = count($finalOnly);
        $persenK  = $total > 0 ? ($jumlahK / $total) * 100 : 0;

        $overallFinal = $persenK >= 80 ? 'KOMPETEN' : 'BELUM KOMPETEN';

        if ($form1) {
            $kompetensi = KompetensiPk::find($form1['pk_id']); 

            $data['nama']            = strtoupper($form1->asesi_name);
            $data['tanggal_mulai']   = Carbon::parse($form1->updated_at)->translatedFormat('d F Y');
            $data['tanggal_selesai'] = $form6EndedAt;
            $data['status']          = $overallFinal;
            $data['gelar']           = $kompetensi->nama_level;
            $data['nomor_surat']     = '-'; // default, akan diisi saat simpan
        }

        DB::beginTransaction();
        try {
            // Generate nomor surat otomatis + nomor urut
            [$nomorUrut, $nomorSurat] = SertifikatPk::generateNomorSurat();
            $data['nomor_surat'] = $nomorSurat;

            // Buat nama file aman: gabungkan nama + nomor surat
            $safeNama = preg_replace('/[^A-Za-z0-9\-]/', '_', $data['nama']);
            $safeNomor = preg_replace('/[^A-Za-z0-9\-]/', '_', $nomorSurat);
            $fileName = "sertifikat_{$safeNama}_{$safeNomor}.pdf";

            $path = 'sertifikat/' . $fileName;

            // Generate PDF
            $pdf = Pdf::loadView('sertifikat.keperawatan', $data);

            // Simpan file PDF
            Storage::disk('public')->put($path, $pdf->output());

            // Simpan metadata sertifikat
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
                'tanggal_selesai' => $form6Progress['updated_at'],
                'file_path'       => $path,
            ]);

            DB::commit();

            return response()->json([
                'message'     => 'Sertifikat berhasil disimpan',
                'preview_url' => url("storage/{$path}"),
                'data'        => $sertifikat,
                'nomor_surat' => $nomorSurat,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            return response()->json([
                'message' => 'Gagal menyimpan sertifikat',
                'error'   => $e->getMessage(),
            ], 500);
        }
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
                $form_1_id = $this->formService->getParentFormIdByFormId($form6->form_6_id);

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






}
