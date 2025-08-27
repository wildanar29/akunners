<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form7/{form7Id}/approve-asesi",
 *     tags={"FORM 7 (PENGUMPULAN BUKTI)"},
 *     summary="Approve Form 7 oleh Asesi",
 *     description="API ini digunakan untuk melakukan approve oleh asesi untuk Form 7",
 *     @OA\Parameter(
 *         name="form7Id",
 *         in="path",
 *         required=true,
 *         description="ID Form 7 yang akan di-approve oleh asesi",
 *         @OA\Schema(type="integer", example=77)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil melakukan approve Form 7 oleh asesi",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Form form_7 berhasil di-approve oleh Asesi"),
 *             @OA\Property(property="data", type="string", example="Submitted")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Form 7 tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data Form 7 tidak ditemukan.")
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
 *                 example={"form_7_id": {"The selected form_7_id is invalid."}}
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

 class ApproveForm7ByAsesi {}