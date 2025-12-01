<?php

namespace App\Docs;

/**
 * @OA\Schema(
 *     schema="Form9AsesiRequest",
 *     type="object",
 *     example={
 *         "subject"="asesi",
 *         "answers"={
 *             {
 *                 "question_id"=1,
 *                 "answer_text"="Penjelasan proses asesmen sudah memadai",
 *                 "is_checked"=true,
 *                 "sub_questions"={}
 *             }
 *         }
 *     }
 * )
 */

/**
 * @OA\Schema(
 *     schema="Form9AsesorRequest",
 *     type="object",
 *     example={
 *         "subject"="asesor",
 *         "answers"={
 *             {
 *                 "question_id"=10,
 *                 "sub_questions"={
 *                     { "sub_question_id"=1, "answer_text"="1", "notes"="Catatan sub 1" },
 *                     { "sub_question_id"=2, "answer_text"="0", "notes"="Catatan sub 2" }
 *                 }
 *             }
 *         }
 *     }
 * )
 */

/**
 * @OA\Post(
 *     path="/form9/{form9Id}/save-jawaban",
 *     tags={"FORM 9 (UMPAN BALIK)"},
 *     summary="Simpan atau perbarui jawaban Form 9 (oleh Asesor atau Asesi)",
 *     description="
        - Asesi → answer_text + is_checked
        - Asesor → answer_text + sub_questions + notes
     ",
 *
 *     @OA\Parameter(
 *         name="form9Id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(ref="#/components/schemas/Form9AsesiRequest"),
 *                 @OA\Schema(ref="#/components/schemas/Form9AsesorRequest")
 *             }
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Jawaban berhasil disimpan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Jawaban berhasil disimpan")
 *         )
 *     )
 * )
 */
class saveOrUpdateAnswers {}
