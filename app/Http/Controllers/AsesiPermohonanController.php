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

    function getUserFormProgress($userId)
    {
        // Ambil data pk_progress dan join ke masing-masing form yang sudah tersedia
        $progress = DB::table('pk_progress')
            ->where('user_id', $userId)
            ->leftJoin('form_1', 'pk_progress.form_1_id', '=', 'form_1.form_1_id')
            ->leftJoin('form_2', 'pk_progress.form_2_id', '=', 'form_2.form_2_id')
            ->leftJoin('form_3', 'pk_progress.form_3_id', '=', 'form_3.form_3_id')
            
            // ->leftJoin('form_4', 'pk_progress.form_4_id', '=', 'form_4.form_4_id')
            // ->leftJoin('form_5', 'pk_progress.form_5_id', '=', 'form_5.form_5_id')
            // ->leftJoin('form_6', 'pk_progress.form_6_id', '=', 'form_6.form_6_id')
            // ->leftJoin('form_7', 'pk_progress.form_7_id', '=', 'form_7.form_7_id')
            // ->leftJoin('form_8', 'pk_progress.form_8_id', '=', 'form_8.form_8_id')
            // ->leftJoin('form_9', 'pk_progress.form_9_id', '=', 'form_9.form_9_id')
            // ->leftJoin('form_10', 'pk_progress.form_10_id', '=', 'form_10.form_10_id')
            // ->leftJoin('form_11', 'pk_progress.form_11_id', '=', 'form_11.form_11_id')
            // ->leftJoin('form_12', 'pk_progress.form_12_id', '=', 'form_12.form_12_id')
            ->select(
                'pk_progress.*',
                'form_1.status as form_1_status',
                'form_2.status as form_2_status',
                'form_3.status as form_3_status',
                // 'form_4.status as form_4_status',
                // 'form_5.status as form_5_status',
                // 'form_6.status as form_6_status',
                // 'form_7.status as form_7_status',
                // 'form_8.status as form_8_status',
                // 'form_9.status as form_9_status',
                // 'form_10.status as form_10_status',
                // 'form_11.status as form_11_status',
                // 'form_12.status as form_12_status'
            )
            ->first();

        if (!$progress) {
            return [
                'message' => 'Data progress tidak ditemukan untuk user ini.',
                'status' => false
            ];
        }

        return [
            'status' => true,
            'user_id' => $userId,
            'form_statuses' => [
                'form_1' => $progress->form_1_status ?? 'Belum Diisi',
                'form_2' => $progress->form_2_status ?? 'Belum Diisi',
                'form_3' => $progress->form_3_status ?? 'Belum Diisi',
                // 'form_4' => $progress->form_4_status ?? 'Belum Diisi',
                // 'form_5' => $progress->form_5_status ?? 'Belum Diisi',
                // 'form_6' => $progress->form_6_status ?? 'Belum Diisi',
                // 'form_7' => $progress->form_7_status ?? 'Belum Diisi',
                // 'form_8' => $progress->form_8_status ?? 'Belum Diisi',
                // 'form_9' => $progress->form_9_status ?? 'Belum Diisi',
                // 'form_10' => $progress->form_10_status ?? 'Belum Diisi',
                // 'form_11' => $progress->form_11_status ?? 'Belum Diisi',
                // 'form_12' => $progress->form_12_status ?? 'Belum Diisi',
            ]
        ];
    }


}
