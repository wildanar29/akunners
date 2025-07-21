<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/update-profile/{nik}",
 *     summary="Update profil perawat berdasarkan NIK",
 *     tags={"User"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="Nomor Induk Kependudukan (NIK) pengguna",
 *         @OA\Schema(type="string", example="1234567890123456")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={},
 *                 @OA\Property(property="nama", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *                 @OA\Property(property="no_telp", type="string", example="081234567890"),
 *                 @OA\Property(property="tempat_lahir", type="string", example="Jakarta"),
 *                 @OA\Property(property="tanggal_lahir", type="string", format="date", example="1990-01-01"),
 *                 @OA\Property(property="kewarganegaraan", type="string", example="Indonesia"),
 *                 @OA\Property(property="jenis_kelamin", type="string", enum={"L", "P"}, example="L"),
 *                 @OA\Property(property="pendidikan", type="string", example="S1 Keperawatan"),
 *                 @OA\Property(property="tahun_lulus", type="integer", example=2012),
 *                 @OA\Property(property="provinsi", type="string", example="Jawa Barat"),
 *                 @OA\Property(property="kota", type="string", example="Bandung"),
 *                 @OA\Property(property="alamat", type="string", example="Jl. Melati No. 123"),
 *                 @OA\Property(property="kode_pos", type="string", example="40234"),
 *                 @OA\Property(property="dari", type="string", format="date", example="2015-01-01"),
 *                 @OA\Property(property="sampai", type="string", format="date", example="2020-01-01"),
 *                 @OA\Property(property="foto", type="file", description="Foto profil dalam format jpg/png/gif")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Profil berhasil diperbarui",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="User data updated successfully."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="nama", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", example="john@example.com"),
 *                 @OA\Property(property="no_telp", type="string", example="081234567890"),
 *                 @OA\Property(property="tempat_lahir", type="string", example="Jakarta"),
 *                 @OA\Property(property="tanggal_lahir", type="string", example="1990-01-01"),
 *                 @OA\Property(property="kewarganegaraan", type="string", example="Indonesia"),
 *                 @OA\Property(property="jenis_kelamin", type="string", example="L"),
 *                 @OA\Property(property="pendidikan", type="string", example="S1 Keperawatan"),
 *                 @OA\Property(property="tahun_lulus", type="integer", example=2012),
 *                 @OA\Property(property="provinsi", type="string", example="Jawa Barat"),
 *                 @OA\Property(property="kota", type="string", example="Bandung"),
 *                 @OA\Property(property="alamat", type="string", example="Jl. Melati No. 123"),
 *                 @OA\Property(property="kode_pos", type="string", example="40234"),
 *                 @OA\Property(property="foto", type="string", example="http://yourdomain.com/storage/foto_nurse/abc.jpg"),
 *                 @OA\Property(property="ijazah", type="string", example="http://yourdomain.com/storage/ijazah.pdf"),
 *                 @OA\Property(property="ujikom", type="string", example="http://yourdomain.com/storage/ujikom.pdf"),
 *                 @OA\Property(property="str", type="string", example="http://yourdomain.com/storage/str.pdf"),
 *                 @OA\Property(property="sip", type="string", example="http://yourdomain.com/storage/sip.pdf"),
 *                 @OA\Property(
 *                     property="sertifikat",
 *                     type="array",
 *                     @OA\Items(type="string", example="http://yourdomain.com/storage/sertifikat1.pdf")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="Data validation failed."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Pengguna tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Server error."),
 *             @OA\Property(property="error", type="string", example="Exception message here")
 *         )
 *     )
 * )
 */

 class UpdateAkunNurse {}