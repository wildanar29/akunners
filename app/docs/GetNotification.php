<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/notification",
 *     summary="Mengambil daftar notifikasi dengan pagination",
 *     description="Endpoint ini mengembalikan daftar notifikasi berdasarkan user yang sedang login. Dapat difilter berdasarkan status `is_read` (sudah dibaca/belum) dan mendukung pagination.",
 *     operationId="getNotifications",
 *     tags={"Notifikasi"},
 *     security={{"bearerAuth": {}}},
 *
 *     @OA\Parameter(
 *         name="is_read",
 *         in="query",
 *         description="Filter notifikasi berdasarkan status baca. Gunakan 1 untuk sudah dibaca, 0 untuk belum.",
 *         required=false,
 *         @OA\Schema(type="boolean", example=0)
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Jumlah notifikasi per halaman (default: 10).",
 *         required=false,
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Nomor halaman untuk pagination.",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil daftar notifikasi",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="OK"),
 *             @OA\Property(property="errorCode", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Notifikasi berhasil diambil."),
 *             @OA\Property(property="errorMessages", type="string", example=""),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="notifications",
 *                     type="array",
 *                     description="Daftar notifikasi pada halaman saat ini.",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="user_id", type="integer", example=123),
 *                         @OA\Property(property="title", type="string", example="Judul Notifikasi"),
 *                         @OA\Property(property="description", type="string", example="Isi notifikasi"),
 *                         @OA\Property(property="is_read", type="boolean", example=false),
 *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-15T07:30:00Z"),
 *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-15T07:45:00Z")
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="pagination",
 *                     type="object",
 *                     description="Informasi pagination",
 *                     @OA\Property(property="current_page", type="integer", example=1),
 *                     @OA\Property(property="last_page", type="integer", example=5),
 *                     @OA\Property(property="per_page", type="integer", example=10),
 *                     @OA\Property(property="total", type="integer", example=45),
 *                     @OA\Property(property="has_more_pages", type="boolean", example=true)
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized, token tidak valid atau belum login",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="errorCode", type="string", example="401"),
 *             @OA\Property(property="message", type="string", example="Unauthorized. Anda harus login terlebih dahulu."),
 *             @OA\Property(property="errorMessages", type="string", example="Token tidak valid atau belum login."),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     )
 * )
 */
class GetNotification {}
