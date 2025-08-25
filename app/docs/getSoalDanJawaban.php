<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/get-soal-jawab-form2",
 *     tags={"FORM 2 (ASESMEN MANDIRI)"},
 *     summary="Ambil data soal dan jawaban Form 2",
 *     description="API ini digunakan untuk menampilkan daftar soal Form 2 dan jawaban asesi berdasarkan pk_id dan user_id.",
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=true,
 *         description="ID Program Keahlian (pk_id)",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         description="User ID dari asesi (user_id)",
 *         @OA\Schema(type="integer", example=1005)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data soal dan jawaban",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="message", type="string", example="Data soal dan jawaban berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="pk_id", type="integer", example=1),
 *                     @OA\Property(property="no_elemen", type="string", example="1"),
 *                     @OA\Property(property="nama_elemen", type="string", example="Mengidentifikasi kebutuhan pelanggan"),
 *                     @OA\Property(property="komponen_id", type="integer", example=2),
 *                     @OA\Property(property="nama_komponen", type="string", example="Kemampuan komunikasi"),
 *                     @OA\Property(property="no_id", type="integer", example=10),
 *                     @OA\Property(property="sub_komponen_id", type="string", example="A"),
 *                     @OA\Property(property="daftar_pertanyaan", type="string", example="Bagaimana Anda memastikan kebutuhan pelanggan terpenuhi?"),
 *                     @OA\Property(property="jawab_form_2_id", type="integer", example=55),
 *                     @OA\Property(property="jawaban_k", type="string", example="Ya"),
 *                     @OA\Property(property="jawaban_bk", type="string", example="Tidak")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 */


 class getSoalDanJawaban {}