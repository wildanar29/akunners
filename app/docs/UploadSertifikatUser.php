<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/upload-sertifikat",
 *     summary="Upload file sertifikat",
 *     tags={"Sertifikat"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"path_file"},
 *                 @OA\Property(
 *                     property="path_file",
 *                     description="File sertifikat (pdf/jpg/jpeg/png, maks 2MB)",
 *                     type="file",
 *                     format="binary"
 *                 ),
 *                 @OA\Property(
 *                     property="nomor_sertifikat",
 *                     type="string",
 *                     description="Nomor sertifikat (opsional)"
 *                 ),
 *                 @OA\Property(
 *                     property="masa_berlaku_sertifikat",
 *                     type="string",
 *                     format="date",
 *                     description="Tanggal masa berlaku sertifikat (opsional)"
 *                 ),
 *                 @OA\Property(
 *                     property="valid",
 *                     type="boolean",
 *                     description="Apakah valid (opsional)"
 *                 ),
 *                 @OA\Property(
 *                     property="authentic",
 *                     type="boolean",
 *                     description="Apakah autentik (opsional)"
 *                 ),
 *                 @OA\Property(
 *                     property="current",
 *                     type="boolean",
 *                     description="Apakah current (opsional)"
 *                 ),
 *                 @OA\Property(
 *                     property="sufficient",
 *                     type="boolean",
 *                     description="Apakah sufficient (opsional)"
 *                 ),
 *                 @OA\Property(
 *                     property="ket",
 *                     type="string",
 *                     maxLength=255,
 *                     description="Keterangan tambahan (opsional)"
 *                 ),
 *                 @OA\Property(
 *                     property="type_sertifikat",
 *                     type="string",
 *                     enum={"NIRA", "SPK"},
 *                     description="Jenis sertifikat (opsional)"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File uploaded successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="File uploaded successfully."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="sertifikat_id", type="integer"),
 *                 @OA\Property(property="user_id", type="integer"),
 *                 @OA\Property(property="path_file", type="string", example="/storage/Sertifikat/123456_file.pdf"),
 *                 @OA\Property(property="valid", type="boolean"),
 *                 @OA\Property(property="authentic", type="boolean"),
 *                 @OA\Property(property="current", type="boolean"),
 *                 @OA\Property(property="sufficient", type="boolean"),
 *                 @OA\Property(property="ket", type="string"),
 *                 @OA\Property(property="nomor_sertifikat", type="string"),
 *                 @OA\Property(property="masa_berlaku_sertifikat", type="string", format="date"),
 *                 @OA\Property(property="type_sertifikat", type="string")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation failed / No file uploaded",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="errors", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=400)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found"),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Unexpected server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="An unexpected server error occurred."),
 *             @OA\Property(property="error", type="string"),
 *             @OA\Property(property="status_code", type="integer", example=500)
 *         )
 *     )
 * )
 */

 class UploadSertifikatUser {}