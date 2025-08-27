<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form4b/soal",
 *     tags={"FORM 4 (ASESMEN)"},
 *     summary="Ambil Soal Form 4B",
 *     description="API ini digunakan untuk mengambil soal 4B. Ditampilkan di asesor sebagai soal untuk Form 4B.",
 *     @OA\Parameter(
 *         name="group_no",
 *         in="query",
 *         required=true,
 *         description="Nomor group IUK",
 *         @OA\Schema(type="string", example="1")
 *     ),
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=true,
 *         description="ID PK yang terkait",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data IUK dan pertanyaan berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Data IUK dan pertanyaan berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="iuk_form3_id", type="integer", example=101),
 *                     @OA\Property(property="no_iuk", type="string", example="1.1"),
 *                     @OA\Property(property="group_no", type="string", example="1"),
 *                     @OA\Property(property="pk_id", type="integer", example=10),
 *                     @OA\Property(
 *                         property="pertanyaan_form4b",
 *                         type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="parent_id", type="integer", example=null),
 *                             @OA\Property(property="pertanyaan", type="string", example="Apakah dokumen X tersedia?"),
 *                             @OA\Property(property="urutan", type="integer", example=1),
 *                             @OA\Property(
 *                                 property="poin_pertanyaan",
 *                                 type="array",
 *                                 @OA\Items(
 *                                     @OA\Property(property="id", type="integer", example=1),
 *                                     @OA\Property(property="pertanyaan_form4b_id", type="integer", example=1),
 *                                     @OA\Property(property="isi_poin", type="string", example="Cek kesesuaian dokumen"),
 *                                     @OA\Property(property="urutan", type="integer", example=1)
 *                                 )
 *                             ),
 *                             @OA\Property(
 *                                 property="children",
 *                                 type="array",
 *                                 @OA\Items(
 *                                     @OA\Property(property="id", type="integer", example=2),
 *                                     @OA\Property(property="parent_id", type="integer", example=1),
 *                                     @OA\Property(property="pertanyaan", type="string", example="Apakah bukti pendukung tersedia?"),
 *                                     @OA\Property(property="urutan", type="integer", example=2),
 *                                     @OA\Property(
 *                                         property="poin_pertanyaan",
 *                                         type="array",
 *                                         @OA\Items(
 *                                             @OA\Property(property="id", type="integer", example=3),
 *                                             @OA\Property(property="pertanyaan_form4b_id", type="integer", example=2),
 *                                             @OA\Property(property="isi_poin", type="string", example="Verifikasi dokumen tambahan"),
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

 class getSoalForm4b {}