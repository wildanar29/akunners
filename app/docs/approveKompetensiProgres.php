<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form5/asesi-approve",
 *     summary="Approve Form 5 oleh Asesi",
 *     description="API ini digunakan untuk menyetujui isi dari form 5 yang dilakukan oleh asesi.",
 *     operationId="approveKompetensiProgres",
 *     tags={"FORM 5"},
 *     @OA\Parameter(
 *         name="form_id",
 *         in="query",
 *         required=true,
 *         description="ID Form 5 yang akan disetujui",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=false,
 *         description="ID PK jika diperlukan untuk pencarian progres",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Status KompetensiProgres berhasil diupdate ke Approved",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Status KompetensiProgres berhasil diupdate ke Approved.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Parameter form_id wajib diisi",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="Parameter form_id wajib diisi.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data KompetensiProgres tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="Data KompetensiProgres tidak ditemukan.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat update status",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat update status."),
 *             @OA\Property(property="error", type="string", example="Exception message here")
 *         )
 *     )
 * )
 */

 class approveKompetensiProgres {}