<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form8",
 *     tags={"FORM 8 (BANDING)"},
 *     summary="Ambil list Form Banding Asesmen",
 *     description="API ini digunakan untuk mengambil list data yang mengajukan banding berdasarkan asesor_id atau asesi_id.",
 *     @OA\Parameter(
 *         name="asesor_id",
 *         in="query",
 *         required=false,
 *         description="Filter berdasarkan ID asesor",
 *         @OA\Schema(type="integer", example=123)
 *     ),
 *     @OA\Parameter(
 *         name="asesi_id",
 *         in="query",
 *         required=false,
 *         description="Filter berdasarkan ID asesi",
 *         @OA\Schema(type="integer", example=456)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="message", type="string", example="Data form banding berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="banding_id", type="integer", example=1),
 *                     @OA\Property(property="form_1_id", type="integer", example=10),
 *                     @OA\Property(property="asesi_id", type="integer", example=456),
 *                     @OA\Property(property="asesor_id", type="integer", example=123),
 *                     @OA\Property(property="tanggal_asesmen", type="string", format="date", example="2025-09-10"),
 *                     @OA\Property(property="alasan_banding", type="string", example="Tidak setuju dengan hasil asesmen"),
 *                     @OA\Property(property="persetujuan_asesi", type="boolean", example=true),
 *                     @OA\Property(property="persetujuan_asesor", type="boolean", example=false)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan pada server",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan pada server")
 *         )
 *     )
 * )
 */


 class getFormBandingByUser {}