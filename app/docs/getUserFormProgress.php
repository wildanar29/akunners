<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/progress/{userId}",
 *     summary="Ambil status progres semua form berdasarkan user_id",
 *     tags={"Progress"},
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         description="ID dari user",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data progres form",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Berhasil mengambil data progres form."),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="user_id", type="integer", example=53),
 *                 @OA\Property(
 *                     property="form_statuses",
 *                     type="object",
 *                     @OA\Property(property="form_1", type="string", example="Approved"),
 *                     @OA\Property(property="form_2", type="string", example="Waiting"),
 *                     @OA\Property(property="form_3", type="string", example="Terkunci")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data progress tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data progress tidak ditemukan untuk user ini."),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat mengambil data progres."),
 *             @OA\Property(property="data", type="string", nullable=true, example=null),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[HY000] ...")
 *         )
 *     )
 * )
 */

 class getUserFormProgress {}