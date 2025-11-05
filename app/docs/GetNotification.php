<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/api/notifications",
 *     summary="Mengambil daftar notifikasi dengan pagination",
 *     description="Endpoint ini mengembalikan daftar notifikasi berdasarkan user yang sedang login. 
 * Dapat difilter berdasarkan status `is_read` (sudah dibaca/belum) dan mendukung pagination.  
 * 
 * **Contoh penggunaan:**  
 * `GET /api/notifications?is_read=0&page=2&per_page=5`
 * 
 * Endpoint ini membutuhkan header Authorization dengan Bearer Token yang valid.",
 *     operationId="getNotifications",
 *     tags={"Notifikasi"},
 *     security={{"bearerAuth": {}}},
 *
 *     @OA\Parameter(
 *         name="is_read",
 *         in="query",
 *         description="Filter notifikasi berdasarkan status baca. Gunakan 1 untuk sudah dibaca, 0 untuk belum dibaca.",
 *         required=false,
 *         @OA\Schema(type="boolean", example=0)
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Nomor halaman untuk pagination (default: 1).",
 *         required=false,
 *         @OA\Schema(type="integer", example=2)
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Jumlah notifikasi per halaman (default: 10).",
 *         required=false,
 *         @OA\Schema(type="integer", example=5)
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
 *                     description="Daftar notifikasi pada halaman saat ini",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="user_id", type="integer", example=123),
 *                         @OA\Property(property="title", type="string", example="Judul Notifikasi"),
 *                         @OA\Property(property="description", type="string", example="Isi notifikasi"),
 *                         @OA\Property(property="is_read", type="boolean", example=false),
 *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-05T07:30:00Z"),
 *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-05T08:00:00Z")
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="pagination",
 *                     type="object",
 *                     description="Informasi pagination",
 *                     @OA\Property(property="current_page", type="integer", example=2),
 *                     @OA\Property(property="last_page", type="integer", example=5),
 *                     @OA\Property(property="per_page", type="integer", example=5),
 *                     @OA\Property(property="total", type="integer", example=22),
 *                     @OA\Property(property="has_more_pages", type="boolean", example=true)
 *                 )
 *             )
 *         ),
 *         @OA\Example(
 *             example="success",
 *             summary="Contoh response sukses",
 *             value={
 *                 "status": "OK",
 *                 "errorCode": "",
 *                 "message": "Notifikasi berhasil diambil.",
 *                 "errorMessages": "",
 *                 "data": {
 *                     "notifications": {
 *                         {
 *                             "id": 6,
 *                             "user_id": 123,
 *                             "title": "Transaksi baru diterima",
 *                             "description": "Transaksi pembelian berhasil diproses.",
 *                             "is_read": false,
 *                             "created_at": "2025-11-05T13:00:00Z",
 *                             "updated_at": "2025-11-05T13:30:00Z"
 *                         }
 *                     },
 *                     "pagination": {
 *                         "current_page": 2,
 *                         "last_page": 5,
 *                         "per_page": 5,
 *                         "total": 22,
 *                         "has_more_pages": true
 *                     }
 *                 }
 *             }
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
 *         ),
 *         @OA\Example(
 *             example="unauthorized",
 *             summary="Contoh response gagal (Unauthorized)",
 *             value={
 *                 "status": "ERROR",
 *                 "errorCode": "401",
 *                 "message": "Unauthorized. Anda harus login terlebih dahulu.",
 *                 "errorMessages": "Token tidak valid atau belum login.",
 *                 "data": null
 *             }
 *         )
 *     )
 * )
 */
class GetNotification {}
