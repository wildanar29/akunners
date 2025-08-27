<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form10/soal/{form10Id}",
 *     tags={"FORM 10 (DAFTAR TILIK)"},
 *     summary="Ambil Soal Form 10",
 *     description="API ini digunakan untuk mengambil soal-soal pada Form 10",
 *     @OA\Parameter(
 *         name="form10Id",
 *         in="path",
 *         required=true,
 *         description="ID Form 10 yang ingin diambil soalnya",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil list soal pada Form 10",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=10),
 *                     @OA\Property(property="pk_id", type="integer", example=12),
 *                     @OA\Property(property="daftar_tilik_id", type="integer", example=3),
 *                     @OA\Property(property="pertanyaan", type="string", example="Apakah prosedur X sudah dilakukan?"),
 *                     @OA\Property(property="urutan", type="integer", example=1),
 *                     @OA\Property(
 *                         property="children",
 *                         type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="id", type="integer", example=11),
 *                             @OA\Property(property="pertanyaan", type="string", example="Langkah X.1 sudah dilaksanakan?"),
 *                             @OA\Property(property="urutan", type="integer", example=1)
 *                         )
 *                     ),
 *                     @OA\Property(
 *                         property="jawaban",
 *                         type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="id", type="integer", example=50),
 *                             @OA\Property(property="dilakukan", type="boolean", example=true),
 *                             @OA\Property(property="catatan", type="string", example="Sudah sesuai SOP")
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Form 10 tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data Form 10 tidak ditemukan.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat mengambil soal Form 10",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Gagal mengambil list soal."),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[42S22]: Column not found...")
 *         )
 *     )
 * )
 */

 class getSoalList {}