<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/get-form3-c",
 *     summary="Menampilkan data Form 3C dalam format HTML",
 *     description="Mengambil data Form 3C berdasarkan pk_id dan opsional no_elemen, lalu menampilkannya dalam bentuk tabel HTML.",
 *     tags={"FORM 3"},
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=true,
 *         description="ID Kompetensi PK yang ingin difilter",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="no_elemen",
 *         in="query",
 *         required=false,
 *         description="Nomor elemen untuk filter opsional",
 *         @OA\Schema(type="string", example="3.2")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil menampilkan halaman HTML Form 3C",
 *         @OA\MediaType(
 *             mediaType="text/html",
 *             @OA\Schema(type="string", example="<!DOCTYPE html><html>...</html>")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Gagal menampilkan data karena pk_id tidak diberikan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Parameter pk_id wajib diisi."),
 *             @OA\Property(property="data", type="string", nullable=true)
 *         )
 *     )
 * )
 */


 class getAllDataFormC {}