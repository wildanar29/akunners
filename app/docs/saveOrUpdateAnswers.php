<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form9/{form9Id}/save-jawaban",
 *     tags={"FORM 9 (UMPAN BALIK)"},
 *     summary="Simpan atau perbarui jawaban Form 9 (oleh Asesor atau Asesi)",
 *     description="
Endpoint ini digunakan untuk menyimpan atau memperbarui jawaban Form 9.

- Asesi → answer_text + is_checked (tanpa sub_questions)
- Asesor → answer_text + sub_questions + notes (opsional)
",
 *
 *     @OA\Parameter(
 *         name="form9Id",
 *         in="path",
 *         required=true,
 *         description="ID Form 9",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             oneOf={
 *
 *                 @OA\Schema(
 *                     title="Contoh Body Asesi",
 *                     type="object",
 *                     example={
 *                         "subject"="asesi",
 *                         "answers"={
 *                             {
 *                                 "question_id"=1,
 *                                 "answer_text"="Penjelasan proses asesmen sudah memadai",
 *                                 "is_checked"=true,
 *                                 "sub_questions"={}
 *                             },
 *                             {
 *                                 "question_id"=2,
 *                                 "answer_text"="Saya diberikan kesempatan menilai diri sendiri",
 *                                 "is_checked"=true,
 *                                 "sub_questions"={}
 *                             }
 *                         }
 *                     }
 *                 ),
 *
 *                 @OA\Schema(
 *                     title="Contoh Body Asesor",
 *                     type="object",
 *                     example={
 *                         "subject"="asesor",
 *                         "answers"={
 *                             {
 *                                 "question_id"=10,
 *                                 "sub_questions"={
 *                                     { "sub_question_id"=1, "answer_text"="1", "notes"="Catatan sub 1" },
 *                                     { "sub_question_id"=2, "answer_text"="0", "notes"="Catatan sub 2" },
 *                                     { "sub_question_id"=3, "answer_text"="1", "notes"="Catatan sub 3" },
 *                                     { "sub_question_id"=4, "answer_text"="1", "notes"="Catatan sub 4" }
 *                                 }
 *                             },
 *                             {
 *                                 "question_id"=13,
 *                                 "answer_text"="Bukti asesmen konsisten terhadap dimensi kompetensi",
 *                                 "sub_questions"={}
 *                             }
 *                         }
 *                     }
 *                 )
 *
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
