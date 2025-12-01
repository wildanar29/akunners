<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form9/{form9Id}/save-jawaban",
 *     tags={"FORM 9 (UMPAN BALIK)"},
 *     summary="Simpan atau perbarui jawaban Form 9 (oleh Asesor atau Asesi)",
 *     description="
**PERBEDAAN FORMAT BODY:**

🔹 **ASESI**
- Mengirim: `answer_text`, `is_checked`
- `sub_questions` dan `notes` **tidak digunakan**

🔹 **ASESOR**
- Mengirim: `answer_text` (opsional)
- `sub_questions[]` wajib berisi: `sub_question_id`, `answer_text`, `notes` (opsional)
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
 *         description="Untuk Asesi hanya kirim answer_text + is_checked. Sub_questions tidak dipakai.",
 *         @OA\JsonContent(
 *             type="object",
 *             example={
 *                 "subject"="asesor",
 *                 "answers"={
 *                     {
 *                         "question_id"=10,
 *                         "sub_questions"={
 *                             { "sub_question_id"=1, "answer_text"="1", "notes"="Catatan sub 1" },
 *                             { "sub_question_id"=2, "answer_text"="0", "notes"="Catatan sub 2" }
 *                         }
 *                     },
 *                     {
 *                         "question_id"=13,
 *                         "answer_text"="Bukti asesmen konsisten terhadap dimensi kompetensi",
 *                         "sub_questions"={}
 *                     }
 *                 }
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
