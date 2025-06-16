<?php

namespace App\Http\Controllers;

use App\Models\DaftarUser; // Pastikan model User sudah digunakan
use App\Models\UsersOtp; // Model untuk tabel users_otp
use App\Models\PasswordReset; // Model untuk tabel password_reset
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Service\WablasService;  
use Exception;
use Illuminate\Support\Facades\Redis; // Tambahkan Redis facade
use Illuminate\Support\Facades\Http;
use Postmark\PostmarkClient;
use GuzzleHttp\Client as GuzzleClient;



class WhatsappController extends Controller
{
    private $wablasService;

    public function __construct(WablasService $wablasService)
    {
        $this->wablasService = $wablasService;
    }
	

    public function sendOtp(Request $request)
    {
        // Ambil NIK dari request
        $nik = $request->input('nik');
    
        // Validasi untuk memastikan NIK ada
        if (!$nik) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'MISSING_NIK',
                'message' => 'NIK not found.',
                'data' => []
            ], 400);
        }
    
        // Gunakan NIK untuk mengambil data user lengkap dari Redis
        $userData = Redis::get('user:' . $nik);
    
        // Debug: Log data user
        \Log::info("Redis data for user NIK {$nik}: " . $userData);
    
        // Pastikan data ada di Redis
        if (!$userData) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'MISSING_USER_DATA',
                'message' => 'User data not found in Redis.',
                'data' => []
            ], 400);
        }
    
        // Decode JSON untuk mengambil nama dan email
        $user = json_decode($userData, true);
        $nama = $user['nama'] ?? null;
        $email = $user['email'] ?? null;
    
        // Validasi jika nama atau email tidak ditemukan
        if (!$nama || !$email) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'MISSING_DATA',
                'message' => 'Name or Email not found in Redis.',
                'data' => []
            ], 400);
        }
    
        // Cek apakah email sudah ada di tabel daftar_users
        $userExists = DaftarUser::where('email', $email)->exists();
        if ($userExists) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'DUPLICATE_ENTRY',
                'message' => 'Email is already in use.',
                'data' => []
            ], 400);
        }
    
        // Cek apakah email sudah ada di tabel users_otps
        $otpRecord = UsersOtp::where('email', $email)->first();
    
        // Generate OTP angka acak (6 digit)
        $kodeOtp = rand(100000, 999999);
    
        // Waktu kadaluarsa OTP (5 menit dari sekarang)
        $expiresAt = Carbon::now('UTC')->addMinutes(5);
    
        if ($otpRecord) {
            // Jika email ada di tabel users_otps, update OTP
            $otpRecord->update([
                'otp' => $kodeOtp,
                'expires_at' => $expiresAt,
                'validate_otp' => false,
            ]);
        } else {
            // Jika email tidak ada di tabel users_otps, buat record baru
            $otpRecord = UsersOtp::create([
                'email' => $email,
                'otp' => $kodeOtp,
                'expires_at' => $expiresAt,
                'validate_otp' => false,
            ]);
        }
    
        $timeInJakarta = $otpRecord->expires_at->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s');
    
        // Menyusun pesan
        $message = "Hai Nurse " . $nama . ",\n\n";
        $message .= "Ini adalah kode OTP untuk verifikasi akun Anda: " . $kodeOtp . "\n\n";
        $message .= "Mohon jangan berikan kode ini kepada orang lain.\n\n";
        $message .= "Kode berlaku hingga: " . $timeInJakarta . " (WIB)";
    
        $guzzleClient = new GuzzleClient([
		'verify' => false, // Disable SSL verification
		]);

		$client = new GuzzleClient([
			'verify' => false, // ✅ bypass SSL
			'base_uri' => 'https://api.postmarkapp.com/',
			'headers' => [
				'X-Postmark-Server-Token' => env('POSTMARK_TOKEN'),
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
			]
		]);


        $response = $client->post('email', [
			'json' => [
				'From' => env('MAIL_FROM_ADDRESS'),
				'To' => $email,
				'Subject' => 'Kode OTP untuk Nurse ' . $nama,
				'HtmlBody' => nl2br($message),
			]
		]);

    
        return response()->json([
            'status' => 'OK',
            'message' => 'OTP has been sent successfully to your email.',
            'data' => [
                'email' => $email,
                'expires_at' => $timeInJakarta,
            ]
        ]);
    }


    public function validateOtp(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|integer'
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

        $noTelp = $request->input('email');
        $otp = $request->input('otp');

        // Cari record OTP berdasarkan nomor telepon
        $otpRecord = UsersOtp::where('email', $noTelp)
            ->where('otp', $otp)
            ->first();

        // Periksa apakah OTP ditemukan
        if (!$otpRecord) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'OTP_NOT_FOUND',
				'message' => 'OTP code not found or incorrect.',
                'errorMessages' => [],
                'data' => []
            ], 404);
        }

        // Periksa apakah OTP sudah kedaluwarsa
        if (Carbon::now('UTC')->greaterThan($otpRecord->expires_at)) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'OTP_EXPIRED',
				'message' => 'OTP code has expired.',
                'errorMessages' => [],
                'data' => []
            ], 400);
        }

        // Tandai OTP sebagai telah divalidasi
        $otpRecord->update(['validate_otp' => true]);

        return response()->json([
            'status' => 'SUCCESS',
            'errorCode' => null,
			'message' => 'OTP code successfully validated.',
            'errorMessages' => [],
            'data' => []
        ], 200);
    }
	


    public function resetOtp(Request $request)
    {
        // Ambil nomor telepon dari request
        $noTelp = $request->input('no_telp');

        // Validasi untuk memastikan nomor telepon ada
        if (!$noTelp) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'MISSING_PHONE_NUMBER',
                'message' => 'No telephone number not found.',
                'data' => []
            ], 400);
        }

        // Cari NIK berdasarkan nomor telepon di Redis
        $nik = Redis::get('user_by_phone:' . $noTelp);

        // Debug: Log NIK yang ditemukan
        \Log::info("Redis found NIK for phone {$noTelp}: " . $nik);

        // Jika NIK tidak ditemukan, berarti user tidak terdaftar
        if (!$nik) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'MISSING_USER_DATA',
                'message' => 'User data not found in Redis.',
                'data' => []
            ], 400);
        }

        // Gunakan NIK untuk mengambil data user lengkap
        $userData = Redis::get('user:' . $nik);

        // Debug: Log data user
        \Log::info("Redis data for user NIK {$nik}: " . $userData);

        // Pastikan data ada di Redis
        if (!$userData) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'MISSING_USER_DATA',
                'message' => 'User data not found in Redis.',
                'data' => []
            ], 400);
        }

        // Decode JSON untuk mengambil nama
        $user = json_decode($userData, true);
        $nama = $user['nama'] ?? null;

        // Validasi jika nama tidak ditemukan
        if (!$nama) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'MISSING_DATA',
                'message' => 'Name not found in Redis.',
                'data' => []
            ], 400);
        }

        // Ambil pengguna berdasarkan no_telp
        $user = UsersOtp::where('no_telp', $noTelp)->first();

        if (!$user) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'USER_NOT_FOUND',
				'message' => 'User not found.',
                'errorMessages' => [],
                'data' => []
            ], 404);
        }

        // Hapus semua OTP lama yang belum divalidasi untuk no_telp ini
        UsersOtp::where('no_telp', $noTelp)
            ->where('validate_otp', false)
            ->delete();

        // Generate OTP baru
        $kodeOtp = rand(100000, 999999);
        $expiresAt = Carbon::now('UTC')->addMinutes(5);

        // Simpan OTP baru ke tabel
        $otpRecord = UsersOtp::create([
            'no_telp' => $noTelp,
            'otp' => $kodeOtp,
            'expires_at' => $expiresAt,
            'validate_otp' => false,
        ]);

        $timeInJakarta = $otpRecord->expires_at->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s');

        // Menyusun pesan
        $message = "Halo Nurse " . $nama . ",\n\n";
        $message .= "Ini adalah kode OTP untuk verifikasi akun Anda: " . $kodeOtp . "\n\n";
        $message .= "Mohon jangan berikan kode ini kepada orang lain.\n\n";
        $message .= "Kode berlaku hingga: " . $timeInJakarta . "\n\n";
        $message .= "Terima kasih!";

        // Kirimkan pesan ke API Wablas
        $payload = [
            "data" => [
                [
                    "phone" => $noTelp,
                    "message" => $message
                ]
            ]
        ];

        try {
            $response = $this->wablasService->sendMessage($payload);

            if (isset($response['status']) && $response['status'] === true) {
                return response()->json([
                    'status' => 'SUCCESS',
                    'errorCode' => null,
					'message' => 'New OTP code sent successfully!',
                    'errorMessages' => [],
                    'data' => $response
                ], 200);
            }

            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'SEND_MESSAGE_FAILED',
				'message' => $response['message'] ?? 'Failed to send the new OTP code.',
                'errorMessages' => [],
                'data' => []
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'EXCEPTION_ERROR',
				'message' => 'An error occurred: ' . $e->getMessage(),
                'errorMessages' => [],
                'data' => []
            ], 500);
        }
    }
	

    public function sendOtpPassword(Request $request)
    {
        // Validasi input untuk memastikan data dalam format JSON
        $validator = Validator::make($request->all(), [
            'no_telp'   => 'required|string',  // Hanya nomor telepon yang diterima
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

        // Mendapatkan nomor telepon dari input
        $noTelp = $request->input('no_telp');

        // Ambil pengguna berdasarkan no_telp
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

        // Generate OTP angka acak (6 digit)
        $kodeOtp = rand(100000, 999999);

        // Waktu kadaluarsa OTP (5 menit dari sekarang)
        $expiresAt = Carbon::now('UTC')->addMinutes(5);

        // Simpan atau perbarui OTP di tabel password_reset
        $otpRecord = PasswordReset::updateOrCreate(
            ['no_telp' => $noTelp],
            [
                'otp' => $kodeOtp,
                'expires_at' => $expiresAt,
                'validate_otp_password' => false, // Default belum divalidasi
            ]
        );

        // Format waktu kadaluarsa ke zona waktu Jakarta
        $timeInJakarta = $otpRecord->expires_at->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s');

        // Menyusun pesan untuk Reset Password
        $message = "Halo Nurse " . $user->nama . ",\n\n";
        $message .= "Ini adalah kode OTP untuk mereset password akun Anda: " . $kodeOtp . "\n\n";
        $message .= "Mohon jangan berikan kode ini kepada orang lain.\n\n";
        $message .= "Kode berlaku hingga: " . $timeInJakarta . "\n\n";
        $message .= "Terima kasih!";

        // Kirimkan pesan ke API Wablas
        $payload = [
            "data" => [
                [
                    "phone" => $noTelp,
                    "message" => $message
                ]
            ]
        ];

        try {
            $response = $this->wablasService->sendMessage($payload);

            if (isset($response['status']) && $response['status'] === true) {
                return response()->json([
                    'status' => 'SUCCESS',
                    'errorCode' => null,
					'message' => 'OTP code for Password Reset sent successfully!',
                    'errorMessages' => [],
                    'data' => $response
                ], 200);
            }

            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'SEND_MESSAGE_FAILED',
				'message' => $response['message'] ?? 'Failed to send OTP.',
                'errorMessages' => [],
                'data' => []
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'EXCEPTION_ERROR',
				'message' => 'An error occurred: ' . $e->getMessage(),
                'errorMessages' => [],
                'data' => []
            ], 500);
        }
    }
	
	

    public function validateOtpPassword(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'no_telp' => 'required|string', // Nomor telepon
            'otp' => 'required|numeric', // OTP harus berupa angka
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

        // Ambil input
        $noTelp = $request->input('no_telp');
        $otp = $request->input('otp');

        // Cari record berdasarkan nomor telepon
        $otpRecord = PasswordReset::where('no_telp', $noTelp)->first();

        // Periksa apakah record ditemukan
        if (!$otpRecord) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'OTP_NOT_FOUND',
				'message' => 'OTP code not found.',
                'errorMessages' => [],
                'data' => []
            ], 404);
        }

        // Periksa apakah OTP cocok dan masih berlaku
        if ($otpRecord->otp != $otp) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'INVALID_OTP',
				'message' => 'Incorrect OTP.',
                'errorMessages' => [],
                'data' => []
            ], 401);
        }

        if (Carbon::now('UTC')->gt($otpRecord->expires_at)) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'OTP_EXPIRED',
				'message' => 'OTP code has expired.',
                'errorMessages' => [],
                'data' => []
            ], 402);
        }

        // Tandai OTP sebagai telah divalidasi
        $otpRecord->update([
            'validate_otp_password' => true,
        ]);

        return response()->json([
            'status' => 'SUCCESS',
            'errorCode' => null,
			'message' => 'OTP code validated successfully.',
            'errorMessages' => [],
            'data' => []
        ], 200);
    }
	

    public function resetOtpPassword(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'no_telp' => 'required|string', // Nomor telepon
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

        // Ambil nomor telepon dari input
        $noTelp = $request->input('no_telp');

        // Cari pengguna berdasarkan nomor telepon
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

        // Cari record OTP berdasarkan nomor telepon
        $otpRecord = PasswordReset::where('no_telp', $noTelp)->first();

        // Periksa apakah record ditemukan
        if (!$otpRecord) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'OTP_NOT_FOUND',
				'message' => 'OTP code not found.',
                'errorMessages' => [],
                'data' => []
            ], 404);
        }

        // Periksa apakah OTP telah kadaluarsa
        if (Carbon::now('UTC')->lte($otpRecord->expires_at)) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'OTP_NOT_EXPIRED',
				'message' => 'OTP code is still valid, no need to reset.',
                'errorMessages' => [],
                'data' => []
            ], 400);
        }

        // Generate OTP baru
        $newOtp = rand(100000, 999999);

        // Perbarui waktu kadaluarsa (5 menit dari sekarang)
        $newExpiresAt = Carbon::now('UTC')->addMinutes(5);

        // Update record dengan OTP baru
        $otpRecord->update([
            'otp' => $newOtp,
            'expires_at' => $newExpiresAt,
            'validate_otp_password' => false, // Reset validasi OTP
        ]);

        // Konversi waktu kadaluarsa ke zona waktu Jakarta untuk ditampilkan dalam pesan
        $timeInJakarta = $newExpiresAt->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s');

        // Susun pesan dengan menyertakan nama pengguna
        $message = "Halo Nurse " . $user->nama . ",\n\n";
        $message .= "Ini adalah kode OTP baru untuk reset password akun Anda: " . $newOtp . "\n\n";
        $message .= "Mohon jangan bagikan kode ini kepada orang lain.\n\n";
        $message .= "Kode berlaku hingga: " . $timeInJakarta . "\n\n";
        $message .= "Terima kasih!";

        // Kirimkan pesan ke API Wablas
        $payload = [
            "data" => [
                [
                    "phone" => $noTelp,
                    "message" => $message
                ]
            ]
        ];

        try {
            $response = $this->wablasService->sendMessage($payload);

            if (isset($response['status']) && $response['status'] === true) {
                return response()->json([
                    'status' => 'SUCCESS',
                    'errorCode' => null,
					'message' => 'New OTP code successfully sent.',
                    'errorMessages' => [],
                    'data' => $response
                ], 200);
            }

            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'SEND_MESSAGE_FAILED',
				'message' => $response['message'] ?? 'Failed to send message.',
                'errorMessages' => [],
                'data' => []
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'EXCEPTION_ERROR',
				'message' => 'An error occurred: ' . $e->getMessage(),
					'errorMessages' => [],
                'data' => []
            ], 500);
        }
    }
}