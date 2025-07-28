<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/get-educations",
 *     summary="Ambil master data pendidikan",
 *     description="API ini dapat digunakan untuk mengambil master data pendidikan (dropdown) saat melakukan registrasi akun.",
 *     operationId="getEducations",
 *     tags={"MASTER"},
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data pendidikan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="message", type="string", example="Data pendidikan berhasil diambil."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="nama_pendidikan", type="string", example="S1"),
 *                     @OA\Property(property="kode", type="string", example="S1")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data pendidikan tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERR"),
 *             @OA\Property(property="message", type="string", example="Data pendidikan tidak ditemukan."),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan pada server",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERR"),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan: ..."),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     )
 * )
 */

 class getEducations {}