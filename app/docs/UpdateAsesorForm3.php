<?php

namespace App\Docs;
/**
 * @OA\Put(
 *     path="/form3/update/{form3_id}",
 *     summary="Approve Form 3 oleh Asesor",
 *     description="API ini digunakan oleh Asesor untuk meng-approve Form 3 setelah disetujui oleh Asesi. Menandai progres sebagai Completed dan menyimpan data asesor yang meng-approve.",
 *     tags={"FORM 3"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="form3_id",
 *         in="path",
 *         required=true,
 *         description="ID dari Form 3 yang akan diperbarui oleh asesor.",
 *         @OA\Schema(type="integer", example=12)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Form3 berhasil diperbarui dan progres ditandai Completed.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Form3 berhasil diperbarui dan progres ditandai Completed."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="form_3_id", type="integer", example=12),
 *                 @OA\Property(property="asesor_name", type="string", example="Drs. Andi Asesor"),
 *                 @OA\Property(property="asesor_date", type="string", format="date-time", example="2025-07-23T14:10:00"),
 *                 @OA\Property(property="no_reg", type="string", example="ASESOR-00123")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User belum login.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="User belum login."),
 *             @OA\Property(property="data", type="string", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="User bukan asesor.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="Anda tidak memiliki izin untuk mengisi bagian asesor."),
 *             @OA\Property(property="data", type="string", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Form3 tidak ditemukan atau no_reg asesor tidak ditemukan.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="Data Form3 tidak ditemukan."),
 *             @OA\Property(property="data", type="string", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan internal saat memperbarui data.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat memperbarui data."),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[23000]: Integrity constraint violation...")
 *         )
 *     )
 * )
 */

 class UpdateAsesorForm3 {}