<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/jawaban-form2/update/asesor",
 *     tags={"FORM 2"},
 *     summary="Update Penilaian Form 2 oleh Asesor",
 *     description="API ini digunakan untuk asesor menilai self assessment asesi atau form 2. Asesor dapat mengisi secara acak soal mana yang ingin diisi. Apabila terdapat soal yang tidak diisi maka penilaian asesor akan diambil dari penilaian asesi sebelumnya.",
 *     operationId="updateJawabanForm2ByNoId",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"data"},
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     required={"jawab_form_2_id", "k_asesor", "bk_asesor"},
 *                     @OA\Property(property="jawab_form_2_id", type="integer", example=12),
 *                     @OA\Property(property="k_asesor", type="boolean", example=true),
 *                     @OA\Property(property="bk_asesor", type="boolean", example=false)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengupdate penilaian",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Update selesai."),
 *             @OA\Property(property="updated_count", type="integer", example=3),
 *             @OA\Property(
 *                 property="not_found",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="jawab_form_2_id", type="integer", example=99)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Akses ditolak. Hanya pengguna dengan role Asesor yang dapat mengupdate data.",
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
 *             @OA\Property(
 *                 property="errors",
 *                 type="object"
 *             )
 *         )
 *     )
 * )
 */

 class updateJawabanForm2ByNoId {}