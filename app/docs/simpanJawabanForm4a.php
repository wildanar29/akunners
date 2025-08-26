<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form4a/jawaban",
 *     tags={"FORM 4 (ASESMEN)"},
 *     summary="Simpan Jawaban Form 4A",
 *     description="API ini digunakan untuk menyimpan jawaban atau isi dari form 4A yang diisi oleh asesor.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"form_1_id","user_id","jawaban"},
 *             @OA\Property(property="form_1_id", type="integer", example=1, description="ID Form 1 terkait"),
 *             @OA\Property(property="user_id", type="integer", example=10, description="ID user asesor"),
 *             @OA\Property(
 *                 property="jawaban",
 *                 type="array",
 *                 description="Daftar jawaban yang diisi asesor",
 *                 @OA\Items(
 *                     type="object",
 *                     required={"iuk_form3_id","pencapaian"},
 *                     @OA\Property(property="iuk_form3_id", type="integer", example=5, description="ID IUK Form 3"),
 *                     @OA\Property(property="pencapaian", type="boolean", example=true, description="Status pencapaian (true/false)"),
 *                     @OA\Property(property="nilai", type="integer", nullable=true, example=85, description="Nilai (opsional)"),
 *                     @OA\Property(property="catatan", type="string", nullable=true, example="Cukup baik namun perlu perbaikan.", description="Catatan tambahan (opsional)")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Jawaban berhasil disimpan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Jawaban berhasil disimpan")
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
 *                 additionalProperties=@OA\Schema(type="array", @OA\Items(type="string"))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server saat menyimpan jawaban",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menyimpan jawaban"),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[23000]: Integrity constraint violation ...")
 *         )
 *     )
 * )
 */


 class simpanJawabanForm4a {}