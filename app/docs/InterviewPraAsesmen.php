<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/konsultasi/pra-asesmen",
 *     tags={"FORM 5 (PRA ASESMEN)"},
 *     summary="Pengajuan Konsultasi Pra Asesmen oleh Asesi",
 *     description="Endpoint ini digunakan oleh Asesi untuk mengajukan konsultasi pra asesmen berdasarkan Form 1 yang telah diisi.",
 *     operationId="pengajuanKonsultasiPraAsesmen",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"date", "time", "place", "form_1_id"},
 *             @OA\Property(property="date", type="string", format="date", example="2025-06-17"),
 *             @OA\Property(property="time", type="string", example="14:00"),
 *             @OA\Property(property="place", type="string", example="Ruang Konsultasi 1"),
 *             @OA\Property(property="form_1_id", type="integer", example=123)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Pengajuan konsultasi berhasil disimpan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=201),
 *             @OA\Property(property="message", type="string", example="Pengajuan konsultasi pra asesmen berhasil disimpan."),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Pengguna belum login atau token tidak valid",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="Pengguna belum login atau token tidak valid."),
 *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Akses ditolak. Hanya asesi yang dapat melakukan pengajuan konsultasi.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="Akses ditolak. Hanya asesi yang dapat melakukan pengajuan konsultasi."),
 *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Pengajuan untuk form ini sudah ada",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=409),
 *             @OA\Property(property="message", type="string", example="Pengajuan untuk form ini sudah ada. Anda tidak dapat mengajukan dua kali.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data asesor tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="Data asesor dengan no_reg tersebut tidak ditemukan.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "date": {"The date field is required."},
 *                     "form_1_id": {"The form_1_id field is required."}
 *                 }
 *             )
 *         )
 *     )
 * )
 */

 class InterviewPraAsesmen {}