<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/get-profile/{nik}",
 *     summary="Ambil data akun nurse berdasarkan NIK",
 *     tags={"User"},
 *     @OA\Parameter(
 *         name="nik",
 *         in="path",
 *         required=true,
 *         description="Nomor Induk Kependudukan (NIK) nurse",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data akun nurse berhasil ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="User data found from database."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="nama", type="string", example="Dewi Sartika"),
 *                 @OA\Property(property="email", type="string", example="dewi@example.com"),
 *                 @OA\Property(property="no_telp", type="string", example="081234567890"),
 *                 @OA\Property(property="tempat_lahir", type="string", example="Bandung"),
 *                 @OA\Property(property="tanggal_lahir", type="string", format="date", example="1990-01-01"),
 *                 @OA\Property(property="kewarganegaraan", type="string", example="Indonesia"),
 *                 @OA\Property(property="jenis_kelamin", type="string", enum={"L", "P"}, example="P"),
 *                 @OA\Property(property="pendidikan", type="string", example="S1 Keperawatan"),
 *                 @OA\Property(property="tahun_lulus", type="string", example="2012"),
 *                 @OA\Property(property="provinsi", type="string", example="Jawa Barat"),
 *                 @OA\Property(property="kota", type="string", example="Bandung"),
 *                 @OA\Property(property="alamat", type="string", example="Jl. Sukajadi No. 123"),
 *                 @OA\Property(property="kode_pos", type="string", example="40163"),
 *                 @OA\Property(property="role_id", type="integer", example=4),
 *                 @OA\Property(property="role_name", type="string", example="Perawat"),
 *                 @OA\Property(property="foto", type="string", format="url", example="http://localhost/storage/foto_nurse/dewi.jpg"),
 *                 @OA\Property(property="ijazah", type="object",
 *                     @OA\Property(property="url", type="string", example="http://localhost/storage/ijazah.pdf")
 *                 ),
 *                 @OA\Property(property="ujikom", type="object",
 *                     @OA\Property(property="url", type="string", example="http://localhost/storage/ujikom.pdf"),
 *                     @OA\Property(property="nomor", type="string", example="UKM123456"),
 *                     @OA\Property(property="masa_berlaku", type="string", format="date", example="2027-01-01")
 *                 ),
 *                 @OA\Property(property="str", type="object",
 *                     @OA\Property(property="url", type="string", example="http://localhost/storage/str.pdf"),
 *                     @OA\Property(property="nomor", type="string", example="STR123456"),
 *                     @OA\Property(property="masa_berlaku", type="string", format="date", example="2025-12-31")
 *                 ),
 *                 @OA\Property(property="sip", type="object",
 *                     @OA\Property(property="url", type="string", example="http://localhost/storage/sip.pdf"),
 *                     @OA\Property(property="nomor", type="string", example="SIP123456"),
 *                     @OA\Property(property="masa_berlaku", type="string", format="date", example="2026-12-31")
 *                 ),
 *                 @OA\Property(property="spk", type="object",
 *                     @OA\Property(property="url", type="string", example="http://localhost/storage/spk.pdf"),
 *                     @OA\Property(property="nomor", type="string", example="SPK123456"),
 *                     @OA\Property(property="masa_berlaku", type="string", format="date", example="2026-12-31")
 *                 ),
 *                 @OA\Property(property="sertifikat", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="url", type="string", example="http://localhost/storage/sertifikat1.pdf"),
 *                         @OA\Property(property="type", type="string", example="BTCLS"),
 *                         @OA\Property(property="nomor", type="string", example="CERT123456"),
 *                         @OA\Property(property="masa_berlaku", type="string", format="date", example="2026-06-01")
 *                     )
 *                 ),
 *                 @OA\Property(property="jabatan_history", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="working_unit_id", type="integer", example=2),
 *                         @OA\Property(property="user_jabatan_id", type="integer", example=5),
 *                         @OA\Property(property="working_unit_name", type="string", example="Puskesmas Sukamaju"),
 *                         @OA\Property(property="jabatan_id", type="integer", example=3),
 *                         @OA\Property(property="nama_jabatan", type="string", example="Perawat Ahli"),
 *                         @OA\Property(property="dari", type="string", format="date", example="2020-01-01"),
 *                         @OA\Property(property="sampai", type="string", format="date", example="2023-01-01")
 *                     )
 *                 )
 *             ),
 *             @OA\Property(property="message_detail", type="string", example="User data successfully retrieved and stored in cache.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="User not found."),
 *             @OA\Property(property="detail", type="string", example="Account with NIK '1234567890' is not registered in the system."),
 *             @OA\Property(property="solution", type="string", example="Please check your NIK or make sure you are registered.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Server error."),
 *             @OA\Property(property="error", type="string", example="Detail error exception...")
 *         )
 *     )
 * )
 */

 class GetAkunNurseByNIK {}