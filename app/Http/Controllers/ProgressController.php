<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\KompetensiProgres;
use App\Models\KompetensiTrack;
use App\Models\BidangModel;
use App\Models\KompetensiPk;
use App\Models\DaftarTilik;
use App\Models\DaftarUser;

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
                    'status'  => 'ERROR',
                    'message' => 'Parameter asesi_id dan pk_id wajib diisi.',
                    'data'    => null,
                ], 400);
            }

            /** ===============================
             * 🔹 MAP JUDUL STATIS
             * =============================== */
            $formTitleMap = [
                'form_1' => 'PENGAJUAN ASESMEN',
                'form_2' => 'ASESMEN MANDIRI',
                'form_3' => 'RENCANA ASESMEN',
                'intv_pra_asesmen' => 'JANJI TEMU KONSULTASI',
                'form_5' => 'KONSULTASI PRA ASESMEN',
                'form_4a' => 'UJI OBSERVASI',
                'form_4b' => 'UJI LISAN',
                'form_4c' => 'UJI TULIS',
                'form_4d' => 'PORTOFOLIO',
                'form_7' => 'BUKTI ASESMEN',
                'form_8' => 'UJI BANDING',
                'form_9' => 'UMPAN BALIK',
                'form_12' => 'REKAPITULASI ASESMEN',
                'form_6' => 'DAFTAR CEK PELAKSANAAN ASESMEN',
            ];

            /** ===============================
             * 🔹 DAFTAR TILIK (form_10.xxx)
             * 🔹 FIX: keyBy form_number (BUKAN form_type)
             * =============================== */
            $daftarTilikMap = DaftarTilik::where('pk_id', $pk_id)
                ->get()
                ->keyBy(fn ($item) => trim($item->form_number));

            Log::info('Daftar Tilik Map', ['map' => $daftarTilikMap->toArray()]);

            /** ===============================================================
             * 🔹 Ambil NAMA PK
             * =============================================================== */
            $pk = KompetensiPk::find($pk_id);
            $namaPk = $pk ? $pk->nama_level : null;

            /** ===============================================================
             * 🔹 Ambil data form + relasi user asesi
             * =============================================================== */
            $item = BidangModel::with(['asesiUser:user_id,email,no_telp,foto'])
                ->where('pk_id', $pk_id)
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
                    'status'  => 'SUCCESS',
                    'message' => 'Data tidak tersedia.',
                    'data'    => null,
                ], 200);
            }

            /** ===============================================================
             * 🔹 Data Kontak ASESI
             * =============================================================== */
            $asesiEmail  = $item->asesiUser->email ?? null;
            $asesiNoTelp = $item->asesiUser->no_telp ?? null;
            $asesiFoto   = $item->asesiUser && $item->asesiUser->foto
                ? url('storage/' . $item->asesiUser->foto)
                : null;

            $endDate = null;
            $endDateStatus = null;

            if (!empty($item->asesi_date)) {
                $endDate = Carbon::parse($item->asesi_date)
                    ->addDays(30)
                    ->format('Y-m-d');

                $endDateStatus = Carbon::now()->lessThanOrEqualTo(
                    Carbon::parse($endDate)
                );
            }

            /** ===============================================================
             * 🔹 Data Kontak ASESOR
             * =============================================================== */
            $asesorUser = DaftarUser::where('user_id', $item->asesor_id)
                ->select('user_id', 'email', 'no_telp', 'foto')
                ->first();

            $asesorEmail  = $asesorUser->email ?? null;
            $asesorNoTelp = $asesorUser->no_telp ?? null;
            $asesorFoto   = $asesorUser && $asesorUser->foto
                ? url('storage/' . $asesorUser->foto)
                : null;

            /** ===============================================================
             * 🔹 Ambil progres utama
             * =============================================================== */
            $progresUtama = KompetensiProgres::where('form_id', $item->form_1_id)
                ->select('id', 'form_id', 'status', 'user_id')
                ->first();

            if ($progresUtama) {
                $item->status_utama = $progresUtama->status;
                $item->pk_id = $pk_id;

                $item->tracks_utama = DB::table('kompetensi_tracks')
                    ->where('progres_id', $progresUtama->id)
                    ->where('form_type', 'form_1')
                    ->orderBy('activity_time', 'asc')
                    ->get();
            } else {
                $item->status_utama = null;
                $item->tracks_utama = [];
                $item->pk_id = $pk_id;
            }

            /** ===============================================================
             * 🔹 Ambil progres anak + TITLE (FIXED)
             * =============================================================== */
            $progres = KompetensiProgres::where('parent_form_id', $item->form_1_id)
                ->select('id', 'form_id', 'status', 'user_id', 'parent_form_id')
                ->get()
                ->map(function ($prog) use ($pk_id, $formTitleMap, $daftarTilikMap) {

                    $prog->pk_id = $pk_id;

                    $prog->form_type = KompetensiTrack::where('progres_id', $prog->id)
                        ->value('form_type');

                    Log::info('Progres ID ' . $prog->id . ' has form_type: ' . $prog->form_type);

                    /** 🔹 TITLE */
                    if ($prog->form_type && str_starts_with($prog->form_type, 'form_10.')) {
                        $prog->title = $daftarTilikMap[$prog->form_type]->description ?? null;
                    } else {
                        $prog->title = $formTitleMap[$prog->form_type] ?? null;
                    }

                    $prog->tracks = DB::table('kompetensi_tracks')
                        ->where('progres_id', $prog->id)
                        ->orderBy('activity_time', 'asc')
                        ->get();

                    return $prog;
                });

            /** =====================
             * 🔥 URUTAN CUSTOM
             * ===================== */
            $order = [
                'form_1' => 1,
                'form_2' => 2,
                'form_3' => 3,
                'intv_pra_asesmen' => 4,
                'form_5' => 5,

                // form 4
                'form_4a' => 9,
                'form_4b' => 7,
                'form_4c' => 6,
                'form_4d' => 8,

                // form 10
                'form_10.001' => 10,
                'form_10.002' => 11,
                'form_10.003' => 12,
                'form_10.004' => 13,
                'form_10.005' => 14,
                'form_10.006' => 15,
                'form_10.007' => 16,
                'form_10.008' => 17,
                'form_10.009' => 18,
                'form_10.010' => 19,
                'form_10.011' => 20,
                'form_10.012' => 21,

                // form lainnya
                'form_7' => 22,
                'form_8' => 23,
                'form_9' => 24,
                'form_12' => 25,
                'form_6' => 26,
            ];

            $progres = $progres->sortBy(fn ($p) => $order[$p->form_type] ?? 99999)->values();

            /** ===============================================================
             * 🔹 Response Final (TIDAK DIKURANGI)
             * =============================================================== */
            return response()->json([
                'status'  => 'SUCCESS',
                'message' => 'Data progres berhasil diambil.',
                'data'    => [
                    'form_1_id' => $item->form_1_id,
                    'asesi_name' => $item->asesi_name,
                    'asesi_id' => $item->asesi_id,
                    'asesi_date' => $item->asesi_date,
                    'end_date' => $endDate,
                    'end_date_status' => $endDateStatus,
                    'asesi_email' => $asesiEmail,
                    'asesi_no_telp' => $asesiNoTelp,
                    'asesi_foto' => $asesiFoto,
                    'asesor_name' => $item->asesor_name,
                    'asesor_id' => $item->asesor_id,
                    'asesor_email' => $asesorEmail,
                    'asesor_no_telp' => $asesorNoTelp,
                    'asesor_foto' => $asesorFoto,
                    'pk_id' => $pk_id,
                    'nama_pk' => $namaPk,
                    'status_utama' => $item->status_utama,
                    'tracks_utama' => $item->tracks_utama,
                    'progres' => $progres,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'ERROR',
                'message' => 'Gagal mengambil data progres: ' . $e->getMessage(),
                'data'    => null,
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
