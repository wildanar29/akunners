<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form9/{form9Id}/save-jawaban",
 *     tags={"FORM 9 (UMPAN BALIK)"},
 *     summary="Simpan atau perbarui jawaban Form 9 (oleh Asesor atau Asesi)",
 *     description="
 * Endpoint ini digunakan untuk **menyimpan atau memperbarui jawaban Form 9** berdasarkan role pengguna (`asesor` atau `asesi`).
 * 
 * 🔹 **Asesi** → hanya memiliki pertanyaan utama dengan field tambahan `is_checked` (true/false).  
 * 🔹 **Asesor** → memiliki pertanyaan utama dan daftar `sub_questions` untuk setiap pertanyaan tertentu.
 * 
 * Contoh pemanggilan:
 * - `/form9/1/save-jawaban` dengan body `subject=asesi`
 * - `/form9/1/save-jawaban` dengan body `subject=asesor`
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
 *         description="Contoh struktur JSON untuk Asesi (dengan is_checked) dan Asesor (dengan sub_questions)",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     title="Contoh request body untuk Asesi (dengan is_checked)",
 *                     example={
 *                         "subject": "asesi",
 *                         "answers": {
 *                             {
 *                                 "question_id": 1,
 *                                 "answer_text": "Saya mendapatkan penjelasan yang cukup memadai mengenai proses asesmen/uji kompetensi",
 *                                 "is_checked": true,
 *                                 "sub_questions": {}
 *                             },
 *                             {
 *                                 "question_id": 2,
 *                                 "answer_text": "Saya diberikan kesempatan untuk mempelajari standar kompetensi yang akan diujikan dan menilai diri sendiri terhadap pencapaiannya",
 *                                 "is_checked": true,
 *                                 "sub_questions": {}
 *                             },
 *                             {
 *                                 "question_id": 3,
 *                                 "answer_text": "Asesor memberikan kesempatan untuk mendiskusikan/ menegosiasikan metoda, instrumen dan sumber asesmen serta jadwal asesmen",
 *                                 "is_checked": true,
 *                                 "sub_questions": {}
 *                             }
 *                         }
 *                     }
 *                 ),
 * 
 *                 @OA\Schema(
 *                     title="Contoh request body untuk Asesor (dengan sub_questions)",
 *                     example={
 *                         "subject": "asesor",
 *                         "answers": {
 *                             {
 *                                 "question_id": 10,
 *                                 "sub_questions": {
 *                                     {"sub_question_id": 1, "answer_text": "1"},
 *                                     {"sub_question_id": 2, "answer_text": "0"},
 *                                     {"sub_question_id": 3, "answer_text": "1"},
 *                                     {"sub_question_id": 4, "answer_text": "1"}
 *                                 }
 *                             },
 *                             {
 *                                 "question_id": 11,
 *                                 "sub_questions": {
 *                                     {"sub_question_id": 5, "answer_text": "1"},
 *                                     {"sub_question_id": 6, "answer_text": "1"},
 *                                     {"sub_question_id": 7, "answer_text": "0"},
 *                                     {"sub_question_id": 8, "answer_text": "1"}
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
 *             }
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response=200,
 *         description="Jawaban berhasil disimpan atau diperbarui",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Jawaban berhasil disimpan/diperbarui")
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal (contoh: question_id tidak ditemukan atau data tidak sesuai)",
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
