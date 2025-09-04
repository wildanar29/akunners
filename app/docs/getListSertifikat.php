<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/sertifikat/list",
 *     tags={"Sertifikat"},
 *     summary="Ambil list asesi untuk generate sertifikat",
 *     description="API ini digunakan untuk menampilkan list asesi yang menunggu untuk generate sertifikat oleh bidang",
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=false,
 *         description="Filter berdasarkan status Form 6",
 *         @OA\Schema(type="string", example="selesai")
 *     ),
 *     @OA\Parameter(
 *         name="has_certificate",
 *         in="query",
 *         required=false,
 *         description="Filter apakah sudah punya sertifikat (true/false)",
 *         @OA\Schema(type="boolean", example=true)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List Form 6 berhasil diambil",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="List Form 6 berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="form_6_id", type="integer", example=25),
 *                     @OA\Property(property="form_1_id", type="integer", example=10),
 *                     @OA\Property(property="pk_id", type="integer", example=5),
 *                     @OA\Property(property="asesi_id", type="integer", example=12),
 *                     @OA\Property(property="asesi_name", type="string", example="Andi Wijaya"),
 *                     @OA\Property(property="status", type="string", example="selesai"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-01T10:00:00Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-02T12:00:00Z"),
 *                     @OA\Property(
 *                         property="sertifikat",
 *                         type="object",
 *                         nullable=true,
 *                         @OA\Property(property="id", type="integer", example=3),
 *                         @OA\Property(property="nomor_surat", type="string", example="SK-123/PK/2025"),
 *                         @OA\Property(property="preview_url", type="string", example="http://localhost:8000/storage/sertifikat/sk-123.pdf")
 *                     ),
 *                     @OA\Property(property="sudah_sertifikat", type="boolean", example=true)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat mengambil data Form 6",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat mengambil data Form 6"),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[HY000]...")
 *         )
 *     )
 * )
 */


 class getListSertifikat {}