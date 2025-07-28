<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/upload-sertifikat",
 *     tags={"Upload Sertifikat"},
 *     summary="Upload atau update sertifikat kompetensi",
 *     description="Endpoint ini digunakan untuk mengunggah atau memperbarui file sertifikat kompetensi milik user. File yang diperbolehkan adalah PDF atau gambar (jpg, jpeg, png) dengan ukuran maksimal 2MB.",
 *     operationId="uploadSertifikat",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={},
 *                 @OA\Property(
 *                     property="path_file",
 *                     type="string",
 *                     format="binary",
 *                     description="File sertifikat (PDF, JPG, JPEG, PNG max 2MB)"
 *                 ),
 *                 @OA\Property(property="nomor_kompetensi", type="string", example="123/ABC/2025"),
 *                 @OA\Property(property="masa_berlaku_kompetensi", type="string", format="date", example="2026-12-31"),
 *                 @OA\Property(property="valid", type="boolean", example=true),
 *                 @OA\Property(property="authentic", type="boolean", example=false),
 *                 @OA\Property(property="current", type="boolean", example=true),
 *                 @OA\Property(property="sufficient", type="boolean", example=true),
 *                 @OA\Property(property="ket", type="string", maxLength=255, example="Sertifikat hasil uji kompetensi tahun 2025")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File berhasil diperbarui",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="File updated successfully."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="ujikom_id", type="integer", example=1),
 *                 @OA\Property(property="user_id", type="integer", example=123),
 *                 @OA\Property(property="file_path", type="string", example="Ujikom/1627373823_sertifikat.pdf"),
 *                 @OA\Property(property="nomor_kompetensi", type="string", example="123/ABC/2025"),
 *                 @OA\Property(property="masa_berlaku_kompetensi", type="string", example="2026-12-31")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="File berhasil diupload",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="File uploaded successfully."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="ujikom_id", type="integer", example=2),
 *                 @OA\Property(property="user_id", type="integer", example=123),
 *                 @OA\Property(property="file_path", type="string", example="Ujikom/1627373823_sertifikat.pdf"),
 *                 @OA\Property(property="nomor_kompetensi", type="string", example="123/ABC/2025"),
 *                 @OA\Property(property="masa_berlaku_kompetensi", type="string", example="2026-12-31")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=201)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validation failed. Please ensure all data entered is correct."),
 *             @OA\Property(property="errors", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=400)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="An unexpected server error occurred."),
 *             @OA\Property(property="error", type="string", example="file_put_contents(): Failed to open stream..."),
 *             @OA\Property(property="status_code", type="integer", example=500)
 *         )
 *     )
 * )
 */

 class uploadSertifikat {}