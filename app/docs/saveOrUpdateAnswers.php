<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form9/{form9Id}/save-jawaban",
 *     tags={"FORM 9 (UMPAN BALIK)"},
 *     summary="Simpan atau perbarui jawaban Form 9 (oleh Asesor atau Asesi)",
 *     description="
 * Endpoint ini digunakan untuk menyimpan atau memperbarui jawaban Form 9 berdasarkan role (asesor atau asesi).
 *
 * - Asesi → mengisi answer_text + is_checked (tanpa sub_questions)
 * - Asesor → mengisi answer_text & sub_questions yang dapat berisi notes (opsional)
 *     ",
 *
 *     @OA\Parameter(
 *         name="form9Id",
 *         in="path",
 *         required=true,
 *         description="ID Form 9 yang akan disimpan jawabannya",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         description="Struktur JSON request body untuk Asesi dan Asesor",
 *         @OA\JsonContent(
 *             oneOf={
 *
 *                 @OA\Schema(
 *                     title="Contoh request body untuk Asesi",
 *                     example={
 *                         "subject": "asesi",
 *                         "answers": {
 *                             {
 *                                 "question_id": 1,
 *                                 "answer_text": "Penjelasan proses asesmen sudah memadai",
 *                                 "is_checked": true,
 *                                 "sub_questions": {}
 *                             },
 *                             {
 *                                 "question_id": 2,
 *                                 "answer_text": "Saya diberikan kesempatan menilai diri sendiri",
 *                                 "is_checked": true,
 *                                 "sub_questions": {}
 *                             }
 *                         }
 *                     }
 *                 ),
 *
 *                 @OA\Schema(
 *                     title="Contoh request body untuk Asesor dengan sub_questions + notes",
 *                     example={
 *                         "subject": "asesor",
 *                         "answers": {
 *                             {
 *                                 "question_id": 10,
 *                                 "sub_questions": {
 *                                     {"sub_question_id": 1, "answer_text": "1", "notes": "Catatan untuk sub 1"},
 *                                     {"sub_question_id": 2, "answer_text": "0", "notes": "Catatan untuk sub 2"},
 *                                     {"sub_question_id": 3, "answer_text": "1", "notes": "Catatan untuk sub 3"},
 *                                     {"sub_question_id": 4, "answer_text": "1", "notes": "Catatan untuk sub 4"}
 *                                 }
 *                             },
 *                             {
 *                                 "question_id": 11,
 *                                 "sub_questions": {
 *                                     {"sub_question_id": 5, "answer_text": "1", "notes": "Catatan untuk sub 5"},
 *                                     {"sub_question_id": 6, "answer_text": "1", "notes": "Catatan untuk sub 6"},
 *                                     {"sub_question_id": 7, "answer_text": "0", "notes": "Catatan untuk sub 7"},
 *                                     {"sub_question_id": 8, "answer_text": "1", "notes": "Catatan untuk sub 8"}
 *                                 }
 *                             },
 *                             {
 *                                 "question_id": 13,
 *                                 "answer_text": "Bukti asesmen konsisten terhadap dimensi kompetensi: task skill",
 *                                 "sub_questions": {}
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
 *         description="Jawaban berhasil disimpan atau diperbarui",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Jawaban berhasil disimpan atau diperbarui")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal (question_id tidak valid atau JSON salah format)",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "answers.0.sub_questions.0.notes": {
 *                         "The notes field must be a string."
 *                     }
 *                 }
 *             )
 *         )
 *     ),
 *
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
