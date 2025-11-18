<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/sertifikat/get/{pk_id}",
 *     summary="Ambil detail sertifikat berdasarkan pk_id",
 *     description="Mengambil detail sertifikat berdasarkan pk_id dan user yang sedang login",
 *     tags={"HASIL ASSESSMENT"},
 *
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="path",
 *         required=true,
 *         description="ID PK yang digunakan untuk filter sertifikat",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Detail sertifikat berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Detail sertifikat berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=16),
 *                 @OA\Property(property="form_1_id", type="integer", example=89),
 *                 @OA\Property(property="pk_id", type="integer", example=1),
 *                 @OA\Property(property="nomor_urut", type="integer", example=1),
 *                 @OA\Property(property="nomor_surat", type="string", example="001/ASSKOM-RSI/XI/2025"),
 *                 @OA\Property(property="nama", type="string", example="WILDAN AWALUDIN"),
 *                 @OA\Property(property="gelar", type="string", example="Kompetensi Perawat Klinis 1 (PK 1)"),
 *                 @OA\Property(property="status", type="string", example="BELUM KOMPETEN"),
 *                 @OA\Property(property="tanggal_mulai", type="string", format="date-time", example="2025-09-04T00:00:00.000000Z"),
 *                 @OA\Property(property="tanggal_selesai", type="string", format="date-time", example="2025-11-04T00:00:00.000000Z"),
 *                 @OA\Property(property="preview_url", type="string", example="http://localhost:8000/sertifikat/view/89")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Tidak ada sertifikat untuk pk_id ini",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Tidak ada sertifikat untuk pk_id ini")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={"pk_id": {"The pk_id field is required."}}
 *             )
 *         )
 *     )
 * )
 */

 class getSertifikatByPkId {}