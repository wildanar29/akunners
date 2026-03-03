<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/transkrip/list",
 *     operationId="getListTranskripNilai",
 *     tags={"HASIL ASSESSMENT"},
 *     summary="Mendapatkan daftar transkrip nilai berdasarkan Form 6",
 *     description="Mengambil daftar Form 6 beserta informasi apakah sudah memiliki transkrip nilai atau belum.",
 *     
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=false,
 *         description="Filter berdasarkan status Form 6",
 *         @OA\Schema(type="string", example="approved")
 *     ),
 * 
 *     @OA\Parameter(
 *         name="has_transkrip",
 *         in="query",
 *         required=false,
 *         description="Filter berdasarkan keberadaan transkrip (true = sudah ada, false = belum ada)",
 *         @OA\Schema(type="boolean", example=true)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="List Transkrip Nilai berhasil diambil",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="List Transkrip Nilai berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="form_6_id", type="integer", example=10),
 *                     @OA\Property(property="form_1_id", type="integer", example=5),
 *                     @OA\Property(property="pk_id", type="integer", example=2),
 *                     @OA\Property(property="asesi_id", type="integer", example=15),
 *                     @OA\Property(property="asesi_name", type="string", example="Budi Santoso"),
 *                     @OA\Property(property="status", type="string", example="approved"),
 *                     @OA\Property(property="created_at", type="string", format="date-time"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time"),
 *                     
 *                     @OA\Property(
 *                         property="transkrip",
 *                         type="object",
 *                         nullable=true,
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="nomor_surat", type="string", example="TR-001/PK/2026"),
 *                         @OA\Property(property="preview_url", type="string", example="http://localhost/storage/transkrip/file.pdf")
 *                     ),
 *
 *                     @OA\Property(property="sudah_transkrip", type="boolean", example=true)
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat mengambil data",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat mengambil data Transkrip Nilai"),
 *             @OA\Property(property="error", type="string", example="Error message")
 *         )
 *     )
 * )
 */

 class getListTranskripNilai {}