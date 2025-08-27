<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/form4d/jawaban",
 *     tags={"FORM 4 (ASESMEN)"},
 *     summary="Simpan Jawaban Form 4D",
 *     description="API ini digunakan untuk menyimpan jawaban form 4D oleh asesor",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"form_1_id","user_id","jawaban"},
 *             @OA\Property(property="form_1_id", type="integer", example=101, description="ID Form 1 sebagai parent"),
 *             @OA\Property(property="user_id", type="integer", example=5, description="ID user asesor yang mengisi form"),
 *             @OA\Property(
 *                 property="jawaban",
 *                 type="array",
 *                 description="List jawaban Form 4D",
 *                 @OA\Items(
 *                     @OA\Property(property="pertanyaan_form4d_id", type="integer", example=12, description="ID pertanyaan form 4D"),
 *                     @OA\Property(property="pencapaian", type="boolean", example=true, description="Status pencapaian (true = tercapai, false = tidak tercapai)")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Jawaban Form 4D berhasil disimpan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Jawaban Form 4D berhasil disimpan")
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
 *                 example={"jawaban.0.pertanyaan_form4d_id": {"The pertanyaan_form4d_id field is required."}}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat menyimpan data",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menyimpan data"),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[HY000] ...")
 *         )
 *     )
 * )
 */


 class simpanJawabanForm4d {}