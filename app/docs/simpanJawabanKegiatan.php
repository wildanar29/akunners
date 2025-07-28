<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form5/jawaban-kegiatan",
 *     tags={"Form 5"},
 *     summary="Simpan Jawaban Kegiatan Form 5",
 *     description="API ini digunakan untuk mengisi jawaban Form 5 oleh asesor.",
 *     operationId="simpanJawabanKegiatan",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"jawaban"},
 *             @OA\Property(
 *                 property="jawaban",
 *                 type="array",
 *                 @OA\Items(
 *                     required={"form_5_id", "kegiatan_id", "is_tercapai"},
 *                     @OA\Property(property="form_5_id", type="integer", example=101, description="ID Form 5"),
 *                     @OA\Property(property="kegiatan_id", type="integer", example=20, description="ID Kegiatan Form 5"),
 *                     @OA\Property(property="is_tercapai", type="boolean", example=true, description="Apakah kegiatan tercapai"),
 *                     @OA\Property(property="catatan", type="string", example="Sudah dikerjakan dengan baik", description="Catatan tambahan (opsional)")
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Jawaban berhasil disimpan dan status diperbarui",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Jawaban kegiatan berhasil disimpan dan status diperbarui.")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=422),
 *             @OA\Property(property="message", type="string", example="Validasi gagal."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server saat menyimpan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menyimpan data."),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[...]: ...")
 *         )
 *     )
 * )
 */

 class simpanJawabanKegiatan {}