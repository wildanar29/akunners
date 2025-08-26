<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/get-form3-b",
 *     summary="Mengambil data Form 3B dalam format HTML berdasarkan pk_id dan (opsional) no_elemen",
 *     tags={"FORM 3 (RENCANA ASESMEN)"},
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=true,
 *         description="ID Kompetensi PK yang ingin ditampilkan",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="no_elemen",
 *         in="query",
 *         required=false,
 *         description="Filter berdasarkan nomor elemen tertentu",
 *         @OA\Schema(type="string", example="2.1")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="HTML halaman Form 3B berhasil ditampilkan",
 *         @OA\MediaType(
 *             mediaType="text/html",
 *             @OA\Schema(type="string", example="<!DOCTYPE html>...")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Parameter pk_id wajib diisi.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Parameter pk_id wajib diisi."),
 *             @OA\Property(property="data", type="string", nullable=true)
 *         )
 *     )
 * )
 */

 class getAllDataFormB {}