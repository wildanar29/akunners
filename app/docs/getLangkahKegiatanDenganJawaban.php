<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form5/soal-jawab",
 *     summary="Ambil Soal dan Jawaban Form 5",
 *     description="API ini digunakan untuk mengambil soal dan jawaban pada Form 5",
 *     operationId="getLangkahKegiatanDenganJawaban",
 *     tags={"Form 5"},
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=true,
 *         description="ID dari PK (Program Kerja)",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="form_5_id",
 *         in="query",
 *         required=true,
 *         description="ID dari Form 5",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data langkah, kegiatan, dan jawaban berhasil diambil.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Data langkah, kegiatan, dan jawaban berhasil diambil."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="nama_langkah", type="string", example="Langkah 1"),
 *                     @OA\Property(
 *                         property="kegiatans",
 *                         type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="id", type="integer", example=5),
 *                             @OA\Property(property="nama_kegiatan", type="string", example="Mengisi laporan"),
 *                             @OA\Property(property="is_tercapai", type="boolean", example=true),
 *                             @OA\Property(property="catatan", type="string", example="Sudah dilakukan dengan baik")
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Parameter pk_id dan form_5_id wajib diisi.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="Parameter pk_id dan form_5_id wajib diisi."),
 *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat mengambil data.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat mengambil data."),
 *             @OA\Property(property="error", type="string", example="Detail error...")
 *         )
 *     )
 * )
 */

 class getLangkahKegiatanDenganJawaban {}