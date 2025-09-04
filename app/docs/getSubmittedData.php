<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/submitted-data",
 *     tags={"MENU"},
 *     summary="API ini digunakan untuk melihat pengajuan dari masing masing form",
 *     description="Mengambil data yang sudah berstatus Submitted berdasarkan pk_id, asesor_id, dan key form",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pk_id","asesor_id","key"},
 *             @OA\Property(property="pk_id", type="integer", example=123, description="ID PK"),
 *             @OA\Property(property="asesor_id", type="integer", example=456, description="ID Asesor"),
 *             @OA\Property(property="key", type="string", example="form_1", description="Jenis form, misalnya form_1 atau form_2")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data retrieved successfully atau tidak ada data",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Data retrieved successfully."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(type="object")
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

 class getSubmittedData {}