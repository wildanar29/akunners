<?php

namespace App\Http\Controllers;

use App\Models\DaftarUser;
use App\Models\HistoryJabatan;
use App\Models\WorkingUnit;
use App\Models\JabatanModel; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Pastikan ini ditambahkan di atas kontroler Anda
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // Tambahkan DB untuk query manual
use Illuminate\Support\Facades\Storage;
use App\Models\UserRole;
use App\Models\UsersOtp;
use App\Models\DataAsesorModel;
use App\Models\PasswordReset;
use OpenApi\Annotations as OA;
use Tymon\JWTAuth\Facades\JWTAuth; 
use Illuminate\Support\Facades\Redis; // Tambahkan Redis facade
use Illuminate\Support\Facades\Log;


	/**
	* @OA\Info(
	*     title="API Documentation",
	*     version="1.0.0",
	*     description="Dokumentasi API untuk aplikasi Akunurse", 
	* )
	*/
	class UsersController extends Controller
	{
	/**
     * @OA\Post(
     *     path="/register-akun",
     *     summary="Mendaftarkan akun baru untuk Pengguna dan disimpan ke Redis",
     *     operationId="RegisterAkunNurse",
     *     tags={"Akun"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nik", "nama", "email", "role_id", "no_telp"},
     *             type="object",
     *             @OA\Property(property="nik", type="string", example="240076", description="Nomor Induk Karyawan (NIK)"),
     *             @OA\Property(property="nama", type="string", example="I Gede Daiva Andika", description="Nama lengkap perawat."),
     *             @OA\Property(property="email", type="string", example="igededaivaa@gmail.com", description="Email untuk akun."),
	 *             @OA\Property(property="role_id", type="id", example="1", description="Asesi"),
     *             @OA\Property(property="no_telp", type="string", example="089665795500", description="Nomor telepon akun.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Akun berhasil didaftarkan.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="pesan", type="string", example="Akun Registrasi Berhasil dan disimpan di Redis."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="nik", type="string", example="240076"),
     *                 @OA\Property(property="nama", type="string", example="I Gede Daiva Andika"),
     *                 @OA\Property(property="email", type="string", example="igededaivaa@gmail.com"),
	 *                 @OA\Property(property="role_id", type="id", example="1"),
     *                 @OA\Property(property="no_telp", type="string", example="089665795500")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validasi data gagal.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="pesan", type="string", example="Validasi data gagal. Silakan periksa input Anda."),
     *             @OA\Property(property="kesalahan", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource yang diminta tidak ditemukan.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="pesan", type="string", example="Resource yang diminta tidak ditemukan.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Terjadi kesalahan pada server.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="pesan", type="string", example="Terjadi kesalahan pada server."),
     *             @OA\Property(property="kesalahan", type="string", example="Database connection failed.")
     *         )
     *     )
     * )
     */
	 
	public function RegisterAkunNurse(Request $request)
    {
        try {
           // Validasi umum
			$rules = [
				'nik' => 'required|unique:users',
				'nama' => 'required|string|max:255',
				'email' => 'required|email|unique:users',
				'role_id' => 'required|integer|exists:user_role,role_id',
				'no_telp' => 'required|max:13|unique:users',
			];

			// Jika role_id = 2 (asesor), tambahkan validasi wajib untuk no_reg dan tanggal_berlaku
			if ($request->role_id == 2) {
				$rules['no_reg'] = 'required|string';
				$rules['tanggal_berlaku'] = 'required|date';
			}
	
			$validator = Validator::make($request->all(), $rules);
	

             // Jika validasi gagal
			if ($validator->fails()) {
				return response()->json([
					'status' => 400,
					'message' => 'Validation failed. Please check your input.',
					'errors' => $validator->errors(),
				], 400);
			}

			// Ambil role_name dari database berdasarkan role_id
			$role = \DB::table('user_role')->where('role_id', $request->role_id)->first();
			$roleName = $role ? $role->role_name : null;


			$redisKey = 'user:' . $request->nik;
			$redisEmailKey = 'user_by_email:' . $request->email; // Simpan referensi NIK berdasarkan email

			$data = [
				'nik' => $request->nik,
				'nama' => $request->nama,
				'email' => $request->email,
				'no_telp' => $request->no_telp,
				'role_id' => $request->role_id,
				'role_name' => $roleName
			];

			 // Jika role_id = 2 (asesor), tambahkan no_reg dan tanggal_berlaku
			 if ($request->role_id == 2) {
				$data['no_reg'] = $request->no_reg;
				$data['tanggal_berlaku'] = $request->tanggal_berlaku;
			}

			Redis::set($redisKey, json_encode($data));
			Redis::set($redisEmailKey, $request->nik); // Simpan referensi nik berdasarkan no_telp


             // Respons berbeda berdasarkan role_id
			if ($request->role_id == 2) {
				return response()->json([
					'status' => 200,
					'message' => 'Account registered successfully and stored in Redis.',
					'data' => [
						'nik' => $request->nik,
						'name' => $request->nama,
						'email' => $request->email,
						'phone_number' => $request->no_telp,
						'role_id' => $request->role_id,
						'role_name' => $roleName,
						'no_reg' => $request->no_reg,
						'tanggal_berlaku' => $request->tanggal_berlaku,
					],
				], 200);
			} else {
				return response()->json([
					'status' => 200,
					'message' => 'Account registered successfully and stored in Redis.',
					'data' => [
						'nik' => $request->nik,
						'name' => $request->nama,
						'email' => $request->email,
						'phone_number' => $request->no_telp,
						'role_id' => $request->role_id,
						'role_name' => $roleName,
					],
				], 200);
			}


		} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			// Kesalahan jika resource tidak ditemukan
			return response()->json([
				'status' => 404,
				'message' => 'Resource not found.',
			], 404);

		} catch (\Exception $e) {
			return response()->json([
				'status' => 500,
				'message' => 'Internal Server Error',
				'error' => $e->getMessage(),
			], 500);
		}


		// Log atau cek hasil
		if ($result) {
			return response()->json([
				'status' => 201,
				'message' => 'Data berhasil disimpan di Redis.',
				'data' => $request->all(),
			]);
		} else {
			return response()->json([
				'status' => 500,
				'message' => 'Gagal menyimpan data ke Redis.',
			]);
		}
	}
 
 
 /**
 * @OA\Post(
 *     path="/create-password",
 *     summary="Membuat password pengguna untuk Register Akun",
 *     description="API ini digunakan untuk membuat password pengguna berdasarkan nomor telepon setelah OTP terverifikasi.",
 *     tags={"Akun"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"no_telp", "password", "confirm_password"},
 *             @OA\Property(property="no_telp", type="string", example="08123456789"),
 *             @OA\Property(property="password", type="string", example="Password123"),
 *             @OA\Property(property="confirm_password", type="string", example="Password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password created successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Password created successfully and user registered.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation failed.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validation failed. Please check your input.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="MISSING_USER_DATA",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User data not found in Redis.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="An error occurred on the server.")
 *         )
 *     )
 * )
 */


	public function createPassword(Request $request)
	{
		// Validasi input
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
			'password' => 'required|min:6',  // Minimal panjang password 6 karakter
			'confirm_password' => 'required|same:password',  // Password dan konfirmasi harus sama
		]);
	
		// Jika validasi gagal
		if ($validator->fails()) {
			return response()->json([
				'status' => 'ERROR',
				'errorCode' => 'INVALID_INPUT',
				'message' => 'Invalid data.',
				'errorMessages' => $validator->errors(),
				'data' => []
			], 400);
		}
	
		// Ambil nomor telepon dari request
		$noTelp = $request->input('email');
		// Ambil NIK berdasarkan nomor telepon dari Redis
		$nik = Redis::get('user_by_email:' . $noTelp);
	
		// Debugging: Log NIK
		\Log::info("Redis found NIK for phone {$noTelp}: " . $nik);
	
		// Jika NIK tidak ditemukan
		if (!$nik) {
			return response()->json([
				'status' => 'ERROR',
				'errorCode' => 'MISSING_USER_DATA',
				'message' => 'User data not found in Redis.',
				'data' => []
			], 400);
		}
	
		// Ambil data user dari Redis berdasarkan NIK
		$userData = Redis::get('user:' . $nik);
	
		// Debugging: Log user data
		\Log::info("Redis data for user NIK {$nik}: " . $userData);
	
		// Pastikan data user ada di Redis
		if (!$userData) {
			return response()->json([
				'status' => 'ERROR',
				'errorCode' => 'MISSING_USER_DATA',
				'message' => 'User data not found in Redis.',
				'data' => []
			], 400);
		}
	
		// Decode JSON dari Redis
		$user = json_decode($userData, true);
		$nama = $user['nama'] ?? null;
		$email = $user['no_telp'] ?? null;
		$role_id = $user['role_id'] ?? null;
		$role_name = $user['role_name'] ?? null;
	
		// Validasi jika data masih ada yang kosong
		if (!$nama || !$email || !$role_id) {
			return response()->json([
				'status' => 'ERROR',
				'errorCode' => 'MISSING_USER_INFO',
				'message' => 'Incomplete user data in Redis.',
				'data' => []
			], 400);
		}
	
		// Simpan data user ke dalam database
		try {
			$user = DaftarUser::create([
				'nik' => $nik,
				'nama' => $nama,
				'no_telp' => $email,
				'email' => $noTelp,
				'role_id' => $role_id,
				'password' => Hash::make($request->password), // Hash password
			]);

			  // Jika user adalah asesor (role_id = 2), simpan juga ke tabel data_asesor
			  if ($role_id == 2) {

				// Ambil data dari Redis
				$userData = json_decode(Redis::get('user:' . $nik), true);

				// Pastikan Redis menyimpan no_reg & tanggal_berlaku
				$no_reg = $userData['no_reg'] ?? $request->input('no_reg');
				$tanggal_berlaku = $userData['tanggal_berlaku'] ?? $request->input('tanggal_berlaku');
	
				// Pastikan data asesor ada
				if (!$no_reg || !$tanggal_berlaku) {
					return response()->json([
						'status' => 'ERROR',
						'errorCode' => 'MISSING_ASESOR_DATA',
						'message' => 'No_reg or tanggal_berlaku is missing for asesor.',
						'data' => []
					], 400);
				}
	
				// Simpan ke tabel data_asesor
				DataAsesorModel::create([
					'user_id' => $user->user_id,
					'no_reg' => $no_reg,
					'tanggal_berlaku' => $tanggal_berlaku,
					'aktif' => 1,
				]);
			}

			// Hapus cache di Redis setelah berhasil mendaftar
			Redis::del('user:' . $nik);
			Redis::del('user_by_phone:' . $noTelp);
		
			
			// Respons sukses
			return response()->json([
				'status' => 'SUCCESS',
				'message' => 'Password created successfully and user registered.',
				'data' => [
					'nik' => $nik,
					'name' => $nama,
					'no_telp' => $email,
					'email' => $noTelp,
					'role_id' => $role_id,
					'role_name' => $role_name,
				],
			], 200);
	
		} catch (\Exception $e) {
			// Kesalahan server
			return response()->json([
				'status' => 'ERROR',
				'message' => 'Server error.',
				'error' => $e->getMessage(),
			], 500);
		}
	}
 
	
/**
 * @OA\Post(
 *     path="/new-password",
 *     summary="Memperbarui password pengguna saat melakukan Reset Password",
 *     description="API ini digunakan untuk memperbarui password pengguna setelah verifikasi.",
 *     tags={"Akun"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"no_telp", "new_password", "confirm_password"},
 *             @OA\Property(
 *                 property="no_telp",
 *                 type="string",
 *                 example="089665795500",
 *                 description="Nomor telepon pengguna yang terdaftar."
 *             ),
 *             @OA\Property(
 *                 property="new_password",
 *                 type="string",
 *                 example="strongpassword123",
 *                 description="Password baru untuk pengguna."
 *             ),
 *             @OA\Property(
 *                 property="confirm_password",
 *                 type="string",
 *                 example="strongpassword123",
 *                 description="Konfirmasi password yang harus sama dengan password baru."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password updated successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="message", type="string", example="Password updated successfully."),
 *             @OA\Property(property="data", type="object", additionalProperties={"type": "string"})
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid data.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="errorCode", type="string", example="INVALID_INPUT"),
 *             @OA\Property(property="message", type="string", example="Invalid data."),
 *             @OA\Property(property="errorMessages", type="object"),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="errorCode", type="string", example="USER_NOT_FOUND"),
 *             @OA\Property(property="message", type="string", example="User not found."),
 *             @OA\Property(property="errorMessages", type="array", @OA\Items()),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="errorCode", type="string", example="SERVER_ERROR"),
 *             @OA\Property(property="message", type="string", example="An error occurred on the server."),
 *             @OA\Property(property="errorMessages", type="array", @OA\Items()),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     )
 * )
 */


    public function newPassword(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'no_telp' => 'required|string',          // Nomor telepon wajib diisi
            'password' => 'required|min:6',         // Minimal panjang password 6 karakter
            'confirm_password' => 'required|same:password', // Konfirmasi password harus sama dengan password
        ]);

        if ($validator->fails()) {
			return response()->json([
				'status' => 'ERROR',
				'errorCode' => 'INVALID_INPUT',
				'message' => 'Invalid data.',
				'errorMessages' => $validator->errors(),
				'data' => []
			], 400);
		}

        // Ambil data dari request
        $noTelp = $request->input('no_telp');
        $password = $request->input('password');

        // Cari record di tabel password_reset untuk memvalidasi OTP
        $otpRecord = PasswordReset::where('no_telp', $noTelp)
            ->where('validate_otp_password', true) // OTP harus sudah divalidasi
            ->first();

        // Check if the OTP has been validated
		if (!$otpRecord) {
			return response()->json([
				'status' => 'ERROR',
				'errorCode' => 'OTP_NOT_VALIDATED',
				'message' => 'OTP code has not been validated or was not found.',
				'errorMessages' => [],
				'data' => []
			], 404);
		}

        // Cari pengguna berdasarkan nomor telepon di tabel users (DaftarUser)
        $user = DaftarUser::where('no_telp', $noTelp)->first();

        if (!$user) {
			return response()->json([
				'status' => 'ERROR',
				'errorCode' => 'USER_NOT_FOUND',
				'message' => 'User not found.',
				'errorMessages' => [],
				'data' => []
			], 404);
		}

        // Perbarui password pengguna
        $user->update([
            'password' => Hash::make($password) // Enkripsi password sebelum menyimpan
        ]);

        // Hapus record dari tabel password_reset setelah digunakan
        $otpRecord->delete();

        // Berikan respon sukses
        return response()->json([
			'status' => 'SUCCESS',
			'errorCode' => null,
			'message' => 'Password has been successfully created or updated.',
			'errorMessages' => [],
			'data' => []
		], 200);
    }
	
	
/**
 * @OA\Get(
 *     path="/login-akun",
 *     summary="Login untuk akun nurse",
 *     description="API ini digunakan untuk otentikasi pengguna berdasarkan NIK dan password.",
 *     tags={"Akun"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"nik", "password"},
 *             @OA\Property(
 *                 property="nik",
 *                 type="string",
 *                 example="240076",
 *                 description="Nomor Induk Karyawan (NIK) pengguna."
 *             ),
 *             @OA\Property(
 *                 property="password",
 *                 type="string",
 *                 example="123456",
 *                 description="Password pengguna untuk autentikasi."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Login successful."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="user_id", type="int", example="10"),
 *                 @OA\Property(property="role_id", type="integer", example=1),
 *                 @OA\Property(property="role_name", type="string", example="Nurse"),
 *                 @OA\Property(property="working_unit_id", type="integer", example=101),
 *                 @OA\Property(property="working_unit_name", type="string", example="Pediatrics Unit"),
 *                 @OA\Property(property="working_area_id", type="integer", example=201),
 *                 @OA\Property(property="working_area_name", type="string", example="Hospital A"),
 *          	   @OA\Property(property="jabatan_id", type="integer", example="2"),
 * 				   @OA\Property(property="nama_jabatan", type="string", example="PJ SHIFT"),
 *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgzL2xvZ2luLWFrdW4iLCJpYXQiOjE3Mzc1OTYwNzcsImV4cCI6MTczNzU5OTY3NywibmJmIjoxNzM3NTk2MDc3LCJqdGkiOiI0QjBMQmpGTUxhS0FVT3BvIiwic3ViIjoiMTAiLCJwcnYiOiIzODliYmRmNjI3ZjYwNDc5ODdhNDUzMmExNzdmYTY1MWRhMDQ1YTMxIn0.AsdE8YkcNEzV-sOn5hpwCG0simXmwTj3nkmWT2Y7XBA")
 *             ),
 *             @OA\Property(property="detailed_message", type="string", example="Login successful")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Data validation failed.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="Validation failed. Please check your input."),
 *             @OA\Property(property="errors", type="object", additionalProperties={"type": "string"})
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized access.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="Unauthorized. Incorrect NIK or password."),
 *             @OA\Property(property="errors", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="An error occurred on the server."),
 *             @OA\Property(property="errors", type="array", @OA\Items())
 *         )
 *     )
 * )
 */

    public function LoginAkunNurse(Request $request)
        {
            try {
                // Validasi data
                $validator = Validator::make($request->all(), [
                    'nik' => 'required',
                    'password' => 'required',
					//'player_id' => 'required' // Pastikan frontend mengirim Player ID OneSignal
                ]);

                if ($validator->fails()) {
					return response()->json([
						'status' => 400,
						'message' => 'Data validation failed. Please check your input.',
						'errors' => $validator->errors(),
						'solution' => 'Ensure all required fields are filled. NIK, password, and Player ID cannot be empty.'
					], 400);
				}

                // Cari pengguna berdasarkan NIK
                $user = DaftarUser::where('nik', $request->nik)->first();

                if (!$user) {
					return response()->json([
						'status' => 404,
						'message' => 'User not found.',
						'details' => "An account with NIK '{$request->nik}' is not registered in the system.",
						'solution' => 'Check your NIK again or ensure you have registered.'
					], 404);
				}

                // Periksa password
                if (!Hash::check($request->password, $user->password)) {
					return response()->json([
						'status' => 401,
						'message' => 'Incorrect password.',
						'details' => 'The password you entered does not match the data in the database.',
						'solution' => 'Ensure you enter the correct password. If forgotten, contact the admin to reset your password.'
					], 401);
				}

                // Ambil nama role berdasarkan role_id
                $role = UserRole::where('role_id', $user->role_id)->first();
				// Pastikan role_name ada
                $roleName = $role ? $role->role_name : null;


             	 // Ambil history jabatan terbaru untuk mendapatkan working_unit_id dan jabatan_id
				$latestHistory = DB::table('history_jabatan_user')
				->where('user_id', $user->user_id)
				->latest('created_at') // Ambil yang terbaru berdasarkan tanggal
				->first();

				$working_unit_id = $latestHistory->working_unit_id ?? null;
				$jabatan_id = $latestHistory->jabatan_id ?? null;

				// Ambil nama working unit dan area kerja
				$workingUnit = DB::table('working_unit')
					->join('working_area', 'working_unit.working_area_id', '=', 'working_area.working_area_id')
					->where('working_unit.working_unit_id', $working_unit_id)
					->select(
						'working_unit.working_unit_name',
						'working_area.working_area_id',
						'working_area.working_area_name'
					)
					->first();

				// Ambil nama jabatan
				$jabatan = DB::table('jabatan')
					->where('jabatan_id', $jabatan_id)
					->select('nama_jabatan')
					->first();

				$nama_jabatan = $jabatan ? $jabatan->nama_jabatan : null;
                
                 // Generate JWT token  
                $token = JWTAuth::fromUser($user); 

                // Simpan token ke dalam tabel users  
                $user->token = $token; // Menyimpan token ke dalam kolom token  
                $user->save(); // Simpan perubahan ke database  

				 // Simpan Player ID (OneSignal) ke dalam kolom device_token
				 $user->device_token = $request->player_id;
				 $user->save();
		
				
                // Berhasil login
                return response()->json([
					'status' => 200,
					'message' => 'Login successful.',
					'data' => [
						'name' => $user->nama,
						'nik' => $user->nik,
						'user_id' => $user->user_id, // Return user_id
						'role_id' => $user->role_id,
						'role_name' => $roleName, // Add role_name if available
						'working_unit' => $workingUnit ? [
							'working_unit_id' => $working_unit_id,
							'working_unit_name' => $workingUnit->working_unit_name,
							'working_area_id' => $workingUnit->working_area_id,
							'working_area_name' => $workingUnit->working_area_name
						] : null,
						'jabatan' => [
							'jabatan_id' => $jabatan_id,
							'nama_jabatan' => $nama_jabatan
						],

						'token' => $token,
					]
				], 200);
             } catch (\Exception $e) {
				return response()->json([
					'status' => 500,
					'message' => 'An error occurred on the server.',
					'details' => $e->getMessage(),
					'solution' => 'Please contact the system administrator for further assistance.'
				], 500);
            }
        }  

		
/**
 * @OA\Post(
 *     path="/update-profile/{nik}",
 *     summary="Update data akun perawat berdasarkan Nomor Induk Karyawan.",
 *     description="API ini memungkinkan pembaruan data akun perawat berdasarkan Nomor Induk Karyawan (NIK).",
 *     tags={"Profile"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="Nomor Induk Karyawan (NIK) perawat.",
 *         @OA\Schema(type="string", example="987654321001")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="nama", type="string", example="Siti Aminah", description="Nama lengkap perawat."),
 *             @OA\Property(property="email", type="string", example="siti.aminah@example.com", description="Email perawat."),
 *             @OA\Property(property="no_telp", type="string", example="081234567890", description="Nomor telepon perawat."),
 *             @OA\Property(property="tempat_lahir", type="string", example="Jakarta", description="Tempat lahir perawat."),
 *             @OA\Property(property="tanggal_lahir", type="string", format="date", example="1992-02-15", description="Tanggal lahir dalam format YYYY-MM-DD."),
 *             @OA\Property(property="kewarganegaraan", type="string", example="Indonesia", description="Kewarganegaraan perawat."),
 *             @OA\Property(property="jenis_kelamin", type="string", enum={"L", "P"}, example="P", description="Jenis kelamin: L untuk laki-laki, P untuk perempuan."),
 *             @OA\Property(property="pendidikan", type="string", example="D3 Keperawatan", description="Pendidikan terakhir perawat."),
 *             @OA\Property(property="tahun_lulus", type="integer", example=2010, description="Tahun lulus."),
 *             @OA\Property(property="provinsi", type="string", example="DKI Jakarta", description="Provinsi tempat tinggal."),
 *             @OA\Property(property="kota", type="string", example="Jakarta Selatan", description="Kota tempat tinggal."),
 *             @OA\Property(property="alamat", type="string", example="Jl. Melati No. 5", description="Alamat tempat tinggal."),
 *             @OA\Property(property="kode_pos", type="string", example="12000", description="Kode pos tempat tinggal."),
 *             @OA\Property(property="working_unit_id", type="integer", example=8, description="ID unit kerja."),
 *  		   @OA\Property(property="jabatan_id", type="integer", example=8, description="ID Jabatan."),
 *             @OA\Property(property="foto", type="string", format="binary", description="File foto profil perawat.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Nurse account successfully updated.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Data successfully updated."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="nik", type="string", example="987654321001"),
 *                 @OA\Property(property="nama", type="string", example="Siti Aminah"),
 *                 @OA\Property(property="role_name", type="string", example="Nurse"),
 *                 @OA\Property(property="working_unit_name", type="string", example="UGD"),
 *                 @OA\Property(property="working_area_name", type="string", example="Intalasi Gawat Darurat")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input data.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="Data validation failed. Please check your input."),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="email", type="string", example="The email field is required.")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Nurse account not found.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="pesan", type="string", example="Data tidak ditemukan."),
 *             @OA\Property(property="message", type="string", example="Nurse account not found.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan pada server.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="pesan", type="string", example="Kesalahan server."),
 *             @OA\Property(property="deskripsi", type="string", example="An error occurred while updating the nurse account.")
 *         )
 *     )
 * )
 */

	public function UpdateAkunNurse(Request $request, $nik)
	{
		try {
			// Validasi data
			$validator = Validator::make($request->all(), [
				'nama'            => 'nullable|string|max:255',
				'email'           => 'nullable|email|unique:users,email,' . $nik . ',nik',
				'no_telp' 		  => 'nullable|max:13|unique:users,no_telp,' . $nik . ',nik',
				'tempat_lahir'    => 'nullable|string|max:255',
				'tanggal_lahir'   => 'nullable|date',
				'kewarganegaraan' => 'nullable|string|max:50',
				'jenis_kelamin'   => 'nullable|in:L,P',
				'pendidikan'      => 'nullable|string|max:50',
				'tahun_lulus'     => 'nullable|digits:4|integer',
				'provinsi'        => 'nullable|string|max:255',
				'kota'            => 'nullable|string|max:255',
				'alamat'          => 'nullable|string|max:255',
				'kode_pos'        => 'nullable|string|max:10',
				'dari'            => 'nullable|date',
				'sampai'          => 'nullable|date',
				'foto'            => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
			]);
	
			if ($validator->fails()) {
				return response()->json([
					'status'  => 400,
					'message' => 'Data validation failed.',
					'errors'  => $validator->errors(),
				], 400);
			}
	
			// Cari user berdasarkan NIK
			$user = DaftarUser::where('nik', $nik)->first();
			if (!$user) {
				return response()->json([
					'status'  => 404,
					'message' => 'User not found.',
				], 404);
			}
	
			DB::beginTransaction();

			// Update nomor telepon di tabel users_otps
			//$userOtp = UsersOtp::where('no_telp', $user->no_telp)->first();

			//if (!$userOtp) {
				//return response()->json([
					//'status' => 404,
					//'message' => 'Phone number not found in users_otps.',
				//], 404);

			//}

			// Update no_telp di users_otps
			//$userOtp->update(['no_telp' => $request->no_telp]);

			// Update data user
			$user->update([
				'nama'            => $request->nama ?? $user->nama,
				'email'           => $request->email ?? $user->email,
				'no_telp'         => $request->no_telp ?? $user->no_telp,
				'tempat_lahir'    => $request->tempat_lahir ?? $user->tempat_lahir,
				'tanggal_lahir'   => $request->tanggal_lahir ?? $user->tanggal_lahir,
				'kewarganegaraan' => $request->kewarganegaraan ?? $user->kewarganegaraan,
				'jenis_kelamin'   => $request->jenis_kelamin ?? $user->jenis_kelamin,
				'pendidikan'      => $request->pendidikan ?? $user->pendidikan,
				'tahun_lulus'     => $request->tahun_lulus ?? $user->tahun_lulus,
				'provinsi'        => $request->provinsi ?? $user->provinsi,
				'kota'            => $request->kota ?? $user->kota,
				'alamat'          => $request->alamat ?? $user->alamat,
				'kode_pos'        => $request->kode_pos ?? $user->kode_pos,
			]);
	
			// Update foto jika ada
			if ($request->hasFile('foto')) {
				$file = $request->file('foto');
				if ($file->isValid()) {
					if ($user->foto) {
						Storage::disk('public')->delete($user->foto);
					}
					$path = $file->store('foto_nurse', 'public');
					if (!$path) {
						Log::error('Failed to store foto file.', ['nik' => $nik]);
					}
					$user->foto = $path;
				} else {
					Log::error('Uploaded foto file is not valid.', ['nik' => $nik]);
				}
			} else {
				Log::info('No foto file was uploaded.', ['nik' => $nik]);
			}
	
			// Simpan perubahan ke database
			$user->save();
	
			// Hapus cache data user dari Redis
			Redis::del("user:{$nik}");
	
			// Ambil URL dokumen berdasarkan NIK
			$ijazah = isset($user->ijazah->path_file) ? url('storage/' . $user->ijazah->path_file) : null;
			$ujikom = isset($user->ujikom->path_file) ? url('storage/' . $user->ujikom->path_file) : null;
			$str    = isset($user->str->path_file) ? url('storage/' . $user->str->path_file) : null;
			$sip    = isset($user->sip->path_file) ? url('storage/' . $user->sip->path_file) : null;
	
			// Ambil semua sertifikat yang terkait dengan user
			$sertifikat = $user->sertifikat->map(function ($item) {
				return url('storage/' . $item->path_file);
			});
	
			DB::commit();
	
			// Response lengkap
			return response()->json([
				'status'            => 200,
				'message'           => 'User data updated successfully.',
				'data'              => [
					'nama'              => $user->nama,
					'email'             => $user->email,
					'no_telp'           => $user->no_telp,
					'tempat_lahir'      => $user->tempat_lahir,
					'tanggal_lahir'     => $user->tanggal_lahir,
					'kewarganegaraan'   => $user->kewarganegaraan,
					'jenis_kelamin'     => $user->jenis_kelamin,
					'pendidikan'        => $user->pendidikan,
					'tahun_lulus'       => $user->tahun_lulus,
					'provinsi'          => $user->provinsi,
					'kota'              => $user->kota,
					'alamat'            => $user->alamat,
					'kode_pos'          => $user->kode_pos,
					'foto'              => $user->foto ? url('storage/' . $user->foto) : null,
					'ijazah'            => $ijazah,
					'ujikom'            => $ujikom,
					'str'               => $str,
					'sip'               => $sip,
					'sertifikat'        => $sertifikat,
				],
			], 200);
	
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json(['status' => 500, 'message' => 'Server error.', 'error' => $e->getMessage()], 500);
		}
	}

/**
 * @OA\Get(
 *     path="/get-profile/{nik}",
 *     summary="Mendapatkan data akun perawat berdasarkan NIK",
 *     tags={"Profile"},
 *     description="API ini digunakan untuk mendapatkan data pengguna (perawat) berdasarkan NIK yang diberikan.",
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         description="Nomor Induk Karyawan (NIK) perawat yang ingin diambil datanya.",
 *         required=true,
 *         @OA\Schema(
 *             type="string",
 *             example="1234567890"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Nurse data successfully found.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="status",
 *                 type="integer",
 *                 example=200
 *             ),
 *             @OA\Property(
 *                 property="pesan",
 *                 type="string",
 *                 example="User data found."
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="nama",
 *                     type="string",
 *                     example="I Gede Daiva Andika"
 *                 ),
 *                 @OA\Property(
 *                     property="email",
 *                     type="string",
 *                     example="igededaivaa@gmail.com"
 *                 ),
 *                 @OA\Property(
 *                     property="no_telp",
 *                     type="string",
 *                     example="081234567890"
 *                 ),
 *                 @OA\Property(
 *                     property="role_name",
 *                     type="string",
 *                     example="Asesi"
 *                 ),
 *                 @OA\Property(
 *                     property="foto",
 *                     type="string",
 *                     example="http://app.rsimmanuel.net:9091/storage/foto_nurse/nurse_12345.jpg"
 *                 )
 *    			  @OA\Property(
 *                     property="ijazah",
 *                     type="string",
 *                     example="http://app.rsimmanuel.net:9091/storage/ijazah/awgawghawhawhaw.jpg"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="status",
 *                 type="integer",
 *                 example=404
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="User not found."
 *             ),
 *             @OA\Property(
 *                 property="detail",
 *                 type="string",
 *                 example="Account with NIK '1234567890' is not registered in the system."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error occurred.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="status",
 *                 type="integer",
 *                 example=500
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="A server error occurred."
 *             ),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Error details"
 *             )
 *         )
 *     )
 * )
 */
	public function GetAkunNurseByNIK($nik)
	{
		try {

	
			// Jika tidak ada di Redis, ambil dari database
			$user = DaftarUser::where('nik', $nik)->first();
			
			if (!$user) {
				return response()->json([
					'status' => 404,
					'message' => 'User not found.',
					'detail' => "Account with NIK '{$nik}' is not registered in the system.",
					'solution' => 'Please check your NIK or make sure you are registered.'
				], 404);
			}
	
			// Ambil data tambahan (role, working_unit, working_area)
			$role = DB::table('user_role')->where('role_id', $user->role_id)->first();
			$role_name = $role ? $role->role_name : null;
	
			 // Ambil semua history jabatan
			 $historyJabatan = HistoryJabatan::where('user_id', $user->user_id)->get();

			 // Siapkan array untuk menampung hasilnya
			 $jabatanData = $historyJabatan->map(function ($history) {
				 // Ambil nama working unit
				 $working_unit = DB::table('working_unit')->where('working_unit_id', $history->working_unit_id)->first();
				 $working_unit_name = $working_unit ? $working_unit->working_unit_name : null;
	 
				 // Ambil nama jabatan
				 $jabatan = DB::table('jabatan')->where('jabatan_id', $history->jabatan_id)->first();
				 $nama_jabatan = $jabatan ? $jabatan->nama_jabatan : null;
	 
				 // Kembalikan array yang berisi data jabatan
				 return [
					 'working_unit_id' => $history->working_unit_id,
					 'user_jabatan_id' => $history->user_jabatan_id,
					 'working_unit_name' => $working_unit_name,
					 'jabatan_id' => $history->jabatan_id,
					 'nama_jabatan' => $nama_jabatan,
					 'dari' => $history->dari,
					 'sampai' => $history->sampai
				 ];
			 });
			Log::info('ini user data', ['user' => $user->toArray()]);
			// Ambil URL dokumen dan masa berlaku berdasarkan NIK
			$ijazah = [
				'url' => isset($user->ijazah->path_file) ? url('storage/' . $user->ijazah->path_file) : null,
			];
	
			$ujikom = [
				'url' => isset($user->ujikom->path_file) ? url('storage/' . $user->ujikom->path_file) : null,
				'nomor' => $user->ujikom->nomor_kompetensi ?? null,
				'masa_berlaku' => $user->ujikom->masa_berlaku_kompetensi ?? null
			];
	
			$str = [
				'url' => isset($user->str->path_file) ? url('storage/' . $user->str->path_file) : null,
				'nomor' => $user->str->nomor_str ?? null,
				'masa_berlaku' => $user->str->masa_berlaku_str ?? null
				
			];
	
			$sip = [
				'url' => isset($user->sip->path_file) ? url('storage/' . $user->sip->path_file) : null,
				'nomor' => $user->sip->nomor_sip ?? null,
				'masa_berlaku' => $user->sip->masa_berlaku_sip ?? null
				
			];

			$spk = [
				'url' => isset($user->spk->path_file) ? url('storage/' . $user->spk->path_file) : null,
				'nomor' => $user->spk->nomor_spk ?? null,
				'masa_berlaku' => $user->spk->masa_berlaku_spk ?? null
				
			];
	
			// Ambil semua sertifikat beserta masa berlakunya
			$sertifikat = $user->sertifikat->map(function ($item) {
				return [
					'url' => url('storage/' . $item->path_file),
					'type' => $item->type_sertifikat,
					'nomor' => $item->nomor_sertifikat,
					'masa_berlaku' => $item->masa_berlaku_sertifikat ?? null
					
				];
			});
	
			// Format data user
			$userData = [
				'nama' => $user->nama,
				'email' => $user->email,
				'no_telp' => $user->no_telp,
				'tempat_lahir' => $user->tempat_lahir,
				'tanggal_lahir' => $user->tanggal_lahir,
				'kewarganegaraan' => $user->kewarganegaraan,
				'jenis_kelamin' => $user->jenis_kelamin,
				'pendidikan' => $user->pendidikan,
				'tahun_lulus' => $user->tahun_lulus,
				'provinsi' => $user->provinsi,
				'kota' => $user->kota,
				'alamat' => $user->alamat,
				'kode_pos' => $user->kode_pos,
				'role_id' => $user->role_id,
				'role_name' => $role_name,
				'jabatan_history' => $jabatanData, // ⬅️ Data history jabatan dimasukkan di sini
				'foto' => $user->foto ? url('storage/foto_nurse/' . basename($user->foto)) : null,
				'ijazah' => $ijazah,
				'ujikom' => $ujikom,
				'str' => $str,
				'sip' => $sip,
				'spk' => $spk,
				'sertifikat' => $sertifikat,
			];
		
			return response()->json([
				'status' => 200,
				'message' => 'User data found from database.',
				'data' => $userData,
				'message_detail' => 'User data successfully retrieved and stored in cache.'
			], 200);
		} catch (\Exception $e) {
			return response()->json([
				'status' => 500,
				'message' => 'Server error.',
				'error' => $e->getMessage()
			], 500);
		}
	}
/**
 * @OA\Get(
 *     path="/check-profile/{nik}",
 *     summary="Pengecekan Kelengkapan Data Pengguna Berdasarkan NIK",
 *     description="Fungsi ini digunakan untuk memeriksa apakah data pengguna berdasarkan NIK sudah lengkap atau tidak.",
 *     tags={"Profile"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="Nomor Induk Kependudukan (NIK) dari pengguna yang ingin dicek kelengkapannya.",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data pengguna ditemukan dan lengkap.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="User data found and complete."),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="nama", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", example="johndoe@example.com"),
 *                 @OA\Property(property="no_telp", type="string", example="08123456789"),
 *                 @OA\Property(property="role_name", type="string", example="Administrator"),
 *                 @OA\Property(property="working_area_name", type="string", example="Bandung"),
 *                 @OA\Property(property="foto", type="string", example="http://example.com/storage/foto.jpg")
 *             ),
 *             @OA\Property(property="message_detail", type="string", example="User data successfully retrieved.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="User data is incomplete.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="User data is incomplete."),
 *             @OA\Property(property="missing_fields", type="array", @OA\Items(type="string", example="email")),
 *             @OA\Property(property="solution", type="string", example="Please check the incomplete data.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Pengguna tidak ditemukan.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="User not found."),
 *             @OA\Property(property="detail", type="string", example="Account with NIK '1234567890123456' is not registered in the system."),
 *             @OA\Property(property="solution", type="string", example="Please check your NIK or ensure you have registered.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error occurred.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="An error occurred on the server."),
 *             @OA\Property(property="error", type="string", example="Error message details."),
 *             @OA\Property(property="solution", type="string", example="Please try again later. If the issue persists, contact the system admin.")
 *         )
 *     )
 * )
 */

	public function CheckDataCompleteness($nik)
	{
		try {
			$user = DaftarUser::where('nik', $nik)->first();

			if (!$user) {
				return response()->json([
					'status' => 404,
					'message' => 'User not found. The account with NIK ' . $nik . ' is not registered in the system. Please check your NIK or ensure you have registered.'
				], 404);
			}

			$latestHistory = HistoryJabatan::where('user_id', $user->user_id)->latest()->first();
			$working_unit_id = $latestHistory?->working_unit_id ?? null;
			$jabatan_id = $latestHistory?->jabatan_id ?? null;

			$requiredFields = [
				'nama', 'email', 'no_telp', 'tempat_lahir', 'tanggal_lahir',
				'kewarganegaraan', 'jenis_kelamin', 'pendidikan', 'tahun_lulus',
				'provinsi', 'kota', 'alamat', 'kode_pos', 'role_id'
			];

			$fieldLabels = [
				'nama' => 'Nama Lengkap',
				'email' => 'Email',
				'no_telp' => 'Nomor Telepon',
				'tempat_lahir' => 'Tempat Lahir',
				'tanggal_lahir' => 'Tanggal Lahir',
				'kewarganegaraan' => 'Kewarganegaraan',
				'jenis_kelamin' => 'Jenis Kelamin',
				'pendidikan' => 'Pendidikan',
				'tahun_lulus' => 'Tahun Lulus',
				'provinsi' => 'Provinsi',
				'kota' => 'Kota/Kabupaten',
				'alamat' => 'Alamat Lengkap',
				'kode_pos' => 'Kode Pos',
				'role_id' => 'Peran/Role',
				'working_unit_id' => 'Unit Kerja',
				'jabatan_id' => 'Jabatan',
			];

			$missingFields = [];

			foreach ($requiredFields as $field) {
				if (empty($user->$field)) {
					$missingFields[] = $field;
				}
			}

			if (empty($working_unit_id)) $missingFields[] = 'working_unit_id';
			if (empty($jabatan_id)) $missingFields[] = 'jabatan_id';

			$missingFieldsReadable = array_map(fn($field) => $fieldLabels[$field] ?? $field, $missingFields);

			$documentFields = [
				'ijazah' => isset($user->ijazah->path_file) ? url('storage/' . $user->ijazah->path_file) : null,
				'sip' => isset($user->sip->path_file) ? [
					'url' => url('storage/' . $user->sip->path_file),
					'masa_berlaku' => $user->sip->masa_berlaku_sip ?? null,
					'nomor' => $user->sip->nomor_sip ?? null
				] : null,
				'str' => isset($user->str->path_file) ? [
					'url' => url('storage/' . $user->str->path_file),
					'masa_berlaku' => $user->str->masa_berlaku_str ?? null,
					'nomor' => $user->str->nomor_str ?? null
				] : null
			];

			$missingDocuments = [];
			foreach ($documentFields as $key => $value) {
				if (empty($value)) {
					$missingDocuments[] = strtoupper($key);
				}
			}

			if (count($missingFieldsReadable) > 0 || count($missingDocuments) > 0) {
				$message = "Data berikut belum lengkap: " . implode(', ', $missingFieldsReadable);
				if (count($missingDocuments) > 0) {
					$message .= ". Dokumen yang belum diunggah: " . implode(', ', $missingDocuments);
				}

				return response()->json([
					'status' => 400,
					'message' => $message,
					'solution' => 'Silakan lengkapi data atau dokumen yang belum diisi/unggah.'
				], 400);
			}

			// Jika lengkap, lanjut menyiapkan detail
			$role = DB::table('user_role')->where('role_id', $user->role_id)->first();
			$role_name = $role->role_name ?? null;

			$working_unit = DB::table('working_unit')->where('working_unit_id', $user->working_unit_id)->first();
			$working_unit_name = $working_unit->working_unit_name ?? null;

			$working_area = DB::table('working_area')
				->where('working_area_id', $working_unit?->working_area_id)
				->first();

			$jabatan = DB::table('jabatan')
				->where('jabatan_id', $user->jabatan_id)
				->select('nama_jabatan')
				->first();

			$sertifikat = $user->sertifikat->map(function ($item) {
				return [
					'url' => url('storage/' . $item->path_file),
					'masa_berlaku' => $item->masa_berlaku_sertifikat ?? null,
					'nomor' => $item->nomor_sertifikat ?? null
				];
			});

			$userData = [
				'nama' => $user->nama,
				'email' => $user->email,
				'no_telp' => $user->no_telp,
				'tempat_lahir' => $user->tempat_lahir,
				'tanggal_lahir' => $user->tanggal_lahir,
				'kewarganegaraan' => $user->kewarganegaraan,
				'jenis_kelamin' => $user->jenis_kelamin,
				'pendidikan' => $user->pendidikan,
				'tahun_lulus' => $user->tahun_lulus,
				'provinsi' => $user->provinsi,
				'kota' => $user->kota,
				'alamat' => $user->alamat,
				'kode_pos' => $user->kode_pos,
				'role_id' => $user->role_id,
				'role_name' => $role_name,
				'working_unit_id' => $user->working_unit_id,
				'working_unit_name' => $working_unit_name,
				'working_area_id' => $working_area?->working_area_id,
				'working_area_name' => $working_area?->working_area_name,
				'jabatan_id' => $user->jabatan_id,
				'nama_jabatan' => $jabatan?->nama_jabatan,
				'ijazah' => $documentFields['ijazah'],
				'sip' => $documentFields['sip'],
				'str' => $documentFields['str'],
				'sertifikat' => $sertifikat,
				'foto' => $user->foto ? url('storage/foto_nurse/' . basename($user->foto)) : null,
			];

			return response()->json([
				'status' => 200,
				'message' => 'User data found and complete.',
				'data' => $userData
			], 200);
		} catch (\Exception $e) {
			return response()->json([
				'status' => 500,
				'message' => 'Terjadi kesalahan pada server.',
				'error' => $e->getMessage()
			], 500);
		}
	}



	/**
 * @OA\Put(
 *     path="/edit-jabatan-working/{nik}",
 *     summary="Edit data History Jabatan berdasarkan Nomor Induk Karyawan (NIK).",
 *     description="API ini memungkinkan pembaruan atau edit data history jabatan pengguna berdasarkan NIK dan ID jabatan.",
 *     tags={"History Jabatan"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="Nomor Induk Karyawan (NIK) pengguna yang history jabatan-nya akan diperbarui.",
 *         @OA\Schema(type="string", example="1234567890")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="user_jabatan_id", type="integer", example=1, description="ID history jabatan yang akan diperbarui."),
 *             @OA\Property(property="working_unit_id", type="integer", example=8, description="ID unit kerja yang akan diperbarui."),
 *             @OA\Property(property="jabatan_id", type="integer", example=2, description="ID jabatan yang akan diperbarui."),
 *             @OA\Property(property="dari", type="string", format="date", example="2023-01-01", description="Tanggal mulai jabatan."),
 *             @OA\Property(property="sampai", type="string", format="date", example="2023-12-31", description="Tanggal selesai jabatan.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="History Jabatan berhasil diperbarui.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="History Jabatan updated successfully."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="user_jabatan_id", type="integer", example=1),
 *                 @OA\Property(property="working_unit_id", type="integer", example=8),
 *                 @OA\Property(property="jabatan_id", type="integer", example=2),
 *                 @OA\Property(property="dari", type="string", format="date", example="2023-01-01"),
 *                 @OA\Property(property="sampai", type="string", format="date", example="2023-12-31")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi data input gagal.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="Data validation failed."),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="user_jabatan_id", type="array", items=@OA\Items(type="string", example="The user_jabatan_id field is required."))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan (pengguna atau history jabatan).",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan pada server.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Failed to update History Jabatan: {error message}")
 *         )
 *     )
 * )
 */
public function updateHistoryJabatan(Request $request, $nik)
{
	try {
		// Validasi input
		$validator = Validator::make($request->all(), [
			'user_jabatan_id'  => 'required|integer|exists:history_jabatan_user,user_jabatan_id',
			'working_unit_id'  => 'nullable|integer|exists:working_unit,working_unit_id',
			'jabatan_id'       => 'nullable|integer|exists:jabatan,jabatan_id',
			'dari'             => 'nullable|date',
			'sampai'           => 'nullable|date',
		]);

		if ($validator->fails()) {
			return response()->json([
				'status'  => 400,
				'message' => 'Data validation failed.',
				'errors'  => $validator->errors(),
			], 400);
		}

		// Cari user berdasarkan NIK
		$user = DaftarUser::where('nik', $nik)->first();
		if (!$user) {
			return response()->json([
				'status'  => 404,
				'message' => 'User not found.',
			], 404);
		}

		DB::beginTransaction();

		// Cari history berdasarkan user_jabatan_id dan user_id
		$history = HistoryJabatan::where('user_jabatan_id', $request->user_jabatan_id)
			->where('user_id', $user->user_id)
			->first();

		if (!$history) {
			return response()->json([
				'status'  => 404,
				'message' => 'History Jabatan not found.',
			], 404);
		}

		// Update data
		$history->update([
			'working_unit_id' => $request->working_unit_id ?? $history->working_unit_id,
			'jabatan_id'      => $request->jabatan_id ?? $history->jabatan_id,
			'dari'            => $request->filled('dari') ? $request->dari : $history->dari,
			'sampai'          => $request->filled('sampai') ? $request->sampai : $history->sampai,
		]);

		DB::commit();

		return response()->json([
			'status'  => 200,
			'message' => 'History Jabatan updated successfully.',
			'data'    => $history,
		], 200);

	} catch (\Exception $e) {
		DB::rollBack();
		return response()->json([
			'status'  => 500,
			'message' => 'Failed to update History Jabatan: ' . $e->getMessage(),
		], 500);
	}
}

/**
 * @OA\Delete(
 *     path="/delete-jabatan-working/{nik}",
 *     summary="Hapus riwayat jabatan user",
 *     description="Menghapus data history jabatan berdasarkan NIK dan user_jabatan_id",
 *     tags={"History Jabatan"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="NIK user",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_jabatan_id"},
 *             @OA\Property(
 *                 property="user_jabatan_id",
 *                 type="integer",
 *                 example=5
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="History Jabatan deleted successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="History Jabatan deleted successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Data validation failed.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="Data validation failed."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User or history not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to delete History Jabatan.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Failed to delete History Jabatan: error message")
 *         )
 *     )
 * )
 */

public function deleteHistoryJabatan(Request $request, $nik)
{
	try {
		// Validasi input
		$validator = Validator::make($request->all(), [
			'user_jabatan_id' => 'required|integer|exists:history_jabatan_user,user_jabatan_id',
		]);

		if ($validator->fails()) {
			return response()->json([
				'status'  => 400,
				'message' => 'Data validation failed.',
				'errors'  => $validator->errors(),
			], 400);
		}

		// Cari user berdasarkan NIK
		$user = DaftarUser::where('nik', $nik)->first();
		if (!$user) {
			return response()->json([
				'status'  => 404,
				'message' => 'User not found.',
			], 404);
		}

		DB::beginTransaction();

		// Cari history berdasarkan user_jabatan_id dan user_id
		$history = HistoryJabatan::where('user_jabatan_id', $request->user_jabatan_id)
			->where('user_id', $user->user_id)
			->first();

		if (!$history) {
			DB::rollBack();
			return response()->json([
				'status'  => 404,
				'message' => 'History Jabatan not found.',
			], 404);
		}

		// Hapus data
		$history->delete();

		DB::commit();

		return response()->json([
			'status'  => 200,
			'message' => 'History Jabatan deleted successfully.',
		], 200);

	} catch (\Exception $e) {
		DB::rollBack();
		return response()->json([
			'status'  => 500,
			'message' => 'Failed to delete History Jabatan: ' . $e->getMessage(),
		], 500);
	}
}

/**
* @OA\Post(
*     path="/input-jabatan-working/{nik}",
*     summary="Insert data History Jabatan baru berdasarkan Nomor Induk Karyawan (NIK).",
*     description="API ini memungkinkan penambahan data history jabatan pengguna berdasarkan NIK dan informasi jabatan.",
*     tags={"History Jabatan"},
*     @OA\Parameter(
*         name="nik",
*         in="path",
*         required=true,
*         description="Nomor Induk Karyawan (NIK) pengguna yang history jabatan-nya akan ditambahkan.",
*         @OA\Schema(type="string", example="1234567890")
*     ),
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             type="object",
*             @OA\Property(property="working_unit_id", type="integer", example=8, description="ID unit kerja yang akan ditambahkan."),
*             @OA\Property(property="jabatan_id", type="integer", example=2, description="ID jabatan yang akan ditambahkan."),
*             @OA\Property(property="dari", type="string", format="date", example="2023-01-01", description="Tanggal mulai jabatan."),
*             @OA\Property(property="sampai", type="string", format="date", example="2023-12-31", description="Tanggal selesai jabatan (opsional).")
*         )
*     ),
*     @OA\Response(
*         response=201,
*         description="History Jabatan berhasil ditambahkan.",
*         @OA\JsonContent(
*             type="object",
*             @OA\Property(property="status", type="integer", example=201),
*             @OA\Property(property="message", type="string", example="History Jabatan inserted successfully."),
*             @OA\Property(property="data", type="object",
*                 @OA\Property(property="user_jabatan_id", type="integer", example=1),
*                 @OA\Property(property="user_id", type="integer", example=123),
*                 @OA\Property(property="working_unit_id", type="integer", example=8),
*                 @OA\Property(property="jabatan_id", type="integer", example=2),
*                 @OA\Property(property="dari", type="string", format="date", example="2023-01-01"),
*                 @OA\Property(property="sampai", type="string", format="date", example="2023-12-31")
*             )
*         )
*     ),
*     @OA\Response(
*         response=400,
*         description="Validasi data input gagal.",
*         @OA\JsonContent(
*             type="object",
*             @OA\Property(property="status", type="integer", example=400),
*             @OA\Property(property="message", type="string", example="Data validation failed."),
*             @OA\Property(property="errors", type="object",
*                 @OA\Property(property="working_unit_id", type="array", items=@OA\Items(type="string", example="The working_unit_id field is required."))
*             )
*         )
*     ),
*     @OA\Response(
*         response=404,
*         description="Data pengguna tidak ditemukan.",
*         @OA\JsonContent(
*             type="object",
*             @OA\Property(property="status", type="integer", example=404),
*             @OA\Property(property="message", type="string", example="User not found.")
*         )
*     ),
*     @OA\Response(
*         response=500,
*         description="Kesalahan pada server.",
*         @OA\JsonContent(
*             type="object",
*             @OA\Property(property="status", type="integer", example=500),
*             @OA\Property(property="message", type="string", example="Failed to insert History Jabatan: {error message}")
*         )
*     )
* )
*/

public function insertHistoryJabatan(Request $request, $nik)
{
	try {
		$validator = Validator::make($request->all(), [
			'working_unit_id' => 'required|integer|exists:working_unit,working_unit_id',
			'jabatan_id'      => 'required|integer|exists:jabatan,jabatan_id',
			'dari'            => 'required|date',
			'sampai'          => 'nullable|date',
		]);

		if ($validator->fails()) {
			return response()->json([
				'status' => 400,
				'message' => 'Data validation failed.',
				'errors' => $validator->errors(),
			], 400);
		}

		$user = DaftarUser::where('nik', $nik)->first();
		if (!$user) {
			return response()->json([
				'status' => 404,
				'message' => 'User not found.',
			], 404);
		}

		$history = HistoryJabatan::create([
			'user_id'         => $user->user_id,
			'working_unit_id' => $request->working_unit_id,
			'jabatan_id'      => $request->jabatan_id,
			'dari'            => $request->dari,
			'sampai'          => $request->sampai,
		]);

		return response()->json([
			'status' => 201,
			'message' => 'History Jabatan inserted successfully.',
			'data'    => [
				'user_jabatan_id' => $history->user_jabatan_id, // ⬅️ Ini munculkan
				'user_id'         => $history->user_id,
				'working_unit_id' => $history->working_unit_id,
				'jabatan_id'      => $history->jabatan_id,
				'dari'            => $history->dari,
				'sampai'          => $history->sampai,
			],
		], 201);

	} catch (\Exception $e) {
		return response()->json([
			'status' => 500,
			'message' => 'Failed to insert History Jabatan: ' . $e->getMessage(),
		], 500);
	}
}

	
}


