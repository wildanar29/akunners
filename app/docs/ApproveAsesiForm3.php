<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/approve-form3-asesi",
 *     summary="Approve Form 3 oleh Asesi",
 *     description="API ini digunakan untuk approve Form 3 oleh asesi (melakukan update karena Form 3 sebelumnya sudah diinisialisasi pada saat Form 2 selesai).",
 *     tags={"FORM 3 (RENCANA ASESMEN)"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=201,
 *         description="Form 3 berhasil disimpan atau diperbarui.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="OK"),
 *             @OA\Property(property="message", type="string", example="Form3 berhasil disimpan atau diperbarui."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="form_3_id", type="integer", example=12),
 *                 @OA\Property(property="user_id", type="integer", example=5),
 *                 @OA\Property(property="asesi_name", type="string", example="Budi Santoso"),
 *                 @OA\Property(property="asesi_date", type="string", format="date-time", example="2025-07-23T10:23:45"),
 *                 @OA\Property(property="status", type="string", example="Waiting")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User belum login.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="User belum login."),
 *             @OA\Property(property="data", type="string", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="User tidak memiliki role Asesi.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Anda tidak memiliki izin untuk mengisi Form3."),
 *             @OA\Property(property="data", type="string", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Form 1 belum tersedia atau progres tidak ditemukan.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Form 1 belum tersedia untuk user ini."),
 *             @OA\Property(property="data", type="string", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Form 3 sudah pernah dibuat sebelumnya.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Form 3 sudah pernah dibuat untuk progres ini."),
 *             @OA\Property(property="data", type="string", nullable=true)
 *         )
 *     )
 * )
 */

 class ApproveAsesiForm3 {}