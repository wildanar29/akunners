<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/register-akun",
 *     tags={"Auth"},
 *     summary="Register akun perawat/asesor/bidang dan simpan ke Redis",
 *     description="Mendaftarkan akun baru berdasarkan role_id tertentu (role_id = 2 untuk asesor, 3 untuk bidang), dan menyimpannya ke Redis.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nik", "nama", "email", "role_id", "no_telp"},
 *             @OA\Property(property="nik", type="string", example="1234567890"),
 *             @OA\Property(property="nama", type="string", example="Andi Nugroho"),
 *             @OA\Property(property="email", type="string", format="email", example="andi@example.com"),
 *             @OA\Property(property="role_id", type="integer", example=2, description="ID role, misal: 2 untuk asesor, 3 untuk bidang"),
 *             @OA\Property(property="no_telp", type="string", example="081234567890"),
 *             @OA\Property(property="no_reg", type="string", example="REG202501", description="Wajib jika role_id adalah 2 atau 3"),
 *             @OA\Property(property="valid_from", type="string", format="date", example="2025-07-21", description="Wajib jika role_id adalah 2 atau 3"),
 *             @OA\Property(property="valid_until", type="string", format="date", example="2025-12-31", description="Wajib jika role_id adalah 2 atau 3")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil menyimpan data ke Redis",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Account registered successfully and stored in Redis."),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="Validation failed. Please check your input."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Role tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="Resource not found.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error server",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Internal Server Error"),
 *             @OA\Property(property="error", type="string", example="Unexpected error.")
 *         )
 *     )
 * )
 */

 class RegisterAkunNurse {}