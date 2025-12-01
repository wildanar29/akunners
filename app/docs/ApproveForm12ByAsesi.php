<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form12/{form12Id}/approve-asesi",
 *     tags={"FORM 12 (REKAP NILAI)"},
 *     summary="Approve atau Reject Form 12 oleh Asesi",
 *     description="
 * Endpoint ini digunakan agar Asesi dapat melakukan APPROVE atau REJECT terhadap Form 12.
 * 
 * Gunakan body JSON:
 * {
 *     \"action\": \"approve\" atau \"reject\"
 * }
 *
 * - Jika action = approve:
 *      → Status Form 12 akan menjadi 'Completed'
 *      → Sistem otomatis menginisialisasi Form 9 bila belum ada
 *
 * - Jika action = reject:
 *      → Status Form 12 akan menjadi 'Rejected'
 *      → Tidak membuat Form 9
 *      → Asesor menerima notifikasi penolakan
 * ",
 *
 *     @OA\Parameter(
 *         name="form12Id",
 *         in="path",
 *         required=true,
 *         description="ID Form 12 yang akan diproses Asesi",
 *         @OA\Schema(type="integer", example=9)
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         description="Aksi yang dipilih Asesi (approve atau reject)",
 *         @OA\JsonContent(
 *             required={"action"},
 *             @OA\Property(
 *                 property="action",
 *                 type="string",
 *                 enum={"approve", "reject"},
 *                 example="approve",
 *                 description="Jenis aksi yang dilakukan Asesi. Wajib: approve atau reject."
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Proses approve / reject berhasil",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Form 12 berhasil di-approve oleh Asesi"
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Form 12 tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data Form 12 tidak ditemukan.")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal (form_12_id atau action tidak valid)",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "action": {"The action field is required or invalid (must be approve/reject)."}
 *                 }
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server saat memproses form 12",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Terjadi kesalahan saat menyimpan data: unexpected error"
 *             )
 *         )
 *     )
 * )
 */
class ApproveForm12ByAsesi {}
