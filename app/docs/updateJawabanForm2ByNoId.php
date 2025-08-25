<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/jawaban-form2/update/asesor",
 *     tags={"FORM 2 ASESMEN MANDIRI"},
 *     summary="Update Penilaian Asesor untuk Form 2",
 *     description="API ini digunakan untuk melakukan update penilaian oleh asesor. Digunakan saat asesor mewawancarai asesi pada konsultasi pra asesmen dan memastikan nilai yang diinput oleh asesi adalah benar.",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"form_2_id", "data"},
 *             @OA\Property(property="form_2_id", type="integer", example=38),
 *             @OA\Property(property="pk_id", type="integer", example=5, nullable=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     required={"jawab_form_2_id", "k_asesor", "bk_asesor"},
 *                     @OA\Property(property="jawab_form_2_id", type="integer", example=1014),
 *                     @OA\Property(property="k_asesor", type="boolean", example=true),
 *                     @OA\Property(property="bk_asesor", type="boolean", example=false)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Update berhasil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Update selesai."),
 *             @OA\Property(property="updated_count", type="integer", example=2),
 *             @OA\Property(
 *                 property="not_found",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="jawab_form_2_id", type="integer", example=1234)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Akses ditolak (bukan asesor)",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="Akses ditolak. Hanya pengguna dengan role Asesor yang dapat mengupdate data.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 */


 class updateJawabanForm2ByNoId {}