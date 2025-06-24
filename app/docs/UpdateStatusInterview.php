<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/interview/update-status",
 *     tags={"FORM 5"},
 *     summary="Update Status Interview",
 *     description="Endpoint ini digunakan oleh asesor untuk memperbarui status interview (Accepted, Canceled, Reschedule).",
 *     operationId="updateStatusInterview",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"interview_id", "action"},
 *             @OA\Property(property="interview_id", type="integer", example=12, description="ID dari jadwal interview"),
 *             @OA\Property(property="action", type="string", enum={"accepted", "canceled", "reschedule"}, example="reschedule", description="Tindakan yang diambil asesor"),
 *             @OA\Property(property="date", type="string", format="date", example="2025-06-20", description="Wajib diisi jika action = reschedule"),
 *             @OA\Property(property="time", type="string", example="10:00", description="Wajib diisi jika action = reschedule"),
 *             @OA\Property(property="place", type="string", example="Ruang Konsultasi A", description="Wajib diisi jika action = reschedule"),
 *             @OA\Property(property="asesor_id", type="integer", example=42, description="Opsional, jika tidak login")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Status interview berhasil diperbarui",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Status interview berhasil diperbarui."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="status", type="string", example="Rescheduled"),
 *                 @OA\Property(property="date", type="string", example="2025-06-20"),
 *                 @OA\Property(property="time", type="string", example="10:00"),
 *                 @OA\Property(property="place", type="string", example="Ruang Konsultasi A")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User tidak terautentikasi dan tidak ada asesor_id dikirim",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="User tidak terautentikasi dan asesor_id tidak disediakan."),
 *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Akses ditolak atau bukan pemilik jadwal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="Akses ditolak. Anda bukan pemilik jadwal interview ini."),
 *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data interview tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="Data interview tidak ditemukan."),
 *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi input gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "interview_id": {"The interview_id field is required."},
 *                     "action": {"The action field is required."}
 *                 }
 *             )
 *         )
 *     )
 * )
 */


 class UpdateStatusInterview {}