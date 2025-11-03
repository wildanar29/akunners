<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form9/questions",
 *     tags={"FORM 9 (UMPAN BALIK)"},
 *     summary="Ambil daftar pertanyaan Form 9 berdasarkan role (asesi atau asesor)",
 *     description="
 * Endpoint ini digunakan untuk mengambil daftar pertanyaan Form 9.
 * 
 * Gunakan parameter **subject** untuk menentukan peran:
 * - `/form9/questions?subject=asesi` → menampilkan pertanyaan untuk asesi
 * - `/form9/questions?subject=asesor` → menampilkan pertanyaan untuk asesor
 * 
 * Anda juga dapat menambahkan parameter `pk_id` untuk memfilter berdasarkan ID PK tertentu.
 *     ",
 *     @OA\Parameter(
 *         name="subject",
 *         in="query",
 *         required=true,
 *         description="Pilih role: 'asesi' atau 'asesor'",
 *         @OA\Schema(type="string", enum={"asesi", "asesor"}, example="asesi")
 *     ),
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=false,
 *         description="Filter berdasarkan ID PK",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 * 
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil daftar pertanyaan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     oneOf={
 *                         @OA\Schema(
 *                             description="Contoh hasil jika subject=asesi",
 *                             example={
 *                                 "success": true,
 *                                 "data": {
 *                                     {
 *                                         "question_id": 1,
 *                                         "section": null,
 *                                         "sub_section": null,
 *                                         "question_text": "Saya mendapatkan penjelasan yang cukup memadai mengenai proses asesmen/uji kompetensi",
 *                                         "criteria": null,
 *                                         "order_no": 1,
 *                                         "subject": "asesi",
 *                                         "has_sub_questions": 0,
 *                                         "pk_id": 1
 *                                     },
 *                                     {
 *                                         "question_id": 2,
 *                                         "section": null,
 *                                         "sub_section": null,
 *                                         "question_text": "Saya diberikan kesempatan untuk mempelajari standar kompetensi yang akan diujikan dan menilai diri sendiri terhadap pencapaiannya",
 *                                         "criteria": null,
 *                                         "order_no": 2,
 *                                         "subject": "asesi",
 *                                         "has_sub_questions": 0,
 *                                         "pk_id": 1
 *                                     }
 *                                 }
 *                             }
 *                         ),
 *                         @OA\Schema(
 *                             description="Contoh hasil jika subject=asesor",
 *                             example={
 *                                 "success": true,
 *                                 "data": {
 *                                     {
 *                                         "question_id": 10,
 *                                         "section": "Pemenuhan prinsip-prinsip asesmen",
 *                                         "sub_section": null,
 *                                         "question_text": "1. Apakah perencaaan asesmen sudah memenuhi prinsip asesmen (valid, flexible, reliable dan fair)",
 *                                         "criteria": null,
 *                                         "order_no": 1,
 *                                         "subject": "asesor",
 *                                         "has_sub_questions": 1,
 *                                         "pk_id": 1
 *                                     },
 *                                     {
 *                                         "question_id": 11,
 *                                         "section": "Pemenuhan prinsip-prinsip asesmen",
 *                                         "sub_section": null,
 *                                         "question_text": "2. Apakah perangkat asesmen sudah memenuhi prinsip asesmen (valid, flexible, reliable dan fair)",
 *                                         "criteria": null,
 *                                         "order_no": 2,
 *                                         "subject": "asesor",
 *                                         "has_sub_questions": 1,
 *                                         "pk_id": 1
 *                                     }
 *                                 }
 *                             }
 *                         )
 *                     }
 *                 )
 *             )
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response=404,
 *         description="Tidak ada pertanyaan ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Tidak ada pertanyaan ditemukan untuk filter yang diberikan"),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 * 
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
