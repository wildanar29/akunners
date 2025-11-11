<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/notification/send-feedback-doc/{form_1_id}",
 *     tags={"Notifikasi"},
 *     summary="Mengirim notifikasi feedback dokumen kepada asesi",
 *     operationId="sendNotifyReject",
 *     description="Endpoint ini digunakan oleh asesor untuk mengirimkan notifikasi feedback kepada asesi terkait dokumen yang perlu diperbaiki atau diperbarui. 
 *     Pesan dikirim melalui layanan notifikasi internal sistem.",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="form_1_id",
 *         in="path",
 *         required=true,
 *         description="ID Form 1 yang terkait dengan feedback dokumen",
 *         @OA\Schema(type="integer", example=89)
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         description="Pesan feedback yang akan dikirim ke pengguna",
 *         @OA\JsonContent(
 *             required={"message"},
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 maxLength=1000,
 *                 example="masukkan ijazah yang terbaru ya",
 *                 description="Isi pesan feedback yang akan diterima oleh asesi"
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Notifikasi feedback dokumen berhasil dikirim",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Notifikasi feedback dokumen berhasil dikirim."),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="form_1_id", type="integer", example=89),
 *                 @OA\Property(property="asesi_id", type="integer", example=12),
 *                 @OA\Property(property="penerima", type="string", example="Sinta Marlina"),
 *                 @OA\Property(property="notifikasi", type="string", example="Terkirim")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=400,
 *         description="Parameter form_1_id tidak dikirim",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Parameter form_1_id wajib diisi.")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Data Form 1 atau user tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="User penerima notifikasi tidak ditemukan.")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal (pesan kosong atau tidak sesuai format)",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="validation_error"),
 *             @OA\Property(property="message", type="string", example="Pesan notifikasi wajib diisi dengan format teks."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={"message": {"The message field is required."}}
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat mengirim notifikasi",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat mengirim notifikasi feedback dokumen."),
 *             @OA\Property(property="error", type="string", example="Exception message here")
 *         )
 *     )
 * )
 */
class sendNotifyReject {}
