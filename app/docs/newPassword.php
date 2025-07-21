<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/new-password",
 *     summary="Atur atau ubah password pengguna setelah OTP tervalidasi",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password", "confirm_password"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
 *             @OA\Property(property="confirm_password", type="string", format="password", example="newpassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password berhasil diatur atau diperbarui",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="errorCode", type="string", example=null),
 *             @OA\Property(property="message", type="string", example="Password has been successfully created or updated."),
 *             @OA\Property(property="errorMessages", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="errorCode", type="string", example="INVALID_INPUT"),
 *             @OA\Property(property="message", type="string", example="Invalid data."),
 *             @OA\Property(property="errorMessages", type="object"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="OTP belum tervalidasi atau pengguna tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="errorCode", type="string", example="OTP_NOT_VALIDATED"),
 *             @OA\Property(property="message", type="string", example="OTP code has not been validated or was not found."),
 *             @OA\Property(property="errorMessages", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="data", type="object")
 *         )
 *     )
 * )
 */

 class newPassword {}