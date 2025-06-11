<?php

namespace App\Http\Controllers;

use App\Models\SertifikatModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Factory as Validator;
use Tymon\JWTAuth\Facades\JWTAuth;  
use Illuminate\Support\Facades\Log;
use App\Models\DaftarUser;

class SertifikatController extends Controller
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
 *     path="/upload-sertifikat",
 *     summary="Upload File Sertifikat",
 *     description="Mengunggah file Sertifikat dan menyimpan detailnya ke database.",
 *     operationId="uploadFileSertifikat",
 *     tags={"Upload Sertifikat"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"path_file", "nomor_sertifikat", "masa_berlaku"},
 *                 @OA\Property(
 *                     property="path_file",
 *                     type="string",
 *                     format="binary",
 *                     description="File yang akan diunggah (PDF, JPG, JPEG, PNG, maksimal 2MB)."
 *                 ),
 *                 @OA\Property(
 *                     property="nomor_sertifikat",
 *                     type="string",
 *                     description="Nomor sertifikat yang diunggah.",
 *                     example="SERT-202500123"
 *                 ),
 *                 @OA\Property(
 *                     property="masa_berlaku",
 *                     type="string",
 *                     format="date",
 *                     description="Tanggal masa berlaku sertifikat.",
 *                     example="2027-06-15"
 *                 )
 *                 @OA\Property(
 *                     property="type_sertifikat",
 *                     type="string",
 *                     format="date",
 *                     description="NIRA, SPK atau NULL (Optional).",
 *                     example="NIRA / SPK"
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
 *                 @OA\Property(property="sertifikat_id", type="integer", example=4, description="ID sertifikat yang tersimpan."),
 *                 @OA\Property(property="user_id", type="integer", example=11, description="ID pengguna yang mengunggah."),
 *                 @OA\Property(property="nomor_sertifikat", type="string", example="SERT-202500123"),
 *                 @OA\Property(property="masa_berlaku", type="string", example="2027-06-15"),
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
        // Validate the incoming request
        $validation = $this->validator->make($request->all(), [
            'path_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'nomor_sertifikat' => 'nullable|string',
            'masa_berlaku_sertifikat' => 'nullable|date',
            'valid' => 'nullable|boolean', // Optional
            'authentic' => 'nullable|boolean', // Optional
            'current' => 'nullable|boolean', // Optional
            'sufficient' => 'nullable|boolean', // Optional            
            'ket' => 'nullable|string|max:255',       // Optional
            'type_sertifikat' => 'nullable|in:NIRA,SPK', // <-- tambah validasi ENUM dan nullable
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
                return response()->json(['message' => 'User not found'], 404);  
            }  


            if ($request->hasFile('path_file')) {
                $file = $request->file('path_file');
    
                // Generate unique file name
                $fileName = time() . '_' . $file->getClientOriginalName();
    
                // Store the file in "Sip" directory under storage/app/public
                $filePath = $file->storeAs('Sertifikat', $fileName, 'public');
    
                // Save file and details to the database
                $ijazahFile = new SertifikatModel();
                $ijazahFile->path_file = $filePath;
                $ijazahFile->user_id = $user->user_id; // Mengisi user_id dari token 
                // Set values for valid, authentic, current, sufficient
                $ijazahFile->valid = $request->input('valid', null);
                $ijazahFile->authentic = $request->input('authentic', null);
                $ijazahFile->current = $request->input('current', null);
                $ijazahFile->sufficient = $request->input('sufficient', null);
    
                // Save keterangan (ket)
                $ijazahFile->ket = $request->input('ket', null);
                $ijazahFile->nomor_sertifikat = $request->input('nomor_sertifikat', null);
                $ijazahFile->masa_berlaku_sertifikat = $request->input('masa_berlaku_sertifikat', null);
                $ijazahFile->type_sertifikat = $request->input('type_sertifikat', null); // <-- Tambahkan di sini
    
                // Save record
                $ijazahFile->save();
    
                return response()->json([
                    'success' => true,
                    'message' => 'File uploaded successfully.',
                    'data' => [
						'sertifikat_id' => $ijazahFile->sertifikat_id,
                        'user_id' => $ijazahFile->user_id,
                        'path_file' => Storage::url($filePath),
                        'valid' => $ijazahFile->valid,
                        'authentic' => $ijazahFile->authentic,
                        'current' => $ijazahFile->current,
                        'sufficient' => $ijazahFile->sufficient,
                        'ket' => $ijazahFile->ket,
                        'nomor_sertifikat' => $ijazahFile->nomor_sertifikat,
                        'masa_berlaku_sertifikat' => $ijazahFile->masa_berlaku_sertifikat,
                        'type_sertifikat' => $ijazahFile->type_sertifikat, // <-- Tampilkan juga
                    ],
                    'status_code' => 200,
                ], 200);
            }
    
            return response()->json([
                'success' => false,
				'message' => 'No file uploaded. Please upload a valid file.',
                'status_code' => 400,
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
				'message' => 'An unexpected server error occurred.',
                'error' => $e->getMessage(),
                'status_code' => 500,
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
            $file = SertifikatModel::find($id);

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
 *     path="/update-sertifikat/{id}",
 *     summary="Update File Penilaian dari Asesor ke Asesi berdasarkan sertifikat_id",
 *     description="Memperbarui detail file Penilaian berdasarkan sertifikat_id yang diberikan.",
 *     operationId="updateFileDetailsSertifikat",
 *     tags={"Asesor"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID file yang akan diperbarui.",
 *         @OA\Schema(type="integer", example="4")
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
            $SertifikatFile = SertifikatModel::where('user_id', $user->user_id)->first();
            if (!$SertifikatFile) {
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
                    $SertifikatFile->$field = $request->input($field) ? 1 : 0; // true = 1, false = 0 
                }
            }
    
            // Update nilai `ket` jika ada
            if ($request->has('ket')) {
                $SertifikatFile->ket = $request->input('ket');
            }
    
            // Simpan perubahan
            $SertifikatFile->save();
    
            return response()->json([
                'success' => true,
                'message' => 'File details updated successfully.',
                'data' => [
                    'user_id' => $user->user_id,
                    'sertifikat_id' => $SertifikatFile->sertifikat_id,
                    'path_file' => Storage::url($SertifikatFile->path_file),
                    'valid' => $SertifikatFile->valid,
                    'authentic' => $SertifikatFile->authentic,
                    'current' => $SertifikatFile->current,
                    'sufficient' => $SertifikatFile->sufficient,
                    'ket' => $SertifikatFile->ket,
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
 *     path="/sertifikat/file/{nik}/{sertifikat_id}",
 *     summary="Delete File Serfitifikat berdasarkan NIK dan sertifikat_id",
 *     description="Menghapus file Sertifikat dari sistem berdasarkan NIK yang diberikan.",
 *     operationId="deleteFileSertifikat",
 *     tags={"Upload Sertifikat"},
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

    public function deleteFile($nik, $sertifikat_id)
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

        // Cari file berdasarkan user_id DAN sertifikat_id
        $file = SertifikatModel::where('user_id', $user->user_id)
        ->where('sertifikat_id', $sertifikat_id)
        ->first();

        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found. Please check the NIK and sertifikat_id and try again.',
                'details' => [
                    'nik' => $nik,
                    'sertifikat_id' => $sertifikat_id,
                    'reason' => 'No file associated with this user and sertifikat_id was found in the system.'
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
    
        // Simpan ID sebelum dihapus
        $sertifikatId = $file->sertifikat_id;
        $userId = $user->user_id;
    
        // Hapus rekaman database
        $file->delete();
    
        // Response sukses dengan sertifikat_id & user_id
        return response()->json([
            'success' => true,
            'message' => 'File successfully deleted from the system.',
            'details' => [
                'sertifikat_id' => $sertifikatId,
                'user_id' => $userId,
                'storage_status' => $fileDeletedFromStorage 
                    ? 'File deleted from storage.' 
                    : 'File not found in storage.',
                'database_status' => 'File record has been removed from the database.',
            ],
            'status_code' => 200
        ], 200);
    }
    
	
	/**
 * @OA\Get(
 *     path="/storage/Sertifikat/{path}",
 *     summary="Memperlihatkan file Sertifikat",
 *     description="Endpoint ini digunakan untuk memperlihatkan file Sertifikat berdasarkan path yang diberikan.",
 *     operationId="getSertifikatFilePath",
 *     tags={"Upload Sertifikat"},
 *     @OA\Parameter(
 *         name="path",
 *         in="path",
 *         required=true,
 *         description="Path file Sertifikat yang ingin diperlihatkan.",
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

    public function getSertifikat($path)
    {
        // Logika untuk mengunduh file ijazah
        return response()->file(storage_path('app/public/Sertifikat/' . $path));
    }


    /**
 * @OA\Get(
 *     path="/get-no-expired-sertifikat/{nik}",
 *     summary="Mendapatkan data Nomor dan Masa Berlaku Sertifikat berdasarkan NIK",
 *     description="Mengambil nomor dan masa berlaku Sertifikat berdasarkan NIK pengguna.",
 *     operationId="getSertifikatByNik",
 *     tags={"Upload Sertifikat"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="NIK pengguna untuk mengambil data Sertifikat",
 *         @OA\Schema(type="string", example="1234567890123456")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data Sertifikat ditemukan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Sertifikat data found"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="nomor", type="string", example="SER-987654"),
 *                 @OA\Property(property="masa_berlaku", type="string", format="date", example="2026-01-01")
 *                 @OA\Property(property="type_sertifikat", type="string", example="NIRA")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User tidak ditemukan atau Sertifikat tidak ditemukan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found"),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     )
 * )
 */


    public function getSertifikatByNik($nik)
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
        $sertifikat = SertifikatModel::where('user_id', $user->user_id)->first();

        if (!$sertifikat) {
            return response()->json([
                'success' => false,
                'message' => 'Sertifikat not found for this user',
                'status_code' => 404
            ], 404);
        }

        // Mengembalikan data dalam bentuk JSON
        return response()->json([
            'success' => true,
            'message' => 'Sertifikat data found',
            'data' => [
                'type_sertifikat' => $sertifikat->type_sertifikat,
                'nomor_sertifikat' => $sertifikat->nomor_sertifikat,
                'masa_berlaku_sertifikat' => $sertifikat->masa_berlaku_sertifikat
            ],
            'status_code' => 200
        ], 200);
    }

}
