<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/sertifikat/preview",
 *     operationId="previewSertifikatByFormId",
 *     tags={"HASIL ASSESSMENT"},
 *     summary="Preview Sertifikat Kompetensi (Tanpa Disimpan)",
 *     description="Generate preview sertifikat kompetensi dalam bentuk PDF berdasarkan form_1_id tanpa menyimpan file ke storage.",
 *
 *     @OA\Parameter(
 *         name="form_1_id",
 *         in="query",
 *         required=true,
 *         description="ID Form 1 untuk generate preview sertifikat",
 *         @OA\Schema(
 *             type="integer",
 *             example=12
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil generate preview PDF sertifikat",
 *         @OA\MediaType(
 *             mediaType="application/pdf"
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Form1 tidak ditemukan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Form1 tidak ditemukan")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Parameter form_1_id tidak diisi",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="form_1_id wajib diisi")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Direktur aktif tidak ditemukan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Direktur aktif tidak ditemukan")
 *         )
 *     )
 * )
 */

 class previewSertifikatByFormId {}