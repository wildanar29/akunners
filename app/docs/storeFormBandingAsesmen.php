<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form8/banding/store",
 *     tags={"FORM 8 (BANDING)"},
 *     summary="API ini digunakan untuk melakukan Banding terhadap nilai yang diberikan",
 *     description="Menyimpan data Form Banding Asesmen berdasarkan form_1_id, termasuk alasan banding, persetujuan asesi, dan persetujuan asesor.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"form_1_id","alasan_banding"},
 *             @OA\Property(property="form_1_id", type="integer", example=1, description="ID dari Form 1"),
 *             @OA\Property(property="alasan_banding", type="string", example="Saya tidak setuju dengan hasil penilaian", description="Alasan banding dari asesi"),
 *             @OA\Property(property="persetujuan_asesi", type="boolean", example=true, description="Persetujuan asesi terhadap banding"),
 *             @OA\Property(property="persetujuan_asesor", type="boolean", example=false, description="Persetujuan asesor terhadap banding")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Form banding berhasil disimpan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Form banding asesmen berhasil disimpan"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="banding_id", type="integer", example=10),
 *                 @OA\Property(property="form_1_id", type="integer", example=1),
 *                 @OA\Property(property="asesi_id", type="integer", example=5),
 *                 @OA\Property(property="asesor_id", type="integer", example=3),
 *                 @OA\Property(property="tanggal_asesmen", type="string", format="date", example="2025-08-26"),
 *                 @OA\Property(property="alasan_banding", type="string", example="Saya tidak setuju dengan hasil penilaian"),
 *                 @OA\Property(property="persetujuan_asesi", type="boolean", example=true),
 *                 @OA\Property(property="persetujuan_asesor", type="boolean", example=false)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data Form 1 tidak ditemukan atau tidak lengkap",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data asesi, asesor, atau tanggal asesmen tidak ditemukan dari form_1_id")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={"form_1_id": {"The form_1_id field is required."}, "alasan_banding": {"The alasan_banding field is required."}}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan server saat menyimpan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menyimpan data")
 *         )
 *     )
 * )
 */

 class storeFormBandingAsesmen {}