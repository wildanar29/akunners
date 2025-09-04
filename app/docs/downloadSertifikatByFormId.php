<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/sertifikat/download/{form_1_id}",
 *     tags={"HASIL ASSESSMENT"},
 *     summary="Download Sertifikat",
 *     description="API ini digunakan untuk mendownload sertifikat yang sudah ada",
 *     operationId="downloadSertifikatByFormId",
 *     @OA\Parameter(
 *         name="form_1_id",
 *         in="path",
 *         required=true,
 *         description="ID form 1 yang terkait dengan sertifikat",
 *         @OA\Schema(type="integer", example=123)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File sertifikat berhasil diunduh",
 *         @OA\Header(
 *             header="Content-Type",
 *             description="Tipe file sertifikat (contoh: application/pdf)",
 *             @OA\Schema(type="string", example="application/pdf")
 *         ),
 *         @OA\Header(
 *             header="Content-Disposition",
 *             description="Header untuk memaksa browser melakukan download",
 *             @OA\Schema(type="string", example="attachment; filename=\"sertifikat_ASESI_123.pdf\"")
 *         ),
 *         @OA\MediaType(
 *             mediaType="application/pdf"
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Sertifikat tidak ditemukan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Sertifikat untuk form ini tidak ditemukan")
 *         )
 *     )
 * )
 */

 class downloadSertifikatByFormId {}