<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form6/jawaban",
 *     summary="Simpan Jawaban Form 6 oleh Asesor",
 *     description="API ini digunakan untuk menyimpan jawaban yang diinput oleh asesor.",
 *     operationId="simpanJawabanForm6",
 *     tags={"FORM 6"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pk_id", "jawaban"},
 *             @OA\Property(property="pk_id", type="integer", example=1, description="ID PK yang terkait dengan Form 6"),
 *             @OA\Property(
 *                 property="jawaban",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     required={"kegiatan_id", "pencapaian"},
 *                     @OA\Property(property="kegiatan_id", type="integer", example=10),
 *                     @OA\Property(property="pencapaian", type="boolean", example=true)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Jawaban berhasil disimpan.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Jawaban berhasil disimpan.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Jawaban sudah pernah disimpan.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Jawaban untuk kegiatan ID 10 sudah pernah disimpan.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Validasi gagal."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat menyimpan jawaban.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menyimpan jawaban."),
 *             @OA\Property(property="error", type="string", example="Exception message")
 *         )
 *     )
 * )
 */

 class simpanJawabanForm6 {}