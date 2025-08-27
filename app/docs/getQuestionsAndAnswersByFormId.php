<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form9/{form9Id}/soal-jawab",
 *     tags={"FORM 9 (UMPAN BALIK)"},
 *     summary="API ini digunakan untuk mengambil soal dan jawaban pada form 9",
 *     description="Mengembalikan daftar soal beserta jawaban (jika ada) berdasarkan form_9_id, 
 *                  termasuk sub-pertanyaan dan jawabannya.",
 *     @OA\Parameter(
 *         name="form9Id",
 *         in="path",
 *         required=true,
 *         description="ID Form 9",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil soal & jawaban",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="question_id", type="integer", example=10),
 *                     @OA\Property(property="section", type="string", example="asesor"),
 *                     @OA\Property(property="sub_section", type="string", example="Komunikasi"),
 *                     @OA\Property(property="question_text", type="string", example="Apakah instruksi asesor jelas?"),
 *                     @OA\Property(property="criteria", type="string", example="Kejelasan"),
 *                     @OA\Property(property="order_no", type="integer", example=1),
 *                     @OA\Property(property="subject", type="string", example="asesi"),
 *                     @OA\Property(property="has_sub_questions", type="integer", example=0),
 *                     @OA\Property(
 *                         property="answers",
 *                         type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="id", type="integer", example=55),
 *                             @OA\Property(property="form_9_id", type="integer", example=1),
 *                             @OA\Property(property="question_id", type="integer", example=10),
 *                             @OA\Property(property="answer_text", type="string", example="Ya, sangat jelas")
 *                         )
 *                     ),
 *                     @OA\Property(
 *                         property="sub_questions",
 *                         type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="sub_question_id", type="integer", example=101),
 *                             @OA\Property(property="sub_label", type="string", example="Detail tambahan"),
 *                             @OA\Property(property="order_no", type="integer", example=1),
 *                             @OA\Property(
 *                                 property="answers",
 *                                 type="array",
 *                                 @OA\Items(
 *                                     @OA\Property(property="id", type="integer", example=77),
 *                                     @OA\Property(property="form_9_id", type="integer", example=1),
 *                                     @OA\Property(property="sub_question_id", type="integer", example=101),
 *                                     @OA\Property(property="answer_text", type="string", example="Jawaban sub pertanyaan")
 *                                 )
 *                             )
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Tidak ada soal ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Tidak ada pertanyaan untuk form_9_id: 1"),
 *             @OA\Property(property="data", type="array", example={})
 *         )
 *     ),
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