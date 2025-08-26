<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form4a/soal-jawaban",
 *     tags={"FORM 4 (ASESMEN)"},
 *     summary="Ambil Soal dan Jawaban Form 4A",
 *     description="API ini digunakan untuk mengambil soal dan jawaban yang telah diisi oleh asesor.",
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=true,
 *         description="ID PK yang digunakan untuk filter data",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="group_no",
 *         in="query",
 *         required=true,
 *         description="Nomor grup untuk filter data (contoh: 1,2,3)",
 *         @OA\Schema(type="string", example="1")
 *     ),
 *     @OA\Parameter(
 *         name="form_1_id",
 *         in="query",
 *         required=true,
 *         description="ID Form 1 terkait jawaban",
 *         @OA\Schema(type="integer", example=1001)
 *     ),
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         description="ID user asesor",
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
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="no_elemen_form_3", type="string", example="1"),
 *                     @OA\Property(
 *                         property="kuk_form3",
 *                         type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="id", type="integer", example=11),
 *                             @OA\Property(property="no_kuk", type="string", example="1.1"),
 *                             @OA\Property(
 *                                 property="iuk_form3",
 *                                 type="array",
 *                                 @OA\Items(
 *                                     @OA\Property(property="iuk_form3_id", type="integer", example=21),
 *                                     @OA\Property(property="no_iuk", type="string", example="IUK-1"),
 *                                     @OA\Property(property="group_no", type="string", example="1"),
 *                                     @OA\Property(
 *                                         property="poin_form4",
 *                                         type="array",
 *                                         @OA\Items(ref="#/components/schemas/PoinForm4")
 *                                     ),
 *                                     @OA\Property(
 *                                         property="jawaban",
 *                                         type="object",
 *                                         @OA\Property(property="pencapaian", type="boolean", nullable=true, example=true),
 *                                         @OA\Property(property="nilai", type="integer", nullable=true, example=85),
 *                                         @OA\Property(property="catatan", type="string", nullable=true, example="Sudah cukup baik")
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
 *
 * @OA\Schema(
 *     schema="PoinForm4",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=12),
 *     @OA\Property(property="isi_poin", type="string", example="Melakukan pengecekan dokumen pendukung"),
 *     @OA\Property(property="urutan", type="integer", example=1),
 *     @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
 *     @OA\Property(
 *         property="children",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/PoinForm4")
 *     )
 * )
 */


 class getSoalDanJawabanForm4a {}