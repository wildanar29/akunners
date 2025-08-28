<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/tracks-by-form",
 *     tags={"TRACK PROGRES"},
 *     summary="Ambil data tracking progres",
 *     description="API ini digunakan untuk mengambil data tracking progres atau setiap update. 
 *         - `user_id` diisi berdasarkan `asesi_id`.
 *         - `form_id` diisi dengan ID form yang ingin dilihat progresnya.
 *         - `parent_form_id` diisi dengan `form_1_id`.",
 *     @OA\Parameter(
 *         name="form_id",
 *         in="query",
 *         required=true,
 *         description="ID form yang ingin dilihat progresnya",
 *         @OA\Schema(type="integer", example=87)
 *     ),
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         description="ID user (diisi berdasarkan asesi_id)",
 *         @OA\Schema(type="integer", example=68)
 *     ),
 *     @OA\Parameter(
 *         name="parent_form_id",
 *         in="query",
 *         required=true,
 *         description="ID parent form (diisi oleh form_1_id)",
 *         @OA\Schema(type="integer", example=89)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data tracks berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="OK"),
 *             @OA\Property(property="message", type="string", example="Data tracks berhasil diambil."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="progres_id", type="integer", example=10),
 *                     @OA\Property(property="activity", type="string", example="Form 9 Approved"),
 *                     @OA\Property(property="activity_time", type="string", format="date-time", example="2025-08-28 10:00:00")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Parameter form_id, user_id, dan parent_form_id wajib diisi"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data progres tidak ditemukan untuk kombinasi parameter tersebut"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat mengambil data tracks"
 *     )
 * )
 */

 class getTracksByFormId {}