<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/form4c/riwayat/{form1Id}/{asesiId}",
 *     summary="Mengambil riwayat attempt Form 4C untuk seorang Asesi",
 *     description="Endpoint ini mengembalikan daftar riwayat attempt (percobaan) Form 4C berdasarkan form_1_id dan user_id (asensi).",
 *     tags={"FORM 4 (ASESMEN)"},
 *
 *     @OA\Parameter(
 *         name="form1Id",
 *         in="path",
 *         required=true,
 *         description="ID Form 1 (form_1_id)",
 *         @OA\Schema(type="integer", example=101)
 *     ),
 *     
 *     @OA\Parameter(
 *         name="asesiId",
 *         in="path",
 *         required=true,
 *         description="ID Asesi (user_id)",
 *         @OA\Schema(type="integer", example=80)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Riwayat attempt berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Riwayat attempt Form 4C berhasil diambil."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=12),
 *                     @OA\Property(property="form_1_id", type="integer", example=101),
 *                     @OA\Property(property="user_id", type="integer", example=80),
 *                     @OA\Property(property="attempt", type="integer", example=1),
 *                     @OA\Property(property="tanggal_attempt", type="string", format="date-time", example="2025-11-21 10:35:00"),
 *                     @OA\Property(property="total_jawaban", type="integer", example=20),
 *                     @OA\Property(property="jawaban_benar", type="integer", example=18),
 *                     @OA\Property(property="jawaban_salah", type="integer", example=2),
 *                     @OA\Property(property="nilai", type="number", format="float", example=90.00),
 *                     @OA\Property(property="skor", type="integer", example=90),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-21 10:35:00"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-21 10:35:00")
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Riwayat attempt tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Belum ada riwayat attempt untuk Form 4C ini."),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=500,
 *         description="Server error"
 *     )
 * )
 */

 class getRiwayatAttempt4c {}