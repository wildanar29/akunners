<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/panduan-form2",
 *     tags={"PANDUAN ASESMEN"},
 *     summary="Menampilkan panduan resmi penilaian mandiri Form 3D",
 *     description="Endpoint ini digunakan untuk menampilkan panduan atau petunjuk resmi bagi peserta asesmen terkait tata cara penilaian mandiri pada Form 3D.",
 *     operationId="panduanForm2",
 *     @OA\Response(
 *         response=200,
 *         description="Panduan penilaian mandiri berhasil ditampilkan.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="OK"),
 *             @OA\Property(property="message", type="string", example="Panduan penilaian mandiri berhasil ditampilkan."),
 *             @OA\Property(
 *                 property="data",
 *                 type="string",
 *                 example="<!DOCTYPE html><html><head><title>Panduan Penilaian Mandiri</title></head><body><h2>Panduan Penilaian Mandiri</h2>...</body></html>"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat memproses data."
 *     )
 * )
 */

 class panduanForm2 {}