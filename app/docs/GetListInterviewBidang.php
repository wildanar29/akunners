<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/interview/bidang",
 *     tags={"FORM 5"},
 *     summary="Ambil Jadwal Interview Berdasarkan Bidang",
 *     description="Endpoint ini digunakan oleh pengguna dengan role 'Bidang' (role_id = 3) untuk melihat jadwal interview berdasarkan filter yang tersedia.",
 *     operationId="getJadwalInterviewByBidang",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent(
 *             @OA\Property(property="bidang_id", type="integer", example=1001, description="ID user bidang. Jika tidak dikirim, digunakan user login."),
 *             @OA\Property(property="date", type="string", format="date", example="2025-06-17", description="Tanggal interview"),
 *             @OA\Property(property="time", type="string", example="10:00", description="Waktu interview"),
 *             @OA\Property(property="place", type="string", example="Ruang A", description="Lokasi interview (opsional, akan dicari secara like)"),
 *             @OA\Property(property="asesor_id", type="integer", example=201, description="ID user asesor"),
 *             @OA\Property(property="status", type="string", example="Waiting", description="Status interview (Waiting, Accepted, Canceled, Rescheduled)")
 *         )
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
 *                     @OA\Property(property="interview_id", type="integer", example=10),
 *                     @OA\Property(property="user_id", type="integer", example=3001),
 *                     @OA\Property(property="date", type="string", example="2025-06-17"),
 *                     @OA\Property(property="time", type="string", example="10:00"),
 *                     @OA\Property(property="place", type="string", example="Ruang A"),
 *                     @OA\Property(property="form_1_id", type="integer", example=5),
 *                     @OA\Property(property="asesor_id", type="integer", example=201),
 *                     @OA\Property(property="status", type="string", example="Waiting")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Akses ditolak. Hanya bidang yang dapat mengakses.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="Akses ditolak. Hanya user dengan role_id = 3 (Bidang) yang dapat mengakses."),
 *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User tidak login",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="User tidak terautentikasi."),
 *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
 *         )
 *     )
 * )
 */


 class GetListInterviewBidang {}