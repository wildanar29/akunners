<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form12/by-pk",
 *     summary="Ambil Rekap Nilai Form 12",
 *     description="API ini digunakan untuk mengambil rekapitulasi nilai sebelum dilakukan approve oleh asesi",
 *     tags={"FORM 12 (REKAP NILAI)"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pk_id", "asesi_id"},
 *             @OA\Property(property="pk_id", type="integer", example=1, description="ID PK yang akan diambil"),
 *             @OA\Property(property="asesi_id", type="integer", example=10, description="ID Asesi yang akan difilter"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Sukses mengambil data",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="no_elemen_form_3", type="integer", example=1),
 *                     @OA\Property(property="final", type="string", example="K"),
 *                     @OA\Property(
 *                         property="kukForm3",
 *                         type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="no_kuk", type="integer", example=1),
 *                             @OA\Property(property="final", type="string", example="BK"),
 *                             @OA\Property(
 *                                 property="iukForm3",
 *                                 type="array",
 *                                 @OA\Items(
 *                                     @OA\Property(property="id", type="integer", example=1),
 *                                     @OA\Property(property="no_iuk", type="integer", example=1),
 *                                     @OA\Property(property="final", type="string", example="K"),
 *                                     @OA\Property(
 *                                         property="soalForm7",
 *                                         type="array",
 *                                         @OA\Items(
 *                                             @OA\Property(property="id", type="integer", example=1),
 *                                             @OA\Property(
 *                                                 property="jawabanForm7",
 *                                                 type="array",
 *                                                 @OA\Items(
 *                                                     @OA\Property(property="id", type="integer", example=1),
 *                                                     @OA\Property(property="soal_form7_id", type="integer", example=5),
 *                                                     @OA\Property(property="keputusan", type="string", example="K"),
 *                                                     @OA\Property(property="asesi_id", type="integer", example=10)
 *                                                 )
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
 *         response=404,
 *         description="Data tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="not_found"),
 *             @OA\Property(property="message", type="string", example="Data tidak ditemukan untuk pk_id: 1 dan asesi_id: 99")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 */

 class getByPkId {}