<?php

namespace App\Docs;

/**
 * @OA\Delete(
 *     path="/users/{user_id}",
 *     tags={"Auth"},
 *     summary="Soft delete user",
 *     description="Menonaktifkan akun user menggunakan mekanisme soft delete (mengisi kolom deleted_at). User yang sudah dihapus tidak dapat login kembali.",
 *     operationId="softDeleteUser",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="ID user yang akan dihapus",
 *         @OA\Schema(
 *             type="integer",
 *             example=12
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="User berhasil di-soft delete",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="User berhasil dihapus (soft delete)"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="user_id", type="integer", example=12),
 *                 @OA\Property(property="nama", type="string", example="Wildan AR"),
 *                 @OA\Property(property="email", type="string", example="wildan@email.com"),
 *                 @OA\Property(property="deleted_at", type="string", nullable=true, example=null)
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=400,
 *         description="User sudah dihapus sebelumnya",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User sudah dihapus sebelumnya"),
 *             @OA\Property(property="data", type="object", nullable=true)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="User tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User tidak ditemukan"),
 *             @OA\Property(property="data", type="object", nullable=true)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menghapus user"),
 *             @OA\Property(property="data", type="string", example="SQL error message")
 *         )
 *     )
 * )
 */

 class softDeleteUser {}