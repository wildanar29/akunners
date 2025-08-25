<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/jawaban-asesi",
 *     summary="Submit atau update jawaban self assessment oleh Asesi",
 *     tags={"FORM 2 (ASESMEN MANDIRI)"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"form_2_id", "jawaban"},
 *             @OA\Property(
 *                 property="form_2_id",
 *                 type="integer",
 *                 example=1,
 *                 description="ID Form 2 yang sedang diisi oleh Asesi"
 *             ),
 *             @OA\Property(
 *                 property="jawaban",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     required={"no_id", "k", "bk"},
 *                     @OA\Property(property="no_id", type="integer", example=101, description="ID soal Form 2"),
 *                     @OA\Property(property="k", type="boolean", example=true, description="Kompeten (true/false)"),
 *                     @OA\Property(property="bk", type="boolean", example=false, description="Belum Kompeten (true/false)")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Jawaban berhasil disimpan atau diperbarui",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Jawaban berhasil disimpan atau diperbarui."),
 *             @OA\Property(property="penilaian_asesi", type="number", format="float", example=85.5),
 *             @OA\Property(property="total_k", type="integer", example=10),
 *             @OA\Property(property="total_bk", type="integer", example=2),
 *             @OA\Property(property="form_2_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Form tidak dapat diproses. Status InAssessment pada Form 1 tidak ditemukan.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Form tidak dapat diproses. Status InAssessment pada Form 1 tidak ditemukan."),
 *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data form_1 tidak ditemukan untuk user ini",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Data form_1 tidak ditemukan untuk user ini")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi input gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(
 *                     property="form_2_id",
 *                     type="array",
 *                     @OA\Items(type="string", example="The form_2_id field is required.")
 *                 ),
 *                 @OA\Property(
 *                     property="jawaban.0.no_id",
 *                     type="array",
 *                     @OA\Items(type="string", example="The jawaban.0.no_id field is required.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat menyimpan jawaban.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat menyimpan jawaban."),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row...")
 *         )
 *     )
 * )
 */


 class JawabanAsesi {}