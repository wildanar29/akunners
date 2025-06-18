<?php

namespace App\Http\Controllers;

use App\Models\IjazahModel;
use App\Models\SipModel;
use App\Models\StrModel;
use App\Models\SertifikatModel;
use App\Models\UjikomModel;
use App\Models\BidangModel; // Model form_1
use Illuminate\Support\Facades\DB;
use App\Models\PkProgressModel; 
use App\Models\PkStatusModel; 
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\UsersController; // Ganti dengan nama controller yang berisi CheckDataCompleteness

class AsesiPermohonanController extends Controller
{


    /**
     * @OA\Post(
     *     path="/ajuan-asesi",
     *     summary="Mengajukan permohonan asesi berdasarkan bearer token",
     *     description="Endpoint ini digunakan untuk mengajukan permohonan sebagai asesi. 
     *     Pastikan dokumen Ijazah, Ujikom/Askom, STR, dan SIP telah diunggah.",
     *     tags={"Ajuan Asesi"},
     *     security={{"bearerAuth":{}}},
     *     
     *     @OA\Response(
     *         response=201,
     *         description="Permohonan berhasil diajukan.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data successfully inserted into Form_1."),
     *             @OA\Property(property="form_1_id", type="integer", example=1),
     *             @OA\Property(property="data_status", type="object"),
     *             @OA\Property(property="status_code", type="integer", example=201)
     *         )
     *     ),
     *     
     *     @OA\Response(
     *         response=400,
     *         description="Permohonan gagal karena dokumen wajib tidak tersedia.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Submission failed. The following documents must have a valid file path: Ijazah, Transkrip."),
     *             @OA\Property(property="missing_documents", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="status_code", type="integer", example=400)
     *         )
     *     ),
     *     
     *     @OA\Response(
     *         response=401,
     *         description="Token tidak valid atau pengguna tidak ditemukan.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized. Invalid token or user not found."),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     ),
     *     
     *     @OA\Response(
     *         response=500,
     *         description="Terjadi kesalahan pada server.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred while inserting data."),
     *             @OA\Property(property="error", type="string", example="SQLSTATE[23000]: Integrity constraint violation"),
     *             @OA\Property(property="status_code", type="integer", example=500)
     *         )
     *     )
     * )
     */
   public function AjuanPermohonanAsesi(Request $request)
    {
        try {
            // Ambil user dari token
            $user = auth()->user();

            // Jika user tidak ditemukan dari token, kirim error
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Invalid token or user not found.',
                    'status_code' => 401,
                ], 401);
            }

            // Panggil controller lain dan gunakan fungsi CheckDataCompleteness
            $dataChecker = new UsersController();
            $checkDataResponse = $dataChecker->CheckDataCompleteness($user->nik);

            // Jika response dari CheckDataCompleteness mengandung status selain 200, kembalikan error
            if ($checkDataResponse->getStatusCode() !== 200) {
                return $checkDataResponse;
            }

            // Ambil data terkait dari berbagai tabel berdasarkan user_id
            $ijazah = IjazahModel::where('user_id', $user->user_id)->first();
            // $ujikom = UjikomModel::where('user_id', $user->user_id)->first(); // Dikomentari sesuai permintaan
            $str = StrModel::where('user_id', $user->user_id)->first();
            $sip = SipModel::where('user_id', $user->user_id)->first();
            $sertifikat = SertifikatModel::where('user_id', $user->user_id)->first();

            // Kumpulkan dokumen yang belum tersedia atau tidak memiliki path_file
            $missingDocuments = [];

            if (!$ijazah || empty($ijazah->path_file)) $missingDocuments[] = 'Ijazah';
            // if (!$ujikom || empty($ujikom->path_file)) $missingDocuments[] = 'Ujikom'; // Dikomentari sesuai permintaan
            if (!$str || empty($str->path_file)) $missingDocuments[] = 'STR';
            if (!$sip || empty($sip->path_file)) $missingDocuments[] = 'SIP';

            // Jika ada dokumen yang belum tersedia atau tidak memiliki path_file, kembalikan error
            if (!empty($missingDocuments)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Submission failed. The following documents must have a valid file path: ' . implode(', ', $missingDocuments) . '.',
                    'missing_documents' => $missingDocuments,
                    'status_code' => 400,
                ], 400);
            }

            // Cek apakah permohonan sudah pernah diajukan
            $existingBidang = BidangModel::where('user_id', $user->user_id)->first();

            // Data yang akan disimpan atau diperbarui
            $dataUpdate = [
                'asesi_name' => $user->nama,
                'asesi_date' => Carbon::now()->toDateString(),
                'ijazah_id' => $ijazah->ijazah_id,
                // 'ujikom_id' => $ujikom->ujikom_id, // Dikomentari sesuai permintaan
                'str_id' => $str->str_id,
                'sip_id' => $sip->sip_id,
                'sertifikat_id' => $sertifikat ? $sertifikat->user_id : null,
                'updated_at' => Carbon::now(),
                'status' => 'Waiting',
            ];

            if ($existingBidang) {
                $existingBidang->update($dataUpdate);

                return response()->json([
                    'success' => true,
                    'message' => 'Data successfully updated in Form_1.',
                    'form_1_id' => $existingBidang->form_1_id,
                    'updated_data' => $dataUpdate,
                    'status_code' => 200,
                ], 200);
            } else {
                $dataUpdate['user_id'] = $user->user_id;
                $dataUpdate['created_at'] = Carbon::now();

                $newBidang = BidangModel::create($dataUpdate);
                $form_1_id = $newBidang->form_1_id;
            }

            // *** Tambahkan Data ke pk_progress ***
            $progress = PkProgressModel::create([
                'user_id' => $user->user_id,
                'form_1_id' => $form_1_id,
            ]);

            // *** Tambahkan Data ke pk_status ***
            PkStatusModel::create([
                'progress_id' => $progress->progress_id,
                'form_1_status' => 'Open',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data successfully inserted into Form_1, Pk_Progress, and Pk_Status.',
                'form_1' => [
                    'form_1_id' => $form_1_id,
                    'user_id' => $user->user_id,
                    'asesi_name' => $dataUpdate['asesi_name'],
                    'asesi_date' => $dataUpdate['asesi_date'],
                    'ijazah_id' => $dataUpdate['ijazah_id'],
                    // 'ujikom_id' => $dataUpdate['ujikom_id'] ?? null, // Dikomentari sesuai permintaan
                    'str_id' => $dataUpdate['str_id'],
                    'sip_id' => $dataUpdate['sip_id'],
                    'sertifikat_id' => $dataUpdate['sertifikat_id'],
                    'status' => $dataUpdate['status'],
                    'created_at' => $dataUpdate['created_at'] ?? null,
                    'updated_at' => $dataUpdate['updated_at'],
                ],
                'pk_progress' => [
                    'form_1_status' => 'Open',
                ],
                'status_code' => 201,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while processing data.',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ], 500);
        }
    }

   public function getUserFormProgress($userId)
	{
		try {
			// Ambil data pk_progress dan join ke masing-masing form yang sudah tersedia
			$progress = DB::table('pk_progress')
				->where('pk_progress.user_id', $userId)
				->leftJoin('form_1', 'pk_progress.form_1_id', '=', 'form_1.form_1_id')
				->leftJoin('form_2', 'pk_progress.form_2_id', '=', 'form_2.form_2_id')
				->leftJoin('form_3', 'pk_progress.form_3_id', '=', 'form_3.form_3_id')
				// ->leftJoin('form_4', ...)
				// dst...
				->select(
					'pk_progress.*',
					'form_1.status as form_1_status',
					'form_2.status as form_2_status',
					'form_3.status as form_3_status'
					// 'form_4.status as form_4_status',
					// dst...
				)
				->first();

			if (!$progress) {
				return response()->json([
					'status' => false,
					'message' => 'Data progress tidak ditemukan untuk user ini.',
					'data' => null
				], 404);
			}

			return response()->json([
				'status' => true,
				'message' => 'Berhasil mengambil data progres form.',
				'data' => [
					'user_id' => $userId,
					'form_statuses' => [
						'form_1' => $progress->form_1_status ?? 'Terkunci',
						'form_2' => $progress->form_2_status ?? 'Terkunci',
						'form_3' => $progress->form_3_status ?? 'Terkunci',
						// 'form_4' => $progress->form_4_status ?? 'Belum Diisi',
						// dst...
					]
				]
			]);
		} catch (\Exception $e) {
			// Tangani error tak terduga
			return response()->json([
				'status' => false,
				'message' => 'Terjadi kesalahan saat mengambil data progres.',
				'data' => null,
				'error' => $e->getMessage() // opsional, hapus jika tak ingin expose error detail
			], 500);
		}
	}



}
