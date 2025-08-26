<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form5/langkah-kegiatan",
 *     tags={"FORM 5 (PRA ASESMEN)"},
 *     summary="Ambil Langkah dan Kegiatan",
 *     description="API ini digunakan untuk melihat soal atau langkah dan kegiatan pada Form 5.",
 *     operationId="getLangkahDanKegiatan",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=true,
 *         description="ID Program Kompetensi (pk_id) yang akan diambil langkah dan kegiatannya",
 *         @OA\Schema(type="integer", example=12)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data langkah dan kegiatan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Data langkah dan kegiatan berhasil diambil."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="pk_id", type="integer", example=12),
 *                     @OA\Property(property="nomor_langkah", type="integer", example=1),
 *                     @OA\Property(property="langkah", type="string", example="Melakukan persiapan alat"),
 *                     @OA\Property(
 *                         property="kegiatans",
 *                         type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="id", type="integer", example=101),
 *                             @OA\Property(property="langkah_form_5_id", type="integer", example=1),
 *                             @OA\Property(property="kegiatan", type="string", example="Mengambil alat dan bahan")
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=400,
 *         description="Parameter pk_id tidak diisi",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="Parameter pk_id wajib diisi."),
 *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Gagal mengambil data karena kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat mengambil data."),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[...]: ...")
 *         )
 *     )
 * )
 */


 class getLangkahDanKegiatan {}