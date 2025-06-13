<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Factory as Validator;
use App\Models\SakModel;
use Tymon\JWTAuth\Facades\JWTAuth;  
use Illuminate\Support\Facades\Log;
use App\Models\DaftarUser;


class SakController extends Controller
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

   public function upload(Request $request)
	{
		// Validasi permintaan yang masuk    
		$validation = $this->validator->make($request->all(), [    
			'path_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
			'nomor_sak' => 'required|string',
			'masa_berlaku_sak' => 'required|date',    
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
				$filePath = $file->storeAs('Sak', $fileName, 'public');
			}

			$existingFile = SakModel::where('user_id', $user->user_id)->first();

			if ($existingFile) {
				if ($filePath && $existingFile->path_file && Storage::exists($existingFile->path_file)) {
					Storage::delete($existingFile->path_file);
				}

				$existingFile->update([
					'path_file' => $filePath ?? $existingFile->path_file,
					'nomor_sak' => $request->input('nomor_sak', null),
					'masa_berlaku_sak' => $request->input('masa_berlaku_sak', null),
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
						'sak_id' => $existingFile->sak_id,
						'user_id' => $user->user_id,
						'file_path' => $existingFile->path_file,
						'nomor_sak' => $existingFile->nomor_sak,
						'masa_berlaku_sak' => $existingFile->masa_berlaku_sak,
					],
					'status_code' => 200
				], 200);
			}

			// Jika data baru
			$newFile = TranskripModel::create([
				'path_file' => $filePath,
				'user_id' => $user->user_id,
				'nomor_sak' => $request->input('nomor_sak', null),
				'masa_berlaku_sak' => $request->input('masa_berlaku_sak', null),
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
					'sak_id' => $newFile->sak_id,
					'user_id' => $user->user_id,
					'file_path' => $newFile->path_file,
					'nomor_sak' => $newFile->nomor_sak,
					'masa_berlaku_sak' => $newFile->masa_berlaku_sak,
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
            $file = SakModel::find($id);

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
            $SakFile = SakModel::where('user_id', $user->user_id)->first();
            if (!$SakFile) {
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
                    $SakFile->$field = $request->input($field) ? 1 : 0; // true = 1, false = 0 
                }
            }

            // Update nilai `ket` jika ada
            if ($request->has('ket')) {
                $SakFile->ket = $request->input('ket');
            }

            // Simpan perubahan
            $SakFile->save();

            return response()->json([
                'success' => true,
                'message' => 'File details updated successfully.',
                'data' => [
                    'user_id' => $user->user_id,
                    'sak_id' => $sakFile->str_id,
                    'path_file' => Storage::url($SakFile->path_file),
                    'valid' => $SakFile->valid,
                    'authentic' => $SakFile->authentic,
                    'current' => $SakFile->current,
                    'sufficient' => $SakFile->sufficient,
                    'ket' => $SakFile->ket,
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
        $file = SakModel::where('user_id', $user->user_id)->first();
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
            'nomor_sak' => null,
            'masa_berlaku_sak' => null,
        ]);
    
        // Response sukses dengan user_id & ijazah_id
        return response()->json([
            'success' => true,
            'message' => 'File successfully deleted from the system.',
            'details' => [
                'sak_id' => $file->sak_id, // Menampilkan ID file ijazah
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
 
	
    public function getTranskrip($path)
    {
        // Logika untuk mengunduh file ijazah
        return response()->file(storage_path('app/public/Sak/' . $path));
    }


    public function getSakByNik($nik)
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

            // Cari data SAK berdasarkan user_id
            $sak = SakModel::where('user_id', $user->user_id)->first();

            if (!$sak) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data SAK Not Found.',
                    'status_code' => 404
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'nomor_sak' => $sak->nomor_sak,
                    'masa_berlaku_sak' => $sak->masa_berlaku_sak
                ],
                'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching SAK data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }


}
