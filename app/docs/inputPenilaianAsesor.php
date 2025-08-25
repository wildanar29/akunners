<?php

namespace App\Docs;

/**
 * @OA\Put(
 *     path="/penilaian-asesor",
 *     summary="Input atau update penilaian asesor untuk Form 2",
 *     tags={"FORM 2 (ASESMEN MANDIRI)"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"form_2_id", "status"},
 *             @OA\Property(
 *                 property="form_2_id",
 *                 type="integer",
 *                 example=1,
 *                 description="ID Form 2 yang akan dinilai"
 *             ),
 *             @OA\Property(
 *                 property="status",
 *                 type="string",
 *                 enum={"Approved", "Cancel"},
 *                 example="Approved",
 *                 description="Status penilaian dari asesor. Bisa Approved atau Cancel."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Status updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Status updated successfully"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="form_2_id", type="integer", example=1),
 *                 @OA\Property(property="status", type="string", example="Approved"),
 *                 @OA\Property(property="asesor_date", type="string", format="date-time", example="2025-07-23T14:45:00")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="object",
 *                 @OA\Property(property="form_2_id", type="array", @OA\Items(type="string", example="The form_2_id field is required.")),
 *                 @OA\Property(property="status", type="array", @OA\Items(type="string", example="The selected status is invalid."))
 *             )
 *         )
 *     )
 * )
 */

 class inputPenilaianAsesor {}