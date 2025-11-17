<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form4c/jawaban",
 *     tags={"FORM 4 (ASESMEN)"},
 *     summary="Simpan jawaban Form 4C",
 *     description="Mengirim jawaban Form 4C dari asesi, sekaligus mengembalikan hasil penilaian (score) yang dihitung dari jawaban.",
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"form_1_id","asesi_id","jawaban"},
 *
 *             @OA\Property(property="form_1_id", type="integer", example=1, description="ID Form 1 terkait"),
 *             @OA\Property(property="asesi_id", type="integer", example=68, description="ID Asesi yang mengisi Form 4C"),
 *
 *             @OA\Property(
 *                 property="jawaban",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="pertanyaan_form4c_id", type="integer", example=10, description="ID pertanyaan Form 4C"),
 *                     @OA\Property(property="question_choice_id", type="integer", example=25, description="ID pilihan jawaban"),
 *                     @OA\Property(property="catatan", type="string", nullable=true, example="Penjelasan tambahan")
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Jawaban berhasil disimpan dan score dikembalikan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Semua jawaban berhasil disimpan."),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 description="Hasil perhitungan nilai Form 4C",
 *                 @OA\Property(property="total_jawaban", type="integer", example=21),
 *                 @OA\Property(property="jawaban_benar", type="integer", example=20),
 *                 @OA\Property(property="jawaban_salah", type="integer", example=1),
 *                 @OA\Property(property="skor", type="integer", example=20),
 *                 @OA\Property(property="persentase", type="string", example="95.24")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=409,
 *         description="Sebagian jawaban duplikat, tetapi score tetap dikembalikan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Beberapa pertanyaan sudah pernah dijawab dan tidak disimpan ulang."),
 *             @OA\Property(
 *                 property="duplikat_pertanyaan_ids",
 *                 type="array",
 *                 @OA\Items(type="integer", example=10)
 *             ),
 *             @OA\Property(
 *                 property="result",
 *                 type="object",
 *                 description="Penilaian meskipun sebagian jawaban duplikat",
 *                 @OA\Property(property="total_jawaban", type="integer", example=21),
 *                 @OA\Property(property="jawaban_benar", type="integer", example=20),
 *                 @OA\Property(property="jawaban_salah", type="integer", example=1),
 *                 @OA\Property(property="skor", type="integer", example=20),
 *                 @OA\Property(property="persentase", type="string", example="95.24")
 *             )
 *         )
 *     ),
 *
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
 *
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan pada server",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Gagal menyimpan jawaban"),
 *             @OA\Property(property="error", type="string", example="Choice data not found for question_choice_id: 25")
 *         )
 *     )
 * )
 */
class storeJawabanForm4c {}
