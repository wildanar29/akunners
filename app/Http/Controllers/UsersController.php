<?php

namespace App\Http\Controllers;

use App\Models\DaftarUser;
use App\Models\HistoryJabatan;
use App\Models\WorkingUnit;
use App\Models\JabatanModel;
use App\Models\Role;
use App\Models\UserRole;
use App\Models\UsersOtp;
use App\Models\DataAsesorModel;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;
use Tymon\JWTAuth\Facades\JWTAuth; 

	/**
	* @OA\Info(
	*     title="API Documentation",
	*     version="1.0.0",
	*     description="Dokumentasi API untuk aplikasi Akunurse", 
	* )
	*/
class UsersController extends Controller
{
	public function RegisterAkunNurse(Request $request)
	{
		try {
			// Gunakan role_id dari request, tapi akan diproses sebagai current_role_id
			$requestData = $request->all();
			$roleId = $requestData['role_id'] ?? null;

			$rules = [
				'nik' => 'required|unique:users',
				'nama' => 'required|string|max:255',
				'email' => 'required|email|unique:users',
				'role_id' => 'required|integer|exists:roles,role_id', // tetap role_id untuk input
				'no_telp' => 'required|max:13|unique:users',
			];

			if (in_array($roleId, [2, 3])) {
				$rules['no_reg'] = 'required|string';
				$rules['valid_from'] = 'required|date';
				$rules['valid_until'] = 'required|date|after_or_equal:valid_from';
			}

			$validator = Validator::make($requestData, $rules);

			if ($validator->fails()) {
				return response()->json([
					'status' => 400,
					'message' => 'Validation failed. Please check your input.',
					'errors' => $validator->errors(),
				], 400);
			}

			// Ambil role name berdasarkan role_id dari request
			$role = Role::where('role_id', $roleId)->first();
			$roleName = $role ? $role->role_name : null;

			// Data yang akan disimpan ke Redis
			$data = [
				'nik' => $requestData['nik'],
				'nama' => $requestData['nama'],
				'email' => $requestData['email'],
				'no_telp' => $requestData['no_telp'],
				'current_role_id' => $roleId, // simpan sebagai current_role_id
				'role_name' => $roleName
			];

			if (in_array($roleId, [2, 3])) {
				$data['no_reg'] = $requestData['no_reg'];
				$data['valid_from'] = $requestData['valid_from'];
				$data['valid_until'] = $requestData['valid_until'];
			}

			// Simpan ke Redis
			Redis::set('user:' . $requestData['nik'], json_encode($data));
			Redis::set('user_by_email:' . $requestData['email'], $requestData['nik']);

			// Response
			return response()->json([
				'status' => 200,
				'message' => 'Account registered successfully and stored in Redis.',
				'data' => $data
			], 200);

		} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
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
	}

	public function createPassword(Request $request)
	{
		// Validasi input
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
			'password' => 'required|min:6',
			'confirm_password' => 'required|same:password',
		]);

		if ($validator->fails()) {
			\Log::warning('Password creation failed: Invalid input.', [
				'errors' => $validator->errors(),
				'request' => $request->all()
			]);

			return response()->json([
				'status' => 'ERROR',
				'errorCode' => 'INVALID_INPUT',
				'message' => 'Invalid data.',
				'errorMessages' => $validator->errors(),
				'data' => []
			], 400);
		}

		$emailInput = $request->input('email');
		$nik = Redis::get('user_by_email:' . $emailInput);

		if (!$nik) {
			\Log::error('NIK not found in Redis for email.', ['email' => $emailInput]);
			return response()->json([
				'status' => 'ERROR',
				'errorCode' => 'MISSING_USER_DATA',
				'message' => 'User data not found in Redis.',
				'data' => []
			], 400);
		}

		$userData = Redis::get('user:' . $nik);
		if (!$userData) {
			\Log::error('User data not found in Redis by NIK.', ['nik' => $nik]);
			return response()->json([
				'status' => 'ERROR',
				'errorCode' => 'MISSING_USER_DATA',
				'message' => 'User data not found in Redis.',
				'data' => []
			], 400);
		}

		$user = json_decode($userData, true);
		$nama = $user['nama'] ?? null;
		$no_telp = $user['no_telp'] ?? null;
		$current_role_id = $user['current_role_id'] ?? $user['role_id'] ?? null;
		$role_name = $user['role_name'] ?? null;

		if (!$nama || !$no_telp || !$current_role_id) {
			\Log::error('Incomplete user data from Redis.', compact('nik', 'nama', 'no_telp', 'current_role_id', 'user'));
			return response()->json([
				'status' => 'ERROR',
				'errorCode' => 'MISSING_USER_INFO',
				'message' => 'Incomplete user data in Redis.',
				'data' => []
			], 400);
		}

		try {
			// Mulai transaction
			DB::beginTransaction();

			$user = DaftarUser::create([
				'nik' => $nik,
				'nama' => $nama,
				'no_telp' => $no_telp,
				'email' => $emailInput,
				'current_role_id' => $current_role_id,
				'password' => Hash::make($request->password),
			]);

			// Simpan role sesuai current_role_id dan semua role di bawahnya
			for ($role = 1; $role <= $current_role_id; $role++) {
				UserRole::create([
					'user_id' => $user->user_id,
					'role_id' => $role,
				]);
			}

			// Jika asesor
			if (in_array($current_role_id, [2, 3])) {
				$userData = json_decode(Redis::get('user:' . $nik), true);
				$no_reg = $userData['no_reg'] ?? $request->input('no_reg');
				$valid_from = $userData['valid_from'] ?? $request->input('valid_from');
				$valid_until = $userData['valid_until'] ?? $request->input('valid_until');

				if (!$no_reg || !$valid_from || !$valid_until) {
					\Log::error('Asesor data incomplete.', compact('nik', 'no_reg', 'valid_from', 'valid_until', 'userData'));
					DB::rollBack();
					return response()->json([
						'status' => 'ERROR',
						'errorCode' => 'MISSING_ASESOR_DATA',
						'message' => 'No_reg, valid_from, or valid_until is missing for asesor.',
						'data' => []
					], 400);
				}

				DataAsesorModel::create([
					'user_id' => $user->user_id,
					'no_reg' => $no_reg,
					'valid_from' => $valid_from,
					'valid_until' => $valid_until,
					'aktif' => 1,
				]);
			}

			DB::commit(); // Sukses

			// Bersihkan Redis
			Redis::del('user:' . $nik);
			Redis::del('user_by_email:' . $emailInput);

			return response()->json([
				'status' => 'SUCCESS',
				'message' => 'Password created successfully and user registered.',
				'data' => compact('nik', 'nama', 'no_telp', 'emailInput', 'current_role_id', 'role_name')
			], 200);

		} catch (\Exception $e) {
			DB::rollBack(); // Gagal, rollback semua query
			\Log::error('Exception during user registration.', [
				'error' => $e->getMessage(),
				'trace' => $e->getTraceAsString()
			]);

			return response()->json([
				'status' => 'ERROR',
				'message' => 'Server error.',
				'error' => $e->getMessage(),
			], 500);
		}
	}

	public function GantiPassword(Request $request)
	{
		try {
			// Validasi input
			$validator = Validator::make($request->all(), [
				'old_password' => 'required',
				'new_password' => 'required|min:6',
				'confirm_password' => 'required|same:new_password',
			]);

			if ($validator->fails()) {
				return response()->json([
					'status' => 400,
					'message' => 'Data validation failed. Please check your input.',
					'errors' => $validator->errors(),
					'solution' => 'Ensure old password, new password, and confirm password are filled correctly.'
				], 400);
			}

			// Ambil user dari JWT
			$user = auth()->user();

			if (!$user) {
				return response()->json([
					'status' => 401,
					'message' => 'Unauthorized.',
					'solution' => 'Please login again to continue.'
				], 401);
			}

			// Cek apakah old_password sesuai dengan password di database
			if (!Hash::check($request->old_password, $user->password)) {
				return response()->json([
					'status' => 401,
					'message' => 'Old password is incorrect.',
					'solution' => 'Please enter your current valid password.'
				], 400);
			}

			// Update password dengan hash baru
			$user->password = Hash::make($request->new_password);
			$user->save();

			return response()->json([
				'status' => 200,
				'message' => 'Password berhasil diupdate.',
				// 'solution' => 'Use your new password to login next time.'
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

	public function LoginAkunNurse(Request $request)
	{
		try {
			// Validasi data
			$validator = Validator::make($request->all(), [
				'nik' => 'required',
				'password' => 'required',
				'player_id' => 'sometimes|string|nullable' // Player ID opsional, bisa null
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

			// Ambil nama role
			$role = Role::where('role_id', $user->current_role_id)->first();
			$roleName = $role ? $role->role_name : null;

			// Ambil history jabatan terbaru
			$latestHistory = DB::table('history_jabatan_user')
				->where('user_id', $user->user_id)
				->latest('created_at')
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

			// Simpan token ke database
			$user->token = $token;

			// Simpan atau perbarui device_token hanya jika ada player_id baru
			if ($request->filled('player_id')) {
				$user->device_token = $request->player_id;
			}

			$user->save();

			// Berhasil login
			return response()->json([
				'status' => 200,
				'message' => 'Login successful.',
				'data' => [
					'name' => $user->nama,
					'nik' => $user->nik,
					'user_id' => $user->user_id,
					'current_role' => [
						'role_id' => $user->current_role_id,
						'role_name' => $roleName
					],
					'role_name' => $roleName,
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
	

    public function newPassword(Request $request)
	{
		// Validasi input
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',                   // Email wajib diisi dan valid
			'password' => 'required|min:6',                // Password minimal 6 karakter
			'confirm_password' => 'required|same:password' // Konfirmasi password harus cocok
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
		$email = $request->input('email');
		$password = $request->input('password');

		// Cari record di tabel password_reset berdasarkan email
		$otpRecord = PasswordReset::where('email', $email)
			->where('validate_otp_password', true)
			->first();

		if (!$otpRecord) {
			return response()->json([
				'status' => 'ERROR',
				'errorCode' => 'OTP_NOT_VALIDATED',
				'message' => 'OTP code has not been validated or was not found.',
				'errorMessages' => [],
				'data' => []
			], 404);
		}

		// Cari pengguna berdasarkan email di tabel users (DaftarUser)
		$user = DaftarUser::where('email', $email)->first();

		if (!$user) {
			return response()->json([
				'status' => 'ERROR',
				'errorCode' => 'USER_NOT_FOUND',
				'message' => 'User not found.',
				'errorMessages' => [],
				'data' => []
			], 404);
		}

		// Update password
		$user->update([
			'password' => Hash::make($password)
		]);

		// Hapus record OTP setelah dipakai
		$otpRecord->delete();

		// Respon sukses
		return response()->json([
			'status' => 'SUCCESS',
			'errorCode' => null,
			'message' => 'Password has been successfully created or updated.',
			'errorMessages' => [],
			'data' => []
		], 200);
	}

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
			$role = Role::where('role_id', $user->current_role_id)->first();
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
					'sertifikat_id' => $item->sertifikat_id,
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
				'role_id' => $user->current_role_id, // ⬅️ Gunakan current_role_id
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

	public function CheckDataCompleteness($nik)
	{
		try {

			$user = DaftarUser::where('nik', $nik)->first();

			if (!$user) {
				return response()->json([
					'status' => 404,
					'message' => 'User not found.',
					'detail' => "The account with NIK '{$nik}' is not registered in the system.",
					'solution' => 'Please check your NIK or ensure you have registered.'
				], 404);
			}

			$latestHistory = HistoryJabatan::where('user_id', $user->user_id)->latest()->first();
			$working_unit_id = $latestHistory?->working_unit_id ?? null;
			$jabatan_id = $latestHistory?->jabatan_id ?? null;

			$requiredFields = [
				'nama', 'email', 'no_telp', 'tempat_lahir', 'tanggal_lahir',
				'kewarganegaraan', 'jenis_kelamin', 'pendidikan', 'tahun_lulus',
				'provinsi', 'kota', 'alamat', 'kode_pos', 'current_role_id'
			];

			$missingFields = [];

			foreach ($requiredFields as $field) {
				if (empty($user->$field)) {
					$missingFields[] = $field;
				}
			}

			if (empty($working_unit_id)) {
				$missingFields[] = 'working_unit_id';
			}
			if (empty($jabatan_id)) {
				$missingFields[] = 'jabatan_id';
			}

			// Dokumen yang diperiksa
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
				] : null,

				// 'ujikom' => isset($user->ujikom->path_file) ? [
				// 	'url' => url('storage/' . $user->ujikom->path_file),
				// 	'masa_berlaku' => $user->ujikom->masa_berlaku_kompetensi ?? null,
				// 	'nomor' => $user->ujikom->nomor_kompetensi ?? null
				// ] : null
			];

			$missingDocuments = [];
			foreach ($documentFields as $key => $value) {
				if (empty($value)) {
					$missingDocuments[] = $key;
				}
			}

			// if (empty($user->ujikom?->path_file)) {
			// 	$missingDocuments[] = 'ujikom';
			// }

			if (count($missingFields) > 0 || count($missingDocuments) > 0) {
				return response()->json([
					'status' => 400,
					'message' => 'User data is incomplete.',
					'missing_fields' => $missingFields,
					'missing_documents' => $missingDocuments,
					'detail' => "The following data is incomplete: " . implode(', ', $missingFields) .
						(count($missingDocuments) > 0 ? ". Missing documents: " . implode(', ', $missingDocuments) : ""),
					'solution' => 'Please review the missing data and complete the required information.'
				], 400);
			}

			$role = Role::where('role_id', $user->current_role_id)->first();
			$role_name = $role ? $role->role_name : null;

			$working_unit = DB::table('working_unit')->where('working_unit_id', $user->working_unit_id)->first();
			$working_unit_name = $working_unit ? $working_unit->working_unit_name : null;

			$working_area = DB::table('working_area')
				->where('working_area_id', $working_unit ? $working_unit->working_area_id : null)
				->first();
			$working_area_id = $working_area ? $working_area->working_area_id : null;
			$working_area_name = $working_area ? $working_area->working_area_name : null;

			$jabatan = DB::table('jabatan')
				->where('jabatan_id', $user->jabatan_id)
				->select('nama_jabatan')
				->first();
			$jabatan_id = $jabatan ? $user->jabatan_id : null;
			$nama_jabatan = $jabatan ? $jabatan->nama_jabatan : null;

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
				'role_id' => $user->current_role_id,
				'role_name' => $role_name,
				'working_unit_id' => $user->working_unit_id,
				'working_unit_name' => $working_unit_name,
				'working_area_id' => $working_area_id,
				'working_area_name' => $working_area_name,
				'jabatan_id' => $jabatan_id,
				'nama_jabatan' => $nama_jabatan,
				'ijazah' => $documentFields['ijazah'],
				'sip' => $documentFields['sip'],
				'str' => $documentFields['str'],
				// 'ujikom' => $documentFields['ujikom'],
				'sertifikat' => $sertifikat,
				'foto' => $user->foto ? url('storage/foto_nurse/' . basename($user->foto)) : null,
			];

			return response()->json([
				'status' => 200,
				'message' => 'User data found and complete.',
				'source' => 'Database',
				'data' => $userData,
				'message_detail' => 'User data retrieved successfully from the database.'
			], 200);

		} catch (\Exception $e) {
			return response()->json([
				'status' => 500,
				'message' => 'An error occurred on the server.',
				'kesalahan' => $e->getMessage(),
				'solusi' => 'Please try again later. If the issue persists, contact the system admin.'
			], 500);
		}
	}

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


