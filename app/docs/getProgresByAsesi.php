<?php

namespace App\Docs;

/**
 * @OA\Put(
 *     path="/input-asesor",
 *     tags={"FORM 1"},
 *     summary="Input atau update data asesor untuk form 1",
 *     operationId="insertAsesor",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"form_1_id"},
 *             @OA\Property(property="form_1_id", type="integer", example=101, description="ID Form 1 yang akan diperbarui"),
 *             @OA\Property(property="no_reg", type="string", example="ASE12345", description="Nomor registrasi asesor"),
 *             @OA\Property(property="status", type="string", enum={"Rejected"}, example="Rejected", description="Status form (hanya 'Rejected' yang diterima)"),
 *             @OA\Property(property="keterangan", type="string", example="Data tidak valid", description="Keterangan jika status adalah Rejected")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data asesor berhasil diperbarui",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Data asesor berhasil diupdate."),
 *             @OA\Property(property="data", type="object"),
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
 *         description="User tidak memiliki izin",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Anda tidak memiliki izin untuk melakukan aksi ini."),
 *             @OA\Property(property="status_code", type="integer", example=403)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data tidak ditemukan untuk form_1_id."),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan pada server",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat memproses data."),
 *             @OA\Property(property="error", type="string", example="Exception message here"),
 *             @OA\Property(property="status_code", type="integer", example=500)
 *         )
 *     )
 * )
 */


 class getProgresByAsesi {}