<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/get-kompetensi-pk",
 *     tags={"MASTER"},
 *     summary="Ambil data kompetensi PK yang aktif",
 *     description="Mengambil daftar level kompetensi PK yang aktif dari tabel `kompetensi_pk`.",
 *     operationId="getKompetensiPk",
 *     @OA\Response(
 *         response=200,
 *         description="Data kompetensi PK berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="message", type="string", example="Data kompetensi PK berhasil diambil."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="pk_id", type="integer", example=1),
 *                     @OA\Property(property="nama_level", type="string", example="Level 1")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERR"),
 *             @OA\Property(property="message", type="string", example="Data kompetensi PK tidak ditemukan."),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERR"),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan: ..."),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 */


 class getKompetensiPk {}