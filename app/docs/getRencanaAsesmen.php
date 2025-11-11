<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form3/data",
 *     tags={"FORM 3 (RENCANA ASESMEN)"},
 *     summary="Menampilkan data SPO dan Rencana Asesmen berdasarkan pk_id",
 *     description="Endpoint ini digunakan untuk menampilkan daftar SPO dan rencana asesmen (elemen, KUK, dan IUK) berdasarkan pk_id tertentu.",
 *     operationId="getRencanaAsesmen",
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=true,
 *         description="ID dari paket kompetensi (pk_id) yang ingin diambil datanya",
 *         @OA\Schema(
 *             type="integer",
 *             example=5
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil menampilkan data SPO dan Rencana Asesmen",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Berhasil menampilkan data SPO dan Rencana Asesmen."),
 *             @OA\Property(property="data", type="string", example="<!DOCTYPE html> ... (HTML tampilan rencana asesmen) ...")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Parameter pk_id wajib diisi",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Parameter pk_id wajib diisi."),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan untuk pk_id tersebut",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Data tidak ditemukan untuk pk_id tersebut."),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     ),
 *     security={},
 *     deprecated=false
 * )
 */

 class getRencanaAsesmen {}