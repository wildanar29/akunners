<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/jadwal/interview/asesor",
 *     tags={"FORM 5"},
 *     summary="Ambil Jadwal Interview Asesor",
 *     description="Endpoint ini digunakan untuk mengambil daftar jadwal interview berdasarkan asesor_id, no_reg, atau user login (jika tidak ada parameter).",
 *     operationId="getJadwalInterviewByAsesor",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="asesor_id",
 *         in="query",
 *         description="User ID dari asesor",
 *         required=false,
 *         @OA\Schema(type="integer", example=42)
 *     ),
 *     @OA\Parameter(
 *         name="no_reg",
 *         in="query",
 *         description="Nomor registrasi asesor",
 *         required=false,
 *         @OA\Schema(type="string", example="REG-00123")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data jadwal interview berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Data jadwal interview berhasil diambil."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="asesor_id", type="integer", example=42),
 *                     @OA\Property(property="date", type="string", format="date", example="2025-06-20"),
 *                     @OA\Property(property="time", type="string", example="09:00"),
 *                     @OA\Property(property="place", type="string", example="Ruang Interview 3"),
 *                     @OA\Property(property="form_1_id", type="integer", example=101),
 *                     @OA\Property(property="status", type="string", example="Scheduled")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User tidak terautentikasi",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="User tidak terautentikasi."),
 *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Akses ditolak. Hanya asesor yang dapat melihat jadwal ini.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="Akses ditolak. Hanya asesor yang dapat melihat jadwal ini."),
 *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Asesor tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="Asesor tidak ditemukan berdasarkan no_reg."),
 *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
 *         )
 *     )
 * )
 */

 class GetJadwalInterviewAsesor {}