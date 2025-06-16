<?php

namespace App\Docs;

/**
 * @OA\Put(
 *     path="/update-sak/{nik}",
 *     summary="Perbarui metadata file SAK berdasarkan NIK",
 *     tags={"SAK"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="NIK pengguna yang terkait dengan file SAK",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Field metadata yang dapat diperbarui",
 *         @OA\JsonContent(
 *             @OA\Property(property="valid", type="boolean", example=true),
 *             @OA\Property(property="authentic", type="boolean", example=false),
 *             @OA\Property(property="current", type="boolean", example=true),
 *             @OA\Property(property="sufficient", type="boolean", example=false),
 *             @OA\Property(property="ket", type="string", example="Dokumen tidak sesuai format")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil memperbarui data file SAK",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="File details updated successfully."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="user_id", type="integer", example=123),
 *                 @OA\Property(property="sak_id", type="integer", example=456),
 *                 @OA\Property(property="path_file", type="string", example="/storage/sak/example.pdf"),
 *                 @OA\Property(property="valid", type="boolean", example=true),
 *                 @OA\Property(property="authentic", type="boolean", example=false),
 *                 @OA\Property(property="current", type="boolean", example=true),
 *                 @OA\Property(property="sufficient", type="boolean", example=false),
 *                 @OA\Property(property="ket", type="string", example="Dokumen tidak sesuai format")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validation failed. Please ensure all fields are filled correctly."),
 *             @OA\Property(property="errors", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=400)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User atau file tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found. Please check the NIK and try again."),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="An unexpected error occurred while updating file details."),
 *             @OA\Property(property="error", type="string", example="Exception message"),
 *             @OA\Property(property="status_code", type="integer", example=500)
 *         )
 *     )
 * )
 */

 class UpdateSak {}