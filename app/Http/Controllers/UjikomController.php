<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Factory as Validator;
use App\Models\UjikomModel;
use Tymon\JWTAuth\Facades\JWTAuth;  
use Illuminate\Support\Facades\Log;
use App\Models\DaftarUser;

class UjikomController extends Controller
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
 *     path="/upload-ujikom",
 *     summary="Upload File Ujikom",
 *     description="Mengunggah file Ujikom dan menyimpan detailnya ke database.",
 *     operationId="uploadFileUjikom",
 *     tags={"Upload Ujikom"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"path_file", "nomor_kompetensi", "masa_berlaku_kompetensi"},
 *                 @OA\Property(
 *                     property="path_file",
 *                     type="string",
 *                     format="binary",
 *                     description="File yang akan diunggah (PDF, JPG, JPEG, PNG, maksimal 2MB)."
 *                 ),
 *                 @OA\Property(
 *                     property="nomor_kompetensi",
 *                     type="string",
 *                     description="Nomor sertifikat kompetensi yang diunggah.",
 *                     example="KOMP-202500789"
 *                 ),
 *                 @OA\Property(
 *                     property="masa_berlaku_kompetensi",
 *                     type="string",
 *                     format="date",
 *                     description="Tanggal masa berlaku sertifikat kompetensi.",
 *                     example="2027-05-30"
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
 *                 @OA\Property(property="ujikom_id", type="integer", example=4, description="ID file Ujikom yang tersimpan."),
 *                 @OA\Property(property="user_id", type="integer", example=11, description="ID pengguna yang mengunggah."),
 *                 @OA\Property(property="nomor_kompetensi", type="string", example="KOMP-202500789"),
 *                 @OA\Property(property="masa_berlaku_kompetensi", type="string", example="2027-05-30"),
 *                 @OA\Property(property="path_file", type="string", example="http://app.rsimmanuel.net:9091/storage/Ujikom/file.pdf"),
 *                 @OA\Property(property="valid", type="boolean", example=null),
 *                 @OA\Property(property="authentic", type="boolean", example=null),
 *                 @OA\Property(property="current", type="boolean", example=null),
 *                 @OA\Property(property="sufficient", type="boolean", example=null),
 *                 @OA\Property(property="ket", type="string", example="null")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal atau file tidak ditemukan.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal. Pastikan file di Upload."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 description="Detail kesalahan validasi."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan tak terduga di server.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan tak terduga di server."),
 *             @OA\Property(property="error", type="string", example="Detail kesalahan server.")
 *         )
 *     )
 * )
 */



  public function upload(Request $request)
	{
		// Validasi permintaan yang masuk
		$validation = $this->validator->make($request->all(), [
			'path_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
			'nomor_kompetensi' => 'required|string',
			'masa_berlaku_kompetensi' => 'required|date',
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
			$user = JWTAuth::parseToken()->authenticate();

			if (!$user) {
				Log::error('User not found with the provided token.');
				return response()->json([
					'success' => false,
					'message' => 'User not found.',
					'status_code' => 404
				], 404);
			}

			$existingFile = UjikomModel::where('user_id', $user->user_id)->first();

			// Proses file jika ada
			$filePath = null;
			if ($request->hasFile('path_file')) {
				$file = $request->file('path_file');
				$fileName = time() . '_' . $file->getClientOriginalName();
				$filePath = $file->storeAs('Ujikom', $fileName, 'public');
			}

			// Jika ada data sebelumnya
			if ($existingFile) {
				// Hapus file lama jika ada file baru
				if ($filePath && $existingFile->path_file && Storage::exists($existingFile->path_file)) {
					Storage::delete($existingFile->path_file);
				}

				// Data yang akan diupdate
				$updateData = [
					'nomor_kompetensi' => $request->input('nomor_kompetensi'),
					'masa_berlaku_kompetensi' => $request->input('masa_berlaku_kompetensi'),
					'valid' => $request->input('valid'),
					'authentic' => $request->input('authentic'),
					'current' => $request->input('current'),
					'sufficient' => $request->input('sufficient'),
					'ket' => $request->input('ket'),
				];

				if ($filePath) {
					$updateData['path_file'] = $filePath;
				}

				$existingFile->update($updateData);

				return response()->json([
					'success' => true,
					'message' => 'File updated successfully.',
					'data' => [
						'ujikom_id' => $existingFile->ujikom_id,
						'user_id' => $user->user_id,
						'file_path' => $existingFile->path_file,
						'nomor_kompetensi' => $existingFile->nomor_kompetensi,
						'masa_berlaku_kompetensi' => $existingFile->masa_berlaku_kompetensi,
					],
					'status_code' => 200
				], 200);
			}

			// Data baru
			$createData = [
				'user_id' => $user->user_id,
				'nomor_kompetensi' => $request->input('nomor_kompetensi'),
				'masa_berlaku_kompetensi' => $request->input('masa_berlaku_kompetensi'),
				'valid' => $request->input('valid'),
				'authentic' => $request->input('authentic'),
				'current' => $request->input('current'),
				'sufficient' => $request->input('sufficient'),
				'ket' => $request->input('ket'),
			];

			if ($filePath) {
				$createData['path_file'] = $filePath;
			}

			$newFile = UjikomModel::create($createData);

			return response()->json([
				'success' => true,
				'message' => 'File uploaded successfully.',
				'data' => [
					'ujikom_id' => $newFile->ujikom_id,
					'user_id' => $user->user_id,
					'file_path' => $newFile->path_file,
					'nomor_kompetensi' => $newFile->nomor_kompetensi,
					'masa_berlaku_kompetensi' => $newFile->masa_berlaku_kompetensi,
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
            $file = UjikomModel::find($id);

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
 *     path="/update-ujikom/{nik}",
 *     summary="Update File Penilaian dari Asesor ke Asesi berdasarkan NIK",
 *     description="Memperbarui detail file Penilaian berdasarkan NIK.",
 *     operationId="updateFileDetailsUjikom",
 *     tags={"Asesor"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="NIK file yang akan diperbarui.",
 *         @OA\Schema(type="string", example="12345678")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="valid", type="boolean", nullable=true, description="Apakah file valid (opsional)."),
 *             @OA\Property(property="authentic", type="boolean", nullable=true, description="Apakah file autentik (opsional)."),
 *             @OA\Property(property="current", type="boolean", nullable=true, description="Apakah file saat ini berlaku (opsional)."),
 *             @OA\Property(property="sufficient", type="boolean", nullable=true, description="Apakah file mencukupi (opsional)."),
 *             @OA\Property(property="ket", type="string", nullable=true, description="Keterangan tambahan terkait file (opsional).")
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
 *                 @OA\Property(property="path_file", type="string", example="http://app.rsimmanuel.net:9091/storage/Ujikom/file.pdf"),
 *                 @OA\Property(property="valid", type="boolean", example=true),
 *                 @OA\Property(property="authentic", type="boolean", example=true),
 *                 @OA\Property(property="current", type="boolean", example=false),
 *                 @OA\Property(property="sufficient", type="boolean", example=true),
 *                 @OA\Property(property="ket", type="string", example="Keterangan diperbarui.")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal. Pastikan semua bidang diisi dengan benar."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 description="Detail kesalahan validasi."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="File tidak ditemukan.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="File tidak ditemukan. Silakan periksa ID dan coba lagi.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan tak terduga di server.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan tak terduga saat memperbarui detail file."),
 *             @OA\Property(property="error", type="string", example="Detail kesalahan server.")
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
            $UjikomFile = UjikomModel::where('user_id', $user->user_id)->first();
            if (!$UjikomFile) {
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
                    $UjikomFile->$field = $request->input($field) ? 1 : 0; // true = 1, false = 0 
                }
            }

            // Update nilai `ket` jika ada
            if ($request->has('ket')) {
                $UjikomFile->ket = $request->input('ket');
            }

            // Simpan perubahan
            $UjikomFile->save();

            return response()->json([
                'success' => true,
                'message' => 'File details updated successfully.',
                'data' => [
                    'user_id' => $user->user_id,
                    'ujikom_id' => $UjikomFile->ujikom_id,
                    'path_file' => Storage::url($UjikomFile->path_file),
                    'valid' => $UjikomFile->valid,
                    'authentic' => $UjikomFile->authentic,
                    'current' => $UjikomFile->current,
                    'sufficient' => $UjikomFile->sufficient,
                    'ket' => $UjikomFile->ket,
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
 *     path="/ujikom/file/{nik}",
 *     summary="Delete File Ujikom berdasarkan NIK",
 *     description="Menghapus file Ujikom dari sistem berdasarkan NIK yang diberikan.",
 *     operationId="deleteFileUjikom",
 *     tags={"Upload Ujikom"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="NIK file yang ingin dihapus.",
 *         @OA\Schema(type="string", example="12345678")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File berhasil dihapus.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="File berhasil dihapus dari sistem."),
 *             @OA\Property(
 *                 property="details",
 *                 type="object",
 *                 @OA\Property(property="ujikom_id", type="integer", example=4),
 *                 @OA\Property(property="file_path", type="string", example="Ujikom/file123.pdf"),
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
 *                 @OA\Property(property="id", type="integer", example=-5),
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
 *                 @OA\Property(property="id", type="integer", example=123),
 *                 @OA\Property(property="reason", type="string", example="File dengan ID ini tidak ada dalam sistem.")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server tak terduga.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan tak terduga saat mencoba menghapus file."),
 *             @OA\Property(
 *                 property="error_details",
 *                 type="object",
 *                 @OA\Property(property="error_message", type="string", example="SQLSTATE[HY000]: General error: 1364 Field doesn't have a default value."),
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
        $file = UjikomModel::where('user_id', $user->user_id)->first();
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
            'nomor_kompetensi' => null,
            'masa_berlaku_kompetensi' => null,
        ]);
    
        // Response sukses dengan user_id & ijazah_id
        return response()->json([
            'success' => true,
            'message' => 'File successfully deleted from the system.',
            'details' => [
                'ujikom_id' => $file->ujikom_id, // Menampilkan ID file ijazah
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
 *     path="/storage/Ujikom/{path}",
 *     summary="Memperlihatkan file Ujikom",
 *     description="Endpoint ini digunakan untuk memperlihatkan file Ujikom berdasarkan path yang diberikan.",
 *     operationId="getUjikomFilePath",
 *     tags={"Upload Ujikom"},
 *     @OA\Parameter(
 *         name="path",
 *         in="path",
 *         required=true,
 *         description="Path file Ujikom yang ingin diperlihatkan.",
 *         @OA\Schema(
 *             type="string", example="file123.pdf" 
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

    public function getUjikom($path)
    {
        // Logika untuk mengunduh file ijazah
        return response()->file(storage_path('app/public/Ujikom/' . $path));
    }



/**
     * @OA\Get(
     *     path="/get-no-expired-ujikom/{nik}",
     *     summary="Ambil Data Nomor dan Masa Berlaku Ujikom berdasarkan NIK",
     *     description="Mengambil data nomor Ujikom dan masa berlaku berdasarkan NIK user.",
     *     operationId="getUjikomByNik",
     *     tags={"Upload Ujikom"},
     *     @OA\Parameter(
     *         name="nik",
     *         in="path",
     *         required=true,
     *         description="NIK dari user yang ingin diambil datanya",
     *         @OA\Schema(type="string", example="3201234567890001")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data Ujikom ditemukan.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ujikom data found."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="nomor_str", type="string", example="Kom-987654321"),
     *                 @OA\Property(property="masa_berlaku", type="string", example="2027-06-30")
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User atau Ujikom tidak ditemukan.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User or Ujikom not found."),
     *             @OA\Property(property="status_code", type="integer", example=404)
     *         )
     *     )
     * )
     */
    public function getUjikomByNik($nik)
    {
        // Cari user berdasarkan NIK
        $user = DaftarUser::where('nik', $nik)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'status_code' => 404
            ], 404);
        }

        // Cari data STR berdasarkan user_id
        $str = UjikomModel::where('user_id', $user->user_id)->first();

        if (!$str) {
            return response()->json([
                'success' => false,
                'message' => 'Kompetensi not found for this user',
                'status_code' => 404
            ], 404);
        }

        // Mengembalikan data dalam bentuk JSON
        return response()->json([
            'success' => true,
            'message' => 'Kompetensi data found',
            'data' => [
                'nomor_kompetensi' => $str->nomor_kompetensi,
                'masa_berlaku_kompetensi' => $str->masa_berlaku_kompetensi
            ],
            'status_code' => 200
        ], 200);
    }




}
