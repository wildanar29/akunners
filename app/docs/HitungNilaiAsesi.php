<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/kalkulasi-jawaban-asesi",
 *     summary="Hitung nilai asesmen mandiri Form 2",
 *     description="Menghitung total K, total BK, nilai (%) dan kelulusan asesmen mandiri berdasarkan jawaban asesi.",
 *     tags={"FORM 2 (ASESMEN MANDIRI)"},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="form_2_id",
 *                 type="integer",
 *                 example=83
 *             ),
 *             @OA\Property(
 *                 property="jawaban",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="no_id", type="integer", example=1),
 *                     @OA\Property(property="k", type="boolean", example=false),
 *                     @OA\Property(property="bk", type="boolean", example=true)
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Perhitungan nilai berhasil.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Perhitungan nilai berhasil."),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="total_soal", type="integer", example=147),
 *                 @OA\Property(property="total_k", type="integer", example=57),
 *                 @OA\Property(property="total_bk", type="integer", example=10),
 *                 @OA\Property(property="penilaian_asesi", type="number", format="float", example=38.78),
 *                 @OA\Property(property="is_pass", type="boolean", example=false)
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Validasi gagal.")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Total soal tidak ditemukan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Total soal form 2 tidak ditemukan.")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menghitung nilai."),
 *             @OA\Property(property="data", type="object")
 *         )
 *     )
 * )
 */


 class HitungNilaiAsesi {}