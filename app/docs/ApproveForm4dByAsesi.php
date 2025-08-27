<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form4d/{form4dId}/approve-asesi",
 *     tags={"FORM 4 (ASESMEN)"},
 *     summary="Approve Form 4D oleh Asesi",
 *     description="API ini digunakan untuk menyetujui form 4D yang sudah diisi oleh asesor dan akan disetujui oleh Asesi",
 *     @OA\Parameter(
 *         name="form4dId",
 *         in="path",
 *         required=true,
 *         description="ID Form 4D yang akan di-approve",
 *         @OA\Schema(type="integer", example=88)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Form 4D berhasil di-approve oleh Asesi",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Form 4D berhasil di-approve oleh Asesi"),
 *             @OA\Property(property="data", type="object", example={})
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data Form 4D tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data Form 4d tidak ditemukan.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={"form_4d_id": {"The form_4d_id field is required."}}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat menyimpan data",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menyimpan data: ...")
 *         )
 *     )
 * )
 */

 class ApproveForm4dByAsesi {}