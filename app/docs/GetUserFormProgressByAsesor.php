<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/progress/asesi/{asesorId}",
 *     tags={"PROGRESS"},
 *     summary="Ambil daftar progres form dari semua asesi berdasarkan asesor",
 *     description="Mengembalikan daftar user (asesi) dan status form mereka berdasarkan ID asesor.",
 *     operationId="getAsesiProgressByAsesor",
 *     @OA\Parameter(
 *         name="asesorId",
 *         in="path",
 *         description="ID user asesor",
 *         required=true,
 *         @OA\Schema(type="integer", example=53)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data progres berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Berhasil mengambil data progres semua asesi untuk asesor ini."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="user_id", type="integer", example=101),
 *                     @OA\Property(property="user_name", type="string", example="Ahmad Fulan"),
 *                     @OA\Property(
 *                         property="form_statuses",
 *                         type="object",
 *                         @OA\Property(property="form_1", type="string", example="Selesai"),
 *                         @OA\Property(property="form_2", type="string", example="Terkunci"),
 *                         @OA\Property(property="form_3", type="string", example="Terkunci")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Tidak ditemukan data progres untuk asesor ini."),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat mengambil data progres."),
 *             @OA\Property(property="data", type="string", example=null),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[42S22]: Column not found...")
 *         )
 *     )
 * )
 */

 class GetUserFormProgressByAsesor {}