<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Factory as Validator;
use App\Models\IjazahModel;
use Tymon\JWTAuth\Facades\JWTAuth;  
use Illuminate\Support\Facades\Log;  
use App\Models\DaftarUser;

class IjazahController extends Controller
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
 *     path="/upload-ijazah",
 *     tags={"Upload Ijazah"},
 *     summary="Unggah file (PDF, JPG, JPEG, PNG) untuk Asesi",
 *     description="Mengunggah file dan menyimpannya ke dalam sistem. File yang diunggah harus memiliki format tertentu dan ukuran maksimum.",
 *     operationId="UploadFileIjazah",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Kirim file atau Upload File Ijazah.",
 *         @OA\JsonContent(
 *             required={"path_file"},
 *             @OA\Property(property="path_file", type="string", format="binary")
 *     
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File berhasil diunggah dan disimpan.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="File uploaded successfully."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="ijazah_id", type="int", example="19"),
 *                 @OA\Property(property="user_id", type="int", example="10"),
 *                 @OA\Property(property="path_file", type="string", example="http://app.rsimmanuel.net:9091/storage/ijazah/1615203532_filename.pdf"),
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
 *         description="Validasi gagal atau file tidak diunggah.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal. Pastikan file di Upload."),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="path_file", type="array", @OA\Items(type="string", example="File is required.")),
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=400)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan tak terduga di server.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan tak terduga di server."),
 *             @OA\Property(property="error", type="string", example="Error message")
 *         )
 *     )
 * )
 */
 

   public function upload(Request $request)    
    {
        // Debug awal: apakah file ada?
        Log::debug('Cek file upload: ', [
            'hasFile' => $request->hasFile('path_file'),
            'fileInfo' => $request->hasFile('path_file') ? [
                'originalName' => $request->file('path_file')->getClientOriginalName(),
                'mimeType' => $request->file('path_file')->getMimeType(),
                'size' => $request->file('path_file')->getSize(),
                'extension' => $request->file('path_file')->getClientOriginalExtension(),
                'error' => $request->file('path_file')->getError(), // 0 = no error
            ] : null
        ]);

        // Validasi permintaan yang masuk    
        $validation = $this->validator->make($request->all(), [    
            'path_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',    
            'valid' => 'nullable|boolean',    
            'authentic' => 'nullable|boolean',    
            'current' => 'nullable|boolean',    
            'sufficient' => 'nullable|boolean',    
            'ket' => 'nullable|string|max:255',    
        ]);  

        if ($validation->fails()) {
            $errors = $validation->errors();
            $customMessages = [];

            if ($errors->has('path_file')) {
                foreach ($errors->get('path_file') as $msg) {
                    if (str_contains($msg, 'uploaded')) {
                        $customMessages[] = 'Gagal mengunggah file. Ukuran melebihi batas maksimum atau ada kesalahan saat upload.';
                    } elseif (str_contains($msg, 'mimes')) {
                        $customMessages[] = 'Format file tidak didukung. Hanya diperbolehkan: pdf, jpg, jpeg, png.';
                    } elseif (str_contains($msg, 'max')) {
                        $customMessages[] = 'File terlalu besar. Maksimum ukuran 2MB.';
                    } else {
                        $customMessages[] = $msg;
                    }
                }
            }

            Log::warning('Validation failed on upload:', $errors->toArray());

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $customMessages ?: $errors->toArray(),
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

            $existingFile = IjazahModel::where('user_id', $user->user_id)->first();
            $filePath = null;

            if ($request->hasFile('path_file')) {
                $file = $request->file('path_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('ijazah', $fileName, 'public');

                Log::info("File uploaded: {$fileName} saved to {$filePath}");

                if ($existingFile && $existingFile->path_file && Storage::exists($existingFile->path_file)) {
                    Storage::delete($existingFile->path_file);
                    Log::info("Old file deleted: {$existingFile->path_file}");
                }
            }

            if ($existingFile) {
                $existingFile->update([
                    'path_file' => $filePath ?? $existingFile->path_file,
                    'valid' => $request->input('valid', null),
                    'authentic' => $request->input('authentic', null),
                    'current' => $request->input('current', null),
                    'sufficient' => $request->input('sufficient', null),
                    'ket' => $request->input('ket', null),
                ]);

                Log::info("Ijazah data updated for user_id: {$user->user_id}");

                return response()->json([
                    'success' => true,
                    'message' => 'Data updated successfully.',
                    'data' => [
                        'ijazah_id' => $existingFile->ijazah_id,
                        'user_id' => $user->user_id,
                        'file_path' => $existingFile->path_file,
                    ],
                    'status_code' => 200
                ], 200);
            }

            $newFile = IjazahModel::create([
                'path_file' => $filePath,    
                'user_id' => $user->user_id,
                'valid' => $request->input('valid', null),    
                'authentic' => $request->input('authentic', null),    
                'current' => $request->input('current', null),    
                'sufficient' => $request->input('sufficient', null),    
                'ket' => $request->input('ket', null),    
            ]);

            Log::info("New ijazah data inserted for user_id: {$user->user_id}");

            return response()->json([
                'success' => true,
                'message' => 'Data uploaded successfully.',
                'data' => [
                    'ijazah_id' => $newFile->ijazah_id,
                    'user_id' => $user->user_id,
                    'file_path' => $newFile->path_file,
                ],
                'status_code' => 201
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('File upload error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

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
            $file = IjazahModel::find($id);

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
 *     path="/update-ijazah/{nik}",
 *     tags={"Asesor"},
 *     summary="Perbarui Penilaian file Ijazah berdasarkan nik untuk Asesor Terhadap Asesi",
 *     description="Memperbarui informasi detail file berdasarkan nik yang diberikan. Hanya parameter opsional yang akan diperbarui (valid, authentic, current, sufficient, ket).",
 *     operationId="updateFile",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="nik file yang ingin diperbarui",
 *         required=true,
 *         @OA\Schema(type="integer", example="12345678")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Kirim parameter yang ingin diperbarui. Parameter ini opsional, hanya kirim yang ingin diubah.",
 *         @OA\JsonContent(
 *             @OA\Property(property="valid", type="boolean", example=true),
 *             @OA\Property(property="authentic", type="boolean", example=true),
 *             @OA\Property(property="current", type="boolean", example=true),
 *             @OA\Property(property="sufficient", type="boolean", example=true),
 *             @OA\Property(property="ket", type="string", example="Perbarui keterangan tambahan.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File details berhasil diperbarui.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="File details updated successfully."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="path_file", type="string", example="http://app.rsimmanuel.net:9091/storage/ijazah/1615203532_filename.pdf"),
 *                 @OA\Property(property="valid", type="boolean", example=true),
 *                 @OA\Property(property="authentic", type="boolean", example=true),
 *                 @OA\Property(property="current", type="boolean", example=true),
 *                 @OA\Property(property="sufficient", type="boolean", example=true),
 *                 @OA\Property(property="ket", type="string", example="Perbarui keterangan tambahan.")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal atau input tidak lengkap.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal. Pastikan semua data sudah sesuai."),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="valid", type="array", @OA\Items(type="string", example="The valid field is required."))
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=400)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="File dengan ID yang diberikan tidak ditemukan.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="File tidak ditemukan. Silakan periksa ID dan coba lagi."),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan tak terduga saat memperbarui detail file.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan tak terduga saat memperbarui detail file."),
 *             @OA\Property(property="error", type="string", example="Error message")
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
            $ijazahFile = IjazahModel::where('user_id', $user->user_id)->first();
            if (!$ijazahFile) {
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
                    $ijazahFile->$field = $request->input($field) ? 1 : 0; // true = 1, false = 0 
                }
            }
    
            // Update nilai `ket` jika ada
            if ($request->has('ket')) {
                $ijazahFile->ket = $request->input('ket');
            }
    
            // Simpan perubahan
            $ijazahFile->save();
    
            return response()->json([
                'success' => true,
                'message' => 'File details updated successfully.',
                'data' => [
                    'user_id' => $user->user_id,
                    'ijazah_id' => $ijazahFile->ijazah_id,
                    'path_file' => Storage::url($ijazahFile->path_file),
                    'valid' => $ijazahFile->valid,
                    'authentic' => $ijazahFile->authentic,
                    'current' => $ijazahFile->current,
                    'sufficient' => $ijazahFile->sufficient,
                    'ket' => $ijazahFile->ket,
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
 *     path="/ijazah/file/{nik}",
 *     tags={"Upload Ijazah"},
 *     summary="Menghapus file ijazah berdasarkan NIK",
 *     description="Menghapus file ijazah dari sistem berdasarkan NIK yang telah didaftarkan. Jika file ditemukan, maka akan dihapus baik dari storage dan database.",
 *     operationId="deleteFile",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="NIK file ijazah yang ingin dihapus",
 *         required=true,
 *         @OA\Schema(type="integer", example="12345678")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File berhasil dihapus.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="File berhasil dihapus dari sistem."),
 *             @OA\Property(property="details", type="object",
 *                 @OA\Property(property="ijazah_id", type="integer", example=19),
 *                 @OA\Property(property="file_path", type="string", example="ijazah/1615203532_filename.pdf"),
 *                 @OA\Property(property="storage_status", type="string", example="File dihapus dari storage."),
 *                 @OA\Property(property="database_status", type="string", example="Rekaman file telah dihapus dari database.")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="ID yang diberikan tidak valid.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="ID tidak valid. Mohon masukkan ID berupa angka positif."),
 *             @OA\Property(property="details", type="object",
 *                 @OA\Property(property="id", type="integer", example=0),
 *                 @OA\Property(property="reason", type="string", example="ID tidak memenuhi kriteria yang diperlukan.")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=400)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="File tidak ditemukan berdasarkan ID.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="File tidak ditemukan. Mohon periksa ID dan coba lagi."),
 *             @OA\Property(property="details", type="object",
 *                 @OA\Property(property="id", type="integer", example=123),
 *                 @OA\Property(property="reason", type="string", example="File dengan ID ini tidak ada dalam sistem.")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan pada server saat menghapus file.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan tak terduga saat mencoba menghapus file."),
 *             @OA\Property(property="error_details", type="object",
 *                 @OA\Property(property="error_message", type="string", example="Error message from server"),
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
        $file = IjazahModel::where('user_id', $user->user_id)->first();
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
        ]);
    
        // Response sukses dengan user_id & ijazah_id
        return response()->json([
            'success' => true,
            'message' => 'File successfully deleted from the system.',
            'details' => [
                'ijazah_id' => $file->ijazah_id, // Menampilkan ID file ijazah
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
 *     path="/storage/ijazah/{path}",
 *     summary="Memperlihatkan file ijazah",
 *     description="Endpoint ini digunakan untuk memperlihatkan file ijazah berdasarkan path yang diberikan.",
 *     operationId="getIjazah",
 *     tags={"Upload Ijazah"},
 *     @OA\Parameter(
 *         name="path",
 *         in="path",
 *         required=true,
 *         description="Path file ijazah yang ingin diperlihatkan.",
 *         @OA\Schema(
 *             type="string", example="1615203532_filename.pdf" 
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

    public function getIjazah($path)
    {
        // Logika untuk mengunduh file ijazah
        return response()->file(storage_path('app/public/ijazah/' . $path));
    }



}



