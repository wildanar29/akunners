<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/form7/soal-jawaban/{pkId}/{asesiId}",
 *     tags={"FORM 7 (PENGUMPULAN BUKTI)"},
 *     summary="Menampilkan soal dan jawaban Form 7",
 *     description="API ini digunakan untuk menampilkan soal beserta jawabannya untuk form 7",
 *     @OA\Parameter(
 *         name="pkId",
 *         in="path",
 *         required=true,
 *         description="ID PK (paket kompetensi) untuk menentukan soal",
 *         @OA\Schema(type="integer", example=12)
 *     ),
 *     @OA\Parameter(
 *         name="asesiId",
 *         in="path",
 *         required=true,
 *         description="ID Asesi untuk menampilkan jawaban yang sudah diinput",
 *         @OA\Schema(type="integer", example=501)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil menampilkan soal dan jawaban Form 7",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="no_elemen_form_3", type="string", example="1.1"),
 *                     @OA\Property(
 *                         property="kuk_form3",
 *                         type="array",
 *                         @OA\Items(
 *                             type="object",
 *                             @OA\Property(
 *                                 property="iuk_form3",
 *                                 type="object",
 *                                 @OA\Property(
 *                                     property="soal_form7",
 *                                     type="array",
 *                                     @OA\Items(
 *                                         type="object",
 *                                         @OA\Property(property="id", type="integer", example=101),
 *                                         @OA\Property(property="pk_id", type="integer", example=12),
 *                                         @OA\Property(property="iuk_form3_id", type="integer", example=5),
 *                                         @OA\Property(property="sumber_form", type="string", example="Observasi"),
 *                                         @OA\Property(
 *                                             property="jawaban_form7",
 *                                             type="array",
 *                                             @OA\Items(
 *                                                 type="object",
 *                                                 @OA\Property(property="id", type="integer", example=301),
 *                                                 @OA\Property(property="asesi_id", type="integer", example=501),
 *                                                 @OA\Property(property="asesor_id", type="integer", example=1001),
 *                                                 @OA\Property(property="soal_form7_id", type="integer", example=101),
 *                                                 @OA\Property(property="keputusan", type="string", example="Kompeten"),
 *                                                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-26T10:00:00Z"),
 *                                                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-26T10:30:00Z")
 *                                             )
 *                                         )
 *                                     )
 *                                 )
 *                             )
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="The pk_id field is required.")
 *         )
 *     )
 * )
 */


 class getSoalDanJawabanForm7 {}