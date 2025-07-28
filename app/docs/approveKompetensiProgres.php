<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form5/asesi-approve",
 *     summary="Menyetujui Form 5 oleh Asesi",
 *     description="API ini digunakan untuk menyetujui isi dari form 5 yang dilakukan oleh asesi",
 *     tags={"Form 5"},
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         description="ID dari data KompetensiProgres",
 *         required=true,
 *         @OA\Schema(type="integer", example=123)
 *     ),
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         description="ID PK (opsional) untuk filter tambahan",
 *         required=false,
 *         @OA\Schema(type="integer", example=456)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Status berhasil diupdate",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Status KompetensiProgres berhasil diupdate ke Approved.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Parameter id wajib diisi",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="Parameter id wajib diisi.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="Data KompetensiProgres tidak ditemukan atau tidak diubah.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat update status."),
 *             @OA\Property(property="error", type="string", example="Exception message here")
 *         )
 *     )
 * )
 */


 class approveKompetensiProgres {}