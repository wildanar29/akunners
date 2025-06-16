<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/ajuan-asesi",
 *     tags={"FORM 1"},
 *     summary="Ajukan permohonan asesi (Form_1)",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=false,
 *         description="Tidak membutuhkan body karena user diambil dari token.",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Data berhasil dimasukkan ke Form_1, Pk_Progress, dan Pk_Status",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Data successfully inserted into Form_1, Pk_Progress, and Pk_Status."),
 *             @OA\Property(property="form_1", type="object",
 *                 @OA\Property(property="form_1_id", type="integer", example=123),
 *                 @OA\Property(property="user_id", type="integer", example=53),
 *                 @OA\Property(property="asesi_name", type="string", example="John Doe"),
 *                 @OA\Property(property="asesi_date", type="string", format="date", example="2025-06-16"),
 *                 @OA\Property(property="ijazah_id", type="integer", example=10),
 *                 @OA\Property(property="str_id", type="integer", example=5),
 *                 @OA\Property(property="sip_id", type="integer", example=7),
 *                 @OA\Property(property="sertifikat_id", type="integer", nullable=true, example=53),
 *                 @OA\Property(property="status", type="string", example="Waiting"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-16T09:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-16T09:00:00Z")
 *             ),
 *             @OA\Property(property="pk_progress", type="object",
 *                 @OA\Property(property="form_1_status", type="string", example="Open")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=201)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Dokumen belum lengkap",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Submission failed. The following documents must have a valid file path: Ijazah, STR."),
 *             @OA\Property(property="missing_documents", type="array",
 *                 @OA\Items(type="string", example="Ijazah")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=400)
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Token tidak valid atau user tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Unauthorized. Invalid token or user not found."),
 *             @OA\Property(property="status_code", type="integer", example=401)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server saat memproses permohonan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="An unexpected error occurred while processing data."),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[HY000]: ..."),
 *             @OA\Property(property="status_code", type="integer", example=500)
 *         )
 *     )
 * )
 */
 class AjuanPermohonanAsesi {}