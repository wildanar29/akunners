<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/form10/{form10Id}/submit",
 *     tags={"FORM 10 (DAFTAR TILIK)"},
 *     summary="Simpan Jawaban Form 10",
 *     description="API ini digunakan untuk menyimpan data jawaban yang diisi oleh asesor",
 *     @OA\Parameter(
 *         name="form10Id",
 *         in="path",
 *         required=true,
 *         description="ID Form 10 yang ingin disubmit",
 *         @OA\Schema(type="integer", example=7)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Data jawaban daftar tilik",
 *         @OA\JsonContent(
 *             required={"jawaban"},
 *             @OA\Property(
 *                 property="jawaban",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     required={"kegiatan_daftar_tilik_id", "dilakukan"},
 *                     @OA\Property(
 *                         property="kegiatan_daftar_tilik_id",
 *                         type="integer",
 *                         example=15,
 *                         description="ID kegiatan daftar tilik"
 *                     ),
 *                     @OA\Property(
 *                         property="dilakukan",
 *                         type="boolean",
 *                         example=true,
 *                         description="Status apakah kegiatan dilakukan"
 *                     ),
 *                     @OA\Property(
 *                         property="catatan",
 *                         type="string",
 *                         nullable=true,
 *                         example="Sudah dilaksanakan sesuai prosedur"
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Jawaban berhasil disimpan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Jawaban berhasil disimpan.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Form 10 tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data Form 10 tidak ditemukan.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "jawaban.0.kegiatan_daftar_tilik_id": {"The kegiatan daftar tilik id field is required."},
 *                     "jawaban.0.dilakukan": {"The dilakukan field must be true or false."}
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server saat menyimpan jawaban",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Gagal menyimpan jawaban."),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[42S22]: Column not found...")
 *         )
 *     )
 * )
 */

 class submitSoalList {}