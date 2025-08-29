<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KompetensiProgres;
use App\Models\KompetensiTrack;
use App\Models\BidangModel;

class ProgressController extends Controller
{
    // status = Submitted, Approved, Rejected, Assigned
    // status Submitted untuk menampilkan list pengajuan form 1 di bidang
    // status Assigned untuk menampilkan list form 1 yang sudah di assign ke asesor dan muncul juga di bidang
    // status Approved untuk menampilkan list form 1 yang sudah di approve oleh asesor dan muncul juga di bidang
    public function getForm1(Request $request)
    {
        try {
            // Ambil parameter filter dari request
            $pk_id     = $request->input('pk_id');
            $asesor_id = $request->input('asesor_id');
            $status    = $request->input('status');
            $asesi_id  = $request->input('asesi_id');

            // Query builder untuk KompetensiProgres JOIN ke form_1
            $query = DB::table('kompetensi_progres')
                ->join('form_1', 'kompetensi_progres.form_id', '=', 'form_1.form_1_id')
                ->select(
                    'kompetensi_progres.id as progres_id',
                    'kompetensi_progres.status',
                    'kompetensi_progres.form_id',
                    'form_1.pk_id',
                    'form_1.asesor_id',
                    'form_1.asesor_name',
                    'form_1.asesi_id',
                    'form_1.asesi_name',
                    'form_1.no_reg',
                    'form_1.ket',
                    'form_1.ijazah_id',
                    'form_1.spk_id',
                    'form_1.sip_id',
                    'form_1.str_id',
                    'form_1.ujikom_id',
                    'form_1.sertifikat_id',
                    'form_1.created_at'
                );

            // Filter dinamis
            if (!is_null($pk_id)) {
                $query->where('form_1.pk_id', $pk_id);
            }

            if (!is_null($asesor_id)) {
                $query->where('form_1.asesor_id', $asesor_id);
            }

            if (!is_null($status)) {
                $query->where('kompetensi_progres.status', $status);
            }

            if (!is_null($asesi_id)) {
                $query->where('form_1.asesi_id', $asesi_id);
            }

            // Ambil data
            $data = $query->get();

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Data berhasil diambil',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function getProgresByAsesi(Request $request)
    {
        try {
            $asesi_id = $request->query('asesi_id'); 
            $pk_id    = $request->query('pk_id'); 

            if (!$asesi_id || !$pk_id) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Parameter asesi_id dan pk_id wajib diisi.',
                    'data' => null,
                ], 400);
            }

            // Ambil satu data bidang
            $item = BidangModel::where('pk_id', $pk_id)
                ->where('asesi_id', $asesi_id)
                ->select([
                    'form_1_id',
                    'asesi_name',
                    'asesi_id',
                    'asesi_date',
                    'asesor_id',
                    'asesor_name',
                    'asesor_date',
                ])
                ->first();

            if (!$item) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Data tidak ditemukan.',
                    'data' => null,
                ], 404);
            }

            // Ambil status utama
            $statusUtama = \App\Models\KompetensiProgres::where('form_id', $item->form_1_id)
                ->value('status');

            $item->status_utama = $statusUtama;

            // Ambil progres anak
            $progres = \App\Models\KompetensiProgres::where('parent_form_id', $item->form_1_id)
                ->select('id', 'form_id', 'status')
                ->get()
                ->map(function ($prog) {
                    $form_type = \App\Models\KompetensiTrack::where('progres_id', $prog->id)
                        ->value('form_type');

                    $prog->form_type = $form_type;
                    return $prog;
                });

            $item->progres = $progres;

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Data progres berhasil diambil.',
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Gagal mengambil data progres: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }



    public function getTracksByFormId(Request $request)
    {
        $form_id        = $request->input('form_id');
        $user_id        = $request->input('user_id');
        $parent_form_id = $request->input('parent_form_id');

        // validasi parameter wajib (parent_form_id boleh null/kosong)
        if (!$form_id || !$user_id) {
            return response()->json([
                'status'  => 'ERROR',
                'message' => 'Parameter form_id dan user_id wajib diisi.',
                'data'    => [],
            ], 400);
        }

        try {
            // Query progres berdasarkan form_id dan user_id
            $progresQuery = DB::table('kompetensi_progres')
                ->where('form_id', $form_id)
                ->where('user_id', $user_id);

            // kalau parent_form_id kosong/null/spasi → cari yang NULL
            if (is_null($parent_form_id) || trim($parent_form_id) === '') {
                $progresQuery->whereNull('parent_form_id');
            } else {
                $progresQuery->where('parent_form_id', $parent_form_id);
            }

            $progres = $progresQuery->first();

            if (!$progres) {
                return response()->json([
                    'status'  => 'ERROR',
                    'message' => 'Data progres tidak ditemukan untuk kombinasi parameter tersebut.',
                    'data'    => [],
                ], 404);
            }

            // Ambil tracks berdasarkan progres_id
            $tracks = DB::table('kompetensi_tracks')
                ->where('progres_id', $progres->id)
                ->orderBy('activity_time', 'asc')
                ->get();

            return response()->json([
                'status'  => 'OK',
                'message' => 'Data tracks berhasil diambil.',
                'data'    => $tracks,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'ERROR',
                'message' => 'Terjadi kesalahan saat mengambil data tracks.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


}
