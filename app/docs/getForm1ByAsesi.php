<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/get-form1-byasesi",
 *     summary="Ambil daftar form_1 berdasarkan user_id asesi",
 *     description="Endpoint ini digunakan oleh asesi untuk mengambil daftar form_1 miliknya berdasarkan user_id dan status tertentu (default: Waiting).",
 *     operationId="getForm1ByAsesi",
 *     tags={"FORM 1"},
 *     security={{"bearerAuth": {}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id"},
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 example=101,
 *                 description="ID user asesi"
 *             ),
 *             @OA\Property(
 *                 property="status",
 *                 type="string",
 *                 example="Waiting",
 *                 description="Filter berdasarkan status (optional, default: Waiting)"
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Data form_1 berhasil ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="string", example="OK"),
 *             @OA\Property(property="message", type="string", example="Data form_1 berhasil diambil berdasarkan user_id asesi."),
 *             @OA\Property(property="user_id", type="integer", example=101),
 *             @OA\Property(property="status_filter", type="string", example="Waiting"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=400,
 *         description="Parameter user_id tidak dikirim",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="string", example="ERR"),
 *             @OA\Property(property="message", type="string", example="Parameter user_id wajib diisi.")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="User tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="string", example="ERR"),
 *             @OA\Property(property="message", type="string", example="User tidak ditemukan atau bukan asesi.")
 *         )
 *     )
 * )
 */

 class GetForm1ByAsesi {}