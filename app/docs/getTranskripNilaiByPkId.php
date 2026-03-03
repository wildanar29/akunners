<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/transkrip/get/{pk_id}",
 *     tags={"HASIL ASSESSMENT"},
 *     summary="Ambil detail transkrip nilai berdasarkan PK ID",
 *     description="Mengambil detail transkrip nilai milik user login berdasarkan pk_id. Transkrip hanya tersedia jika status asesmen sudah Completed.",
 *     operationId="getTranskripNilaiByPkId",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="path",
 *         required=true,
 *         description="ID PK (Kompetensi PK)",
 *         @OA\Schema(
 *             type="integer",
 *             example=1
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Detail transkrip nilai berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Detail transkrip nilai berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=10),
 *                 @OA\Property(property="form_1_id", type="integer", example=5),
 *                 @OA\Property(property="pk_id", type="integer", example=1),
 *                 @OA\Property(property="nomor_urut", type="integer", example=12),
 *                 @OA\Property(property="nomor_dokumen", type="string", example="012/TNP-RSI/IX/2025"),
 *                 @OA\Property(property="nama", type="string", example="Dr. Ahmad"),
 *                 @OA\Property(property="gelar", type="string", example="Sp.PD"),
 *                 @OA\Property(property="status", type="string", example="Active"),
 *                 @OA\Property(property="tanggal_mulai", type="string", format="date", example="2025-01-01"),
 *                 @OA\Property(property="tanggal_selesai", type="string", format="date", example="2025-12-31"),
 *                 @OA\Property(property="preview_url", type="string", example="https://domain.com/storage/transkrip/file.pdf")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=400,
 *         description="Transkrip belum tersedia karena asesmen belum selesai",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Transkrip nilai belum tersedia karena proses asesmen belum selesai"),
 *             @OA\Property(property="status_form_1", type="string", example="On Progress")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Tidak ada transkrip nilai untuk pk_id ini")
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
 *                 example={"pk_id": {"The selected pk id is invalid."}}
 *             )
 *         )
 *     )
 * )
 */

 class getTranskripNilaiByPkId {}