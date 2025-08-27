<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form4d/soal-jawaban",
 *     tags={"FORM 4 (ASESMEN)"},
 *     summary="Ambil Soal & Jawaban Form 4D",
 *     description="API ini digunakan untuk memperlihatkan soal dan jawaban kepada asesi",
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=true,
 *         description="ID PK",
 *         @OA\Schema(type="integer", example=55)
 *     ),
 *     @OA\Parameter(
 *         name="form_1_id",
 *         in="query",
 *         required=true,
 *         description="ID Form 1 sebagai parent",
 *         @OA\Schema(type="integer", example=101)
 *     ),
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         description="ID user (asesor atau asesi) yang terkait",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data soal dan jawaban Form 4D berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Data soal dan jawaban Form 4D berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=12),
 *                     @OA\Property(property="urutan", type="integer", example=1),
 *                     @OA\Property(property="dokumen", type="string", nullable=true, example="dokumen_pendukung.pdf"),
 *                     @OA\Property(
 *                         property="kuk",
 *                         type="object",
 *                         @OA\Property(property="kuk_form3_id", type="integer", example=33),
 *                         @OA\Property(property="no_elemen_form_3", type="string", example="1.2"),
 *                         @OA\Property(property="no_kuk", type="string", example="1.2.1"),
 *                         @OA\Property(property="kuk_name", type="string", example="Melaksanakan prosedur kerja"),
 *                         @OA\Property(property="pk_id", type="integer", example=55)
 *                     ),
 *                     @OA\Property(
 *                         property="jawaban",
 *                         type="object",
 *                         nullable=true,
 *                         @OA\Property(property="pencapaian", type="boolean", example=true)
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={"pk_id": {"The pk_id field is required."}}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat mengambil data",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat mengambil data"),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[HY000] ...")
 *         )
 *     )
 * )
 */

 class getSoalDanJawabanForm4d {}