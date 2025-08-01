<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form6/soal-jawab/{pkId}",
 *     summary="Ambil Soal dan Jawaban Form 6",
 *     description="Mengambil data soal beserta jawaban dari Form 6 berdasarkan pk_id dan user_id (opsional).",
 *     operationId="getSoalDanJawabanForm6",
 *     tags={"FORM 6"},
 *     @OA\Parameter(
 *         name="pkId",
 *         in="path",
 *         required=true,
 *         description="ID Primary Key dari Form 6",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=false,
 *         description="ID pengguna (jika tidak dikirim, akan menggunakan Auth::id())",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data soal dan jawaban berhasil diambil.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Data soal dan jawaban berhasil diambil."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="nomor_langkah", type="integer", example=1),
 *                     @OA\Property(
 *                         property="kegiatan",
 *                         type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="id", type="integer", example=10),
 *                             @OA\Property(property="nama_kegiatan", type="string", example="Melakukan pemeriksaan..."),
 *                             @OA\Property(property="pencapaian", type="boolean", example=true),
 *                             @OA\Property(
 *                                 property="poin",
 *                                 type="object",
 *                                 @OA\Property(property="id", type="integer", example=100),
 *                                 @OA\Property(
 *                                     property="subPoin",
 *                                     type="array",
 *                                     @OA\Items(
 *                                         @OA\Property(property="id", type="integer", example=200),
 *                                         @OA\Property(property="deskripsi", type="string", example="Langkah sub-poin ...")
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
 *         description="Data soal tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Data soal tidak ditemukan untuk pk_id: 123"),
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
 *             @OA\Property(property="error", type="string", example="Exception message here...")
 *         )
 *     )
 * )
 */


 class getSoalDanJawabanForm6 {}