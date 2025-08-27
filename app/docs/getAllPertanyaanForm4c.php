<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form4c/soal",
 *     tags={"FORM 4 (ASESMEN)"},
 *     summary="Ambil semua soal Form 4C",
 *     description="API ini digunakan untuk mengambil soal Form 4D yang digunakan untuk melihat soal oleh asesor dalam penilaian.",
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         required=true,
 *         description="ID PK yang terkait dengan soal",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data pertanyaan berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Data pertanyaan berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="iuk_form_3_id", type="integer", example=10),
 *                     @OA\Property(property="urutan", type="integer", example=1),
 *                     @OA\Property(
 *                         property="question",
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=100),
 *                         @OA\Property(property="question_text", type="string", example="Jelaskan prosedur pelaksanaan kegiatan ini."),
 *                         @OA\Property(
 *                             property="question_choices",
 *                             type="array",
 *                             @OA\Items(
 *                                 @OA\Property(property="id", type="integer", example=200),
 *                                 @OA\Property(property="is_correct", type="boolean", example=false),
 *                                 @OA\Property(
 *                                     property="choice",
 *                                     type="object",
 *                                     @OA\Property(property="id", type="integer", example=300),
 *                                     @OA\Property(property="choice_label", type="string", example="A"),
 *                                     @OA\Property(property="choice_text", type="string", example="Jawaban pilihan A")
 *                                 )
 *                             )
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 additionalProperties=@OA\Schema(type="array", @OA\Items(type="string"))
 *             )
 *         )
 *     )
 * )
 */

 class getAllPertanyaanForm4c {}