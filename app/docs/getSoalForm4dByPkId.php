<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/form4d/soal",
 *     tags={"FORM 4 (ASESMEN)"},
 *     summary="Ambil Soal Form 4D",
 *     description="API ini digunakna untuk mengambil soal dari Form 4D",
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=true,
 *         description="ID PK yang digunakan untuk filter soal",
 *         @OA\Schema(type="integer", example=12)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Soal Form 4D berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Soal Form 4D berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=5),
 *                     @OA\Property(property="urutan", type="integer", example=1),
 *                     @OA\Property(property="kuk", type="object", example={"id": 3, "kode_kuk": "KUK.1", "deskripsi": "Menjelaskan prosedur ..."}),
 *                     @OA\Property(property="children", type="array", @OA\Items(type="object"))
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 additionalProperties=@OA\Schema(type="array", @OA\Items(type="string"))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat mengambil data",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat mengambil data"),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[HY000]: ...")
 *         )
 *     )
 * )
 */

 class getSoalForm4dByPkId {}