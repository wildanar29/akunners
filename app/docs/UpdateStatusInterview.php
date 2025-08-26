<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/interview/update-status",
 *     tags={"FORM 5 (PRA ASESMEN)"},
 *     summary="Update Status Interview",
 *     description="API ini digunakan untuk menyetujui ajuan jadwal konsultasi pra asesmen sebelum mengerjakan form 5.",
 *     operationId="updateStatusInterview",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"interview_id", "action"},
 *             @OA\Property(property="interview_id", type="integer", example=101, description="ID jadwal interview"),
 *             @OA\Property(property="action", type="string", enum={"Approved", "Rejected", "Reschedule"}, example="Approved", description="Aksi untuk status interview"),
 *             @OA\Property(property="date", type="string", format="date", example="2025-08-01", description="Tanggal baru jika Reschedule"),
 *             @OA\Property(property="time", type="string", example="13:00", description="Waktu baru jika Reschedule"),
 *             @OA\Property(property="place", type="string", example="Ruang A", description="Tempat baru jika Reschedule"),
 *             @OA\Property(property="asesor_id", type="integer", example=5, description="ID asesor jika ingin override user login")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Status interview berhasil diperbarui",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="message", type="string", example="Status interview berhasil diperbarui."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="status", type="string", example="Approved"),
 *                 @OA\Property(property="date", type="string", format="date", example="2025-08-01"),
 *                 @OA\Property(property="time", type="string", example="13:00"),
 *                 @OA\Property(property="place", type="string", example="Ruang A"),
 *             ),
 *             @OA\Property(property="form5_exists", type="boolean", example=false),
 *             @OA\Property(property="form_5_id", type="integer", example=77)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="Parameter action wajib diisi."),
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Tidak terautentikasi",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="User tidak terautentikasi dan asesor_id tidak disediakan."),
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=403,
 *         description="Akses ditolak",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="Anda bukan pemilik jadwal interview ini."),
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Data interview tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="Data interview tidak ditemukan."),
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Gagal membuat Form5",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="FAILED"),
 *             @OA\Property(property="message", type="string", example="Form5 gagal dibuat."),
 *         )
 *     )
 * )
 */

 class UpdateStatusInterview {}