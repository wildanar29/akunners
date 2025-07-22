<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/jawaban-asesi",
 *     tags={"FORM 2"},
 *     summary="Input jawaban asesi pada Form 2",
 *     description="API ini digunakan untuk menyimpan atau memperbarui jawaban asesmen diri oleh asesi pada Form 2.",
 *     operationId="JawabanAsesi",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"form_2_id", "jawaban"},
 *             @OA\Property(
 *                 property="form_2_id",
 *                 type="integer",
 *                 example=101,
 *                 description="ID dari Form 2"
 *             ),
 *             @OA\Property(
 *                 property="jawaban",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     required={"no_id"},
 *                     @OA\Property(property="no_id", type="integer", example=1, description="Nomor soal"),
 *                     @OA\Property(property="k", type="boolean", example=true, description="Kompeten"),
 *                     @OA\Property(property="bk", type="boolean", example=false, description="Belum Kompeten")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Jawaban berhasil disimpan atau diperbarui",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Jawaban berhasil disimpan atau diperbarui."),
 *             @OA\Property(property="penilaian_asesi", type="number", format="float", example=87.5),
 *             @OA\Property(property="total_k", type="integer", example=7),
 *             @OA\Property(property="total_bk", type="integer", example=1),
 *             @OA\Property(property="form_2_id", type="integer", example=101)
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Form tidak ditemukan untuk user",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Data form_1 tidak ditemukan untuk user ini")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server saat menyimpan jawaban",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menyimpan jawaban."),
 *             @OA\Property(property="error", type="string", example="Exception message")
 *         )
 *     )
 * )
 */


 class JawabanAsesi {}