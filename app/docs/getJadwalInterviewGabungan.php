<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/jadwal/interview",
 *     summary="Ambil Jadwal Interview Gabungan",
 *     description="API ini digunakan untuk mengambil jadwal interview yang dapat difilter berdasarkan parameter atau request yang dapat diisi sesuai dengan kebutuhan.",
 *     operationId="getJadwalInterviewGabungan",
 *     tags={"FORM 5"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="date",
 *         in="query",
 *         description="Filter berdasarkan tanggal interview (format: YYYY-MM-DD)",
 *         required=false,
 *         @OA\Schema(type="string", format="date", example="2025-08-01")
 *     ),
 *     @OA\Parameter(
 *         name="time",
 *         in="query",
 *         description="Filter berdasarkan waktu interview",
 *         required=false,
 *         @OA\Schema(type="string", example="09:00")
 *     ),
 *     @OA\Parameter(
 *         name="place",
 *         in="query",
 *         description="Filter berdasarkan lokasi interview",
 *         required=false,
 *         @OA\Schema(type="string", example="Ruang LSP")
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         description="Filter berdasarkan status jadwal (contoh: Waiting)",
 *         required=false,
 *         @OA\Schema(type="string", example="Waiting")
 *     ),
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         description="Filter berdasarkan ID program keahlian",
 *         required=false,
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Parameter(
 *         name="asesor_id",
 *         in="query",
 *         description="Filter berdasarkan user_id asesor",
 *         required=false,
 *         @OA\Schema(type="integer", example=12)
 *     ),
 *     @OA\Parameter(
 *         name="no_reg",
 *         in="query",
 *         description="Filter berdasarkan nomor registrasi asesor",
 *         required=false,
 *         @OA\Schema(type="string", example="ASESOR-001")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data jadwal berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Data jadwal interview berhasil diambil."),
 *             @OA\Property(property="data", type="array", @OA\Items(
 *                 @OA\Property(property="interview_id", type="integer", example=5),
 *                 @OA\Property(property="form_1_id", type="integer", example=10),
 *                 @OA\Property(property="asesor_id", type="integer", example=12),
 *                 @OA\Property(property="date", type="string", format="date", example="2025-08-01"),
 *                 @OA\Property(property="time", type="string", example="13:00"),
 *                 @OA\Property(property="place", type="string", example="Ruang LSP"),
 *                 @OA\Property(property="status", type="string", example="Waiting")
 *             ))
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User belum login",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="User belum login."),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Akses ditolak karena bukan bidang atau asesor",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="Akses ditolak. Hanya Bidang atau Asesor yang dapat mengakses."),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Asesor tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="Asesor tidak ditemukan berdasarkan asesor_id."),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     )
 * )
 */

 class getJadwalInterviewGabungan {}