<?php

namespace App\Docs;
/**
 * @OA\Put(
 *     path="/form1/approve/{form_1_id}",
 *     tags={"FORM 1"},
 *     summary="Menyetujui Form 1 oleh asesor yang ditugaskan",
 *     operationId="approveForm1ById",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="form_1_id",
 *         in="path",
 *         required=true,
 *         description="ID Form 1 yang akan disetujui",
 *         @OA\Schema(type="integer", example=123)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Form 1 berhasil disetujui dan Form 2 dimulai",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Status berhasil diperbarui menjadi Approved dan notifikasi dikirim.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Form tidak ditemukan atau bukan asesor yang ditugaskan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="Data tidak ditemukan atau Anda bukan asesor yang ditugaskan.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat memproses data",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat memproses data."),
 *             @OA\Property(property="error", type="string", example="Exception message here")
 *         )
 *     )
 * )
 */

 class approveForm1ById {}