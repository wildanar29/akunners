<?php

namespace App\Docs;
/**
 * @OA\Put(
 *     path="/edit-jabatan-working/{nik}",
 *     summary="Update riwayat jabatan user berdasarkan NIK",
 *     tags={"User"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         description="NIK user",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_jabatan_id"},
 *             @OA\Property(property="user_jabatan_id", type="integer", example=1, description="ID riwayat jabatan yang ingin diupdate"),
 *             @OA\Property(property="working_unit_id", type="integer", example=2, description="ID unit kerja (opsional)"),
 *             @OA\Property(property="jabatan_id", type="integer", example=3, description="ID jabatan baru (opsional)"),
 *             @OA\Property(property="dari", type="string", format="date", example="2023-01-01", description="Tanggal mulai jabatan (opsional)"),
 *             @OA\Property(property="sampai", type="string", format="date", example="2024-01-01", description="Tanggal akhir jabatan (opsional)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil memperbarui history jabatan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="History Jabatan updated successfully."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="user_jabatan_id", type="integer", example=1),
 *                 @OA\Property(property="working_unit_id", type="integer", example=2),
 *                 @OA\Property(property="jabatan_id", type="integer", example=3),
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
 *         description="User atau History Jabatan tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server saat memperbarui data",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Failed to update History Jabatan: Internal Error")
 *         )
 *     )
 * )
 */

 class updateHistoryJabatan {}