<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/ajuan-asesi",
 *     tags={"FORM 1 (PENGAJUAN ASESMEN)"},
 *     summary="Pengajuan Asesmen (Form 1)",
 *     description="API ini digunakan oleh asesi untuk melakukan pengajuan asesmen atau Form 1. Pengguna harus memiliki token otentikasi dan melengkapi dokumen seperti ijazah, STR, dan SIP sebelum dapat mengajukan.",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pk_id"},
 *             @OA\Property(property="pk_id", type="integer", example=2, description="ID paket kompetensi yang ingin diajukan")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Pengajuan berhasil (update data)",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Data successfully updated in Form_1."),
 *             @OA\Property(property="form_1_id", type="integer", example=12),
 *             @OA\Property(property="updated_data", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Pengajuan berhasil (insert data baru)",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Data successfully inserted into Form_1."),
 *             @OA\Property(property="form_1", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=201)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Dokumen tidak lengkap",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Submission failed. Missing: Ijazah, STR"),
 *             @OA\Property(property="missing_documents", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="status_code", type="integer", example=400)
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Unauthorized. Invalid token or user not found."),
 *             @OA\Property(property="status_code", type="integer", example=401)
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="PK sebelumnya belum diselesaikan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="You must complete PK 1 before submitting PK 2."),
 *             @OA\Property(property="status_code", type="integer", example=403)
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="PK sudah pernah diajukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="message", type="string", example="You have already submitted a request for the selected PK."),
 *             @OA\Property(property="status_code", type="integer", example=409)
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="The pk id field is required."),
 *             @OA\Property(property="errors", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=422)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan internal server",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="An unexpected error occurred while processing data."),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[HY000] ..."),
 *             @OA\Property(property="status_code", type="integer", example=500)
 *         )
 *     )
 * )
 */

 class AjuanPermohonanAsesi {}