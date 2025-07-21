<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/login-akun",
 *     tags={"Auth"},
 *     summary="Login akun nurse (perawat/asesor/bidang)",
 *     description="Endpoint ini digunakan untuk login berdasarkan NIK dan password. Jika login berhasil, akan mengembalikan token JWT, informasi user, role, unit kerja, dan jabatan.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nik", "password"},
 *             @OA\Property(property="nik", type="string", example="1234567890"),
 *             @OA\Property(property="password", type="string", example="secret123"),
 *             @OA\Property(property="player_id", type="string", example="a1b2c3d4e5f6", description="(Optional) OneSignal Player ID untuk notifikasi")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login berhasil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Login successful."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="name", type="string", example="Andi Nugroho"),
 *                 @OA\Property(property="nik", type="string", example="1234567890"),
 *                 @OA\Property(property="user_id", type="integer", example=12),
 *                 @OA\Property(property="role_id", type="integer", example=2),
 *                 @OA\Property(property="role_name", type="string", example="Asesor"),
 *                 @OA\Property(property="working_unit", type="object",
 *                     @OA\Property(property="working_unit_id", type="integer", example=5),
 *                     @OA\Property(property="working_unit_name", type="string", example="Unit Gawat Darurat"),
 *                     @OA\Property(property="working_area_id", type="integer", example=1),
 *                     @OA\Property(property="working_area_name", type="string", example="Wilayah 1")
 *                 ),
 *                 @OA\Property(property="jabatan", type="object",
 *                     @OA\Property(property="jabatan_id", type="integer", example=3),
 *                     @OA\Property(property="nama_jabatan", type="string", example="Perawat Pelaksana")
 *                 ),
 *                 @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR...")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="Data validation failed. Please check your input."),
 *             @OA\Property(property="errors", type="object"),
 *             @OA\Property(property="solution", type="string", example="Ensure all required fields are filled. NIK, password, and Player ID cannot be empty.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Password salah",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="Incorrect password."),
 *             @OA\Property(property="details", type="string"),
 *             @OA\Property(property="solution", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="User not found."),
 *             @OA\Property(property="details", type="string"),
 *             @OA\Property(property="solution", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="An error occurred on the server."),
 *             @OA\Property(property="details", type="string"),
 *             @OA\Property(property="solution", type="string")
 *         )
 *     )
 * )
 */

 class LoginAkunNurse {}