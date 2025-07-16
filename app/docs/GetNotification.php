<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/notification",
 *     summary="Mengambil daftar notifikasi untuk user yang login",
 *     description="Endpoint ini mengembalikan daftar notifikasi berdasarkan user yang sedang login. Opsional bisa memfilter notifikasi berdasarkan status `is_read`.",
 *     operationId="getNotifications",
 *     tags={"Notifikasi"},
 *     security={{"bearerAuth": {}}},
 *
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="is_read",
 *                 type="boolean",
 *                 description="Filter berdasarkan status baca (true atau false)",
 *                 example=true
 *             )
 *         )
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
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="user_id", type="integer", example=123),
 *                         @OA\Property(property="title", type="string", example="Judul Notifikasi"),
 *                         @OA\Property(property="description", type="string", example="Isi notifikasi"),
 *                         @OA\Property(property="is_read", type="boolean", example=false),
 *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-15T07:30:00Z"),
 *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-15T07:45:00Z")
 *                     )
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