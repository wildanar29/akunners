<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form4b/soal-jawaban",
 *     tags={"FORM 4 (ASESMEN)"},
 *     summary="Ambil Soal & Jawaban Form 4B",
 *     description="API ini digunakan untuk mengambil soal dan jawaban yang telah diisi oleh asesor dan diperlihatkan pada asesi sebelum menyetujui form tersebut.",
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=true,
 *         description="ID PK",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="group_no",
 *         in="query",
 *         required=true,
 *         description="Nomor Group IUK",
 *         @OA\Schema(type="string", example="1,2")
 *     ),
 *     @OA\Parameter(
 *         name="form_1_id",
 *         in="query",
 *         required=true,
 *         description="ID Form 1",
 *         @OA\Schema(type="integer", example=101)
 *     ),
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         description="ID User (Asesor)",
 *         @OA\Schema(type="integer", example=202)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data soal dan jawaban berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Data soal dan jawaban berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="iuk_form3_id", type="integer", example=1001),
 *                     @OA\Property(property="no_iuk", type="string", example="1"),
 *                     @OA\Property(property="group_no", type="string", example="1"),
 *                     @OA\Property(
 *                         property="jawaban",
 *                         type="object",
 *                         nullable=true,
 *                         @OA\Property(property="jawaban_asesi", type="string", example="Asesi menjawab dengan baik"),
 *                         @OA\Property(property="pencapaian", type="boolean", example=true),
 *                         @OA\Property(property="nilai", type="integer", example=85),
 *                         @OA\Property(property="catatan", type="string", example="Perlu sedikit perbaikan pada detail jawaban")
 *                     ),
 *                     @OA\Property(
 *                         property="pertanyaan_form4b",
 *                         type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="parent_id", type="integer", example=null),
 *                             @OA\Property(property="pertanyaan", type="string", example="Jelaskan langkah kerja sesuai SOP"),
 *                             @OA\Property(property="urutan", type="integer", example=1),
 *                             @OA\Property(
 *                                 property="poin_pertanyaan",
 *                                 type="array",
 *                                 @OA\Items(
 *                                     @OA\Property(property="id", type="integer", example=10),
 *                                     @OA\Property(property="pertanyaan_form4b_id", type="integer", example=1),
 *                                     @OA\Property(property="isi_poin", type="string", example="Langkah awal sesuai standar"),
 *                                     @OA\Property(property="urutan", type="integer", example=1)
 *                                 )
 *                             ),
 *                             @OA\Property(
 *                                 property="children",
 *                                 type="array",
 *                                 @OA\Items(
 *                                     @OA\Property(property="id", type="integer", example=2),
 *                                     @OA\Property(property="parent_id", type="integer", example=1),
 *                                     @OA\Property(property="pertanyaan", type="string", example="Sebutkan alat yang digunakan"),
 *                                     @OA\Property(property="urutan", type="integer", example=2),
 *                                     @OA\Property(
 *                                         property="poin_pertanyaan",
 *                                         type="array",
 *                                         @OA\Items(
 *                                             @OA\Property(property="id", type="integer", example=11),
 *                                             @OA\Property(property="pertanyaan_form4b_id", type="integer", example=2),
 *                                             @OA\Property(property="isi_poin", type="string", example="Obeng, tang, palu"),
 *                                             @OA\Property(property="urutan", type="integer", example=1)
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


 class getSoalDanJawabanForm4b {}