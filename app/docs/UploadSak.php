<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/upload-sak",
 *     summary="Unggah atau perbarui file SAK untuk pengguna",
 *     operationId="uploadSakFile",
 *     tags={"SAK"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"nomor_sak", "masa_berlaku_sak"},
 *                 @OA\Property(
 *                     property="path_file",
 *                     type="string",
 *                     format="binary",
 *                     description="File PDF/JPG/JPEG/PNG (maks 2MB)"
 *                 ),
 *                 @OA\Property(property="nomor_sak", type="string"),
 *                 @OA\Property(property="masa_berlaku_sak", type="string", format="date"),
 *                 @OA\Property(property="valid", type="boolean"),
 *                 @OA\Property(property="authentic", type="boolean"),
 *                 @OA\Property(property="current", type="boolean"),
 *                 @OA\Property(property="sufficient", type="boolean"),
 *                 @OA\Property(property="ket", type="string", maxLength=255)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil memperbarui file SAK",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="sak_id", type="integer"),
 *                 @OA\Property(property="user_id", type="integer"),
 *                 @OA\Property(property="file_path", type="string"),
 *                 @OA\Property(property="nomor_sak", type="string"),
 *                 @OA\Property(property="masa_berlaku_sak", type="string", format="date")
 *             ),
 *             @OA\Property(property="status_code", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Berhasil mengunggah file SAK baru",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="sak_id", type="integer"),
 *                 @OA\Property(property="user_id", type="integer"),
 *                 @OA\Property(property="file_path", type="string"),
 *                 @OA\Property(property="nomor_sak", type="string"),
 *                 @OA\Property(property="masa_berlaku_sak", type="string", format="date")
 *             ),
 *             @OA\Property(property="status_code", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="errors", type="object"),
 *             @OA\Property(property="status_code", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status_code", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="error", type="string"),
 *             @OA\Property(property="status_code", type="integer")
 *         )
 *     )
 * )
 */

class UploadSak {}
