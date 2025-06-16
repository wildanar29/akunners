<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/get-no-expired-sak/{nik}",
 *     summary="Mengambil nomor dan masa berlaku SAK berdasarkan NIK user",
 *     tags={"SAK"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="Nomor Induk Kependudukan (NIK) user",
 *         @OA\Schema(type="string", example="3201123456789012")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data SAK berhasil ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="nomor_sak", type="string", example="SAK-2025-001"),
 *                 @OA\Property(property="masa_berlaku_sak", type="string", format="date", example="2025-12-31")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User atau data SAK tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User Not Found."),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan pada server."),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[HY000] ..."),
 *             @OA\Property(property="status_code", type="integer", example=500)
 *         )
 *     )
 * )
 */


 class getSakByNik {}