<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/get-elemen-pk/{pk_id}",
 *     summary="Ambil data elemen form 3 berdasarkan pk_id",
 *     description="API ini digunakan untuk mengambil data master berdasarkan level PK (pk_id) yang ditampilkan pada saat asesi ingin melakukan pengajuan asesmen.",
 *     operationId="getElemenAsesmen",
 *     tags={"MASTER"},
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="path",
 *         description="ID level PK",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data elemen form 3",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="OK"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="no_elemen_form_3", type="string", example="1"),
 *                     @OA\Property(property="isi_elemen", type="string", example="Mengukur tanda vital"),
 *                     @OA\Property(property="pk_id", type="integer", example=1)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Request tidak valid"
 *     )
 * )
 */


 class getElemenAsesmen {}