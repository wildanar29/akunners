<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/notification",
 *     summary="Mengambil daftar notifikasi dengan pagination",
 *     description="Endpoint ini mengembalikan daftar notifikasi berdasarkan user yang sedang login. 
 * 
 * Dapat difilter berdasarkan status `is_read` (sudah dibaca/belum), dan mendukung pagination menggunakan parameter `page` serta `per_page`.
 * 
 * **Contoh penggunaan:**  
 * `GET /notification?is_read=0&page=2&per_page=5`  
 * 
 * Header yang diperlukan:  
 * `Authorization: Bearer {token}`",
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
 *                     description="Daftar notifikasi pada halaman saat ini.",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=12),
 *                         @OA\Property(property="user_id", type="integer", example=101),
 *                         @OA\Property(property="title", type="string", example="Transaksi baru diterima"),
 *                         @OA\Property(property="description", type="string", example="Transaksi pembelian telah berhasil diproses."),
 *                         @OA\Property(property="is_read", type="boolean", example=false),
 *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-05T09:30:00Z"),
 *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-05T10:00:00Z")
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="pagination",
 *                     type="object",
 *                     description="Informasi pagination dari hasil query.",
 *                     @OA\Property(property="current_page", type="integer", example=2),
 *                     @OA\Property(property="last_page", type="integer", example=5),
 *                     @OA\Property(property="per_page", type="integer", example=5),
 *                     @OA\Property(property="total", type="integer", example=22),
 *                     @OA\Property(property="has_more_pages", type="boolean", example=true)
 *                 )
 *             )
 *         ),
 *         @OA\Example(
 *             example="successExample",
 *             summary="Contoh response sukses",
 *             value={
 *                 "status": "OK",
 *                 "errorCode": "",
 *                 "message": "Notifikasi berhasil diambil.",
 *                 "errorMessages": "",
 *                 "data": {
 *                     "notifications": {
 *                         {
 *                             "id": 12,
 *                             "user_id": 101,
 *                             "title": "Transaksi baru diterima",
 *                             "description": "Transaksi pembelian telah berhasil diproses.",
 *                             "is_read": false,
 *                             "created_at": "2025-11-05T09:30:00Z",
 *                             "updated_at": "2025-11-05T10:00:00Z"
 *                         },
 *                         {
 *                             "id": 11,
 *                             "user_id": 101,
 *                             "title": "Saldo telah diperbarui",
 *                             "description": "Saldo akun Anda bertambah sebesar Rp50.000.",
 *                             "is_read": true,
 *                             "created_at": "2025-11-04T14:10:00Z",
 *                             "updated_at": "2025-11-04T15:00:00Z"
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
 *             example="unauthorizedExample",
 *             summary="Contoh response unauthorized",
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
