<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/form5/langkah-kegiatan",
 *     tags={"FORM 5 (PRA ASESMEN)"},
 *     summary="Ambil data langkah dan kegiatan Form 5",
 *     description="Mengambil daftar langkah dan kegiatan dalam Form 5",
 *     @OA\Response(
 *         response=200,
 *         description="Data berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Data langkah dan kegiatan berhasil diambil."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="nomor_langkah", type="integer", example=1),
 *                     @OA\Property(property="judul_langkah", type="string", example="Melakukan persiapan asesmen"),
 *                     @OA\Property(property="form_parent", type="string", example="form_5"),
 *                     @OA\Property(property="catatan", type="string", example="Langkah penting sebelum asesmen"),
 *                     @OA\Property(
 *                         property="kegiatans",
 *                         type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="langkah_id", type="integer", example=1),
 *                             @OA\Property(property="deskripsi", type="string", example="Menyiapkan alat dan bahan asesmen"),
 *                             @OA\Property(property="is_tercapai", type="boolean", example=true),
 *                             @OA\Property(property="catatan", type="string", example="Sesuai SOP")
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Gagal mengambil data",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat mengambil data."),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[42S02]: Table not found...")
 *         )
 *     )
 * )
 */

 class SoalForm5 {}