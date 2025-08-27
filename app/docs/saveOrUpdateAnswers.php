<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form9/{form9Id}/save-jawaban",
 *     tags={"FORM 9 (UMPAN BALIK)"},
 *     summary="API ini digunakan untuk menyimpan jawaban yang diberikan oleh asesi atau asesor",
 *     description="Endpoint ini akan menyimpan atau memperbarui jawaban Form 9 berdasarkan role (`asesor` atau `asesi`). 
 *                  Mendukung jawaban untuk pertanyaan utama dan sub-pertanyaan.",
 *     @OA\Parameter(
 *         name="form9Id",
 *         in="path",
 *         required=true,
 *         description="ID Form 9",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"subject","answers"},
 *             @OA\Property(property="subject", type="string", enum={"asesor","asesi"}, example="asesor"),
 *             @OA\Property(
 *                 property="answers",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="question_id", type="integer", example=10),
 *                     @OA\Property(property="answer_text", type="string", nullable=true, example="Jawaban utama"),
 *                     @OA\Property(
 *                         property="sub_questions",
 *                         type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="sub_question_id", type="integer", example=101),
 *                             @OA\Property(property="answer_text", type="string", nullable=true, example="Jawaban sub")
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Jawaban berhasil disimpan atau diperbarui",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Jawaban berhasil disimpan/diperbarui")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "answers.0.question_id": {"The selected question id is invalid."}
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan server",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menyimpan jawaban"),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[HY000]: General error ...")
 *         )
 *     )
 * )
 */

 class saveOrUpdateAnswers {}