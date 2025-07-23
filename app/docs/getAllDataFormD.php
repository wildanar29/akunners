<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/get-form3-d",
 *     summary="Ambil seluruh data Form 3D berdasarkan pk_id",
 *     tags={"Form 3D"},
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=true,
 *         description="ID paket (pk_id) yang digunakan untuk mengambil data Form 3D",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="HTML table berisi data Form 3D",
 *         @OA\MediaType(
 *             mediaType="text/html",
 *             @OA\Schema(type="string", example="<html><body><table>...</table></body></html>")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Parameter pk_id tidak diberikan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Parameter pk_id wajib diisi."),
 *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan untuk pk_id yang diberikan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Data tidak ditemukan untuk pk_id: 1"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
 *         )
 *     )
 * )
 */

 class getAllDataFormD {}