<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/get-form3-a",
 *     summary="Menampilkan data Form 3A dalam format HTML",
 *     description="Endpoint ini mengambil dan menampilkan data Form 3A berdasarkan pk_id dan opsional no_elemen_form_3, lalu menghasilkan tampilan HTML tabel poin diamati.",
 *     tags={"FORM 3 (RENCANA ASESMEN)"},
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=true,
 *         description="ID Kompetensi PK yang ingin ditampilkan",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="no_elemen_form_3",
 *         in="query",
 *         required=false,
 *         description="Nomor elemen Form 3 sebagai filter opsional",
 *         @OA\Schema(type="string", example="2.1")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil menampilkan Form 3A dalam format HTML",
 *         @OA\MediaType(
 *             mediaType="text/html",
 *             @OA\Schema(type="string", example="<!DOCTYPE html><html>...</html>")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Parameter pk_id tidak diberikan",
 *         @OA\MediaType(
 *             mediaType="text/html",
 *             @OA\Schema(type="string", example="<h3 style='color:red;'>Parameter <strong>pk_id</strong> wajib diisi.</h3>")
 *         )
 *     )
 * )
 */

 class getAllDataFormC {}