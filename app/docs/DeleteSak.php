<?php

namespace App\Docs;
/**
 * @OA\Delete(
 *     path="/sak/file/{nik}",
 *     summary="Menghapus file SAK berdasarkan NIK user",
 *     tags={"SAK"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="NIK user yang digunakan untuk mencari file SAK",
 *         @OA\Schema(type="string", example="3201123456789012")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File berhasil dihapus",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="File successfully deleted from the system."),
 *             @OA\Property(property="details", type="object",
 *                 @OA\Property(property="sak_id", type="integer", example=123),
 *                 @OA\Property(property="user_id", type="integer", example=456),
 *                 @OA\Property(property="file_path", type="string", example=null),
 *                 @OA\Property(property="storage_status", type="string", example="File deleted from storage."),
 *                 @OA\Property(property="database_status", type="string", example="File record has been deleted from the database.")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User atau file tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found. Please check the NIK and try again."),
 *             @OA\Property(property="details", type="object",
 *                 @OA\Property(property="nik", type="string", example="3201123456789012"),
 *                 @OA\Property(property="reason", type="string", example="No user associated with this NIK was found in the system.")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     )
 * )
 */


 class DeleteSak {}