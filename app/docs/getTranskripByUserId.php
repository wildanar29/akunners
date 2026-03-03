<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/transkrip/data/{user_id}",
 *     operationId="getTranskripByUserId",
 *     tags={"HASIL ASSESSMENT"},
 *     summary="Mendapatkan daftar transkrip nilai berdasarkan user (asesi)",
 *     description="Mengambil seluruh transkrip nilai berdasarkan asesi_id (user_id).",
 *
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="ID user / asesi",
 *         @OA\Schema(type="integer", example=15)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Daftar transkrip nilai berhasil diambil",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Daftar transkrip nilai berhasil diambil"
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="form_1_id", type="integer", example=5),
 *                     @OA\Property(property="pk_id", type="integer", example=2),
 *                     @OA\Property(property="nomor_urut", type="integer", example=1),
 *                     @OA\Property(property="nomor_surat", type="string", example="TR-001/PK/2026"),
 *                     @OA\Property(property="nama", type="string", example="Budi Santoso"),
 *                     @OA\Property(property="gelar", type="string", example="S.Kom"),
 *                     @OA\Property(property="status", type="string", example="Lulus"),
 *                     @OA\Property(property="tanggal_mulai", type="string", format="date", example="2026-01-10"),
 *                     @OA\Property(property="tanggal_selesai", type="string", format="date", example="2026-01-15"),
 *                     @OA\Property(
 *                         property="preview_url",
 *                         type="string",
 *                         example="http://localhost/transkrip/view/5"
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Tidak ada transkrip nilai untuk user ini",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Tidak ada transkrip nilai untuk user ini"
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat mengambil data",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Terjadi kesalahan saat mengambil data transkrip nilai"
 *             ),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Error message"
 *             )
 *         )
 *     )
 * )
 */

 class getTranskripByUserId {}