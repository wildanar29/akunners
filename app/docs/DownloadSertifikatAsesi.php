<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/sertifikat/download/{form_1_id}",
 *     tags={"HASIL ASSESSMENT"},
 *     summary="Download sertifikat hasil asesmen",
 *     description="API ini digunakan untuk melakukan download sertifikat hasil asesmen berdasarkan form_1_id.",
 *     @OA\Parameter(
 *         name="form_1_id",
 *         in="path",
 *         required=true,
 *         description="ID Form 1 untuk mencari sertifikat",
 *         @OA\Schema(type="integer", example=101)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil download file sertifikat",
 *         @OA\Header(
 *             header="Content-Disposition",
 *             description="Attachment header untuk file sertifikat",
 *             @OA\Schema(type="string", example="attachment; filename=\"sertifikat.pdf\"")
 *         ),
 *         @OA\MediaType(
 *             mediaType="application/pdf",
 *             @OA\Schema(type="string", format="binary")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Sertifikat tidak ditemukan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Sertifikat untuk form ini tidak ditemukan")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan server",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan pada server")
 *         )
 *     )
 * )
 */

 class DownloadSertifikatAsesi {}