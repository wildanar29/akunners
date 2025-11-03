<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form9/{form9Id}/soal-jawab",
 *     tags={"FORM 9 (UMPAN BALIK)"},
 *     summary="Ambil daftar soal dan jawaban Form 9 (urut berdasarkan subject: asesi, lalu asesor)",
 *     description="
 * Endpoint ini mengembalikan daftar **pertanyaan dan jawaban** berdasarkan `form_9_id`.
 * 
 * 🔹 Hasil diurutkan berdasarkan subject:
 *   - `asesi` ditampilkan lebih dulu
 *   - lalu `asesor`
 * 
 * 🔹 Jika suatu pertanyaan memiliki sub-pertanyaan, maka jawaban utama dikosongkan dan jawaban diambil dari `sub_questions`.
 * 
 * 🔹 Nilai `answer_text` akan dikonversi menjadi **boolean** jika berisi angka:
 *   - `'1'` → `true`
 *   - `'0'` → `false`
 *   - selain itu tetap string.
 * 
 * Contoh penggunaan:
 * ```
 * GET /form9/6/soal-jawab
 * ```
 *     ",
 * 
 *     @OA\Parameter(
 *         name="form9Id",
 *         in="path",
 *         required=true,
 *         description="ID Form 9 yang akan diambil datanya",
 *         @OA\Schema(type="integer", example=6)
 *     ),
 * 
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil daftar soal dan jawaban Form 9",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="question_id", type="integer", example=10),
 *                     @OA\Property(property="section", type="string", nullable=true, example="Pemenuhan prinsip-prinsip asesmen"),
 *                     @OA\Property(property="sub_section", type="string", nullable=true, example=null),
 *                     @OA\Property(property="question_text", type="string", example="Apakah perencanaan asesmen sudah memenuhi prinsip asesmen?"),
 *                     @OA\Property(property="criteria", type="string", nullable=true, example=null),
 *                     @OA\Property(property="order_no", type="integer", example=1),
 *                     @OA\Property(property="subject", type="string", example="asesor"),
 *                     @OA\Property(property="has_sub_questions", type="integer", example=1),
 *                     
 *                     @OA\Property(
 *                         property="answers",
 *                         type="array",
 *                         description="Jika tidak memiliki sub-pertanyaan, maka berisi jawaban utama (answer_text bisa berupa string atau boolean)",
 *                         @OA\Items(
 *                             @OA\Property(property="answer_id", type="integer", example=101),
 *                             @OA\Property(property="question_id", type="integer", example=10),
 *                             @OA\Property(property="form_9_id", type="integer", example=6),
 *                             @OA\Property(
 *                                 property="answer_text",
 *                                 oneOf={
 *                                     @OA\Schema(type="boolean", example=true),
 *                                     @OA\Schema(type="string", example="Ya, sudah memenuhi")
 *                                 }
 *                             )
 *                         )
 *                     ),
 * 
 *                     @OA\Property(
 *                         property="sub_questions",
 *                         type="array",
 *                         description="Jika pertanyaan memiliki sub-pertanyaan, maka jawaban berada di sini",
 *                         @OA\Items(
 *                             @OA\Property(property="sub_question_id", type="integer", example=5),
 *                             @OA\Property(property="sub_label", type="string", example="Valid"),
 *                             @OA\Property(property="order_no", type="integer", example=1),
 *                             @OA\Property(
 *                                 property="answers",
 *                                 type="array",
 *                                 @OA\Items(
 *                                     @OA\Property(property="answer_id", type="integer", example=210),
 *                                     @OA\Property(property="sub_question_id", type="integer", example=5),
 *                                     @OA\Property(property="form_9_id", type="integer", example=6),
 *                                     @OA\Property(
 *                                         property="answer_text",
 *                                         oneOf={
 *                                             @OA\Schema(type="boolean", example=false),
 *                                             @OA\Schema(type="string", example="Jawaban deskriptif sub-pertanyaan")
 *                                         }
 *                                     )
 *                                 )
 *                             )
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response=404,
 *         description="Tidak ada pertanyaan untuk form_9_id tertentu",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Tidak ada pertanyaan untuk form_9_id: 6"),
 *             @OA\Property(property="data", type="array", example={})
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat mengambil pertanyaan & jawaban"),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[HY000]: General error ...")
 *         )
 *     )
 * )
 */
class getQuestionsAndAnswersByFormId {}
