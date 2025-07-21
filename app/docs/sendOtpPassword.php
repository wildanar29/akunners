<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/send-otp-reset-password",
 *     tags={"Auth"},
 *     summary="Kirim OTP untuk reset password",
 *     description="Mengirimkan kode OTP ke email yang sudah terdaftar untuk keperluan reset password. OTP berlaku selama 5 menit.",
 *     operationId="sendOtpPassword",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OTP berhasil dikirim",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="errorCode", type="string", nullable=true, example=null),
 *             @OA\Property(property="message", type="string", example="Kode OTP berhasil dikirim ke email!"),
 *             @OA\Property(property="errorMessages", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="errorCode", type="string", example="VALIDATION_ERROR"),
 *             @OA\Property(property="message", type="string", example="Masukkan Email yang terdaftar pada akun Anda sebelumnya."),
 *             @OA\Property(property="errorMessages", type="object"),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Pengguna tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="errorCode", type="string", example="USER_NOT_FOUND"),
 *             @OA\Property(property="message", type="string", example="Pengguna dengan email tersebut tidak ditemukan."),
 *             @OA\Property(property="errorMessages", type="array", @OA\Items()),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Gagal mengirim email OTP",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="errorCode", type="string", example="EMAIL_SEND_ERROR"),
 *             @OA\Property(property="message", type="string", example="Gagal mengirim OTP. Silakan coba lagi nanti."),
 *             @OA\Property(property="errorMessages", type="string", example="SMTP connection failed."),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     )
 * )
 */

 class sendOtpPassword {}