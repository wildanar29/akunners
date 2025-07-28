<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/konsultasi/pra-asesmen",
 *     summary="Pengajuan Konsultasi Pra Asesmen",
 *     description="API ini digunakan untuk melakukan pengajuan jadwal konsultasi pra asesmen yang dilakukan oleh asesi.",
 *     operationId="pengajuanKonsultasiPraAsesmen",
 *     tags={"Konsultasi"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"date","time","place","form_1_id","pk_id"},
 *             @OA\Property(property="date", type="string", format="date", example="2025-08-01"),
 *             @OA\Property(property="time", type="string", example="13:00"),
 *             @OA\Property(property="place", type="string", example="Ruang Konsultasi LSP"),
 *             @OA\Property(property="form_1_id", type="integer", example=10),
 *             @OA\Property(property="pk_id", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Pengajuan konsultasi berhasil disimpan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="201"),
 *             @OA\Property(property="message", type="string", example="Pengajuan konsultasi pra asesmen berhasil disimpan."),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Pengguna belum login atau token tidak valid",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="Pengguna belum login atau token tidak valid."),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Akses ditolak, bukan asesi",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="Akses ditolak. Hanya asesi yang dapat melakukan pengajuan konsultasi."),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Pengajuan duplikat",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=409),
 *             @OA\Property(property="message", type="string", example="Pengajuan untuk form ini sudah ada. Anda tidak dapat mengajukan dua kali.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server saat menyimpan pengajuan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menyimpan pengajuan konsultasi."),
 *             @OA\Property(property="error", type="string", example="SQLSTATE... error")
 *         )
 *     )
 * )
 */

 class pengajuanKonsultasiPraAsesmen {}