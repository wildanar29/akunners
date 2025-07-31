<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/form6/soal/{pkId}",
 *     summary="Ambil Soal Form 6 Berdasarkan PK ID",
 *     description="API ini digunakan untuk menampilkan soal dari form 6 berdasarkan PK id yang dipilih.",
 *     operationId="getSoalForm6",
 *     tags={"FORM 6"},
 *     @OA\Parameter(
 *         name="pkId",
 *         in="path",
 *         required=true,
 *         description="ID PK yang digunakan untuk mengambil soal Form 6",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data soal berhasil diambil.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Data soal berhasil diambil."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(type="object")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data soal tidak ditemukan untuk pk_id yang diberikan.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Data soal tidak ditemukan untuk pk_id: 99"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat mengambil data soal.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat mengambil data soal."),
 *             @OA\Property(property="error", type="string", example="Exception message")
 *         )
 *     )
 * )
 */

 class SoalForm6 {}