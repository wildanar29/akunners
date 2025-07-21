<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/input-jabatan-working/{nik}",
 *     summary="Menambahkan riwayat jabatan untuk user berdasarkan NIK",
 *     tags={"User"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="Nomor Induk Karyawan (NIK) dari user",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"working_unit_id", "jabatan_id", "dari"},
 *             @OA\Property(property="working_unit_id", type="integer", example=1, description="ID unit kerja"),
 *             @OA\Property(property="jabatan_id", type="integer", example=2, description="ID jabatan"),
 *             @OA\Property(property="dari", type="string", format="date", example="2023-01-01", description="Tanggal mulai jabatan"),
 *             @OA\Property(property="sampai", type="string", format="date", nullable=true, example="2024-01-01", description="Tanggal akhir jabatan (boleh kosong)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Data riwayat jabatan berhasil ditambahkan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=201),
 *             @OA\Property(property="message", type="string", example="History Jabatan inserted successfully."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="user_jabatan_id", type="integer", example=10),
 *                 @OA\Property(property="user_id", type="integer", example=5),
 *                 @OA\Property(property="working_unit_id", type="integer", example=1),
 *                 @OA\Property(property="jabatan_id", type="integer", example=2),
 *                 @OA\Property(property="dari", type="string", format="date", example="2023-01-01"),
 *                 @OA\Property(property="sampai", type="string", format="date", example="2024-01-01")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="Data validation failed."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Failed to insert History Jabatan: error message")
 *         )
 *     )
 * )
 */

 class insertHistoryJabatan {}