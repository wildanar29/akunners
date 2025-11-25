<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form10/daftar-tilik",
 *     tags={"FORM 10 (DAFTAR TILIK)"},
 *     summary="Ambil Daftar Tilik beserta status progres",
 *     description="Mengambil daftar tilik berdasarkan pk_id, form_number, sorting, dan juga menambahkan status progres per form menggunakan mapping form_number → form_type.",
 *
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=false,
 *         description="Filter berdasarkan pk_id. Wajib jika ingin mengambil status progres.",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Parameter(
 *         name="asesi_id",
 *         in="query",
 *         required=false,
 *         description="ID asesi untuk mengambil status progres.",
 *         @OA\Schema(type="integer", example=72)
 *     ),
 *
 *     @OA\Parameter(
 *         name="form_number",
 *         in="query",
 *         required=false,
 *         description="Filter berdasarkan nomor form (form_type).",
 *         @OA\Schema(type="string", example="form_10.001")
 *     ),
 *
 *     @OA\Parameter(
 *         name="sort_by",
 *         in="query",
 *         required=false,
 *         description="Kolom untuk mengurutkan data.",
 *         @OA\Schema(type="string", example="urutan")
 *     ),
 *
 *     @OA\Parameter(
 *         name="sort_order",
 *         in="query",
 *         required=false,
 *         description="Arah pengurutan (asc/desc). Default asc.",
 *         @OA\Schema(type="string", enum={"asc", "desc"}, example="asc")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil daftar tilik beserta status",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="pk_id", type="integer", example=1),
 *                     @OA\Property(property="form_number", type="string", example="form_10.001"),
 *                     @OA\Property(property="description", type="string", example="PELAKSANAAN ASESMEN KEPERAWATAN"),
 *                     @OA\Property(property="urutan", type="integer", example=1),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-08T04:38:26.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-12T00:48:30.000000Z"),
 *                     
 *                     @OA\Property(
 *                         property="status",
 *                         type="string",
 *                         nullable=true,
 *                         example="Submitted",
 *                         description="Status progres form berdasarkan form_number → form_type mapping. Null jika belum ada progres."
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Gagal mengambil data daftar tilik",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Gagal mengambil data daftar_tilik."),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[42S22]: Column not found...")
 *         )
 *     )
 * )
 */
class getAll {}
