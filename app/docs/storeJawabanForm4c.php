<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/form4c/jawaban",
 *     tags={"FORM 4 (ASESMEN)"},
 *     summary="Simpan jawaban Form 4C",
 *     description="API ini digunakan untuk menyimpan hasil jawaban yang diisi oleh asesor dalam proses penilaian asesi pada Form 4D.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"form_1_id","user_id","jawaban"},
 *             @OA\Property(property="form_1_id", type="integer", example=1, description="ID Form 1 terkait"),
 *             @OA\Property(property="user_id", type="integer", example=5, description="ID User (asesor) yang mengisi jawaban"),
 *             @OA\Property(
 *                 property="jawaban",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="pertanyaan_form4c_id", type="integer", example=10, description="ID pertanyaan Form 4C"),
 *                     @OA\Property(property="question_choice_id", type="integer", example=25, description="ID pilihan jawaban dari question_choice"),
 *                     @OA\Property(property="catatan", type="string", nullable=true, example="Jawaban tambahan dari asesor")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Jawaban berhasil disimpan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Semua jawaban berhasil disimpan.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Beberapa pertanyaan sudah dijawab sebelumnya",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Beberapa pertanyaan sudah pernah dijawab dan tidak disimpan ulang."),
 *             @OA\Property(property="duplikat_pertanyaan_ids", type="array", @OA\Items(type="integer", example=10))
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
 *         description="Terjadi kesalahan saat menyimpan jawaban",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Gagal menyimpan jawaban"),
 *             @OA\Property(property="error", type="string", example="Choice data not found for question_choice_id: 25")
 *         )
 *     )
 * )
 */

 class storeJawabanForm4c {}