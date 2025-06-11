<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Factory as Validator;
use App\Models\TranskripModel;
use Tymon\JWTAuth\Facades\JWTAuth;  
use Illuminate\Support\Facades\Log;
use App\Models\DaftarUser;


class SpkController extends Controller
{     
    protected $validator;

    /**
     * Constructor to initialize validator.
     *
     * @param \Illuminate\Validation\Factory $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

/**
 * @OA\Post(
 *     path="/upload-spk",
 *     summary="Upload File SPK dan Simpan Detail ke Database",
 *     description="Mengunggah file SPK ke storage dan menyimpan detailnya di database.",
 *     operationId="uploadFileSPK",
 *     tags={"Upload SPK"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"path_file", "nomor_spk", "masa_berlaku_spk"},
 *                 @OA\Property(
 *                     property="path_file",
 *                     type="string",
 *                     format="binary",
 *                     description="File yang ingin diunggah (PDF, JPG, JPEG, PNG, max 2MB)."
 *                 ),
 *                 @OA\Property(
 *                     property="nomor_spk",
 *                     type="string",
 *                     description="Nomor SPK yang diunggah",
 *                     example="SPK-202400123"
 *                 ),
 *                 @OA\Property(
 *                     property="masa_berlaku_spk",
 *                     type="string",
 *                     format="date",
 *                     description="Tanggal masa berlaku SPK",
 *                     example="2026-12-31"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File berhasil diunggah.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="File uploaded successfully."),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="spk_id", type="integer", example=3),
 *                 @OA\Property(property="user_id", type="integer", example=11),
 *                 @OA\Property(property="nomor_spk", type="string", example="SPK-202400123"),
 *                 @OA\Property(property="masa_berlaku_spk", type="string", example="2026-12-31"),
 *                 @OA\Property(property="path_file", type="string", example="http://app.rsimmanuel.net/storage/Spk/1641165725_file.pdf"),
 *                 @OA\Property(property="valid", type="boolean", example=null),
 *                 @OA\Property(property="authentic", type="boolean", example=null),
 *                 @OA\Property(property="current", type="boolean", example=null),
 *                 @OA\Property(property="sufficient", type="boolean", example=null),
 *                 @OA\Property(property="ket", type="string", example="null")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal atau tidak ada file yang diunggah.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Tidak ada file yang diunggah. Harap unggah file yang valid."),
 *             @OA\Property(property="status_code", type="integer", example=400)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan tak terduga dari server.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan tak terduga di server."),
 *             @OA\Property(property="error", type="string", example="Detail pesan kesalahan dari server."),
 *             @OA\Property(property="status_code", type="integer", example=500)
 *         )
 *     )
 * )
 */


   public function upload(Request $request)
	{
		// Validasi permintaan yang masuk    
		$validation = $this->validator->make($request->all(), [    
			'path_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
			'nomor_spk' => 'required|string',
			'masa_berlaku_spk' => 'required|date',    
			'valid' => 'nullable|boolean',    
			'authentic' => 'nullable|boolean',    
			'current' => 'nullable|boolean',    
			'sufficient' => 'nullable|boolean',    
			'ket' => 'nullable|string|max:255',    
		]);  

		if ($validation->fails()) {    
			return response()->json([    
				'success' => false,    
				'message' => 'Validation failed. Please ensure all data entered is correct.',
				'errors' => $validation->errors(),    
				'status_code' => 400,    
			], 400);    
		}  

		try {    
			// Ambil pengguna dari token JWT    
			$user = JWTAuth::parseToken()->authenticate();    

			if (!$user) {
				Log::error('User not found with the provided token.');
				return response()->json([
					'success' => false,
					'message' => 'User not found.',
					'status_code' => 404
				], 404);
			}

			$filePath = null;

			if ($request->hasFile('path_file')) {
				$file = $request->file('path_file');
				$fileName = time() . '_' . $file->getClientOriginalName();
				$filePath = $file->storeAs('Spk', $fileName, 'public');
			}

			$existingFile = TranskripModel::where('user_id', $user->user_id)->first();

			if ($existingFile) {
				if ($filePath && $existingFile->path_file && Storage::exists($existingFile->path_file)) {
					Storage::delete($existingFile->path_file);
				}

				$existingFile->update([
					'path_file' => $filePath ?? $existingFile->path_file,
					'nomor_spk' => $request->input('nomor_spk', null),
					'masa_berlaku_spk' => $request->input('masa_berlaku_spk', null),
					'valid' => $request->input('valid', null),
					'authentic' => $request->input('authentic', null),
					'current' => $request->input('current', null),
					'sufficient' => $request->input('sufficient', null),
					'ket' => $request->input('ket', null),
				]);

				return response()->json([
					'success' => true,
					'message' => 'File updated successfully.',
					'data' => [
						'spk_id' => $existingFile->spk_id,
						'user_id' => $user->user_id,
						'file_path' => $existingFile->path_file,
						'nomor_spk' => $existingFile->nomor_spk,
						'masa_berlaku_spk' => $existingFile->masa_berlaku_spk,
					],
					'status_code' => 200
				], 200);
			}

			// Jika data baru
			$newFile = TranskripModel::create([
				'path_file' => $filePath,
				'user_id' => $user->user_id,
				'nomor_spk' => $request->input('nomor_spk', null),
				'masa_berlaku_spk' => $request->input('masa_berlaku_spk', null),
				'valid' => $request->input('valid', null),
				'authentic' => $request->input('authentic', null),
				'current' => $request->input('current', null),
				'sufficient' => $request->input('sufficient', null),
				'ket' => $request->input('ket', null),
			]);

			return response()->json([
				'success' => true,
				'message' => 'File uploaded successfully.',
				'data' => [
					'spk_id' => $newFile->spk_id,
					'user_id' => $user->user_id,
					'file_path' => $newFile->path_file,
					'nomor_spk' => $newFile->nomor_spk,
					'masa_berlaku_spk' => $newFile->masa_berlaku_spk,
				],
				'status_code' => 201
			], 201);

		} catch (\Exception $e) {
			Log::error('File upload error: ' . $e->getMessage());

			return response()->json([
				'success' => false,
				'message' => 'An unexpected server error occurred.',
				'error' => $e->getMessage(),
				'status_code' => 500
			], 500);
		}
	}


    /**
     * Fetch uploaded file details (example for 404 handling).
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchFile($id)
    {
        try {
            $file = TranskripModel::find($id);

            if (!$file) {
                return response()->json([
                    'success' => false,
					'message' => 'File not found. Please check the ID and try again.',
                    'status_code' => 404,
                ], 404);
            }

            // Generate file URL
            $fileUrl = Storage::url($file->path_file);

            // Return file details
            return response()->json([
                'success' => true,
                'message' => 'File details retrieved successfully.',
                'data' => [
                    'file_url' => $fileUrl,
                    'valid' => $file->valid,
                    'authentic' => $file->authentic,
                    'current' => $file->current,
                    'sufficient' => $file->sufficient,
                    'keterangan' => $file->ket,
                ],
                'status_code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
				'message' => 'An unexpected error occurred while retrieving file details.',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ], 500);
        }
    }
	
	
	/**
 * @OA\Put(
 *     path="/update-spk/{nik}",
 *     summary="Update Penilaian SPK dari Asesor ke Asesi dengan nik",
 *     description="Memperbarui detail Penilaian file SPK berdasarkan nik yang diberikan.",
 *     operationId="updateFileDetailsSPK",
 *     tags={"Asesor"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="NIK file yang ingin diperbarui.",
 *         @OA\Schema(type="string", example="12345678")
 *     ),
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="valid",
 *                     type="boolean",
 *                     description="Status validasi file (opsional)."
 *                 ),
 *                 @OA\Property(
 *                     property="authentic",
 *                     type="boolean",
 *                     description="Status keaslian file (opsional)."
 *                 ),
 *                 @OA\Property(
 *                     property="current",
 *                     type="boolean",
 *                     description="Status keaktualan file (opsional)."
 *                 ),
 *                 @OA\Property(
 *                     property="sufficient",
 *                     type="boolean",
 *                     description="Status kecukupan file (opsional)."
 *                 ),
 *                 @OA\Property(
 *                     property="ket",
 *                     type="string",
 *                     maxLength=255,
 *                     description="Keterangan file (opsional)."
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Detail file berhasil diperbarui.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="File details updated successfully."),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="path_file", type="string", example="http://app.rsimmanuel.net:9091/storage/Spk/1641165725_file.pdf"),
 *                 @OA\Property(property="valid", type="boolean", example=true),
 *                 @OA\Property(property="authentic", type="boolean", example=true),
 *                 @OA\Property(property="current", type="boolean", example=true),
 *                 @OA\Property(property="sufficient", type="boolean", example=true),
 *                 @OA\Property(property="ket", type="string", example="Dokumen lengkap.")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal atau ID tidak valid.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal. Pastikan semua bidang diisi dengan benar."),
 *             @OA\Property(property="errors", type="object", description="Error details"),
 *             @OA\Property(property="status_code", type="integer", example=400)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="File tidak ditemukan.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="File tidak ditemukan. Silakan periksa ID dan coba lagi."),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan tak terduga dari server.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan tak terduga saat memperbarui detail file."),
 *             @OA\Property(property="error", type="string", example="Detail pesan kesalahan dari server."),
 *             @OA\Property(property="status_code", type="integer", example=500)
 *         )
 *     )
 * )
 */


    public function updateFile(Request $request, $nik)
    {
        // Validasi input
        $validation = $this->validator->make($request->all(), [
            'valid' => 'nullable|boolean',     
            'authentic' => 'nullable|boolean', 
            'current' => 'nullable|boolean',   
            'sufficient' => 'nullable|boolean', 
            'ket' => 'nullable|string|max:255', 
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please ensure all fields are filled correctly.',
                'errors' => $validation->errors(),
                'status_code' => 400,
            ], 400);
        }

        try {
            // Cari user berdasarkan NIK
            $user = DaftarUser::where('nik', $nik)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found. Please check the NIK and try again.',
                    'status_code' => 404,
                ], 404);
            }

            // Cari file berdasarkan user_id
            $SpkFile = TranskripModel::where('user_id', $user->user_id)->first();
            if (!$SpkFile) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found. Please check the user NIK and try again.',
                    'status_code' => 404,
                ], 404);
            }

            // Update nilai untuk `valid`, `authentic`, `current`, `sufficient`
            $fields = ['valid', 'authentic', 'current', 'sufficient'];
            foreach ($fields as $field) {
                if ($request->has($field)) {
                    $SpkFile->$field = $request->input($field) ? 1 : 0; // true = 1, false = 0 
                }
            }

            // Update nilai `ket` jika ada
            if ($request->has('ket')) {
                $SpkFile->ket = $request->input('ket');
            }

            // Simpan perubahan
            $SpkFile->save();

            return response()->json([
                'success' => true,
                'message' => 'File details updated successfully.',
                'data' => [
                    'user_id' => $user->user_id,
                    'spk_id' => $spkFile->str_id,
                    'path_file' => Storage::url($SpkFile->path_file),
                    'valid' => $SpkFile->valid,
                    'authentic' => $SpkFile->authentic,
                    'current' => $SpkFile->current,
                    'sufficient' => $SpkFile->sufficient,
                    'ket' => $SpkFile->ket,
                ],
                'status_code' => 200,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while updating file details.',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ], 500);
        }
    }
	

    /**
 * @OA\Delete(
 *     path="/spk/file/{nik}",
 *     summary="Hapus File Spk berdasarkan NIK",
 *     description="Menghapus file berdasarkan NIK. File akan dihapus dari penyimpanan dan catatan terkait di database.",
 *     operationId="deleteFileSpk",
 *     tags={"Upload Spk"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="NIK file yang ingin dihapus.",
 *         @OA\Schema(type="string", example="12345678")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil menghapus file.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="File berhasil dihapus dari sistem."),
 *             @OA\Property(
 *                 property="details",
 *                 type="object",
 *                 @OA\Property(property="spk_id", type="integer", example=3),
 *                 @OA\Property(property="file_path", type="string", example="Spk/1641165725_file.pdf"),
 *                 @OA\Property(property="storage_status", type="string", example="File dihapus dari storage."),
 *                 @OA\Property(property="database_status", type="string", example="Rekaman file telah dihapus dari database.")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="ID tidak valid.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="ID tidak valid. Mohon masukkan ID berupa angka positif."),
 *             @OA\Property(
 *                 property="details",
 *                 type="object",
 *                 @OA\Property(property="id", type="string", example="abc"),
 *                 @OA\Property(property="reason", type="string", example="ID tidak memenuhi kriteria yang diperlukan.")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=400)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="File tidak ditemukan.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="File tidak ditemukan. Mohon periksa ID dan coba lagi."),
 *             @OA\Property(
 *                 property="details",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=999),
 *                 @OA\Property(property="reason", type="string", example="File dengan ID ini tidak ada dalam sistem.")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server saat mencoba menghapus file.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan tak terduga saat mencoba menghapus file."),
 *             @OA\Property(
 *                 property="error_details",
 *                 type="object",
 *                 @OA\Property(property="error_message", type="string", example="Detail error."),
 *                 @OA\Property(property="suggestion", type="string", example="Mohon periksa log server untuk informasi lebih lanjut.")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=500)
 *         )
 *     )
 * )
 */

    public function deleteFile($nik)
    {
        // Cari user berdasarkan NIK
        $user = DaftarUser::where('nik', $nik)->first();
    
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found. Please check the NIK and try again.',
                'details' => [
                    'nik' => $nik,
                    'reason' => 'No user associated with this NIK was found in the system.'
                ],
                'status_code' => 404
            ], 404);
        }
    
        // Cari file berdasarkan user_id
        $file = TranskripModel::where('user_id', $user->user_id)->first();
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found. Please check the user NIK and try again.',
                'details' => [
                    'reason' => 'No file associated with this user was found in the system.'
                ],
                'status_code' => 404
            ], 404);
        }
    
        // Hapus file dari storage jika ada
        $fileDeletedFromStorage = false;
        if ($file->path_file && Storage::exists($file->path_file)) {
            Storage::delete($file->path_file);
            $fileDeletedFromStorage = true;
        }
    
        // Hapus Rekaman Database
        $file->update([
            'path_file' => null,
            'valid' => null,
            'authentic' => null,
            'current' => null,
            'sufficient' => null,
            'ket' => null,
            'nomor_str' => null,
            'masa_berlaku_str' => null,
        ]);
    
        // Response sukses dengan user_id & ijazah_id
        return response()->json([
            'success' => true,
            'message' => 'File successfully deleted from the system.',
            'details' => [
                'spk_id' => $file->spk_id, // Menampilkan ID file ijazah
                'user_id' => $user->user_id,
                'file_path' => $file->path_file,
                'storage_status' => $fileDeletedFromStorage 
                    ? 'File deleted from storage.' 
                    : 'File not found in storage.',
                'database_status' => 'File record has been deleted from the database.',
            ],
            'status_code' => 200
        ], 200);
    }
 
	
	/**
 * @OA\Get(
 *     path="/storage/Spk/{path}",
 *     summary="Memperlihatkan file Spk",
 *     description="Endpoint ini digunakan untuk memperlihatkan file Spk berdasarkan path yang diberikan.",
 *     operationId="getSpk",
 *     tags={"Upload Spk"},
 *     @OA\Parameter(
 *         name="path",
 *         in="path",
 *         required=true,
 *         description="Path file Spk yang ingin diperlihatkan.",
 *         @OA\Schema(
 *             type="string", example="1641165725_file.pdf" 
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Gambar Atau File Akan Ditampilkan.",
 *         @OA\MediaType(
 *             mediaType="application/pdf",
 *             @OA\Schema(
 *                 type="string",
 *                 format="binary"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="File tidak ditemukan.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="File tidak ditemukan.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan server.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Terjadi kesalahan pada server.")
 *         )
 *     )
 * )
 */

    public function getTranskrip($path)
    {
        // Logika untuk mengunduh file ijazah
        return response()->file(storage_path('app/public/Spk/' . $path));
    }


    /**
 * @OA\Get(
 *     path="/get-no-expired-spk/{nik}",
 *     summary="Get Nomor SPK dan Masa Berlaku berdasarkan NIK",
 *     description="Mengambil nomor SPK dan masa berlaku berdasarkan NIK yang diberikan.",
 *     operationId="getSpkByNik",
 *     tags={"Upload Spk"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="Nomor Induk Kependudukan (NIK) pengguna",
 *         @OA\Schema(type="string", example="1234567890123456")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data SPK ditemukan.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="nomor_spk", type="string", example="SPK-202400123"),
 *                 @OA\Property(property="masa_berlaku_spk", type="string", example="2026-12-31")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data SPK tidak ditemukan."),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     )
 * )
 */
    public function getSpkByNik($nik)
    {
        try {
            // Cari user berdasarkan NIK
            $user = DaftarUser::where('nik', $nik)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User Not Found.',
                    'status_code' => 404
                ], 404);
            }

            // Cari data SPK berdasarkan user_id
            $spk = TranskripModel::where('user_id', $user->user_id)->first();

            if (!$spk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data SPK Not Found.',
                    'status_code' => 404
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'nomor_spk' => $spk->nomor_spk,
                    'masa_berlaku_spk' => $spk->masa_berlaku_spk
                ],
                'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching SPK data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }


}
