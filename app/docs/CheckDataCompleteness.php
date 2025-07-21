<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/check-profile/{nik}",
 *     tags={"User"},
 *     summary="Cek kelengkapan data user berdasarkan NIK",
 *     description="Memeriksa kelengkapan data dan dokumen penting dari akun perawat berdasarkan NIK.",
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="Nomor Induk Kependudukan (NIK) user",
 *         @OA\Schema(type="string", example="3201010101010001")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data user lengkap",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="User data found and complete."),
 *             @OA\Property(property="source", type="string", example="Database"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="nama", type="string"),
 *                 @OA\Property(property="email", type="string"),
 *                 @OA\Property(property="no_telp", type="string"),
 *                 @OA\Property(property="tempat_lahir", type="string"),
 *                 @OA\Property(property="tanggal_lahir", type="string", format="date"),
 *                 @OA\Property(property="kewarganegaraan", type="string"),
 *                 @OA\Property(property="jenis_kelamin", type="string"),
 *                 @OA\Property(property="pendidikan", type="string"),
 *                 @OA\Property(property="tahun_lulus", type="string"),
 *                 @OA\Property(property="provinsi", type="string"),
 *                 @OA\Property(property="kota", type="string"),
 *                 @OA\Property(property="alamat", type="string"),
 *                 @OA\Property(property="kode_pos", type="string"),
 *                 @OA\Property(property="role_id", type="integer"),
 *                 @OA\Property(property="role_name", type="string"),
 *                 @OA\Property(property="working_unit_id", type="integer"),
 *                 @OA\Property(property="working_unit_name", type="string"),
 *                 @OA\Property(property="working_area_id", type="integer"),
 *                 @OA\Property(property="working_area_name", type="string"),
 *                 @OA\Property(property="jabatan_id", type="integer"),
 *                 @OA\Property(property="nama_jabatan", type="string"),
 *                 @OA\Property(property="ijazah", type="object",
 *                     @OA\Property(property="url", type="string", format="url")
 *                 ),
 *                 @OA\Property(property="sip", type="object",
 *                     @OA\Property(property="url", type="string", format="url"),
 *                     @OA\Property(property="nomor", type="string"),
 *                     @OA\Property(property="masa_berlaku", type="string")
 *                 ),
 *                 @OA\Property(property="str", type="object",
 *                     @OA\Property(property="url", type="string", format="url"),
 *                     @OA\Property(property="nomor", type="string"),
 *                     @OA\Property(property="masa_berlaku", type="string")
 *                 ),
 *                 @OA\Property(property="sertifikat", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="url", type="string", format="url"),
 *                         @OA\Property(property="masa_berlaku", type="string"),
 *                         @OA\Property(property="nomor", type="string")
 *                     )
 *                 ),
 *                 @OA\Property(property="foto", type="string", format="url")
 *             ),
 *             @OA\Property(property="message_detail", type="string", example="User data retrieved successfully from the database.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Data tidak lengkap",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="User data is incomplete."),
 *             @OA\Property(property="missing_fields", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="missing_documents", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="detail", type="string", example="The following data is incomplete: email, alamat. Missing documents: sip"),
 *             @OA\Property(property="solution", type="string", example="Please review the missing data and complete the required information.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="User not found."),
 *             @OA\Property(property="detail", type="string", example="The account with NIK '3201010101010001' is not registered in the system."),
 *             @OA\Property(property="solution", type="string", example="Please check your NIK or ensure you have registered.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="An error occurred on the server."),
 *             @OA\Property(property="kesalahan", type="string", example="SQLSTATE[...]: Some DB error"),
 *             @OA\Property(property="solusi", type="string", example="Please try again later. If the issue persists, contact the system admin.")
 *         )
 *     )
 * )
 */

 class CheckDataCompleteness {}