<?php

namespace App\Docs;

 /**
 * @OA\Post(
 *     path="/upload-sertifikat",
 *     summary="Upload File Sertifikat",
 *     description="Mengunggah file Sertifikat dan menyimpan detailnya ke database.",
 *     operationId="uploadFileSertifikat",
 *     tags={"Upload Sertifikat"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"path_file", "nomor_sertifikat", "masa_berlaku"},
 *                 @OA\Property(
 *                     property="path_file",
 *                     type="string",
 *                     format="binary",
 *                     description="File yang akan diunggah (PDF, JPG, JPEG, PNG, maksimal 2MB)."
 *                 ),
 *                 @OA\Property(
 *                     property="nomor_sertifikat",
 *                     type="string",
 *                     description="Nomor sertifikat yang diunggah.",
 *                     example="SERT-202500123"
 *                 ),
 *                 @OA\Property(
 *                     property="masa_berlaku",
 *                     type="string",
 *                     format="date",
 *                     description="Tanggal masa berlaku sertifikat.",
 *                     example="2027-06-15"
 *                 )
 *                 @OA\Property(
 *                     property="type_sertifikat",
 *                     type="string",
 *                     format="date",
 *                     description="NIRA, SPK atau NULL (Optional).",
 *                     example="NIRA / SPK"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File berhasil diunggah.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="File uploaded successfully."),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="sertifikat_id", type="integer", example=4, description="ID sertifikat yang tersimpan."),
 *                 @OA\Property(property="user_id", type="integer", example=11, description="ID pengguna yang mengunggah."),
 *                 @OA\Property(property="nomor_sertifikat", type="string", example="SERT-202500123"),
 *                 @OA\Property(property="masa_berlaku", type="string", example="2027-06-15"),
 *                 @OA\Property(property="path_file", type="string", example="http://app.rsimmanuel.net:9091/storage/Ujikom/file.pdf"),
 *                 @OA\Property(property="valid", type="boolean", example=null),
 *                 @OA\Property(property="authentic", type="boolean", example=null),
 *                 @OA\Property(property="current", type="boolean", example=null),
 *                 @OA\Property(property="sufficient", type="boolean", example=null),
 *                 @OA\Property(property="ket", type="string", example="null")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal atau file tidak ditemukan.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal. Pastikan file di Upload."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 description="Detail kesalahan validasi."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan tak terduga di server.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan tak terduga di server."),
 *             @OA\Property(property="error", type="string", example="Detail kesalahan server.")
 *         )
 *     )
 * )
 */


 class AjuanPermohonanAsesi {}