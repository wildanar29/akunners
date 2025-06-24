<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form5/jawaban-kegiatan",
 *     tags={"FORM 5"},
 *     summary="Simpan jawaban kegiatan Form 5",
 *     description="Menyimpan status pencapaian kegiatan untuk Form 5 berdasarkan input dari user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"jawaban"},
 *             @OA\Property(
 *                 property="jawaban",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="form_5_id", type="integer", example=1),
 *                     @OA\Property(property="kegiatan_id", type="integer", example=3),
 *                     @OA\Property(property="is_tercapai", type="boolean", example=true),
 *                     @OA\Property(property="catatan", type="string", example="Sudah dilakukan sesuai standar")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Jawaban kegiatan berhasil disimpan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Jawaban kegiatan berhasil disimpan.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=422),
 *             @OA\Property(property="message", type="string", example="Validasi gagal."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menyimpan data."),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[23000]: Integrity constraint violation: ...")
 *         )
 *     )
 * )
 */


 class JawabanForm5 {}