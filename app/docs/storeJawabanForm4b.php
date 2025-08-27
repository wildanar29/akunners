<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/form4b/jawaban",
 *     tags={"FORM 4 (ASESMEN)"},
 *     summary="Simpan Jawaban Form 4B",
 *     description="API ini digunakan untuk menyimpan jawaban form 4B yang diisi oleh asesor.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"form_1_id","user_id","jawaban"},
 *             @OA\Property(property="form_1_id", type="integer", example=1, description="ID Form 1"),
 *             @OA\Property(property="user_id", type="integer", example=101, description="ID User (Asesor)"),
 *             @OA\Property(
 *                 property="jawaban",
 *                 type="array",
 *                 @OA\Items(
 *                     required={"iuk_form3_id","pencapaian"},
 *                     @OA\Property(property="iuk_form3_id", type="integer", example=1001, description="ID IUK Form 3"),
 *                     @OA\Property(property="jawaban_asesi", type="string", example="Jawaban deskriptif dari asesi"),
 *                     @OA\Property(property="pencapaian", type="boolean", example=true),
 *                     @OA\Property(property="nilai", type="integer", example=85),
 *                     @OA\Property(property="catatan", type="string", example="Asesi cukup baik dalam menjawab pertanyaan")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Jawaban berhasil disimpan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Jawaban berhasil disimpan")
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Jawaban sudah ada sebelumnya",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Jawaban untuk IUK 1001 sudah ada dan tidak dapat disimpan ulang.")
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
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Gagal menyimpan data",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Gagal menyimpan data"),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[23000]: Integrity constraint violation...")
 *         )
 *     )
 * )
 */


 class storeJawabanForm4b {}