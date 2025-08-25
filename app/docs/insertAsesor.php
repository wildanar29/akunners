<?php

namespace App\Docs;
/**
 * @OA\Put(
 *     path="/input-asesor",
 *     tags={"FORM 1 (PENGAJUAN ASESMEN)"},
 *     summary="Input atau tolak asesor oleh user role bidang (role_id = 3)",
 *     description="Endpoint ini digunakan oleh user role bidang untuk menolak form 1 atau menetapkan asesor berdasarkan no_reg. Hanya user dengan role_id = 3 yang dapat mengakses.",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"form_1_id"},
 *             @OA\Property(property="form_1_id", type="integer", example=101, description="ID dari Form 1 yang akan diperbarui"),
 *             @OA\Property(property="status", type="string", enum={"Rejected"}, example="Rejected", description="Status form. Hanya diperlukan jika menolak."),
 *             @OA\Property(property="keterangan", type="string", example="Data tidak lengkap", description="Alasan penolakan. Wajib diisi jika status Rejected."),
 *             @OA\Property(property="no_reg", type="string", example="ASE123456", description="Nomor registrasi asesor. Wajib jika status tidak Rejected."),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data berhasil diperbarui atau asesor berhasil ditetapkan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Data asesor berhasil diupdate."),
 *             @OA\Property(property="data", type="object", description="Data bidang yang diperbarui"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal. Pastikan input sesuai."),
 *             @OA\Property(property="errors", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=400)
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Akses ditolak",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Anda tidak memiliki izin untuk melakukan aksi ini."),
 *             @OA\Property(property="status_code", type="integer", example=403)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data form atau asesor tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Tidak ditemukan asesor aktif dengan no_reg tersebut."),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat memproses data."),
 *             @OA\Property(property="error", type="string", example="Exception message here"),
 *             @OA\Property(property="status_code", type="integer", example=500)
 *         )
 *     )
 * )
 */


 class insertAsesor {}