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
use Illuminate\Support\Facades\Mail;


class MailController extends Controller
{
    private $wablasService;

    public function __construct(WablasService $wablasService)
    {
        $this->wablasService = $wablasService;
    }

    public function sendOtpPassword(Request $request)
    {
        // Validasi input email
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'], // Email harus valid dan terdaftar
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'VALIDATION_ERROR',
                'message' => 'Masukkan Email yang terdaftar pada akun Anda sebelumnya.',
                'errorMessages' => $validator->errors(),
                'data' => []
            ], 400);
        }

        // Ambil pengguna berdasarkan email
        $user = DaftarUser::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'USER_NOT_FOUND',
                'message' => 'Pengguna dengan email tersebut tidak ditemukan.',
                'errorMessages' => [],
                'data' => []
            ], 404);
        }

        // Generate OTP 6 digit
        $kodeOtp = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(5); // OTP berlaku 5 menit

        // Simpan atau perbarui ke tabel password_resets
        PasswordReset::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => $kodeOtp,
                'expires_at' => $expiresAt,
                'validate_otp_password' => false,
            ]
        );

        try {
            // Kirim email via Postmark
            Mail::mailer('postmark')->raw(
                "Halo {$user->nama},\n\n" .
                "Berikut adalah kode OTP Anda untuk reset password: {$kodeOtp}\n\n" .
                "Kode ini berlaku hingga: " . $expiresAt->format('d-m-Y H:i:s') . "\n\n" .
                "Mohon jangan membagikan kode ini kepada siapa pun.\n\n" .
                "Pesan ini dikirim secara otomatis oleh sistem kami.",
                function ($message) use ($request) {
                    $message->to($request->email)
                            ->subject('Kode OTP Reset Password')
                            ->from(config('mail.from.address'), config('mail.from.name'));
                }
            );

            return response()->json([
                'status' => 'SUCCESS',
                'errorCode' => null,
                'message' => 'Kode OTP berhasil dikirim ke email!',
                'errorMessages' => [],
                'data' => []
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email OTP: ' . $e->getMessage());

            return response()->json([
                'status' => 'ERROR',
                'errorCode' => 'EMAIL_SEND_ERROR',
                'message' => 'Gagal mengirim OTP. Silakan coba lagi nanti.',
                'errorMessages' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function validateOtpPassword(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',      // Validasi email
            'otp' => 'required|numeric',      // OTP harus berupa angka
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
        $email = $request->input('email');
        $otp = $request->input('otp');

        // Cari record berdasarkan email
        $otpRecord = PasswordReset::where('email', $email)->first();

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