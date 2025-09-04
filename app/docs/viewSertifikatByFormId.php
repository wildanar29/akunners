<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/sertifikat/view/{form_1_id}",
 *     tags={"HASIL ASSESSMENT"},
 *     summary="View Sertifikat",
 *     description="API ini digunakan untuk melakukan view dari sertifikat yang sudah ada",
 *     operationId="viewSertifikatByFormId",
 *     @OA\Parameter(
 *         name="form_1_id",
 *         in="path",
 *         required=true,
 *         description="ID form 1 yang terkait dengan sertifikat",
 *         @OA\Schema(type="integer", example=123)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File sertifikat berhasil ditampilkan",
 *         @OA\Header(
 *             header="Content-Type",
 *             description="Tipe file sertifikat (contoh: application/pdf)",
 *             @OA\Schema(type="string", example="application/pdf")
 *         ),
 *         @OA\Header(
 *             header="Content-Disposition",
 *             description="Header untuk menampilkan file sertifikat secara inline di browser",
 *             @OA\Schema(type="string", example="inline; filename=\"sertifikat_ASESI_123.pdf\"")
 *         ),
 *         @OA\MediaType(
 *             mediaType="application/pdf"
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Sertifikat atau file tidak ditemukan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Sertifikat untuk form ini tidak ditemukan")
 *         )
 *     )
 * )
 */

 class viewSertifikatByFormId {}