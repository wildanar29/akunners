<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form4c/soal-jawaban",
 *     tags={"FORM 4 (ASESMEN)"},
 *     summary="Ambil soal dan jawaban Form 4C",
 *     description="API ini digunakan untuk memperlihatkan soal beserta jawabannya yang sudah diisi oleh asesor dan akan dilihat oleh asesi sebelum menyetujui form tersebut.",
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=true,
 *         description="ID PK (Program/Kegiatan)",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="group_no",
 *         in="query",
 *         required=true,
 *         description="Nomor group IUK",
 *         @OA\Schema(type="string", example="A1")
 *     ),
 *     @OA\Parameter(
 *         name="form_1_id",
 *         in="query",
 *         required=true,
 *         description="ID Form 1 yang menjadi parent",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         description="ID User (asesor) yang mengisi jawaban",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data soal dan jawaban berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Data soal dan jawaban berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="items",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="iuk_form3_id", type="integer", example=12),
 *                         @OA\Property(property="no_iuk", type="string", example="1"),
 *                         @OA\Property(property="iuk_desc", type="string", example="Deskripsi IUK"),
 *                         @OA\Property(
 *                             property="pertanyaan_form4c",
 *                             type="array",
 *                             @OA\Items(
 *                                 type="object",
 *                                 @OA\Property(property="id", type="integer", example=101),
 *                                 @OA\Property(property="urutan", type="integer", example=1),
 *                                 @OA\Property(
 *                                     property="question",
 *                                     type="object",
 *                                     @OA\Property(property="id", type="integer", example=55),
 *                                     @OA\Property(property="question_text", type="string", example="Apakah bukti ini valid?"),
 *                                     @OA\Property(
 *                                         property="question_choices",
 *                                         type="array",
 *                                         @OA\Items(
 *                                             type="object",
 *                                             @OA\Property(property="id", type="integer", example=201),
 *                                             @OA\Property(property="is_correct", type="boolean", example=true),
 *                                             @OA\Property(
 *                                                 property="choice",
 *                                                 type="object",
 *                                                 @OA\Property(property="id", type="integer", example=301),
 *                                                 @OA\Property(property="choice_label", type="string", example="A"),
 *                                                 @OA\Property(property="choice_text", type="string", example="Ya, sesuai")
 *                                             )
 *                                         )
 *                                     )
 *                                 ),
 *                                 @OA\Property(
 *                                     property="jawaban",
 *                                     nullable=true,
 *                                     type="object",
 *                                     @OA\Property(property="question_choice_id", type="integer", example=201),
 *                                     @OA\Property(property="choice_label", type="string", example="A"),
 *                                     @OA\Property(property="is_correct", type="boolean", example=true),
 *                                     @OA\Property(property="catatan", type="string", example="Catatan asesor", nullable=true)
 *                                 )
 *                             )
 *                         )
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="score",
 *                     type="object",
 *                     description="Ringkasan skor form 4C berdasarkan attempt terakhir",
 *                     @OA\Property(property="total_pertanyaan", type="integer", example=10),
 *                     @OA\Property(property="jawaban_benar", type="integer", example=8),
 *                     @OA\Property(property="jawaban_salah", type="integer", example=2),
 *                     @OA\Property(property="skor", type="integer", example=8, description="Jumlah jawaban benar (dipakai juga sebagai skor)"),
 *                     @OA\Property(property="persentase", type="number", format="float", example=80.00)
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
 *                 additionalProperties=@OA\Schema(type="array", @OA\Items(type="string"))
 *             )
 *         )
 *     )
 * )
 */
class getSoalDanJawabanForm4c {}
