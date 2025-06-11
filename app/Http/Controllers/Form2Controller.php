<?php

namespace App\Http\Controllers;

use App\Models\ElemenKompetensiForm2Model;
use App\Models\JawabanForm2Model;
use App\Models\SoalForm2Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\PenilaianForm2Model;
use App\Models\PkProgressModel;
use Carbon\Carbon; // Pastikan untuk mengimpor Carbon  
use App\Models\BidangModel;
use Illuminate\Support\Facades\Redis; // Tambahkan Redis facade
use Illuminate\Support\Facades\DB;


/**
 * @OA\Tag(
 *     name="Form 2",
 *     description="API terkait Form 2"
 * )
 */

class Form2Controller extends Controller
{
    /**
 * @OA\Get(
 *     path="/soal-form2",
 *     summary="Mengambil daftar soal dari Redis atau Database",
 *     description="Mengambil semua soal berdasarkan elemen kompetensi, komponen serta soalnya. Parameter opsional no_elemen dan no_id dapat digunakan untuk melakukan filter.",
 *     tags={"Form 2"},
 *     @OA\Parameter(
 *         name="no_elemen",
 *         in="query",
 *         description="Filter berdasarkan nomor elemen",
 *         required=false,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="no_id",
 *         in="query",
 *         description="Filter berdasarkan nomor id",
 *         required=false,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data diambil dari Redis atau dari database",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="message", type="string", example="Data taken from Redis"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="no_elemen", type="integer", example=1),
 *                     @OA\Property(property="nama_elemen", type="string", example="Menilai kompetensi teknis"),
 *                     @OA\Property(property="komponen_id", type="string", example="1.1"),
 *                     @OA\Property(property="nama_komponen", type="string", example="Kemampuan problem-solving"),
 *                     @OA\Property(property="no_id", type="integer", example=5),
 *                     @OA\Property(property="sub_komponen_id", type="integer", example=2),
 *                     @OA\Property(property="daftar_pertanyaan", type="string", example="Bagaimana Anda menangani masalah dalam proyek?")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan pada server"
 *     )
 * )
 */

     public function getSoals(Request $request)
    {
        // Ambil parameter dari query string
        $no_elemen = $request->query('no_elemen');
        $no_id     = $request->query('no_id');

        // Tentukan cache key berdasarkan adanya filter
        if ($no_elemen !== null || $no_id !== null) {
            // Gunakan nilai default "all" bila parameter tidak ada
            $filterElemen = $no_elemen !== null ? $no_elemen : 'all';
            $filterId     = $no_id !== null ? $no_id : 'all';
            $cacheKey = 'soal_cache_filtered_' . $filterElemen . '_' . $filterId;
        } else {
            $cacheKey = 'soal_cache';
        }

        //Cek apakah data sudah ada di Redis dengan key tersebut
		
        $cachedData = Redis::get($cacheKey);
        if ($cachedData) {
            $data = json_decode($cachedData, true);
            return response()->json([
                'status'  => 'SUCCESS',
                'message' => 'Data taken from Redis',
                'data'    => $data
            ]);
        }
		

        // Jika cache tidak ada, ambil data dari database
        $elemenKompetensis = ElemenKompetensiForm2Model::with('komponens.soals')
            ->orderBy('no_elemen')
            ->get();

        $result = [];
        foreach ($elemenKompetensis as $elemenKompetensi) {
            foreach ($elemenKompetensi->komponens as $komponen) {
                foreach ($komponen->soals as $soal) {
                    $result[] = [
                        'no_elemen'         => $elemenKompetensi->no_elemen,
                        'nama_elemen'       => $elemenKompetensi->nama_elemen,
                        'komponen_id'       => $komponen->komponen_id,
                        'nama_komponen'     => $komponen->nama_komponen,
                        'no_id'             => $soal->no_id,
                        'sub_komponen_id'   => $soal->sub_komponen_id,
                        'daftar_pertanyaan' => $soal->daftar_pertanyaan,
                    ];
                }
            }
        }

        // Lakukan filtering jika parameter tersedia
        if ($no_elemen !== null) {
            $result = array_filter($result, function ($item) use ($no_elemen) {
                return $item['no_elemen'] == $no_elemen;
            });
        }
        if ($no_id !== null) {
            $result = array_filter($result, function ($item) use ($no_id) {
                return $item['no_id'] == $no_id;
            });
        }

        // Reindex array hasil filter
        $result = array_values($result);

        // Simpan data ke Redis dengan cache key yang sesuai
        Redis::set($cacheKey, json_encode($result));

        $message = ($no_elemen !== null || $no_id !== null) ? 
            'Data taken from database (filtered)' : 'Data taken from database and cached';

        return response()->json([
            'status'  => 'SUCCESS',
            'message' => $message,
            'data'    => $result
        ]);
    }

    /**
 * @OA\Get(
 *     path="/get-form2",
 *     summary="Mengambil semua data Form 2",
 *     description="Mengambil seluruh data dari tabel Form 2 dengan opsi filter berdasarkan form_2_id, date dan no_reg.",
 *     tags={"Form 2"},
 *     @OA\Parameter(
 *         name="form_2_id",
 *         in="query",
 *         description="Filter berdasarkan form_2_id",
 *         required=false,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="date",
 *         in="query",
 *         description="Filter berdasarkan tanggal (format: YYYY-MM-DD)",
 *         required=false,
 *         @OA\Schema(
 *             type="string",
 *             format="date"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="no_reg",
 *         in="query",
 *         description="Filter berdasarkan nomor registrasi",
 *         required=false,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data berhasil diambil",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Data retrieved successfully"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="user_id", type="integer", example=101),
 *                     @OA\Property(property="nilai", type="integer", example=85),
 *                     @OA\Property(property="tanggal", type="string", format="date", example="2024-02-10")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Data not found")
 *         )
 *     )
 * )
 */


     public function getForm2Data(Request $request)
     {
         // Ambil parameter filter dari query string
         $form_2_id = $request->query('form_2_id');
         $date      = $request->query('date');
         $no_reg    = $request->query('no_reg');
     
         // Inisialisasi query builder untuk model PenilaianForm2Model
         $query = PenilaianForm2Model::query();
     
         // Filter berdasarkan form_2_id jika disediakan
         if ($form_2_id !== null) {
             $query->where('form_2_id', $form_2_id);
         }
     
         // Filter berdasarkan date jika disediakan (misalnya, kolom bernama 'date')
         if ($date !== null) {
             $query->whereDate('date', $date);
         }
     
         // Filter berdasarkan no_reg jika disediakan
         if ($no_reg !== null) {
             $query->where('no_reg', $no_reg);
         }
     
         // Eksekusi query
         $data = $query->get();
     
         // Jika data kosong
         if ($data->isEmpty()) {
             return response()->json(['message' => 'Data not found'], 404);
         }
     
         // Kembalikan data dalam format JSON
         return response()->json([
             'message' => 'Data retrieved successfully',
             'data'    => $data
         ]);
     }

    /**
     * @OA\Get(
     *     path="/get-jawaban-form2/{user_jawab_form_2_id}",
     *     summary="Mengambil data soal dan jawaban berdasarkan user_jawab_form_2_id",
     *     description="Mengembalikan daftar soal dan jawaban yang telah diberikan oleh user berdasarkan ID user_jawab_form_2_id. Hanya data penilaian dan soal yang ditampilkan sekali untuk setiap user.",
     *     tags={"Asesor"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="user_jawab_form_2_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID user yang memberikan jawaban"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data berhasil diambil",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_jawab_form_2_id", type="integer", example=26),
     *             @OA\Property(property="asesi_name", type="string", example="INI BUDI"),
     *             @OA\Property(property="penilaian_asesi", type="string", example="80.60"),
     *             @OA\Property(property="asesi_date", type="string", example="2025-04-01"),
     *             @OA\Property(
     *                 property="soal",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="komponen_id", type="string", example="1.1"),
     *                     @OA\Property(property="nama_komponen", type="string", example="Pasien yang dilakukan pengkajian keperawatan diidentifikasi"),
     *                     @OA\Property(property="no_id", type="integer", example=1),
     *                     @OA\Property(property="sub_komponen_id", type="string", example="1.1.1"),
     *                     @OA\Property(property="daftar_pertanyaan", type="string", example="Apakah anda mampu menjelaskan respon biopsikososial atau manifestasi klinik yang diperhatikan oleh pasien dengan minimal care?"),
     *                     @OA\Property(property="k", type="boolean", example=true),
     *                     @OA\Property(property="bk", type="boolean", example=false)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data tidak ditemukan",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Data tidak ditemukan")
     *         )
     *     )
     * )
     */


     public function getDataSoalJawaban($user_jawab_form_2_id)
     {
         // Ambil semua jawaban berdasarkan user_jawab_form_2_id
         $jawaban = JawabanForm2Model::with(['soal.komponen'])
             ->where('user_jawab_form_2_id', $user_jawab_form_2_id)
             ->get();

        // Ambil penilaian_asesi dan asesi_date berdasarkan user_jawab_form_2_id
        $penilaian = PenilaianForm2Model::where('user_jawab_form_2_id', $user_jawab_form_2_id)->first();
     
        // Jika penilaian tidak ditemukan, beri response error
        if (!$penilaian) {
            return response()->json(['error' => 'Penilaian not found'], 404);
        }

        // Format respons dan urutkan
		$response = $jawaban->map(function ($item) {
			return $item->soal->sortBy('sub_komponen_id')->map(function ($soal) use ($item) {
				return [
					'komponen_id' => $soal->komponen_id,
					'nama_komponen' => $soal->komponen->nama_komponen,
					'no_id' => $soal->no_id,
					'sub_komponen_id' => $soal->sub_komponen_id,
					'daftar_pertanyaan' => $soal->daftar_pertanyaan,
					'k' => $item->k,
					'bk' => $item->bk,
				];
			});
		})
		->flatten(1) // Menghilangkan array bersarang
		->sortBy('komponen_id') // Tetap urut berdasarkan komponen_id global
		->values(); // Reset index array

        // Menambahkan data penilaian sekali saja di luar loop
        $response = [
            'user_jawab_form_2_id' => $penilaian->user_jawab_form_2_id,
            'asesi_name' => $penilaian->asesi_name,
            'penilaian_asesi' => $penilaian->penilaian_asesi,
            'asesi_date' => $penilaian->asesi_date,
            'soal' => $response,
        ];

        return response()->json([
			'status' => 'SUCCESS',
			'message' => 'Data retrieved successfully',
			'data' => $response
		]);
    }
     

    /**
     * @OA\Put(
     *     path="/penilaian-asesor-form2",
     *     summary="Memperbarui penilaian asesor untuk Asesi",
     *     tags={"Asesor"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"form_2_id", "status"},
     *             @OA\Property(property="form_2_id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", enum={"Approved", "Cancel"}, example="Approved")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Penilaian asesor berhasil diperbarui",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Status updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="form_2_id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="Approved"),
     *                 @OA\Property(property="asesor_date", type="string", format="date-time", example="2024-08-02T10:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validasi gagal - Input tidak sesuai",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="object", example={"form_2_id": {"Form_2 ID tidak valid"}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data tidak ditemukan",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Data not found")
     *         )
     *     )
     * )
     */


     public function inputPenilaianAsesor(Request $request)
     {
         // Validasi input hanya untuk status
         $validator = Validator::make($request->all(), [
             'form_2_id' => 'required|exists:form_2,form_2_id', // Pastikan form_2_id ada
             'status' => 'required|string|in:Approved,Cancel', // Hanya boleh Approved atau Cancel
         ]);
     
         if ($validator->fails()) {
             return response()->json(['error' => $validator->errors()], 400);
         }
     
         // Cari data penilaian berdasarkan form_2_id
         $penilaian = PenilaianForm2Model::where('form_2_id', $request->form_2_id)->first();
     
         // Jika tidak ada, buat baru
         if (!$penilaian) {
             $penilaian = new PenilaianForm2Model();
             $penilaian->form_2_id = $request->form_2_id;
         }
     
         // Set status baru
         $penilaian->status = $request->status;
         $penilaian->asesor_date = Carbon::now(); // Tambahkan timestamp asesor_date
     
         // Jika status Cancel, null-kan penilaian_asesi & asesi_date di form_2
         if ($request->status === 'Cancel') {
             DB::table('form_2')
                 ->where('form_2_id', $request->form_2_id)
                 ->update(['penilaian_asesi' => null, 'asesi_date' => null]);
     
             // Kosongkan jawaban di tabel jawaban_form_2
             DB::table('jawaban_form_2')
                 ->where('user_jawab_form_2_id', function ($query) use ($request) {
                     $query->select('user_jawab_form_2_id')
                           ->from('form_2')
                           ->where('form_2_id', $request->form_2_id);
                 })
                 ->update(['no_id' => null, 'k' => null, 'bk' => null]);

             } elseif ($request->status === 'Approved') {
                // Cari progress_id dari pk_progress berdasarkan form_2_id
                $progress = PkProgressModel::where('form_2_id', $request->form_2_id)->first();

                if ($progress) {
                    // Update pk_status dengan progress_id yang sesuai
                    DB::table('pk_status')
                        ->where('progress_id', $progress->progress_id)
                        ->update([
                            'form_2_status' => 'Completed',
                            'form_3_status' => 'Open',
                        ]);
                }
            }
     
         // Simpan perubahan status
         $penilaian->save();
     
         return response()->json([
             'message' => 'Status updated successfully',
             'data' => $penilaian
         ]);
     }
     



/**
 * @OA\Post(
 *     path="/jawaban-asesi",
 *     summary="Menyimpan jawaban asesi dan menghitung penilaian",
 *     tags={"Form 2"},
 *     security={{ "bearerAuth":{} }},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"jawaban"},
 *             @OA\Property(
 *                 property="jawaban",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     required={"no_id", "k", "bk"},
 *                     @OA\Property(property="no_id", type="integer", example=1),
 *                     @OA\Property(property="k", type="boolean", example=true),
 *                     @OA\Property(property="bk", type="boolean", example=false)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Jawaban berhasil disimpan dan nilai lebih dari 80",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Answer successfully saved and score more than 80"),
 *             @OA\Property(property="user_jawab_form_2_id", type="integer", example=1),
 *             @OA\Property(property="total_k", type="integer", example=10),
 *             @OA\Property(property="total_bk", type="integer", example=5),
 *             @OA\Property(property="total_soal", type="integer", example=20),
 *             @OA\Property(property="penilaian_asesi", type="number", format="float", example=85.0),
 *             @OA\Property(property="asesi_date", type="string", format="date-time", example="2024-08-02T10:00:00Z"),
 *             @OA\Property(property="no_reg", type="string", example="REG-123456"),
 *             @OA\Property(property="asesi_name", type="string", example="John Doe"),
 *             @OA\Property(property="asesor_name", type="string", example="Dr. Smith")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal - Input tidak sesuai",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="object", example={"jawaban.*.no_id": {"Soal tidak ditemukan"}})
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan atau user tidak ditemukan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="User not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan pada server",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="Terjadi kesalahan: [deskripsi error]")
 *         )
 *     )
 * )
 */



    public function JawabanAsesi(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'jawaban' => 'required|array', // Jawaban harus array
            'jawaban.*.no_id' => 'required|integer|exists:soal_form_2,no_id',
            'jawaban.*.k' => 'boolean',
            'jawaban.*.bk' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Ambil user_id dari token JWT
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Cari form_1 berdasarkan user_id
        $form1 = BidangModel::where('user_id', $user->user_id)->first();
        if (!$form1) {
            return response()->json(['error' => 'Data form_1 tidak ditemukan untuk user ini'], 404);
        }

        // Ambil asesi_name dan asesor_name dari form_1
        $asesi_name = $form1->asesi_name;  // Ambil asesi_name dari form_1
        $asesor_name = $form1->asesor_name;  // Ambil asesor_name dari form_1

        // Ambil jumlah total soal dari tabel soal_form_2
        $total_soal = SoalForm2Model::count();
        if ($total_soal == 0) {
            return response()->json(['error' => 'Total soal tidak boleh 0'], 400);
        }

        // Mulai transaksi database
        DB::beginTransaction();
        try {
            // Ambil semua jawaban user sebelumnya
            $jawabanUser = JawabanForm2Model::where('user_jawab_form_2_id', $user->user_id)->get()->keyBy('no_id');

            $k_count = 0;
            $bk_count = 0;
            $jawabanData = [];

            foreach ($request->jawaban as $input) {
                $no_id = $input['no_id'];
                $k = $input['k'] ?? false;
                $bk = $input['bk'] ?? false;

                if (isset($jawabanUser[$no_id])) {
                    // Update jawaban jika sudah ada
                    $jawabanUser[$no_id]->k = $k;
                    $jawabanUser[$no_id]->bk = $bk;
                    $jawabanUser[$no_id]->save();
                } else {
                    // Cek apakah ada jawaban lama yang telah di-null-kan
                    $jawaban = JawabanForm2Model::where('user_jawab_form_2_id', $user->user_id)
                        ->whereNull('no_id')
                        ->first();
                    
                    if ($jawaban) {
                        // Jika ada yang NULL, update kembali
                        $jawaban->no_id = $no_id;
                        $jawaban->k = $k;
                        $jawaban->bk = $bk;
                        $jawaban->save();
                    } else {
                        // Simpan jawaban baru
                        $jawabanData[] = [
                            'no_id' => $no_id,
                            'k' => $k,
                            'bk' => $bk,
                            'user_jawab_form_2_id' => $user->user_id,
                        ];
                    }
                }
    
                // Akumulasi nilai K dan BK
                $k_count += $k ? 1 : 0;
                $bk_count += $bk ? 1 : 0;
            }


            // Insert semua jawaban baru dalam satu query
            if (!empty($jawabanData)) {
                JawabanForm2Model::insert($jawabanData);
            }

            // Hitung nilai penilaian asesi
            $penilaian_asesi = ($k_count / $total_soal) * 100;

             // **Cek apakah nilai >= 80 sebelum disimpan**
            if ($penilaian_asesi >= 80) {
                // Simpan atau update data penilaian di tabel form_2
                $penilaian = PenilaianForm2Model::updateOrCreate(                    
                    ['no_reg' => $form1->no_reg, 'user_jawab_form_2_id' => $user->user_id],
                    [
                        'k' => $k_count,
                        'bk' => $bk_count,
                        'penilaian_asesi' => $penilaian_asesi,
                        'asesi_date' => Carbon::now(), // Menambahkan asesii_date dengan waktu sekarang
                        'asesi_name' => $asesi_name,  // Menambahkan asesi_name dari form_1
                        'asesor_name' => $asesor_name,  // Menambahkan asesor_name dari form_1
                    ]
                );

                // **Tambahkan form_2_id ke dalam tabel pk_progress**
                PkProgressModel::updateOrCreate(
                    ['user_id' => $user->user_id],
                    ['form_2_id' => $penilaian->form_2_id] // Pastikan form_2_id yang dimasukkan sesuai dengan ID dari PenilaianForm2Model
                );


            } else {
                // Jika nilai < 80, batalkan penyimpanan data penilaian
                DB::rollBack();
                return response()->json([
                    'message' => 'Value less than 80, data is not saved',
                    'user_jawab_form_2_id' => $user->user_id,
                    'total_k' => $k_count,
                    'total_bk' => $bk_count,
                    'total_soal' => $total_soal,
                    'penilaian_asesi' => $penilaian_asesi,
                ], 200);
            }


            // Commit transaksi
            DB::commit();

            return response()->json([
                'message' => 'Answer successfully saved and score more than 80',
                'form_2_id' => $penilaian->form_2_id,
                'user_jawab_form_2_id' => $user->user_id,
                'total_k' => $k_count,
                'total_bk' => $bk_count,
                'total_soal' => $total_soal,
                'penilaian_asesi' => $penilaian_asesi,
                'asesi_date' => $penilaian->asesi_date, // Menambahkan asesi_date ke response
                'no_reg' => $penilaian->no_reg,
                'asesi_name' => $penilaian->asesi_name, 
                'asesor_name' => $penilaian->asesor_name, 
            ], 200);

        } catch (\Exception $e) {
            // Rollback jika ada error
            DB::rollback();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }


}



