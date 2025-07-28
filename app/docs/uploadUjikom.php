<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/upload-ujikom",
 *     tags={"Upload Ujikom"},
 *     summary="Upload atau update file Ujikom beserta metadata kompetensi",
 *     description="Endpoint ini digunakan untuk mengunggah atau memperbarui file Ujikom pengguna. Mendukung file PDF, JPG, JPEG, dan PNG dengan ukuran maksimal 2MB.",
 *     operationId="uploadUjikom",
 *     security={{"bearerAuth":{}}},
 *
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
 *                     description="File Ujikom (PDF, JPG, JPEG, PNG), maksimal 2MB"
 *                 ),
 *                 @OA\Property(
 *                     property="nomor_kompetensi",
 *                     type="string",
 *                     description="Nomor kompetensi"
 *                 ),
 *                 @OA\Property(
 *                     property="masa_berlaku_kompetensi",
 *                     type="string",
 *                     format="date",
 *                     description="Tanggal masa berlaku kompetensi (format: YYYY-MM-DD)"
 *                 ),
 *                 @OA\Property(
 *                     property="valid",
 *                     type="boolean",
 *                     description="Status validasi kompetensi"
 *                 ),
 *                 @OA\Property(
 *                     property="authentic",
 *                     type="boolean",
 *                     description="Status keaslian kompetensi"
 *                 ),
 *                 @OA\Property(
 *                     property="current",
 *                     type="boolean",
 *                     description="Status terkini kompetensi"
 *                 ),
 *                 @OA\Property(
 *                     property="sufficient",
 *                     type="boolean",
 *                     description="Status kecukupan kompetensi"
 *                 ),
 *                 @OA\Property(
 *                     property="ket",
 *                     type="string",
 *                     maxLength=255,
 *                     description="Keterangan tambahan"
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="File berhasil diperbarui",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="ujikom_id", type="integer"),
 *                 @OA\Property(property="user_id", type="integer"),
 *                 @OA\Property(property="file_path", type="string"),
 *                 @OA\Property(property="nomor_kompetensi", type="string"),
 *                 @OA\Property(property="masa_berlaku_kompetensi", type="string", format="date")
 *             ),
 *             @OA\Property(property="status_code", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="File berhasil diunggah",
 *         @OA\JsonContent(ref="#/components/schemas/UploadUjikomSuccess")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="errors", type="object"),
 *             @OA\Property(property="status_code", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status_code", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error server tak terduga",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="error", type="string"),
 *             @OA\Property(property="status_code", type="integer")
 *         )
 *     )
 * )
 */

 class uploadUjikom {}