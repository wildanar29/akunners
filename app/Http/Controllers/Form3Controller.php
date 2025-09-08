<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\TypeForm3_B;
use App\Models\TypeForm3_C;
use App\Models\TypeForm3_A;
use App\Models\TypeForm3_D;
use App\Models\PenilaianForm2Model;
use App\Models\Form3Model;
use App\Models\UserRole;
use App\Models\DaftarUser;
use App\Models\BidangModel;
use App\Models\KompetensiProgres;
use App\Models\KompetensiTrack;
use App\Models\DataAsesorModel;
use App\Models\PkProgressModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;  
use Carbon\Carbon; // Tambahkan ini untuk menggunakan Carbon
use App\Service\OneSignalService;
use App\Models\Notification;

class Form3Controller extends BaseController
{

    protected $oneSignalService;

	public function __construct(OneSignalService $oneSignalService)
	{
		$this->oneSignalService = $oneSignalService;
	}

    public function getAllDataFormB(Request $request)
    {
        $no_elemen = $request->query('no_elemen');
        $pk_id = $request->query('pk_id');

        // Validasi: pk_id wajib
        if (!$pk_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter pk_id wajib diisi.',
                'data' => null
            ], 400);
        }

        // Ambil data dengan relasi
        $query = TypeForm3_B::with(['iukForm3.kukForm3.elemenForm3.kompetensiPk'])->orderBy('id', 'asc');

        // Filter berdasarkan elemen jika diberikan
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

        // Mengelompokkan data berdasarkan Elemen, KUK, dan IUK
        $elemen_group = [];
        foreach ($data as $item) {
            $elemen_key = ($item->iukForm3->kukForm3->elemenForm3->no_elemen_form_3 ?? '-') . ' : ' . 
                        ($item->iukForm3->kukForm3->elemenForm3->isi_elemen ?? '-');

            $kuk_key = ($item->iukForm3->kukForm3->no_kuk ?? '-') . ' : ' . 
                    ($item->iukForm3->kukForm3->kuk_name ?? '-');

            $iuk_key = ($item->iukForm3->no_iuk ?? '-') . ' : ' . 
                    ($item->iukForm3->iuk_name ?? '-');

            // Konversi indikator_pencapaian ke format bullet list
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

        // Buat tampilan HTML
        $html = '<!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Form 3 B</title>
            <style>
                table { width: 100%; max-width: 100%; border-collapse: collapse; overflow-x: auto; display: block;}
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

            $html .= '</tbody></table><br>';
        }

        $html .= '</body></html>';

        // Return JSON
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil menampilkan Form 3B',
            'data' => $html
        ]);
    }


    public function getAllDataFormC(Request $request)
    {
        $no_elemen = $request->query('no_elemen');
        $pk_id = $request->query('pk_id');

        // Validasi: pk_id wajib
        if (!$pk_id) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Parameter pk_id wajib diisi.',
                'data' => null
            ], 400);
        }

        // Ambil data dengan relasi
        $query = TypeForm3_C::with(['iukForm3.kukForm3.elemenForm3.kompetensiPk'])->orderBy('id', 'asc');

        // Filter berdasarkan elemen jika diberikan
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

        // Mengelompokkan data berdasarkan Elemen, KUK, dan IUK
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

        // Buat tampilan HTML
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
                    th, td {
                        padding: 5px;
                        font-size: 12px;
                    }
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

        // Kembalikan JSON response
        return response()->json([
            'status' => 'OK',
            'message' => 'Data berhasil diambil.',
            'data' => $html
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

        // Return JSON dengan HTML di dalam data
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil menampilkan Form 3A',
            'data' => $html
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
                h2 { text-align: left; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #000; padding: 10px; text-align: left; }
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

        // Return JSON
        return response()->json([
            'status' => 'OK',
            'message' => 'Data berhasil diambil.',
            'data' => $html
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

	public function ApproveAsesiForm3()
    {
        $user = auth()->user(); // Ambil user login

        if (!$user) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'User belum login.',
                'data' => null
            ], 401);
        }

        // Cek apakah user adalah Asesi
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

        // Cek apakah user memiliki form_1_id di BidangModel
        $bidang = BidangModel::where('asesi_id', $user->user_id)->first();

        if (!$bidang || !$bidang->form_1_id) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Form 1 belum tersedia untuk user ini.',
                'data' => null
            ], 404);
        }

        // Ambil data progres dari KompetensiProgres berdasarkan form_1_id
        $progres = KompetensiProgres::where('form_id', $bidang->form_1_id)->first();

        if (!$progres) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Progres tidak ditemukan untuk Form 1 ini.',
                'data' => null
            ], 404);
        }

        // Cek apakah sudah ada form_type = form_3 untuk progres ini
        $existingForm3Track = KompetensiTrack::where('progres_id', $progres->progres_id)
            ->where('form_type', 'form_3')
            ->exists();

        if ($existingForm3Track) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Form 3 sudah pernah dibuat untuk progres ini.',
                'data' => null
            ], 409);
        }

        // Update atau create Form3Model (hanya update ases_date)
        $form3 = Form3Model::firstOrNew(['user_id' => $user->user_id]);
        $form3->user_id = $user->user_id;
        $form3->asesi_name = $user->nama;
        $form3->asesi_date = Carbon::now();
        $form3->status = 'Waiting';
        $form3->save();

         $progres = KompetensiProgres::updateOrCreate(
            ['form_id' => $form3->form_3_id],
            ['status' => 'Submitted']
        );


        KompetensiTrack::create([
            'progres_id' => $progres->id,
            'form_type' => 'form_3',
            'activity' => 'Submitted',
            'activity_time' => Carbon::now(),
            'description' => 'Asesi Menyetujui rencana asesmen.',
        ]);

        // Kirim notifikasi ke Asesor jika ada
        if (!empty($bidang->asesor_id)) {
            $userAsesor = DaftarUser::where('user_id', $bidang->asesor_id)->first();
            if ($userAsesor) {
                $this->kirimNotifikasiKeAsesor($userAsesor, $form3->form_3_id);
            }
        }

        return response()->json([
            'status' => 'OK',
            'message' => 'Form3 berhasil disimpan atau diperbarui.',
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

        // Mulai transaksi
        DB::beginTransaction();

        try {
            $form3 = Form3Model::find($form3_id);
            if (!$form3) {
                DB::rollBack();
                return response()->json([
                    'status' => 404,
                    'message' => 'Data Form3 tidak ditemukan.',
                    'data' => null
                ], 404);
            }

            $asesor = DataAsesorModel::where('user_id', $user->user_id)
                ->where('aktif', 1)
                ->first();

            if (!$asesor) {
                DB::rollBack();
                return response()->json([
                    'status' => 404,
                    'message' => 'Data no_reg asesor tidak ditemukan.',
                    'data' => null
                ], 404);
            }

            $form3->asesor_name = $user->nama;
            $form3->asesor_date = Carbon::now();
            $form3->no_reg = $asesor->no_reg;
            $form3->save();

            $progres = KompetensiProgres::where('form_id', $form3->form_3_id)->first();
            if ($progres) {
                $progres->status = 'Completed';
                $progres->save();

                KompetensiTrack::create([
                    'progres_id' => $progres->id,
                    'form_type' => 'form_3',
                    'form_id' => $form3->form_3_id,
                    'activity' => 'Completed',
                    'updated_by' => $user->user_id,
                    'updated_at' => Carbon::now()
                ]);
            }

            // Commit jika semua berhasil
            DB::commit();

            // ✅ Kirim notifikasi ke Asesi
            $userAsesi = DaftarUser::where('user_id', $form3->user_id)->first();
            if ($userAsesi) {
                $title = 'Form 3 Disetujui';
                $message = 'Form 3 Anda telah disetujui oleh asesor. Anda bisa mengajukan jadwal wawancara asesmen.';
                $this->kirimNotifikasiKeUser($userAsesi, $title, $message, $form3->form_3_id);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Form3 berhasil diperbarui dan progres ditandai Completed.',
                'data' => $form3
            ]);
        } catch (\Exception $e) {
            // Rollback jika ada error
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
