<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Education;
use App\Models\ElemenForm3;
use App\Models\KompetensiPk;

class MasterController extends Controller
{
    public function getEducations()
    {
        try {
            $educations = Education::all();

            if ($educations->isEmpty()) {
                return response()->json([
                    'status' => 'ERR',
                    'message' => 'Data pendidikan tidak ditemukan.',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Data pendidikan berhasil diambil.',
                'data' => $educations
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERR',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function getKompetensiPk()
    {
        try {
            $kompetensi = KompetensiPk::where('is_active', true)
                ->orderBy('pk_id')
                ->get(['pk_id', 'nama_level']);

            if ($kompetensi->isEmpty()) {
                return response()->json([
                    'status' => 'ERR',
                    'message' => 'Data kompetensi PK tidak ditemukan.',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Data kompetensi PK berhasil diambil.',
                'data' => $kompetensi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERR',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function getElemenAsesmen($pk_id)
    {
        $data = ElemenForm3::where('pk_id', $pk_id)
            ->orderByRaw('CAST(no_elemen_form_3 AS UNSIGNED) ASC')
            ->get();

        if ($data->isEmpty()) {
            $html = "<p style='font-family: Arial; font-size: 14px;'>Tidak ada data elemen asesmen.</p>";

            return response($html, 200)
                ->header('Content-Type', 'text/html');
        }

        // HTML Responsif
        $html = '
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Elemen Asesmen</title>

            <!-- Wajib agar responsif di mobile -->
            <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 14px;
                    margin: 10px;
                }

                /* Agar tabel bisa discroll pada layar kecil */
                .table-responsive {
                    width: 100%;
                    overflow-x: auto;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    min-width: 400px; /* membuat tabel tidak terlalu kecil */
                }

                th, td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    vertical-align: top;
                    word-wrap: break-word;
                    white-space: normal;
                }

                th {
                    background: #f0f0f0;
                    text-align: center;
                }

                tr:nth-child(even) {
                    background: #fafafa;
                }

                /* Kolom No lebih kecil */
                th:first-child, td:first-child {
                    width: 40px;
                }

                /* Penyesuaian untuk layar sangat kecil (HP ≤ 400px) */
                @media (max-width: 400px) {
                    table {
                        min-width: 100% !important; /* agar tidak terpotong */
                    }
                    th:first-child, td:first-child {
                        width: 30px !important;
                    }
                }

                /* Penyesuaian untuk layar umum HP */
                @media (max-width: 480px) {
                    body {
                        font-size: 12px;
                    }
                    th, td {
                        padding: 6px;
                    }
                }
            </style>
        </head>

        <body>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Isi Elemen</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($data as $row) {
            $html .= '
                        <tr>
                            <td style="text-align: center;">' . htmlspecialchars($row->no_elemen_form_3) . '</td>
                            <td>' . nl2br(htmlspecialchars($row->isi_elemen)) . '</td>
                        </tr>';
        }

        $html .= '
                    </tbody>
                </table>
            </div>
        </body>
        </html>';

        return response($html, 200)
            ->header('Content-Type', 'text/html');
    }

}
