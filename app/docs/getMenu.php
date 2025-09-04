<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/menu",
 *     tags={"MENU"},
 *     summary="API ini digunakan untuk menampilkan list menu dalam menu inbox",
 *     description="Mengambil daftar menu berdasarkan pk_id dan asesor_id",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pk_id","asesor_id"},
 *             @OA\Property(property="pk_id", type="integer", example=123, description="ID PK"),
 *             @OA\Property(property="asesor_id", type="integer", example=456, description="ID Asesor")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Menu retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Menu retrieved successfully."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="pk_id", type="integer", example=123),
 *                     @OA\Property(property="asesor_id", type="integer", example=456),
 *                     @OA\Property(property="key", type="string", example="form_1"),
 *                     @OA\Property(property="menu_name", type="string", example="form 1")
 *                 )
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal."),
 *             @OA\Property(property="errors", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=422),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="An error occurred while retrieving data."),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[HY000]: ..."),
 *             @OA\Property(property="status_code", type="integer", example=500),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     )
 * )
 */

 class getMenu {}