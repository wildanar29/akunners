<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/transkrip/preview",
 *     operationId="previewTranskrip",
 *     tags={"HASIL ASSESSMENT"},
 *     summary="Preview Transkrip Nilai (Tanpa Disimpan)",
 *     description="Generate preview transkrip nilai dalam bentuk PDF berdasarkan form_1_id tanpa menyimpan ke storage.",
 *     
 *     @OA\Parameter(
 *         name="form_1_id",
 *         in="query",
 *         required=true,
 *         description="ID Form 1 yang digunakan untuk generate transkrip preview",
 *         @OA\Schema(
 *             type="integer",
 *             example=10
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil generate preview PDF transkrip nilai",
 *         @OA\MediaType(
 *             mediaType="application/pdf"
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan (Form1 atau Elemen tidak tersedia)",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Data elemen tidak ditemukan")
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
 *         description="Pejabat Kepala Bidang aktif tidak ditemukan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Kepala Bidang aktif tidak ditemukan")
 *         )
 *     )
 * )
 */

 class previewTranskrip {}