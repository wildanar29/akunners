<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/sertifikat/data/{user_id}",
 *     tags={"HASIL ASSESSMENT"},
 *     summary="Ambil daftar sertifikat berdasarkan user_id",
 *     description="API ini digunakan untuk mengambil sertifikat yang sudah ada berdasarkan user_id atau asesi",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="ID user atau asesi",
 *         @OA\Schema(type="integer", example=12)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Daftar sertifikat berhasil diambil",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Daftar sertifikat berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="form_1_id", type="integer", example=10),
 *                     @OA\Property(property="pk_id", type="integer", example=5),
 *                     @OA\Property(property="nomor_urut", type="string", example="001"),
 *                     @OA\Property(property="nomor_surat", type="string", example="SK-123/PK/2025"),
 *                     @OA\Property(property="nama", type="string", example="Budi Santoso"),
 *                     @OA\Property(property="gelar", type="string", example="S.Kom"),
 *                     @OA\Property(property="status", type="string", example="aktif"),
 *                     @OA\Property(property="tanggal_mulai", type="string", format="date", example="2025-01-01"),
 *                     @OA\Property(property="tanggal_selesai", type="string", format="date", example="2025-12-31"),
 *                     @OA\Property(property="preview_url", type="string", example="http://localhost:8000/sertifikat/view/10")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Tidak ada sertifikat untuk user ini",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Tidak ada sertifikat untuk user ini")
 *         )
 *     )
 * )
 */

 class getSertifikatByUserId {}