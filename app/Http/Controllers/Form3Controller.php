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
use App\Models\DataAsesorModel;
use App\Models\PkProgressModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; // Tambahkan ini untuk menggunakan Carbon


class Form3Controller extends BaseController
{
    /**
     * Mengambil semua data dari tabel form3_b dengan relasi yang lengkap.
     */


    /**
 * @OA\Get(
 *     path="/get-form3-b",
 *     summary="Ambil seluruh data untuk Form 3 B dan berbentuk HTML",
 *     description="Mengambil semua data Form 3 B yang dikelompokkan berdasarkan Elemen, KUK, dan IUK. Data dapat difilter menggunakan parameter no_elemen.",
 *     tags={"Form 3"},
 *     @OA\Parameter(
 *         name="no_elemen",
 *         in="query",
 *         description="Filter berdasarkan no_elemen_form_3",
 *         required=false,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Operasi berhasil. Mengembalikan tampilan HTML.",
 *         @OA\MediaType(
 *             mediaType="text/html"
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan pada server"
 *     )
 * )
 */

    
     public function getAllDataFormB(Request $request)
     {
         $no_elemen = $request->query('no_elemen');
     
         // Ambil data dengan relasi
         $query = TypeForm3_B::with(['iukForm3.kukForm3.elemenForm3'])->orderBy('id', 'asc');
     
         if ($no_elemen) {
             $query->whereHas('iukForm3.kukForm3.elemenForm3', function ($q) use ($no_elemen) {
                 $q->where('no_elemen_form_3', $no_elemen);
             });
         }
     
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
     
             // Konversi indikator_pencapaian ke dalam format bullet list
             $indikator_pencapaian = trim($item->indikator_pencapaian ?? '-');
             $indikator_list = "<ul>";
             foreach (explode("\n", $indikator_pencapaian) as $line) {
                 $line = trim($line);
                 if (!empty($line)) {
                     $line = ltrim($line, '- '); // Hapus tanda "-" di awal
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
		 
		 // Generate URL untuk WebView
		 $base_url = url('/'); // URL utama Laravel (otomatis support WebView)
     
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
     
         // Looping elemen, KUK, IUK, dan soal
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
     
		 return response($html)->header('Content-Type', 'text/html');
     }


     /**
 * @OA\Get(
 *     path="/get-form3-c",
 *     summary="Ambil seluruh data untuk Form 3 C dan Berbentuk HTML",
 *     description="Mengambil semua data Form 3 C yang dikelompokkan berdasarkan Elemen, KUK, dan IUK.",
 *     tags={"Form 3"},
 *     @OA\Parameter(
 *         name="no_elemen",
 *         in="query",
 *         description="Filter berdasarkan no_elemen_form_3",
 *         required=false,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Operasi berhasil. Mengembalikan tampilan HTML.",
 *         @OA\MediaType(
 *             mediaType="text/html"
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan pada server"
 *     )
 * )
 */

     public function getAllDataFormC(Request $request)
    {
        $no_elemen = $request->query('no_elemen');
     
        // Ambil data dengan relasi
        $query = TypeForm3_C::with(['iukForm3.kukForm3.elemenForm3'])->orderBy('id', 'asc');
    
        if ($no_elemen) {
            $query->whereHas('iukForm3.kukForm3.elemenForm3', function ($q) use ($no_elemen) {
                $q->where('no_elemen_form_3', $no_elemen);
            });
        }
    
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
    
            // Konversi indikator_pencapaian ke dalam format bullet list
            $standar_jawaban_text = trim($item->standar_jawaban ?? '-');
            if (!empty($standar_jawaban_text) && $standar_jawaban_text !== '-') {
                $standar_jawaban = "";
                foreach (explode("\n", $standar_jawaban_text) as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        $line = ltrim($line, '- '); // Hapus tanda "-" di awal
                        $standar_jawaban .= "{$line}<br><br>"; // Jarak antar item
                    }
                }
            } else {
                $standar_jawaban = "-";
            }

            // Format pertanyaan tanpa bullet list
            $pertanyaan_text = trim($item->pertanyaan ?? '-');
            if (!empty($pertanyaan_text) && $pertanyaan_text !== '-') {
                $pertanyaan = "";
                foreach (explode("\n", $pertanyaan_text) as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        $line = ltrim($line, '- '); // Hapus tanda "-" di awal
                        $pertanyaan .= "{$line}<br><br>"; // Jarak antar item
                    }
                }
            } else {
                $pertanyaan = "-";
            }

            
            $elemen_group[$elemen_key][$kuk_key][$iuk_key][] = [
                'no_soal' => $item->no_soal ?? '-',
                'pertanyaan' => $pertanyaan ?? '-',
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
                table { width: 100%; max-width: 100%; border-collapse: collapse;  overflow-x: auto; display: block; }

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
    
        // Looping elemen, KUK, IUK, dan soal
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
    
        return response($html);
    }

/**
 * @OA\Get(
 *     path="/get-form3-a",
 *     summary="Ambil seluruh data untuk Form 3 A dan Berbentuk HTML",
 *     description="Mengambil semua data Form 3 A yang dikelompokkan berdasarkan Elemen, KUK, dan IUK",
 *     tags={"Form 3"},
 *     @OA\Parameter(
 *         name="no_elemen_form_3",
 *         in="query",
 *         description="Filter berdasarkan no_elemen_form_3",
 *         required=false,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Operasi berhasil. Mengembalikan tampilan HTML.",
 *         @OA\MediaType(
 *             mediaType="text/html"
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan pada server"
 *     )
 * )
 */

    public function getAllDataFormA(Request $request)
    {
        $no_elemen_form_3 = $request->query('no_elemen_form_3'); // Ambil parameter opsional

        $query = TypeForm3_A::with([
            'iukForm3.kukForm3.elemenForm3', // Relasi ke Elemen
            'poinForm3' // Relasi ke PoinForm3 (hasMany)
        ]);

        // Filter berdasarkan no_elemen_form_3 jika diberikan
        if ($no_elemen_form_3) {
            $query->whereHas('iukForm3.kukForm3.elemenForm3', function ($q) use ($no_elemen_form_3) {
                $q->where('no_elemen_form_3', $no_elemen_form_3);
            });
        }

        $data = $query->get();
        
        // Buat tampilan HTML dalam bentuk tabel
        $html = '<!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Form 3 A</title>
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
        <h2>Form 3 A</h2>';
        
        $elemen_group = [];
        foreach ($data as $item) {
            $elemen = optional($item->iukForm3->kukForm3->elemenForm3);
            $elemenKey = "{$elemen->no_elemen_form_3} : {$elemen->isi_elemen}";
            
            $kukKey = "{$item->iukForm3->no_kuk} : {$item->iukForm3->kukForm3->kuk_name}";
            $iukKey = "{$item->no_iuk} : {$item->iukForm3->iuk_name}";
            $poinList = $item->poinForm3->pluck('poin_diamati')->toArray();

            $elemen_group[$elemenKey][$kukKey][$iukKey] = $poinList;
        }
        
        foreach ($elemen_group as $elemen => $kuk_list) {
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

            foreach ($kuk_list as $kuk => $iuk_list) {
                foreach ($iuk_list as $iuk => $poin_diamati) {
                    $poinHtml = '<ul>';
                    foreach ($poin_diamati as $poin) {
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

        $html .= '</body></html>';
        return response($html);
    }


    /**
 * @OA\Get(
 *     path="/get-form3-d",
 *     summary="Ambil data untuk Form 3 D dan Berbentuk HTML",
 *     description="Mengambil seluruh data Form 3 D yang menggabungkan informasi KUK dan Dokumen. Data diurutkan berdasarkan 'kuk_info'.",
 *     tags={"Form 3"},
 *     @OA\Response(
 *         response=200,
 *         description="Operasi berhasil. Mengembalikan tampilan HTML.",
 *         @OA\MediaType(
 *             mediaType="text/html"
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan pada server"
 *     )
 * )
 */


    public function getAllDataFormD()
    {
        $data = TypeForm3_D::with(['kukForm3', 'document'])->get();

        $formattedData = [];

        foreach ($data as $item) {
            $formattedData[] = [
                'kuk_info' => $item->no_kuk . ' - ' . ($item->kukForm3->kuk_name ?? 'Tidak ada nama'),
                'nama_doc' => $item->document->nama_doc ?? null
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

                table { width: 100%; max-width: 100%; border-collapse: collapse; margin-top: 20px; overflow-x: auto; }

                th, td { border: 2px solid black; padding: 10px; }
                
                th { background-color: #f2f2f2; text-align: left; }

                td { text-align: left; }

                @media (max-width: 600px) {
                    th, td {
                        padding: 5px;
                        font-size: 12px;
                    }
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

        return response($html);
    }

   /**
 * @OA\Post(
 *     path="/input-form3/{user_id}",
 *     summary="Input Data Form 3",
 *     description="Membuat data baru di Form3 dengan mengambil data dari Form2 berdasarkan user_id, serta mengupdate pk_progress dan pk_status.",
 *     tags={"Asesor"},
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="ID pengguna yang digunakan untuk mengambil data dari Form2",
 *         @OA\Schema(type="integer", example=123)
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Form3 berhasil dibuat dengan status Approved dan pk_progress serta pk_status diperbarui.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=201),
 *             @OA\Property(property="message", type="string", example="Form3 created successfully with status Approved."),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Permintaan tidak valid.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="Invalid request."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data Form2 tidak ditemukan untuk user_id yang diberikan.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="No data found for the given user_id.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="User yang mencoba input bukan asesor (role_id bukan 2).",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="Unauthorized. Only assessors (role_id = 2) can input Form3.")
 *         )
 *     )
 * )
 */


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
			'status'       => 'Approved'
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
    
	public function ApproveAsesiForm3()
	{
		$user = auth()->user(); // Ambil user yang sedang login

		if (!$user) {
			return response()->json([
				'status' => 401,
				'message' => 'User belum login.',
				'data' => null
			], 401);
		}

		// Validasi apakah user punya role_id = 1 (asesi)
		$isAsesi = $user->roles()->where('role_id', 1)->exists();

		if (!$isAsesi) {
			return response()->json([
				'status' => 403,
				'message' => 'Anda tidak memiliki izin untuk mengisi Form3.',
				'data' => null
			], 403);
		}

		// Cek apakah user_id sudah pernah mengisi Form3
		$existing = Form3Model::where('user_id', $user->user_id)->first();

		if ($existing) {
			return response()->json([
				'status' => 409,
				'message' => 'Anda sudah pernah mengisi Form3.',
				'data' => $existing
			], 409);
		}

		// Simpan data ke model Form3Model
		$form3 = new Form3Model();
		$form3->user_id = $user->user_id;
		$form3->asesi_name = $user->nama;
		$form3->asesi_date = Carbon::now();
		$form3->status = 'Waiting';
		$form3->save();

		return response()->json([
			'status' => 201,
			'message' => 'Form3 created successfully with status Waiting.',
			'data' => $form3
		], 201);
	}


	public function UpdateAsesorForm3($form3_id)
	{
		$user = auth()->user(); // Ambil user yang sedang login

		if (!$user) {
			return response()->json([
				'status' => 401,
				'message' => 'User belum login.',
				'data' => null
			], 401);
		}

		// Validasi apakah user memiliki role Asesor (role_id = 2)
		$isAsesor = $user->roles()->where('role_id', 2)->exists();
		if (!$isAsesor) {
			return response()->json([
				'status' => 403,
				'message' => 'Anda tidak memiliki izin untuk mengisi bagian asesor.',
				'data' => null
			], 403);
		}

		// Cari Form3 berdasarkan ID
		$form3 = Form3Model::find($form3_id);
		if (!$form3) {
			return response()->json([
				'status' => 404,
				'message' => 'Data Form3 tidak ditemukan.',
				'data' => null
			], 404);
		}

		// Ambil data asesor aktif berdasarkan user_id
		$asesor = DataAsesorModel::where('user_id', $user->user_id)->where('aktif', 1)->first();
		if (!$asesor) {
			return response()->json([
				'status' => 404,
				'message' => 'Data no_reg asesor tidak ditemukan.',
				'data' => null
			], 404);
		}

		// Update data asesor ke Form3
		$form3->asesor_name = $user->nama;
		$form3->asesor_date = Carbon::now();
		$form3->no_reg = $asesor->no_reg;
		$form3->status = 'Approved';
		$form3->save();

		return response()->json([
			'status' => 200,
			'message' => 'Form3 updated successfully with asesor data and status set to Approved.',
			'data' => $form3
		], 200);
	}


}
