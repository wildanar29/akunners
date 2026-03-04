<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/login-akun-nurse",
 *     tags={"Auth"},
 *     summary="Login akun nurse",
 *     description="Endpoint untuk login user nurse menggunakan NIK dan password. Sistem akan menolak login jika akun sudah di-soft delete.",
 *     operationId="loginAkunNurse",
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nik","password"},
 *             @OA\Property(property="nik", type="string", example="3276010101010001"),
 *             @OA\Property(property="password", type="string", example="password123"),
 *             @OA\Property(property="device_token", type="string", nullable=true, example="fcm_device_token_example")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Login berhasil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Login successful."),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="name", type="string", example="Wildan AR"),
 *                 @OA\Property(property="nik", type="string", example="3276010101010001"),
 *                 @OA\Property(property="user_id", type="integer", example=12),
 *                 @OA\Property(property="pk_id_active", type="integer", example=5),
 *
 *                 @OA\Property(
 *                     property="roles",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="role_id", type="integer", example=1),
 *                         @OA\Property(property="role_name", type="string", example="Asesi")
 *                     )
 *                 ),
 *
 *                 @OA\Property(
 *                     property="current_role",
 *                     type="object",
 *                     @OA\Property(property="role_id", type="integer", example=1),
 *                     @OA\Property(property="role_name", type="string", example="Asesi")
 *                 ),
 *
 *                 @OA\Property(
 *                     property="working_unit",
 *                     type="object",
 *                     nullable=true,
 *                     @OA\Property(property="working_unit_id", type="integer", example=3),
 *                     @OA\Property(property="working_unit_name", type="string", example="ICU"),
 *                     @OA\Property(property="working_area_id", type="integer", example=1),
 *                     @OA\Property(property="working_area_name", type="string", example="Rawat Inap")
 *                 ),
 *
 *                 @OA\Property(
 *                     property="jabatan",
 *                     type="object",
 *                     @OA\Property(property="jabatan_id", type="integer", example=2),
 *                     @OA\Property(property="nama_jabatan", type="string", example="Perawat")
 *                 ),
 *
 *                 @OA\Property(property="token", type="string", example="jwt_token_string")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="Data validation failed. Please check your input.")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Password salah",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="Incorrect password.")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=403,
 *         description="Akun sudah dihapus (soft delete)",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="Account has been deactivated.")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="User tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     ),
 *
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

 class softDeleteUser {}