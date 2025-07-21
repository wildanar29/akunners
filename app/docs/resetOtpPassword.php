<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/reset-otp-password",
 *     tags={"Auth"},
 *     summary="Reset OTP password via WhatsApp",
 *     description="Mengirim ulang kode OTP melalui WhatsApp jika OTP sebelumnya telah kedaluwarsa.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"no_telp"},
 *             @OA\Property(property="no_telp", type="string", example="6281234567890", description="Nomor telepon yang terdaftar")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="New OTP code successfully sent.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="errorCode", type="string", example=null),
 *             @OA\Property(property="message", type="string", example="New OTP code successfully sent."),
 *             @OA\Property(property="errorMessages", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="OTP masih berlaku atau input tidak valid",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="errorCode", type="string", example="OTP_NOT_EXPIRED"),
 *             @OA\Property(property="message", type="string", example="OTP code is still valid, no need to reset."),
 *             @OA\Property(property="errorMessages", type="array", @OA\Items()),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User atau OTP tidak ditemukan",
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
 *         description="Gagal mengirim pesan atau terjadi error sistem",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="errorCode", type="string", example="SEND_MESSAGE_FAILED"),
 *             @OA\Property(property="message", type="string", example="Failed to send message."),
 *             @OA\Property(property="errorMessages", type="array", @OA\Items()),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     )
 * )
 */


 class resetOtpPassword {}