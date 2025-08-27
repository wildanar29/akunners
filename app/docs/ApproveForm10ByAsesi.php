<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form10/{form10Id}/approve-asesi",
 *     tags={"FORM 10 (DAFTAR TILIK)"},
 *     summary="Approve Form 10 oleh Asesi",
 *     description="API ini digunakan untuk menyetujui Form 10 atau daftar tilik yang diisi oleh asesor dan disetujui oleh asesi",
 *     @OA\Parameter(
 *         name="form10Id",
 *         in="path",
 *         required=true,
 *         description="ID Form 10 yang akan di-approve",
 *         @OA\Schema(type="integer", example=7)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Form 10 berhasil di-approve",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Form form_10 berhasil di-approve oleh Asesi"),
 *             @OA\Property(property="data", type="string", example="Submitted")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Form 10 tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data Form 10 tidak ditemukan.")
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
 *                 example={"form_10_id": {"The selected form 10 id is invalid."}}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menyimpan data: SQLSTATE[42S22]: Column not found...")
 *         )
 *     )
 * )
 */


 class ApproveForm10ByAsesi {}