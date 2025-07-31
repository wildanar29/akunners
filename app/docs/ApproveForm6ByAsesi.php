<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form6/approve",
 *     tags={"FORM 6"},
 *     summary="Approve Form 6 oleh Asesi",
 *     description="API ini digunakan untuk memberikan persetujuan atas nilai yang sudah diisi oleh asesor. Persetujuan ini dilakukan oleh asesi.",
 *     operationId="approveForm6ByAsesi",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"form_6_id"},
 *             @OA\Property(property="form_6_id", type="integer", example=123, description="ID Form 6 yang akan disetujui")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Form6 berhasil di-approve oleh Asesi",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Form6 berhasil di-approve oleh Asesi"),
 *             @OA\Property(property="data", type="string", example="Submitted")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(property="errors", type="object")
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

 class ApproveForm6ByAsesi {}