<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/form4c/{form4cId}/approve-asesi",
 *     tags={"FORM 4 (ASESMEN)"},
 *     summary="Approve Form 4C oleh Asesi",
 *     description="API ini digunakan untuk menyetujui Form 4D yang dilakukan oleh asesi",
 *     @OA\Parameter(
 *         name="form4cId",
 *         in="path",
 *         required=true,
 *         description="ID Form 4C yang akan di-approve",
 *         @OA\Schema(type="integer", example=15)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Form 4C berhasil di-approve oleh Asesi",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Form 4C berhasil di-approve oleh Asesi"),
 *             @OA\Property(property="data", type="object", example={})
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data Form 4C tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data Form 4c tidak ditemukan.")
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
 *         description="Terjadi kesalahan server saat menyimpan data",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menyimpan data: ...")
 *         )
 *     )
 * )
 */


 class ApproveForm4cByAsesi {}