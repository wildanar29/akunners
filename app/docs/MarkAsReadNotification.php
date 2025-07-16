<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/notification/read",
 *     summary="Menandai notifikasi sebagai telah dibaca",
 *     description="Menandai notifikasi sebagai sudah dibaca (is_read = true) berdasarkan notification_id.",
 *     operationId="markAsRead",
 *     tags={"Notifikasi"},
 *     security={{"bearerAuth": {}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"notification_id"},
 *             @OA\Property(
 *                 property="notification_id",
 *                 type="integer",
 *                 description="ID notifikasi yang akan ditandai sebagai dibaca",
 *                 example=5
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Notifikasi berhasil ditandai sebagai dibaca",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="OK"),
 *             @OA\Property(property="errorCode", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Notification marked as read successfully."),
 *             @OA\Property(property="errorMessages", type="string", example=""),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="notification_id", type="integer", example=5),
 *                 @OA\Property(property="is_read", type="boolean", example=true)
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=400,
 *         description="Request tidak valid, notification_id kosong",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="errorCode", type="string", example="400"),
 *             @OA\Property(property="message", type="string", example="Notification ID is required"),
 *             @OA\Property(property="errorMessages", type="string", example="The field notification_id is missing."),
 *             @OA\Property(property="data", type="string", example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Notifikasi tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="errorCode", type="string", example="404"),
 *             @OA\Property(property="message", type="string", example="Notification not found"),
 *             @OA\Property(property="errorMessages", type="string", example="The notification with the given ID does not exist."),
 *             @OA\Property(property="data", type="string", example=null)
 *         )
 *     )
 * )
 */

 class MarkAsReadNotification {}