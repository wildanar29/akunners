<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/generate-sertifikat",
 *     tags={"HASIL ASSESSMENT"},
 *     summary="Generate Sertifikat",
 *     description="API ini digunakan untuk melakukan generate sertifikat bagi asesor yang sudah menyelesaikan proses assessment",
 *     operationId="generateSertifikat",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="form_1_id",
 *                 type="integer",
 *                 example=123,
 *                 description="ID form 1 yang sudah selesai assessment"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Sertifikat berhasil disimpan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Sertifikat berhasil disimpan"),
 *             @OA\Property(property="preview_url", type="string", example="http://localhost:8000/storage/sertifikat/2025/sertifikat_NAMA_123.pdf"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="asesi_id", type="integer", example=45),
 *                 @OA\Property(property="form_1_id", type="integer", example=123),
 *                 @OA\Property(property="pk_id", type="integer", example=5),
 *                 @OA\Property(property="nomor_urut", type="integer", example=12),
 *                 @OA\Property(property="nomor_surat", type="string", example="012/PK/IX/2025"),
 *                 @OA\Property(property="nama", type="string", example="ASESI TEST"),
 *                 @OA\Property(property="gelar", type="string", example="Ahli Madya Keperawatan"),
 *                 @OA\Property(property="status", type="string", example="KOMPETEN"),
 *                 @OA\Property(property="tanggal_mulai", type="string", example="2025-09-01 10:20:30"),
 *                 @OA\Property(property="tanggal_selesai", type="string", example="2025-09-03 12:30:00"),
 *                 @OA\Property(property="file_path", type="string", example="sertifikat/2025/sertifikat_ASESI_TEST_012.pdf")
 *             ),
 *             @OA\Property(property="nomor_surat", type="string", example="012/PK/IX/2025")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Sertifikat sudah ada sebelumnya",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Sertifikat untuk form ini sudah dibuat sebelumnya")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi error ketika menyimpan sertifikat",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Gagal menyimpan sertifikat"),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[23000]: Integrity constraint violation...")
 *         )
 *     )
 * )
 */

 class generate {}