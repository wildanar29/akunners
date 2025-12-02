<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\TypeForm3_B;
use App\Models\TypeForm3_C;
use App\Models\TypeForm3_A;
use App\Models\TypeForm3_D;
use App\Models\PenilaianForm2Model;
use App\Models\InterviewModel;
use App\Models\Form3Model;
use App\Models\UserRole;
use App\Models\DaftarUser;
use App\Models\BidangModel;
use App\Models\KompetensiProgres;
use App\Models\KompetensiTrack;
use App\Models\DataAsesorModel;
use App\Models\PkProgressModel;
use App\Models\ElemenForm3;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;  
use Carbon\Carbon; // Tambahkan ini untuk menggunakan Carbon
use App\Service\OneSignalService;
use App\Service\FormService;
use App\Models\Notification;

class Form3Controller extends BaseController
{

    protected $oneSignalService;
    protected $formService;

	public function __construct(OneSignalService $oneSignalService, FormService $formService)
	{
		$this->oneSignalService = $oneSignalService;
        $this->formService = $formService;
	}

    public function getRencanaAsesmen(Request $request)
    {
        $pk_id = $request->query('pk_id');

        // Validasi parameter pk_id wajib
        if (!$pk_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter pk_id wajib diisi.',
                'data' => null
            ], 400);
        }

        // === Ambil data SPO berdasarkan pk_id ===
        $spoList = \App\Models\Spo::where('pk_id', $pk_id)
            ->orderByRaw('CAST(no_spo AS UNSIGNED) ASC')
            ->get();

        // Ambil data Elemen + relasi KUK + IUK
        $elemenList = \App\Models\ElemenForm3::with(['kukForm3.iukForm3'])
            ->where('pk_id', $pk_id)
            ->orderByRaw('CAST(no_elemen_form_3 AS UNSIGNED) ASC')
            ->get();

        if ($spoList->isEmpty() && $elemenList->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan untuk pk_id tersebut.',
                'data' => null
            ], 404);
        }

        // Mapping nilai group_no ke Metode & Perangkat
        $metodeMap = [
            '4A' => 'Observasi',
            '4B' => 'Uji Lisan',
            '4C' => 'Uji Tulis',
            '4D' => 'Portofolio',
        ];

        $perangkatMap = [
            '4A' => 'Daftar Checklist',
            '4B' => 'Daftar Pertanyaan Lisan',
            '4C' => 'Daftar Pertanyaan Tulisan',
            '4D' => 'Daftar Checklist EMR',
        ];

        // HTML WebView Responsive
        $html = '<!DOCTYPE html>
        <html lang="id">
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

        <title>Rencana Asesmen</title>

        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
                margin: 15px;
                background: #f7f7f7;
                color: #333;
            }

            h2 {
                text-align: center;
                margin: 20px 0 10px;
                font-size: 20px;
                font-weight: 600;
                text-transform: uppercase;
            }

            .card {
                background: #fff;
                padding: 15px;
                border-radius: 10px;
                margin-bottom: 20px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }

            .table-responsive {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin-top: 10px;
                margin-bottom: 25px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                min-width: 650px;
            }

            th, td {
                padding: 10px;
                border: 1px solid #ddd;
                font-size: 14px;
            }

            th {
                background: #eaeaea;
                font-weight: bold;
                text-align: center;
            }

            td.center {
                text-align: center;
            }

            .elemen-header {
                background: #dcdcdc;
                font-weight: bold;
                font-size: 14px;
                text-align: left;
            }

            @media (max-width: 480px) {
                h2 {
                    font-size: 18px;
                }
                th, td {
                    font-size: 13px;
                    padding: 8px;
                }
            }
        </style>

        </head>
        <body>

        <h2>STANDAR PROSEDUR OPERASIONAL (SPO)</h2>
        <div class="card">
        <div class="table-responsive">';

        // === TABEL SPO ===
        if ($spoList->isNotEmpty()) {
            $html .= '<table>
                        <thead>
                            <tr>
                                <th style="width: 10%">No</th>
                                <th style="width: 25%">Nomor SPO</th>
                                <th style="width: 65%">Nama SPO</th>
                            </tr>
                        </thead>
                        <tbody>';

            foreach ($spoList as $index => $spo) {
                $html .= '<tr>
                            <td class="center">' . ($index + 1) . '</td>
                            <td>' . htmlspecialchars($spo->no_spo) . '</td>
                            <td>' . htmlspecialchars($spo->nama_spo) . '</td>
                        </tr>';
            }

            $html .= '</tbody></table>';
        } else {
            $html .= '<p style="text-align:center;"><em>Tidak ada data SPO untuk pk_id ini.</em></p>';
        }

        $html .= '</div></div>'; // end card SPO

        // === RENCANA ASESMEN ===
        $html .= '<h2>RENCANA ASESMEN</h2>';

        foreach ($elemenList as $elemen) {

            $html .= '<div class="card">';
            $html .= '<div class="table-responsive">';
            $html .= '<table>
                        <thead>
                            <tr>
                                <th colspan="4" class="elemen-header">
                                    Elemen: ' . htmlspecialchars($elemen->no_elemen_form_3 . ' - ' . $elemen->isi_elemen) . '
                                </th>
                            </tr>
                            <tr>
                                <th style="width: 25%">Kriteria Unjuk Kerja (KUK)</th>
                                <th style="width: 25%">Indikator Unjuk Kerja (IUK)</th>
                                <th style="width: 25%">Metode Asesmen</th>
                                <th style="width: 25%">Perangkat Asesmen</th>
                            </tr>
                        </thead>
                        <tbody>';

            foreach ($elemen->kukForm3 as $kuk) {
                $rowspan = max(count($kuk->iukForm3), 1);

                if ($rowspan > 0) {

                    $html .= '<tr>
                        <td class="center" rowspan="' . $rowspan . '">' .
                            htmlspecialchars($kuk->no_kuk . ' - ' . $kuk->kuk_name) .
                        '</td>';

                    foreach ($kuk->iukForm3 as $index => $iuk) {
                        if ($index > 0) $html .= '<tr>';

                        $metode = $metodeMap[$iuk->group_no] ?? '-';
                        $perangkat = $perangkatMap[$iuk->group_no] ?? '-';

                        $html .= '<td>' . htmlspecialchars($iuk->no_iuk . ' - ' . $iuk->iuk_name) . '</td>
                                <td class="center">' . htmlspecialchars($metode) . '</td>
                                <td class="center">' . htmlspecialchars($perangkat) . '</td>
                                </tr>';
                    }

                } else {
                    $html .= '<tr>
                        <td class="center">' . htmlspecialchars($kuk->no_kuk . ' - ' . $kuk->kuk_name) . '</td>
                        <td colspan="3" class="center"><em>Tidak ada IUK terkait</em></td>
                    </tr>';
                }
            }

            $html .= '</tbody></table></div></div>';
        }

        $html .= '</body></html>';

        return response($html, 200, [
            'Content-Type' => 'text/html'
        ]);

    }




    public function getAllDataFormB(Request $request)
    {
        $no_elemen = $request->query('no_elemen');
        $pk_id = $request->query('pk_id');

        // Validasi pk_id wajib
        if (!$pk_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter pk_id wajib diisi.',
                'data' => null
            ], 400);
        }

        // Ambil data dengan relasi
        $query = TypeForm3_B::with(['iukForm3.kukForm3.elemenForm3.kompetensiPk'])
            ->orderBy('id', 'asc');

        // Filter berdasarkan elemen
        if ($no_elemen) {
            $query->whereHas('iukForm3.kukForm3.elemenForm3', function ($q) use ($no_elemen) {
                $q->where('no_elemen_form_3', $no_elemen);
            });
        }

        // Filter berdasarkan pk_id
        $query->whereHas('iukForm3.kukForm3.elemenForm3.kompetensiPk', function ($q) use ($pk_id) {
            $q->where('pk_id', $pk_id);
        });

        $data = $query->get();

        // Mengelompokkan data
        $elemen_group = [];

        foreach ($data as $item) {
            $elemen_key = ($item->iukForm3->kukForm3->elemenForm3->no_elemen_form_3 ?? '-') . ' : ' .
                        ($item->iukForm3->kukForm3->elemenForm3->isi_elemen ?? '-');

            $kuk_key = ($item->iukForm3->kukForm3->no_kuk ?? '-') . ' : ' .
                    ($item->iukForm3->kukForm3->kuk_name ?? '-');

            $iuk_key = ($item->iukForm3->no_iuk ?? '-') . ' : ' .
                    ($item->iukForm3->iuk_name ?? '-');

            // Konversi indikator pencapaian menjadi <ul>
            $indikator_pencapaian = trim($item->indikator_pencapaian ?? '-');
            $indikator_list = "<ul>";

            foreach (explode("\n", $indikator_pencapaian) as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $line = ltrim($line, '- ');
                    $indikator_list .= "<li>{$line}</li>";
                }
            }

            $indikator_list .= "</ul>";

            $elemen_group[$elemen_key][$kuk_key][$iuk_key][] = [
                'no_soal' => $item->no_soal ?? '-',
                'pertanyaan' => $item->pertanyaan ?? '-',
                'indikator_pencapaian' => $indikator_list
            ];
        }

        // Bangun HTML
        $html = '<!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Form 3 B</title>
                <style>
                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                    }
                    .table-wrapper {
                        width: 100%;
                        overflow-x: auto;
                    }

                    th, td { border: 2px solid black; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; text-align: center; }
                    ul { margin: 0; padding-left: 20px; }
                    .elemen-header { background-color: #d9d9d9; font-weight: bold; text-align: left; padding: 10px; }
                    @media (max-width: 600px) {
                        th, td {
                            padding: 5px;
                            font-size: 12px;
                        }
                    }
                </style>
            </head>
            <body>
            <h2>Form 3 B</h2>';

        foreach ($elemen_group as $elemen => $kuk_list) {
            $html .= '<div class="table-wrapper"><table>
                <thead>
                    <tr>
                        <th colspan="5" class="elemen-header">Elemen: ' . $elemen . '</th>
                    </tr>
                    <tr>
                        <th>KUK</th>
                        <th>IUK</th>
                        <th>No. Soal</th>
                        <th>Pertanyaan</th>
                        <th>Indikator Pencapaian</th>
                    </tr>
                </thead>
                <tbody>';

            foreach ($kuk_list as $kuk => $iuk_list) {
                $rowspan_kuk = array_sum(array_map('count', $iuk_list));
                $html .= '<tr><td rowspan="' . $rowspan_kuk . '">' . $kuk . '</td>';

                foreach ($iuk_list as $iuk => $soal_list) {
                    $rowspan_iuk = count($soal_list);
                    $html .= '<td rowspan="' . $rowspan_iuk . '">' . $iuk . '</td>';

                    foreach ($soal_list as $index => $soal) {
                        if ($index > 0) {
                            $html .= '<tr>';
                        }
                        $html .= '<td>' . $soal['no_soal'] . '</td>
                                <td>' . $soal['pertanyaan'] . '</td>
                                <td>' . $soal['indikator_pencapaian'] . '</td>
                            </tr>';
                    }
                }
            }

            $html .= '</tbody></table></div><br>';
        }

        $html .= '</body></html>';

        // === RETURN HTML LANGSUNG ===
        return response($html, 200, [
            'Content-Type' => 'text/html'
        ]);
    }



    public function getAllDataFormC(Request $request)
    {
        $no_elemen = $request->query('no_elemen');
        $pk_id = $request->query('pk_id');

        // Validasi pk_id
        if (!$pk_id) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Parameter pk_id wajib diisi.',
                'data' => null
            ], 400);
        }

        // Ambil data
        $query = TypeForm3_C::with(['iukForm3.kukForm3.elemenForm3.kompetensiPk'])
            ->orderBy('id', 'asc');

        // Filter elemen
        if ($no_elemen) {
            $query->whereHas('iukForm3.kukForm3.elemenForm3', function ($q) use ($no_elemen) {
                $q->where('no_elemen_form_3', $no_elemen);
            });
        }

        // Filter pk_id
        $query->whereHas('iukForm3.kukForm3.elemenForm3.kompetensiPk', function ($q) use ($pk_id) {
            $q->where('pk_id', $pk_id);
        });

        $data = $query->get();

        // Grouping data
        $elemen_group = [];

        foreach ($data as $item) {
            $elemen_key = ($item->iukForm3->kukForm3->elemenForm3->no_elemen_form_3 ?? '-') . ' : ' .
                        ($item->iukForm3->kukForm3->elemenForm3->isi_elemen ?? '-');

            $kuk_key = ($item->iukForm3->kukForm3->no_kuk ?? '-') . ' : ' .
                    ($item->iukForm3->kukForm3->kuk_name ?? '-');

            $iuk_key = ($item->iukForm3->no_iuk ?? '-') . ' : ' .
                    ($item->iukForm3->iuk_name ?? '-');

            // Format standar jawaban
            $standar_jawaban_text = trim($item->standar_jawaban ?? '-');
            if (!empty($standar_jawaban_text) && $standar_jawaban_text !== '-') {
                $standar_jawaban = "";
                foreach (explode("\n", $standar_jawaban_text) as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        $line = ltrim($line, '- ');
                        $standar_jawaban .= "{$line}<br><br>";
                    }
                }
            } else {
                $standar_jawaban = "-";
            }

            // Format pertanyaan
            $pertanyaan_text = trim($item->pertanyaan ?? '-');
            if (!empty($pertanyaan_text) && $pertanyaan_text !== '-') {
                $pertanyaan = "";
                foreach (explode("\n", $pertanyaan_text) as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        $line = ltrim($line, '- ');
                        $pertanyaan .= "{$line}<br><br>";
                    }
                }
            } else {
                $pertanyaan = "-";
            }

            $elemen_group[$elemen_key][$kuk_key][$iuk_key][] = [
                'no_soal' => $item->no_soal ?? '-',
                'pertanyaan' => $pertanyaan,
                'standar_jawaban' => $standar_jawaban
            ];
        }

        // Generate HTML
        $html = '<!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Form 3 C</title>
            <style>
                table { width: 100%; max-width: 100%; border-collapse: collapse; overflow-x: auto; display: block; }
                th, td { border: 2px solid black; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; text-align: center; }
                ul { margin: 0; padding-left: 20px; }
                .elemen-header { background-color: #d9d9d9; font-weight: bold; text-align: left; padding: 10px; }
                @media (max-width: 600px) {
                    th, td { padding: 5px; font-size: 12px; }
                }
            </style>
        </head>
        <body>
        <h2>Form 3 C</h2>';

        foreach ($elemen_group as $elemen => $kuk_list) {
            $html .= '<table>
                <thead>
                    <tr>
                        <th colspan="5" class="elemen-header">Elemen: ' . $elemen . '</th>
                    </tr>
                    <tr>
                        <th>KUK</th>
                        <th>IUK</th>
                        <th>No. Soal</th>
                        <th>Pertanyaan</th>
                        <th>Standar Jawaban</th>
                    </tr>
                </thead>
                <tbody>';

            foreach ($kuk_list as $kuk => $iuk_list) {
                $rowspan_kuk = array_sum(array_map('count', $iuk_list));
                $html .= '<tr><td rowspan="' . $rowspan_kuk . '">' . $kuk . '</td>';

                foreach ($iuk_list as $iuk => $soal_list) {
                    $rowspan_iuk = count($soal_list);
                    $html .= '<td rowspan="' . $rowspan_iuk . '">' . $iuk . '</td>';

                    foreach ($soal_list as $index => $soal) {
                        if ($index > 0) {
                            $html .= '<tr>';
                        }
                        $html .= '<td>' . $soal['no_soal'] . '</td>
                                <td>' . $soal['pertanyaan'] . '</td>
                                <td>' . $soal['standar_jawaban'] . '</td>
                            </tr>';
                    }
                }
            }

            $html .= '</tbody></table><br>';
        }

        $html .= '</body></html>';

        // === RETURN HTML LANGSUNG (BUKAN JSON) ===
        return response($html, 200, [
            'Content-Type' => 'text/html'
        ]);
    }



    public function getAllDataFormA(Request $request)
    {
        // Ambil parameter
        $no_elemen_form_3 = $request->query('no_elemen_form_3');
        $pk_id = $request->query('pk_id');

        // Validasi: pk_id wajib diisi
        if (!$pk_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter pk_id wajib diisi.',
                'data' => null
            ], 400);
        }

        $query = TypeForm3_A::with([
            'iukForm3.kukForm3.elemenForm3.kompetensiPk',
            'poinForm3'
        ]);

        // Filter berdasarkan elemen
        if ($no_elemen_form_3) {
            $query->whereHas('iukForm3.kukForm3.elemenForm3', function ($q) use ($no_elemen_form_3) {
                $q->where('no_elemen_form_3', $no_elemen_form_3);
            });
        }

        // Filter berdasarkan pk_id
        $query->whereHas('iukForm3.kukForm3.elemenForm3', function ($q) use ($pk_id) {
            $q->where('pk_id', $pk_id);
        });

        $data = $query->get();

        // Mulai HTML
        $html = '<!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="icon" href="data:,">
            <title>Form 3 A</title>
            <style>
                table { width: 100%; max-width: 100%; border-collapse: collapse; overflow-x: auto; display: block; }
                th, td { border: 2px solid black; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; text-align: center; }
                ul { margin: 0; padding-left: 20px; }
                .elemen-header { background-color: #d9d9d9; font-weight: bold; text-align: left; padding: 10px; }
                .pk-header { background-color: #c9e3ff; font-weight: bold; padding: 10px; font-size: 18px; }
                @media (max-width: 600px) {
                    th, td { padding: 5px; font-size: 12px; }
                }
            </style>
        </head>
        <body>
        <h2>Form 3 A</h2>';

        // Kelompokkan berdasarkan PK → Elemen → KUK → IUK
        $grouped = [];

        foreach ($data as $item) {
            $elemen = optional($item->iukForm3->kukForm3->elemenForm3);
            $kompetensiPk = optional($elemen->kompetensiPk);

            $pkKey = "{$kompetensiPk->pk_id} : {$kompetensiPk->nama_level}";
            $elemenKey = "{$elemen->no_elemen_form_3} : {$elemen->isi_elemen}";
            $kukKey = "{$item->iukForm3->no_kuk} : " . optional($item->iukForm3->kukForm3)->kuk_name;
            $iukKey = "{$item->no_iuk} : {$item->iukForm3->iuk_name}";
            $poinList = $item->poinForm3->pluck('poin_diamati')->toArray();

            $grouped[$pkKey][$elemenKey][$kukKey][$iukKey] = $poinList;
        }

        // Buat HTML berdasarkan kelompok
        foreach ($grouped as $pk => $elemenList) {
            foreach ($elemenList as $elemen => $kukList) {
                $html .= '<table>
                    <thead>
                        <tr>
                            <th colspan="3" class="elemen-header">Elemen: ' . $elemen . '</th>
                        </tr>
                        <tr>
                            <th>KUK</th>
                            <th>IUK</th>
                            <th>Poin Diamati</th>
                        </tr>
                    </thead>
                    <tbody>';

                foreach ($kukList as $kuk => $iukList) {
                    foreach ($iukList as $iuk => $poinDiamati) {
                        $poinHtml = '<ul>';
                        foreach ($poinDiamati as $poin) {
                            $poinHtml .= "<li>{$poin}</li>";
                        }
                        $poinHtml .= '</ul>';

                        $html .= "<tr>
                            <td>{$kuk}</td>
                            <td>{$iuk}</td>
                            <td>{$poinHtml}</td>
                        </tr>";
                    }
                }

                $html .= '</tbody></table><br>';
            }
        }

        $html .= '</body></html>';

        // === RETURN HTML LANGSUNG (BUKAN JSON) ===
        return response($html, 200, [
            'Content-Type' => 'text/html'
        ]);
    }



    public function getAllDataFormD(Request $request)
    {
        $pk_id = $request->input('pk_id');

        if (!$pk_id) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Parameter pk_id wajib diisi.',
                'data' => null,
            ], 400);
        }

        $data = TypeForm3_D::with(['kukForm3', 'document'])
            ->where('pk_id', $pk_id)
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Data tidak ditemukan untuk pk_id: ' . $pk_id,
                'data' => null,
            ], 404);
        }

        $formattedData = [];

        foreach ($data as $item) {
            $formattedData[] = [
                'kuk_info' => $item->no_kuk . ' - ' . ($item->kukForm3->kuk_name ?? 'Tidak ada nama'),
                'nama_doc' => $item->document->nama_doc ?? 'Tidak ada dokumen'
            ];
        }

        // Urutkan berdasarkan no_kuk
        usort($formattedData, function ($a, $b) {
            return version_compare($a['kuk_info'], $b['kuk_info']);
        });

        // Buat tampilan HTML dalam bentuk tabel
        $html = '<!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Form 3 D</title>
            <style>
                h2 { text-align: left; font-family: Arial, sans-serif; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #000; padding: 10px; text-align: left; font-family: Arial, sans-serif; }
                th { background-color: #f2f2f2; }
                @media (max-width: 600px) {
                    th, td { padding: 5px; font-size: 12px; }
                }
            </style>
        </head>
        <body>
            <h2>Form 3 D</h2>
            <table>
                <thead>
                    <tr>
                        <th>KUK</th>
                        <th>Dokumen</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($formattedData as $row) {
            $html .= '<tr>
                        <td>' . htmlspecialchars($row['kuk_info']) . '</td>
                        <td>' . htmlspecialchars($row['nama_doc']) . '</td>
                    </tr>';
        }

        $html .= '</tbody></table></body></html>';

        // === RETURN HTML LANGSUNG (BUKAN JSON) ===
        return response($html, 200, [
            'Content-Type' => 'text/html'
        ]);
    }



    public function Form3Input(Request $request, $user_id)
	{
		// Validasi apakah user_id ada di form_2
		$form2 = PenilaianForm2Model::where('user_jawab_form_2_id', $user_id)->first();

		if (!$form2) {
			return response()->json([
				'status' => 404,
				'message' => 'No data found for the given user_id.'
			], 404);
		}

		// Ambil user login
		$loggedUser = auth()->user();

		// Periksa apakah user login memiliki role asesor (role_id = 2)
		$isAsesor = $loggedUser->roles()->where('role_id', 2)->exists();

		if (!$isAsesor) {
			return response()->json([
				'status' => 403,
				'message' => 'Unauthorized. Only assessors can input Form3.'
			], 403);
		}

		// Buat data baru di Form3
		$form3 = Form3Model::create([
			'user_id'      => $form2->user_jawab_form_2_id,
			'no_reg'       => $form2->no_reg,
			'asesi_name'   => $form2->asesi_name,
			'asesor_name'  => $form2->asesor_name,
			'asesi_date'   => Carbon::now(),
			'asesor_date'  => Carbon::now(),
			'status'       => 'Approved',
            'pk_id'        => $form2->pk_id,
		]);

		// Update pk_progress (form_3_id)
		$progress = PkProgressModel::where('user_id', $user_id)->first();
		if ($progress) {
			$progress->form_3_id = $form3->form_3_id;
			$progress->save();

			// Update pk_status (form_3_status)
			DB::table('pk_status')
				->where('progress_id', $progress->progress_id)
				->update(['form_3_status' => 'Completed']);
		}

		return response()->json([
			'status' => 201,
			'message' => 'Form3 created successfully with status Approved.',
			'data' => $form3
		], 201);
	}
    

    private function kirimNotifikasiKeAsesor(DaftarUser $userAsesor, $formId)
    {
        if (empty($userAsesor->device_token)) {
            Log::warning("Asesor user_id={$userAsesor->user_id} tidak memiliki device_token.");
            return;
        }

        try {
            DB::transaction(function () use ($userAsesor, $formId) {
                $title = 'Rencana Asesmen';
                $message = "Asesi Menyetujui Rencana Asesmen Form 3.";

                // Log sebelum pengiriman notifikasi
                Log::info("Mengirim notifikasi ke OneSignal untuk user_id={$userAsesor->user_id}, form_id={$formId}");

                // Kirim notifikasi ke OneSignal
                $this->oneSignalService->sendNotification(
                    [$userAsesor->device_token],
                    $title,
                    $message
                );

                Log::info("Notifikasi berhasil dikirim ke OneSignal untuk user_id={$userAsesor->user_id}");

                // Simpan notifikasi ke database
                Notification::create([
                    'user_id' => $userAsesor->user_id,
                    'title' => $title,
                    'description' => $message,
                    'is_read' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                Log::info("Notifikasi berhasil disimpan di database untuk user_id={$userAsesor->user_id}, form_id={$formId}");
            });

        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi ke asesor.", [
                'user_id' => $userAsesor->user_id,
                'form_id' => $formId,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);
        }
    }

	public function ApproveAsesiForm3(Request $request)
    {
        // $user = auth()->user(); // Ambil user login
        $form3Id = $request->input('form_3_id'); // Ambil form_3_id dari body
        $form3Data = Form3Model::find($form3Id);
        Log::info("Form 3 Data for form_3_id={$form3Id}: " . ($form3Data ? json_encode($form3Data) : 'wa'));
        $form1Id = $this->formService->getParentFormIdByFormIdAndAsesiId($form3Id, $form3Data->user_id, 'form_3'); // Ambil form_1_id terkait form_3_id
        Log::info("Form 1 ID terkait Form 3 ID {$form3Id}: " . ($form1Id ?? 'null'));
        $user = DaftarUser::where('user_id', $form3Data->user_id)->first();
        Log::info("Logged in user: " . ($user ? json_encode($user) : 'null'));
        // ✅ Cek apakah user login ada
        if (!$user) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'User belum login.',
                'data' => null
            ], 401);
        }

        // ✅ Cek apakah user adalah Asesi
        $isAsesi = UserRole::where('user_id', $user->user_id)
            ->where('role_id', 1)
            ->exists();

        if (!$isAsesi) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Anda tidak memiliki izin untuk mengisi Form3.',
                'data' => null
            ], 403);
        }

        // ✅ Cek apakah user memiliki form_1_id di BidangModel
        $bidang = BidangModel::where('form_1_id', $form1Id)->first();
        Log::info("Bidang data for user_id={$user->user_id}: " . ($bidang ? json_encode($bidang) : 'null'));

        if (!$bidang || !$bidang->form_1_id) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Form 1 belum tersedia untuk user ini.',
                'data' => null
            ], 404);
        }

        // ✅ Ambil Form3 berdasarkan form_3_id dari request, atau buat baru
        if ($form3Id) {
            $form3 = Form3Model::find($form3Id);

            if (!$form3) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Form 3 dengan ID tersebut tidak ditemukan.',
                    'data' => null
                ], 404);
            }

            // Update data form3 yang sudah ada
            $form3->asesi_name = $user->nama;
            $form3->asesi_date = Carbon::now();
            $form3->status = 'Waiting';
            $form3->save();
            Log::info('ini if');

        } else {
            // Buat Form3 baru jika tidak ada form_3_id di request
            $form3 = new Form3Model();
            $form3->user_id = $user->user_id;
            $form3->asesi_name = $user->nama;
            $form3->asesi_date = Carbon::now();
            $form3->status = 'Waiting';
            $form3->save();
            Log::info('ini else');
        }

        // ✅ Cek apakah sudah ada progres untuk form_3 ini
        $existingProgres = KompetensiProgres::where('form_id', $form3->form_3_id)
            ->where('user_id', $user->user_id)
            ->whereHas('track', function ($query) {
                $query->where('form_type', 'form_3');
            })
            ->first();

        try {
            if ($existingProgres) {
                // Jika progres sudah ada → update progres dan tambah track baru
                $progres = $this->formService->updateProgresDanTrack(
                    $form3->form_3_id,
                    'form_3',
                    'Submitted',
                    $user->user_id,
                    'Form 3 telah disetujui oleh asesi.'
                );
            } else {
                // Jika belum ada → buat progres baru dan track-nya
                $progres = $this->createProgresDanTrack(
                    $form3->form_3_id,
                    'form_3',
                    'Submitted',
                    $user->user_id,
                    $bidang->form_1_id ?? null,
                    'Form 3 dibuat dan disubmit oleh asesi.'
                );
            }

            Log::info("Progres untuk Form 3 ID {$form3->form_3_id} berhasil diproses: " . json_encode($progres));
        } catch (\Exception $e) {
            Log::error("Gagal membuat atau memperbarui progres form_3: " . $e->getMessage());

            return response()->json([
                'status' => 'ERROR',
                'message' => 'Terjadi kesalahan saat memperbarui progres form_3.',
                'error' => $e->getMessage()
            ], 500);
        }

        // ✅ Kirim notifikasi ke Asesor jika ada
        if (!empty($bidang->asesor_id)) {
            $userAsesor = DaftarUser::where('user_id', $bidang->asesor_id)->first();
            if ($userAsesor) {
                $this->kirimNotifikasiKeAsesor($userAsesor, $form3->form_3_id);
            }
        }

        return response()->json([
            'status' => 'OK',
            'message' => 'Form 3 berhasil disimpan atau diperbarui.',
            'data' => $form3
        ], 201);
    }



    private function kirimNotifikasiKeUser(DaftarUser $userTarget, string $title, string $message, $formId)
    {
        if (empty($userTarget->device_token)) {
            Log::warning("User user_id={$userTarget->user_id} tidak memiliki device_token.");
            return;
        }

        try {
            DB::transaction(function () use ($userTarget, $title, $message, $formId) {
                Log::info("Mengirim notifikasi ke OneSignal untuk user_id={$userTarget->user_id}, form_id={$formId}");

                $this->oneSignalService->sendNotification(
                    [$userTarget->device_token],
                    $title,
                    $message
                );

                Log::info("Notifikasi berhasil dikirim ke OneSignal untuk user_id={$userTarget->user_id}");

                Notification::create([
                    'user_id' => $userTarget->user_id,
                    'title' => $title,
                    'description' => $message,
                    'is_read' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                Log::info("Notifikasi berhasil disimpan di database untuk user_id={$userTarget->user_id}, form_id={$formId}");
            });
        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi ke user.", [
                'user_id' => $userTarget->user_id,
                'form_id' => $formId,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);
        }
    }

	public function UpdateAsesorForm3($form3_id)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'User belum login.',
                'data' => null
            ], 401);
        }

        // Pastikan user adalah asesor
        $hasRoleAsesor = UserRole::where('user_id', $user->user_id)
            ->where('role_id', 2)
            ->exists();

        if (!$hasRoleAsesor) {
            return response()->json([
                'status' => 403,
                'message' => 'Anda tidak memiliki izin untuk mengisi bagian asesor.',
                'data' => null
            ], 403);
        }

        try {
            // Jalankan seluruh proses dalam 1 transaksi atomik
            DB::beginTransaction();

            /** ===============================
             * 1️⃣ Validasi dan Update Form 3
             * =============================== */
            $form3 = Form3Model::find($form3_id);
            if (!$form3) {
                throw new \Exception('Data Form3 tidak ditemukan.');
            }

            $asesor = DataAsesorModel::where('user_id', $user->user_id)
                ->where('aktif', 1)
                ->first();

            if (!$asesor) {
                throw new \Exception('Data no_reg asesor tidak ditemukan.');
            }

            $form3->asesor_name = $user->nama;
            $form3->asesor_date = Carbon::now();
            $form3->no_reg = $asesor->no_reg;
            $form3->save();

            /** =======================================
             * 2️⃣ Update Progres dan buat Track
             * ======================================= */
            $progres = KompetensiProgres::where('form_id', $form3->form_3_id)->first();
            if ($progres) {
                $progres->status = 'Completed';
                $progres->save();

                KompetensiTrack::create([
                    'progres_id' => $progres->id,
                    'form_type' => 'form_3',
                    'form_id' => $form3->form_3_id,
                    'activity' => 'Completed',
                    'description' => 'Rencana asesmen telah selesai',
                    'updated_by' => $user->user_id,
                    'updated_at' => Carbon::now()
                ]);
            }

            /** =======================================
             * 3️⃣ Ambil data form 1 terkait dan validasi
             * ======================================= */
            $form_1_id = $this->formService->getParentFormIdByFormIdAndAsesiId(
                $form3->form_3_id,
                $form3->user_id,
                'form_3'
            );

            $form1Data = BidangModel::where('form_1_id', $form_1_id)->first();
            if (!$form1Data) {
                throw new \Exception('Data form 1 tidak ditemukan.');
            }

            /** =======================================
             * 4️⃣ Cek apakah interview sudah ada
             * ======================================= */
            $existingInterview = InterviewModel::where('user_id', $form1Data->asesi_id)
                ->where('pk_id', $form1Data->pk_id)
                ->where('form_1_id', $form_1_id)
                ->first();

            if ($existingInterview) {
                Log::warning("Interview sudah ada untuk asesi_id={$form1Data->asesi_id}, pk_id={$form1Data->pk_id}, form_1_id={$form_1_id}");
                throw new \Exception('Interview untuk kombinasi Asesi, PK, dan Form 1 ini sudah ada.');
            }

            /** =======================================
             * 5️⃣ Buat Interview baru
             * ======================================= */
            $interview = new InterviewModel();
            $interview->asesi_name = $form1Data->asesi_name;
            $interview->user_id = $form1Data->asesi_id;
            $interview->date = null;
            $interview->time = null;
            $interview->place = null;
            $interview->form_1_id = $form_1_id;
            $interview->asesor_id = $form1Data->asesor_id;
            $interview->asesor_name = $form1Data->asesor_name;
            $interview->status = 'InAssessment';
            $interview->pk_id = $form1Data->pk_id;
            $interview->save();

            Log::info("✅ Interview baru dibuat: " . json_encode($interview->toArray()));

            /** =======================================
             * 6️⃣ Buat progres & track interview
             * ======================================= */
            $this->formService->createProgresDanTrack(
                $interview->interview_id,
                'intv_pra_asesmen',
                'InAssessment',
                $form1Data->asesi_id,
                $form1Data->form_1_id,
                'Konsultasi Pra asesmen sudah dapat diajukan.'
            );

            /** =======================================
             * 7️⃣ Commit transaksi (baru kirim notifikasi setelah ini)
             * ======================================= */
            DB::commit();

            /** =======================================
             * 8️⃣ Kirim notifikasi ke Asesi
             * ======================================= */
            $userAsesi = DaftarUser::where('user_id', $form3->user_id)->first();
            if ($userAsesi) {
                $title = 'Form 3 Disetujui';
                $message = 'Form 3 Anda telah disetujui oleh asesor. Anda bisa mengajukan jadwal wawancara asesmen.';
                $this->kirimNotifikasiKeUser($userAsesi, $title, $message, $form3->form_3_id);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Form3 berhasil diperbarui dan semua proses telah disimpan dengan aman.',
                'data' => [
                    'form3' => $form3,
                    'interview' => $interview
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("❌ Gagal memperbarui Form3: {$e->getMessage()}");
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }




}
