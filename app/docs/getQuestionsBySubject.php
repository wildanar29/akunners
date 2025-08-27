<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form9/questions",
 *     tags={"FORM 9 (UMPAN BALIK)"},
 *     summary="API ini digunakan untuk mengambil soal form 9 berdasarkan role asesor atau asesi",
 *     description="Endpoint ini akan mengembalikan daftar pertanyaan Form 9. 
 *                  Filter bisa berdasarkan `subject` (asesor/asesi) dan `pk_id`.",
 *     @OA\Parameter(
 *         name="subject",
 *         in="query",
 *         required=false,
 *         description="Filter berdasarkan role (misalnya: asesor atau asesi)",
 *         @OA\Schema(type="string", example="asesor")
 *     ),
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=false,
 *         description="Filter berdasarkan ID PK",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil daftar pertanyaan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="question_id", type="integer", example=1),
 *                     @OA\Property(property="section", type="string", example="asesor"),
 *                     @OA\Property(property="question_text", type="string", example="Bagaimana pendapat Anda mengenai ..."),
 *                     @OA\Property(property="order_no", type="integer", example=1),
 *                     @OA\Property(property="pk_id", type="integer", example=1)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Tidak ada pertanyaan ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Tidak ada pertanyaan ditemukan untuk filter yang diberikan"),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat mengambil pertanyaan"),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[42S22]: Column not found...")
 *         )
 *     )
 * )
 */

 class getQuestionsBySubject {}