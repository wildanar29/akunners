<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form12/{form12Id}/approve-asesi",
 *     tags={"FORM 12 (REKAP NILAI)"},
 *     summary="API ini digunakan untuk approve form 12 oleh asesi",
 *     description="Endpoint ini digunakan agar Asesi dapat melakukan approve terhadap Form 12 (Rekap Nilai). Jika status Form 12 masih 'InAssessment', maka status akan diperbarui menjadi 'Approved'. Selain itu, proses ini juga menginisialisasi Form 9 jika belum ada.",
 *     @OA\Parameter(
 *         name="form12Id",
 *         in="path",
 *         required=true,
 *         description="ID Form 12 yang akan di-approve oleh Asesi",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Form 12 berhasil di-approve oleh Asesi",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Form form_12 berhasil di-approve oleh Asesi"),
 *             @OA\Property(property="data", type="string", example="Approved")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Form 12 tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data Form 12 tidak ditemukan.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal (form_12_id tidak valid)",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={"form_12_id": {"The selected form 12 id is invalid."}}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server saat approve form 12",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menyimpan data: unexpected error")
 *         )
 *     )
 * )
 */


 class ApproveForm12ByAsesi {}