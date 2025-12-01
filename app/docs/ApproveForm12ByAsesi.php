<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form12/{form12Id}/approve-asesi",
 *     tags={"FORM 12 (REKAP NILAI)"},
 *     summary="Approve atau Reject Form 12 oleh Asesi",
 *     description="Endpoint ini digunakan agar Asesi dapat melakukan APPROVE atau REJECT terhadap Form 12. Gunakan body JSON: {action: approve|reject}. Jika approve → status menjadi Completed & Form 9 diinisialisasi. Jika reject → status menjadi Rejected.",
 *
 *     @OA\Parameter(
 *         name="form12Id",
 *         in="path",
 *         required=true,
 *         description="ID Form 12 yang diproses Asesi",
 *         @OA\Schema(type="integer", example=9)
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         description="Aksi approve atau reject yang dipilih Asesi",
 *         @OA\JsonContent(
 *             required={"action"},
 *             @OA\Property(
 *                 property="action",
 *                 type="string",
 *                 enum={"approve", "reject"},
 *                 example="approve",
 *                 description="Action wajib: approve atau reject"
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Proses berhasil",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Form 12 berhasil di-approve oleh Asesi")
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
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={"action": {"The action field is required or invalid."}}
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menyimpan data")
 *         )
 *     )
 * )
 */
class ApproveForm12ByAsesi {}
