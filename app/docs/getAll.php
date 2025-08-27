<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form10/daftar-tilik",
 *     tags={"FORM 10 (DAFTAR TILIK)"},
 *     summary="Ambil Daftar Tilik",
 *     description="API ini digunakan untuk mengambil list daftar tilik",
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=false,
 *         description="Filter berdasarkan pk_id",
 *         @OA\Schema(type="integer", example=12)
 *     ),
 *     @OA\Parameter(
 *         name="form_number",
 *         in="query",
 *         required=false,
 *         description="Filter berdasarkan nomor form",
 *         @OA\Schema(type="string", example="F10-01")
 *     ),
 *     @OA\Parameter(
 *         name="sort_by",
 *         in="query",
 *         required=false,
 *         description="Kolom yang digunakan untuk mengurutkan data",
 *         @OA\Schema(type="string", example="form_number")
 *     ),
 *     @OA\Parameter(
 *         name="sort_order",
 *         in="query",
 *         required=false,
 *         description="Arah pengurutan data (asc/desc), default asc",
 *         @OA\Schema(type="string", enum={"asc", "desc"}, example="asc")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data daftar tilik",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="pk_id", type="integer", example=12),
 *                     @OA\Property(property="form_number", type="string", example="F10-01"),
 *                     @OA\Property(property="judul", type="string", example="Daftar Tilik Uji Kompetensi")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Gagal mengambil data daftar_tilik",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Gagal mengambil data daftar_tilik."),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[42S22]: Column not found...")
 *         )
 *     )
 * )
 */

 class getAll {}