<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/form4a/{form4aId}/approve-asesi",
 *     tags={"FORM 4 (ASESMEN)"},
 *     summary="Persetujuan Form 4A oleh Asesi",
 *     description="API ini digunakan untuk melakukan input persetujuan oleh asesi terhadap Form 4A yang sudah diisi oleh asesor sebelumnya.",
 *     @OA\Parameter(
 *         name="form4aId",
 *         in="path",
 *         required=true,
 *         description="ID Form 4A yang akan di-approve",
 *         @OA\Schema(type="integer", example=123)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Form 4A berhasil di-approve oleh Asesi",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Form 4A berhasil di-approve oleh Asesi"),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data Form 4A tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data Form 4a tidak ditemukan.")
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
 *         description="Terjadi kesalahan saat menyimpan data",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menyimpan data: ...")
 *         )
 *     )
 * )
 */

 class ApproveForm4aByAsesi {}