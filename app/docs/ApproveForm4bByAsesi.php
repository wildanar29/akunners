<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/form4b/{form4bId}/approve-asesi",
 *     tags={"FORM 4 (ASESMEN)"},
 *     summary="Approve Form 4B oleh Asesi",
 *     description="API ini digunakan untuk menyetujui form 4B yang diisi oleh asesor dan disetujui oleh asesi.",
 *     @OA\Parameter(
 *         name="form4bId",
 *         in="path",
 *         required=true,
 *         description="ID Form 4B yang akan di-approve",
 *         @OA\Schema(type="integer", example=123)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Form 4B berhasil di-approve oleh Asesi",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Form 4B berhasil di-approve oleh Asesi"),
 *             @OA\Property(property="data", type="object", example={})
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data Form 4B tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data Form 4b tidak ditemukan.")
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
 *                 additionalProperties=@OA\Schema(type="array", @OA\Items(type="string"))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan pada server",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menyimpan data: {error_message}")
 *         )
 *     )
 * )
 */

 class ApproveForm4bByAsesi {}