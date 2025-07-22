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
                    'form_1.status as form_status',
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
                $query->where('form_1.status', $status);
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

    public function getProgresByAsesi(Request $request, $asesi_id)
    {
        try {
            $pk_id = $request->input('pk_id');

            if (!$pk_id) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Parameter pk_id wajib diisi.',
                    'data' => [],
                ], 400);
            }

            $data = BidangModel::where('pk_id', $pk_id)
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
                ->get()
                ->map(function ($item) {
                    // Ambil status utama dari KompetensiProgres berdasarkan form_id = form_1_id
                    $statusUtama = \App\Models\KompetensiProgres::where('form_id', $item->form_1_id)
                        ->value('status');

                    $item->status_utama = $statusUtama;

                    // Ambil progres anak berdasarkan form_parent_id = form_1_id
                    $progres = \App\Models\KompetensiProgres::where('parent_form_id', $item->form_1_id)
                        ->select('id', 'form_id', 'status')
                        ->get()
                        ->map(function ($prog) {
                            // Ambil form_type dari KompetensiTrack
                            $form_type = \App\Models\KompetensiTrack::where('progres_id', $prog->id)
                                ->value('form_type');

                            $prog->form_type = $form_type;
                            return $prog;
                        });

                    $item->progres = $progres;
                    return $item;
                });

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Data progres berhasil diambil.',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Gagal mengambil data progres: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }



}
