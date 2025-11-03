<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form8",
 *     tags={"FORM 8 (BANDING)"},
 *     summary="Ambil data Form Banding Asesmen berdasarkan filter (banding_id, asesor_id, atau asesi_id)",
 *     description="
 * Endpoint ini digunakan untuk mengambil **satu data Form Banding Asesmen** berdasarkan filter:
 * - `banding_id` (prioritas utama, jika diisi maka hasil difilter berdasarkan ID ini)
 * - `asesor_id` atau `asesi_id` (jika `banding_id` tidak diisi)
 * 
 * Hasil response akan mengembalikan **object tunggal**, bukan array.
 * 
 * Jika data tidak ditemukan, maka field `data` dikembalikan sebagai **object kosong (`{}`)**.
 *     ",
 * 
 *     @OA\Parameter(
 *         name="banding_id",
 *         in="query",
 *         required=false,
 *         description="Filter berdasarkan ID Form Banding Asesmen",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="asesor_id",
 *         in="query",
 *         required=false,
 *         description="Filter berdasarkan ID asesor",
 *         @OA\Schema(type="integer", example=123)
 *     ),
 *     @OA\Parameter(
 *         name="asesi_id",
 *         in="query",
 *         required=false,
 *         description="Filter berdasarkan ID asesi",
 *         @OA\Schema(type="integer", example=456)
 *     ),
 * 
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data Form Banding Asesmen",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="message", type="string", example="Data form banding berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 oneOf={
 *                     @OA\Schema(
 *                         description="Contoh ketika data ditemukan",
 *                         example={
 *                             "banding_id": 1,
 *                             "form_1_id": 10,
 *                             "asesi_id": 456,
 *                             "asesor_id": 123,
 *                             "tanggal_asesmen": "2025-11-03",
 *                             "alasan_banding": "Saya merasa hasil asesmen tidak sesuai dengan kompetensi saya.",
 *                             "persetujuan_asesi": true,
 *                             "persetujuan_asesor": false,
 *                             "created_at": "2025-11-03T10:15:32.000000Z",
 *                             "updated_at": "2025-11-03T10:15:32.000000Z"
 *                         }
 *                     ),
 *                     @OA\Schema(
 *                         description="Contoh ketika data tidak ditemukan",
 *                         example={}
 *                     )
 *                 }
 *             )
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal (parameter tidak sesuai)",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "asesor_id": {"The asesor id must be an integer."}
 *                 }
 *             )
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan pada server",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan pada server"),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[HY000]: General error ...")
 *         )
 *     )
 * )
 */
class getFormBandingByUser {}
