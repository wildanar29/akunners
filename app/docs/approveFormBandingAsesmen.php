<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form8/banding/{bandingId}/approve",
 *     tags={"FORM 8 (BANDING)"},
 *     summary="API ini digunakan untuk menyetujui Form banding yang diajukan oleh asesi",
 *     description="Endpoint ini digunakan oleh Asesor untuk menyetujui Form Banding Asesmen (Form 8) yang diajukan oleh Asesi.",
 *     @OA\Parameter(
 *         name="bandingId",
 *         in="path",
 *         required=true,
 *         description="ID Form Banding (form_8) yang akan di-approve",
 *         @OA\Schema(type="integer", example=12)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Form banding berhasil disetujui",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Form banding asesmen berhasil di-approve oleh Asesor"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="banding_id", type="integer", example=12),
 *                 @OA\Property(property="form_1_id", type="integer", example=3),
 *                 @OA\Property(property="status", type="string", example="Approved"),
 *                 @OA\Property(property="approved_by", type="integer", example=7, description="ID Asesor yang menyetujui"),
 *                 @OA\Property(property="approved_at", type="string", format="date-time", example="2025-08-26T14:30:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Form banding tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data form banding asesmen tidak ditemukan.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal (banding_id tidak valid)",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={"banding_id": {"The selected banding id is invalid."}}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server saat approve form banding",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat approve form banding asesmen: unexpected error")
 *         )
 *     )
 * )
 */

 class approveFormBandingAsesmen {}