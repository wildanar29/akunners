<?php

namespace App\Docs;
/**
 * @OA\Delete(
 *     path="/delete-jabatan-working/{nik}",
 *     summary="Hapus riwayat jabatan user berdasarkan NIK dan user_jabatan_id",
 *     tags={"User"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         description="NIK dari user",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_jabatan_id"},
 *             @OA\Property(property="user_jabatan_id", type="integer", example=5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="History Jabatan berhasil dihapus",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="History Jabatan deleted successfully.")
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
 *         description="User atau history tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server saat menghapus data",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Failed to delete History Jabatan: ...")
 *         )
 *     )
 * )
 */

 class deleteHistoryJabatan {}