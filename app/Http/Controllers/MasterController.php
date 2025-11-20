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
            $html = "<p>Tidak ada data elemen asesmen.</p>";

            return response($html, 200)
                ->header('Content-Type', 'text/html');
        }

        $html = '
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Elemen Asesmen</title>
        </head>
        <body>
            <table border="1" cellpadding="6" cellspacing="0" 
                style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 13px;">
                <thead style="background-color: #f0f0f0;">
                    <tr>
                        <th style="width: 5%; text-align: center;">No</th>
                        <th style="width: 95%;">Isi Elemen</th>
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
        </body>
        </html>';

        return response($html, 200)
                ->header('Content-Type', 'text/html');
    }

}
