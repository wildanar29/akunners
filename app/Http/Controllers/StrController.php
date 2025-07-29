<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Factory as Validator;
use App\Models\StrModel;
use Tymon\JWTAuth\Facades\JWTAuth;  
use Illuminate\Support\Facades\Log;  
use App\Models\DaftarUser;


class StrController extends Controller
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
 *     path="/upload-str",
 *     summary="Upload File STR dan Simpan Detail ke Database",
 *     description="Mengunggah file STR ke server dan menyimpan detailnya di database.",
 *     operationId="uploadFileStr",
 *     tags={"Upload STR"},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Permintaan untuk mengunggah file STR beserta metadata.",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"path_file", "nomor_str", "masa_berlaku_str"},
 *                 @OA\Property(
 *                     property="path_file",
 *                     description="File yang akan diunggah (PDF, JPG, JPEG, PNG, max 2MB).",
 *                     type="string",
 *                     format="binary"
 *                 ),
 *                 @OA\Property(
 *                     property="nomor_str",
 *                     type="string",
 *                     description="Nomor STR yang diunggah",
 *                     example="STR-987654321"
 *                 ),
 *                 @OA\Property(
 *                     property="masa_berlaku_str",
 *                     type="string",
 *                     format="date",
 *                     description="Tanggal masa berlaku STR",
 *                     example="2027-06-30"
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
 *                 @OA\Property(property="str_id", type="integer", example=2),
 *                 @OA\Property(property="user_id", type="integer", example=11),
 *                 @OA\Property(property="nomor_str", type="string", example="STR-987654321"),
 *                 @OA\Property(property="masa_berlaku_str", type="string", example="2027-06-30"),
 *                 @OA\Property(property="path_file", type="string", example="http://app.rsimmanuel.net:9091/storage/Str/1676985600_file.pdf"),
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
 *         description="Permintaan tidak valid atau file tidak diunggah.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Tidak ada file yang diunggah. Harap unggah file yang valid."),
 *             @OA\Property(property="status_code", type="integer", example=400)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan tak terduga di server.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan tak terduga di server."),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Detail pesan kesalahan."
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=500)
 *         )
 *     )
 * )
 */


	 public function upload(Request $request)
	{
		// Validasi permintaan yang masuk    
		$validation = $this->validator->make($request->all(), [    
			'path_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3048', // <- nullable
			'nomor_str' => 'required|string',
			'masa_berlaku_str' => 'required|date',   
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

			// Cek apakah user sudah memiliki data STR
			$existingFile = StrModel::where('user_id', $user->user_id)->first();

			// Inisialisasi path_file
			$filePath = $existingFile ? $existingFile->path_file : null;

			// Jika file dikirim, proses upload
			if ($request->hasFile('path_file')) {
				if ($existingFile && $existingFile->path_file && Storage::exists($existingFile->path_file)) {
					Storage::delete($existingFile->path_file);
				}

				$file = $request->file('path_file');    
				$fileName = time() . '_' . $file->getClientOriginalName();    
				$filePath = $file->storeAs('Str', $fileName, 'public');    
			}

			if ($existingFile) {
				$existingFile->update([
					'path_file' => $filePath,
					'nomor_str' => $request->input('nomor_str'),
					'masa_berlaku_str' => $request->input('masa_berlaku_str'),
					'valid' => $request->input('valid'),
					'authentic' => $request->input('authentic'),
					'current' => $request->input('current'),
					'sufficient' => $request->input('sufficient'),
					'ket' => $request->input('ket'),
				]);

				return response()->json([
					'success' => true,
					'message' => 'Data updated successfully.',
					'data' => [
						'str_id' => $existingFile->str_id,
						'user_id' => $user->user_id,
						'file_path' => $existingFile->path_file,
						'nomor_str' => $existingFile->nomor_str,
						'masa_berlaku_str' => $existingFile->masa_berlaku_str,
					],
					'status_code' => 200
				], 200);
			}

			// Buat entri baru jika belum ada
			$newFile = StrModel::create([
				'path_file' => $filePath,    
				'user_id' => $user->user_id,
				'nomor_str' => $request->input('nomor_str'),  
				'masa_berlaku_str' => $request->input('masa_berlaku_str'),  
				'valid' => $request->input('valid'),    
				'authentic' => $request->input('authentic'),    
				'current' => $request->input('current'),    
				'sufficient' => $request->input('sufficient'),    
				'ket' => $request->input('ket'),    
			]);

			return response()->json([
				'success' => true,
				'message' => 'Data uploaded successfully.',
				'data' => [
					'str_id' => $newFile->str_id,
					'user_id' => $user->user_id,
					'file_path' => $newFile->path_file,
					'nomor_str' => $newFile->nomor_str,
					'masa_berlaku_str' => $newFile->masa_berlaku_str,
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
            $file = StrModel::find($id);

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
 *     path="/update-str/{nik}",
 *     summary="Perbarui Penilaian dari Asesor ke Asesi melalui NIK",
 *     description="Endpoint ini digunakan untuk memperbarui penilaian dari asesor ke asesi berdasarkan NIK.",
 *     tags={"Asesor"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="NIK dari asesi yang ingin diperbarui.",
 *         @OA\Schema(type="string", example="12345678")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"valid", "authentic"},
 *             @OA\Property(property="valid", type="boolean", example=true, description="Status validasi"),
 *             @OA\Property(property="authentic", type="boolean", example=true, description="Status keaslian"),
 *             @OA\Property(property="current", type="boolean", example=true, description="Status terkini"),
 *             @OA\Property(property="sufficient", type="boolean", example=true, description="Status kecukupan"),
 *             @OA\Property(property="ket", type="string", example="Keterangan tambahan", description="Keterangan tambahan")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Penilaian berhasil diperbarui.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Penilaian berhasil diperbarui."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="valid", type="boolean", example=true),
 *                 @OA\Property(property="authentic", type="boolean", example=true),
 *                 @OA\Property(property="current", type="boolean", example=true),
 *                 @OA\Property(property="sufficient", type="boolean", example=true),
 *                 @OA\Property(property="ket", type="string", example="Keterangan tambahan")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Permintaan tidak valid.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data tidak valid."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data tidak ditemukan.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan pada server.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan pada server.")
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
            $StrFile = StrModel::where('user_id', $user->user_id)->first();
            if (!$StrFile) {
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
                    $StrFile->$field = $request->input($field) ? 1 : 0; // true = 1, false = 0 
                }
            }

            // Update nilai `ket` jika ada
            if ($request->has('ket')) {
                $StrFile->ket = $request->input('ket');
            }

            // Simpan perubahan
            $StrFile->save();

            return response()->json([
                'success' => true,
                'message' => 'File details updated successfully.',
                'data' => [
                    'user_id' => $user->user_id,
                    'str_id' => $StrFile->str_id,
                    'path_file' => Storage::url($StrFile->path_file),
                    'valid' => $StrFile->valid,
                    'authentic' => $StrFile->authentic,
                    'current' => $StrFile->current,
                    'sufficient' => $StrFile->sufficient,
                    'ket' => $StrFile->ket,
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
 *     path="/str/file/{nik}",
 *     summary="Hapus Str atau Gagal Mengupload Berdasarkan NIK",
 *     description="Menghapus file berdasarkan NIK, baik dari sistem (storage) maupun dari database.",
 *     operationId="deleteFileStr",
 *     tags={"Upload Str"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         description="NIK file yang ingin dihapus.",
 *         required=true,
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
 *                 @OA\Property(property="str_id", type="integer", example=2),
 *                 @OA\Property(property="file_path", type="string", example="Str/1676985600_file.pdf"),
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
 *                 @OA\Property(property="id", type="integer", example=999),
 *                 @OA\Property(property="reason", type="string", example="File dengan ID ini tidak ada dalam sistem.")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan tak terduga saat mencoba menghapus file.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan tak terduga saat mencoba menghapus file."),
 *             @OA\Property(
 *                 property="error_details",
 *                 type="object",
 *                 @OA\Property(property="error_message", type="string", example="detail pesan kesalahan"),
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
        $file = StrModel::where('user_id', $user->user_id)->first();
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
                'str_id' => $file->str_id, // Menampilkan ID file ijazah
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
 *     path="/storage/Str/{path}",
 *     summary="Memperlihatkan file Str",
 *     description="Endpoint ini digunakan untuk memperlihatkan file Str berdasarkan path yang diberikan.",
 *     operationId="getStr",
 *     tags={"Upload Str"},
 *     @OA\Parameter(
 *         name="path",
 *         in="path",
 *         required=true,
 *         description="Path file Str yang ingin diperlihatkan.",
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
    public function getStr($path)
    {
        // Logika untuk mengunduh file ijazah
        return response()->file(storage_path('app/public/Str/' . $path));
    }



        /**
     * @OA\Get(
     *     path="/get-no-expired-str/{nik}",
     *     summary="Ambil Data Nomor dan Masa Berlaku STR berdasarkan NIK",
     *     description="Mengambil data nomor STR dan masa berlaku berdasarkan NIK user.",
     *     operationId="getStrByNik",
     *     tags={"Upload Str"},
     *     @OA\Parameter(
     *         name="nik",
     *         in="path",
     *         required=true,
     *         description="NIK dari user yang ingin diambil datanya",
     *         @OA\Schema(type="string", example="3201234567890001")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data STR ditemukan.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="STR data found."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="nomor_str", type="string", example="STR-987654321"),
     *                 @OA\Property(property="masa_berlaku_str", type="string", example="2027-06-30")
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User atau STR tidak ditemukan.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User or STR not found."),
     *             @OA\Property(property="status_code", type="integer", example=404)
     *         )
     *     )
     * )
     */
    public function getStrByNik($nik)
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
        $str = StrModel::where('user_id', $user->user_id)->first();

        if (!$str) {
            return response()->json([
                'success' => false,
                'message' => 'STR not found for this user',
                'status_code' => 404
            ], 404);
        }

        // Mengembalikan data dalam bentuk JSON
        return response()->json([
            'success' => true,
            'message' => 'STR data found',
            'data' => [
                'nomor_str' => $str->nomor_str,
                'masa_berlaku_str' => $str->masa_berlaku_str
            ],
            'status_code' => 200
        ], 200);
    }


}
