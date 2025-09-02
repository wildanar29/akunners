<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/change-password",
 *     tags={"Auth"},
 *     summary="Ganti Password",
 *     description="API ini digunakan untuk mengubah password, dengan 3 input yaitu password lama, password baru, dan konfirmasi password baru",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"old_password","new_password","confirm_password"},
 *             @OA\Property(property="old_password", type="string", example="passwordLama123"),
 *             @OA\Property(property="new_password", type="string", example="passwordBaru123"),
 *             @OA\Property(property="confirm_password", type="string", example="passwordBaru123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password berhasil diupdate.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Password berhasil diupdate.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="Data validation failed. Please check your input.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized atau password lama salah",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="Old password is incorrect.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="An error occurred on the server.")
 *         )
 *     )
 * )
 */

 class GantiPassword {}