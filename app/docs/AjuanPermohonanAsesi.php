<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/ajuan-asesi",
 *     summary="Pengajuan Form 1 oleh Asesi",
 *     description="Endpoint ini digunakan oleh Asesi untuk mengajukan permohonan asesmen berdasarkan level kompetensi (PK).",
 *     tags={"FORM 1"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pk_id"},
 *             @OA\Property(property="pk_id", type="integer", example=1, description="ID dari level kompetensi PK yang diajukan")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Data berhasil diperbarui (jika sebelumnya sudah ada pengajuan)",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Data successfully updated in Form_1."),
 *             @OA\Property(property="form_1_id", type="integer", example=5),
 *             @OA\Property(property="updated_data", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="Pengajuan baru berhasil dibuat",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Data successfully inserted into Form_1."),
 *             @OA\Property(property="form_1", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=201)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=400,
 *         description="Dokumen wajib tidak lengkap (Ijazah, STR, SIP)",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Submission failed. Missing: Ijazah, STR"),
 *             @OA\Property(property="missing_documents", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="status_code", type="integer", example=400)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized atau token tidak valid",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Unauthorized. Invalid token or user not found."),
 *             @OA\Property(property="status_code", type="integer", example=401)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=403,
 *         description="PK sebelumnya belum selesai",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="You must complete PK 1 before submitting PK 2."),
 *             @OA\Property(property="status_code", type="integer", example=403)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=409,
 *         description="Pengajuan untuk PK ini sudah pernah dilakukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="message", type="string", example="You have already submitted a request for the selected PK."),
 *             @OA\Property(property="status_code", type="integer", example=409)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal (pk_id kosong atau tidak valid)",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="The pk id field is required."),
 *             @OA\Property(property="errors", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=422)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan sistem saat proses pengajuan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="An unexpected error occurred while processing data."),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[...]", description="Detail pesan error"),
 *             @OA\Property(property="status_code", type="integer", example=500)
 *         )
 *     )
 * )
 */

 class AjuanPermohonanAsesi {}