<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/valid-otp-reset-password",
 *     tags={"Auth"},
 *     summary="Validasi kode OTP untuk reset password",
 *     description="Memvalidasi kode OTP yang dikirimkan ke email untuk proses reset password.",
 *     @OA\Parameter(
 *         name="email",
 *         in="query",
 *         description="Alamat email yang digunakan untuk reset password",
 *         required=true,
 *         @OA\Schema(
 *             type="string",
 *             format="email"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="otp",
 *         in="query",
 *         description="Kode OTP yang dikirim ke email",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OTP code validated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="errorCode", type="string", nullable=true, example=null),
 *             @OA\Property(property="message", type="string", example="OTP code validated successfully."),
 *             @OA\Property(property="errorMessages", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input data",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="errorCode", type="string", example="INVALID_INPUT"),
 *             @OA\Property(property="message", type="string", example="Invalid data."),
 *             @OA\Property(property="errorMessages", type="object"),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="OTP tidak sesuai",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="errorCode", type="string", example="INVALID_OTP"),
 *             @OA\Property(property="message", type="string", example="Incorrect OTP."),
 *             @OA\Property(property="errorMessages", type="array", @OA\Items()),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=402,
 *         description="OTP sudah kadaluarsa",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="errorCode", type="string", example="OTP_EXPIRED"),
 *             @OA\Property(property="message", type="string", example="OTP code has expired."),
 *             @OA\Property(property="errorMessages", type="array", @OA\Items()),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="OTP tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="errorCode", type="string", example="OTP_NOT_FOUND"),
 *             @OA\Property(property="message", type="string", example="OTP code not found."),
 *             @OA\Property(property="errorMessages", type="array", @OA\Items()),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     )
 * )
 */

 class validateOtpPassword {}