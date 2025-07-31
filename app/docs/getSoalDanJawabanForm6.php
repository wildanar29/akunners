<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/form6/soal-jawab/{pkId}",
 *     summary="Ambil Soal dan Jawaban Form 6",
 *     description="API ini digunakan untuk mengambil soal dan jawaban dari form 6 yang ditampilkan kepada asesi sebelum melakukan approve dari form 6 yang sudah diisi oleh asesor sebelumnya.",
 *     operationId="getSoalDanJawabanForm6",
 *     tags={"FORM 6"},
 *     @OA\Parameter(
 *         name="pkId",
 *         in="path",
 *         required=true,
 *         description="ID PK untuk mengambil soal dan jawaban Form 6",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=false,
 *         description="User ID asesor (jika ingin override Auth::id())",
 *         @OA\Schema(type="integer", example=101)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data soal dan jawaban berhasil diambil.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Data soal dan jawaban berhasil diambil."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(type="object")  // Anda bisa detailkan struktur data jika diperlukan
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data soal tidak ditemukan.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Data soal tidak ditemukan untuk pk_id: 99"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Validasi gagal."),
 *             @OA\Property(property="errors", type="object")
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

 class GetSoalDanJawabanForm6 {}