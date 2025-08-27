<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/form7/soal/{pkId}",
 *     tags={"FORM 7 (PENGUMPULAN BUKTI)"},
 *     summary="Ambil Soal Form 7",
 *     description="API ini digunakan untuk menampilkan soal pada Form 7 atau form pengumpulan bukti",
 *     @OA\Parameter(
 *         name="pkId",
 *         in="path",
 *         required=true,
 *         description="ID PK (paket kompetensi) yang digunakan untuk filter soal Form 7",
 *         @OA\Schema(type="integer", example=12)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil daftar soal Form 7",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="no_elemen_form_3", type="integer", example=1),
 *                     @OA\Property(property="kuk_form3", type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="iuk_form3", type="array",
 *                                 @OA\Items(
 *                                     @OA\Property(
 *                                         property="soal_form7",
 *                                         type="array",
 *                                         @OA\Items(
 *                                             @OA\Property(property="id", type="integer", example=101),
 *                                             @OA\Property(property="pk_id", type="integer", example=12),
 *                                             @OA\Property(property="soal", type="string", example="Jelaskan prosedur pemeriksaan dokumen...")
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
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="The pk id field is required.")
 *         )
 *     )
 * )
 */

 class getSoalForm7 {}