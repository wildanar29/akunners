<?php

namespace App\Docs;

/**
 * @OA\Delete(
 *     path="/users/{user_id}",
 *     tags={"Auth"},
 *     summary="Hard delete user",
 *     description="Menghapus user secara permanen beserta data relasinya seperti kompetensi_progres dan kompetensi_tracks",
 *     operationId="hardDeleteUser",
 *
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="ID user yang akan dihapus",
 *         @OA\Schema(
 *             type="integer",
 *             example=68
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="User berhasil dihapus",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="User berhasil dihapus permanen"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="user_id", type="integer", example=68),
 *                 @OA\Property(property="nama", type="string", example="Wildan AR"),
 *                 @OA\Property(property="email", type="string", example="wildan@email.com")
 *             )
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
 *         description="Terjadi kesalahan saat menghapus user",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menghapus user"),
 *             @OA\Property(property="data", type="string", example="SQL error message")
 *         )
 *     )
 * )
 */

 class hardDeleteUser {}