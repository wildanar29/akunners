<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Factory as Validator;
use App\Models\SipModel;
use Tymon\JWTAuth\Facades\JWTAuth;  
use Illuminate\Support\Facades\Log;  
use App\Models\DaftarUser;

class SipController extends Controller
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
 *     path="/upload-sip",
 *     summary="Upload File Sip Untuk Asesi",
 *     description="Mengunggah file SIP dan menyimpan detailnya ke database.",
 *     operationId="UploadFileSip",
 *     tags={"Upload Sip"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"path_file", "nomor_sip", "masa_berlaku"},
 *                 @OA\Property(
 *                     property="path_file",
 *                     type="string",
 *                     format="binary",
 *                     description="File yang akan diunggah (pdf, jpg, jpeg, png). Maksimal ukuran 2MB."
 *                 ),
 *                 @OA\Property(
 *                     property="nomor_sip",
 *                     type="string",
 *                     description="Nomor SIP yang diunggah",
 *                     example="SIP-123456789"
 *                 ),
 *                 @OA\Property(
 *                     property="masa_berlaku_sip",
 *                     type="string",
 *                     format="date",
 *                     description="Tanggal masa berlaku SIP",
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
 *                 @OA\Property(property="sip_id", type="int", example=7),
 *                 @OA\Property(property="user_id", type="int", example=11),
 *                 @OA\Property(property="nomor_sip", type="string", example="SIP-123456789"),
 *                 @OA\Property(property="masa_berlaku_sip", type="string", example="2026-12-31"),
 *                 @OA\Property(property="path_file", type="string", example="http://app.rsimmanuel.net:9091/storage/Sip/1676985600_file.pdf"),
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
 *         description="Validasi gagal atau file tidak ada.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal. Pastikan semua data yang diisi sudah sesuai."),
 *             @OA\Property(property="errors", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=400)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan di server.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan tak terduga di server."),
 *             @OA\Property(property="error", type="string", example="Detail pesan error."),
 *             @OA\Property(property="status_code", type="integer", example=500)
 *         )
 *     )
 * )
 */


   public function upload(Request $request)
	{
		$validation = $this->validator->make($request->all(), [    
			'path_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
			'nomor_sip' => 'required|string',
			'masa_berlaku_sip' =>'required|date',
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

			$existingFile = SipModel::where('user_id', $user->user_id)->first();

			$filePath = null;

			if ($request->hasFile('path_file')) {
				$file = $request->file('path_file');    
				$fileName = time() . '_' . $file->getClientOriginalName();    
				$filePath = $file->storeAs('Sip', $fileName, 'public');
			}

			if ($existingFile) {
				if ($filePath && $existingFile->path_file && Storage::exists($existingFile->path_file)) {
					Storage::delete($existingFile->path_file);
				}

				$existingFile->update([
					'path_file' => $filePath ?? $existingFile->path_file,
					'nomor_sip' => $request->input('nomor_sip'),
					'masa_berlaku_sip' => $request->input('masa_berlaku_sip'),
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
						'sip_id' => $existingFile->sip_id,
						'user_id' => $user->user_id,
						'file_path' => $existingFile->path_file,
						'nomor_sip' => $existingFile->nomor_sip,
						'masa_berlaku_sip' => $existingFile->masa_berlaku_sip,
					],
					'status_code' => 200
				], 200);
			}

			$newFile = SipModel::create([
				'path_file' => $filePath,
				'user_id' => $user->user_id,
				'nomor_sip' => $request->input('nomor_sip'),
				'masa_berlaku_sip' => $request->input('masa_berlaku_sip'),
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
					'sip_id' => $newFile->sip_id,
					'user_id' => $user->user_id,
					'file_path' => $newFile->path_file,
					'nomor_sip' => $newFile->nomor_sip,
					'masa_berlaku_sip' => $newFile->masa_berlaku_sip,
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
            $file = SipModel::find($id);

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
 *     path="/update-sip/{nik}",
 *     summary="Update File Sip Penilaian dari Asesor Terhadap Asesi",
 *     description="Memperbarui detail file berdasarkan NIK.",
 *     operationId="updateFileSip",
 *     tags={"Asesor"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="NIK dari file yang akan diperbarui.",
 *         @OA\Schema(type="string", example=12345678)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="valid", type="boolean", description="Opsional. Menandai apakah file valid."),
 *             @OA\Property(property="authentic", type="boolean", description="Opsional. Menandai apakah file autentik."),
 *             @OA\Property(property="current", type="boolean", description="Opsional. Menandai apakah file terkini."),
 *             @OA\Property(property="sufficient", type="boolean", description="Opsional. Menandai apakah file memadai."),
 *             @OA\Property(
 *                 property="ket",
 *                 type="string",
 *                 maxLength=255,
 *                 description="Opsional. Keterangan tambahan untuk file."
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
 *                 @OA\Property(property="path_file", type="string", example="/storage/Sip/1676985600_file.pdf"),
 *                 @OA\Property(property="valid", type="boolean", example=true),
 *                 @OA\Property(property="authentic", type="boolean", example=false),
 *                 @OA\Property(property="current", type="boolean", example=true),
 *                 @OA\Property(property="sufficient", type="boolean", example=false),
 *                 @OA\Property(property="ket", type="string", example="Keterangan tambahan.")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal. Pastikan semua bidang diisi dengan benar."),
 *             @OA\Property(property="errors", type="object"),
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
 *         description="Kesalahan tak terduga di server.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan tak terduga saat memperbarui detail file."),
 *             @OA\Property(property="error", type="string", example="Detail pesan error."),
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
            $SipFile = SipModel::where('user_id', $user->user_id)->first();
            if (!$SipFile) {
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
                    $SipFile->$field = $request->input($field) ? 1 : 0; // true = 1, false = 0 
                }
            }
    
            // Update nilai `ket` jika ada
            if ($request->has('ket')) {
                $SipFile->ket = $request->input('ket');
            }
    
            // Simpan perubahan
            $SipFile->save();
    
            return response()->json([
                'success' => true,
                'message' => 'File details updated successfully.',
                'data' => [
                    'user_id' => $user->user_id,
                    'sip_id' => $SipFile->sip_id,
                    'path_file' => Storage::url($SipFile->path_file),
                    'valid' => $SipFile->valid,
                    'authentic' => $SipFile->authentic,
                    'current' => $SipFile->current,
                    'sufficient' => $SipFile->sufficient,
                    'ket' => $SipFile->ket,
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
 *     path="/sip/file/{nik}",
 *     summary="Menghapus atau Menggagalkan Upload File Sip dengan NIK",
 *     description="Menghapus file dan detailnya dari sistem berdasarkan NIK.",
 *     operationId="deleteFileSip",
 *     tags={"Upload Sip"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="NIK file yang akan dihapus.",
 *         @OA\Schema(type="string", example="12345678")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File berhasil dihapus dari sistem.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="File berhasil dihapus dari sistem."),
 *             @OA\Property(
 *                 property="details",
 *                 type="object",
 *                 @OA\Property(property="sip_id", type="integer", example=7),
 *                 @OA\Property(property="file_path", type="string", example="Sip/1676985600_file.pdf"),
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
 *                 @OA\Property(property="id", type="integer", example=-1),
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
 *                 @OA\Property(property="id", type="integer", example=1),
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
 *                 @OA\Property(property="error_message", type="string", example="Detail pesan kesalahan."),
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
        $file = SipModel::where('user_id', $user->user_id)->first();
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
            'nomor_sip' => null,
            'masa_berlaku_sip' => null
        ]);
    
        // Response sukses dengan user_id & ijazah_id
        return response()->json([
            'success' => true,
            'message' => 'File successfully deleted from the system.',
            'details' => [
                'sip_id' => $file->sip_id, // Menampilkan ID file ijazah
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
 *     path="/storage/Sip/{path}",
 *     summary="Memperlihatkan file Sip",
 *     description="Endpoint ini digunakan untuk memperlihatkan file Sip berdasarkan path yang diberikan.",
 *     operationId="getSip",
 *     tags={"Upload Sip"},
 *     @OA\Parameter(
 *         name="path",
 *         in="path",
 *         required=true,
 *         description="Path file Sip yang ingin diperlihatkan.",
 *         @OA\Schema(
 *             type="string", example="1676985600_file.pdf" 
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

    public function getSip($path)
    {
        // Logika untuk mengunduh file ijazah
        return response()->file(storage_path('app/public/Sip/' . $path));
    }


/**
 * @OA\Get(
 *     path="/get-no-expired-sipp/{nik}",
 *     summary="Mendapatkan data Nomor dan Masa Berlaku SIP berdasarkan NIK",
 *     description="Mengambil nomor dan masa berlaku SIP berdasarkan NIK pengguna.",
 *     operationId="getSipByNik",
 *     tags={"Upload Sip"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="NIK pengguna untuk mengambil data SIP",
 *         @OA\Schema(type="string", example="1234567890123456")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data SIP ditemukan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="SIP data found"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="nomor", type="string", example="SIP-987654"),
 *                 @OA\Property(property="masa_berlaku", type="string", format="date", example="2026-01-01")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User tidak ditemukan atau SIP tidak ditemukan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found"),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     )
 * )
 */


    public function getSipByNik($nik)
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

        // Cari data SIP berdasarkan user_id
        $sip = SipModel::where('user_id', $user->user_id)->first();

        if (!$sip) {
            return response()->json([
                'success' => false,
                'message' => 'SIP not found for this user',
                'status_code' => 404
            ], 404);
        }

        // Mengembalikan data dalam bentuk JSON
        return response()->json([
            'success' => true,
            'message' => 'SIP data found',
            'data' => [
                'nomor_sip' => $sip->nomor_sip,
                'masa_berlaku_sip' => $sip->masa_berlaku_sip
            ],
            'status_code' => 200
        ], 200);
    }


}
