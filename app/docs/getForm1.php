<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form1",
 *     summary="Ambil data Form 1 berdasarkan filter dinamis",
 *     tags={"FORM 1 (PENGAJUAN ASESMEN)"},
 *     description="Menampilkan daftar Form 1 berdasarkan status tertentu dan filter opsional lainnya.
 *     - `Submitted`: Menampilkan list pengajuan form 1 di bidang
 *     - `Assigned`: Menampilkan list form 1 yang sudah di-assign ke asesor dan muncul juga di bidang
 *     - `Approved`: Menampilkan list form 1 yang sudah di-approve oleh asesor dan muncul juga di bidang",
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         description="ID program kerja untuk memfilter data",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="asesor_id",
 *         in="query",
 *         description="ID asesor untuk memfilter data",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         description="Status Form 1 (Submitted, Approved, Rejected, Assigned)",
 *         required=false,
 *         @OA\Schema(type="string", enum={"Submitted", "Approved", "Rejected", "Assigned"})
 *     ),
 *     @OA\Parameter(
 *         name="asesi_id",
 *         in="query",
 *         description="ID asesi untuk memfilter data",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data Form 1",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="message", type="string", example="Data berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="progres_id", type="integer", example=1),
 *                     @OA\Property(property="status", type="string", example="Submitted"),
 *                     @OA\Property(property="form_id", type="integer", example=2),
 *                     @OA\Property(property="pk_id", type="integer", example=3),
 *                     @OA\Property(property="asesor_id", type="integer", example=5),
 *                     @OA\Property(property="asesor_name", type="string", example="Drs. Asesor A"),
 *                     @OA\Property(property="asesi_id", type="integer", example=8),
 *                     @OA\Property(property="asesi_name", type="string", example="Ahmad"),
 *                     @OA\Property(property="form_status", type="string", example="Approved"),
 *                     @OA\Property(property="no_reg", type="string", example="REG-001"),
 *                     @OA\Property(property="ket", type="string", example="Keterangan pengajuan"),
 *                     @OA\Property(property="ijazah_id", type="integer", example=1),
 *                     @OA\Property(property="spk_id", type="integer", example=2),
 *                     @OA\Property(property="sip_id", type="integer", example=3),
 *                     @OA\Property(property="str_id", type="integer", example=4),
 *                     @OA\Property(property="ujikom_id", type="integer", example=5),
 *                     @OA\Property(property="sertifikat_id", type="integer", example=6),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-22T12:00:00Z")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Gagal mengambil data",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Gagal mengambil data: error message"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 */

 class getForm1 {}