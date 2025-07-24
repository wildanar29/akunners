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


        return response()->json([
            'status' => 'OK',
            'data' => $data,
        ]);
    }
}
