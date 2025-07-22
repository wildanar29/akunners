<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/soal-form2",
 *     summary="Ambil daftar soal Form 2 berdasarkan pk_id",
 *     tags={"FORM 2"},
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=true,
 *         description="ID Paket Kompetensi (pk_id)",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="no_elemen",
 *         in="query",
 *         required=false,
 *         description="Nomor elemen kompetensi (opsional)",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="no_id",
 *         in="query",
 *         required=false,
 *         description="ID pertanyaan spesifik (opsional)",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data soal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="message", type="string", example="Data taken from database for PK 1"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="pk_id", type="integer", example=1),
 *                     @OA\Property(property="no_elemen", type="integer", example=1),
 *                     @OA\Property(property="nama_elemen", type="string", example="Melakukan Instalasi"),
 *                     @OA\Property(property="komponen_id", type="integer", example=101),
 *                     @OA\Property(property="nama_komponen", type="string", example="Persiapan alat"),
 *                     @OA\Property(property="no_id", type="integer", example=5),
 *                     @OA\Property(property="sub_komponen_id", type="integer", example=7),
 *                     @OA\Property(property="daftar_pertanyaan", type="string", example="Apa yang dilakukan saat instalasi?")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={"pk_id": {"The pk id field is required."}}
 *             )
 *         )
 *     )
 * )
 */


 class getSoals {}