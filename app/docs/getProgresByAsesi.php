<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/progress/assessment/{asesi_id}",
 *     summary="Mengambil data progres asesmen berdasarkan asesi dan pk_id",
 *     tags={"Progress"},
 *     @OA\Parameter(
 *         name="asesi_id",
 *         in="path",
 *         description="ID dari Asesi",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="pk_id",
 *         in="query",
 *         description="ID dari Program Kerja (PK)",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data progres berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="message", type="string", example="Data progres berhasil diambil."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="form_1_id", type="integer", example=1),
 *                     @OA\Property(property="asesi_name", type="string", example="Budi"),
 *                     @OA\Property(property="asesi_id", type="integer", example=101),
 *                     @OA\Property(property="asesi_date", type="string", format="date", example="2025-07-01"),
 *                     @OA\Property(property="asesor_id", type="integer", example=5),
 *                     @OA\Property(property="asesor_name", type="string", example="Ibu Siti"),
 *                     @OA\Property(property="asesor_date", type="string", format="date", example="2025-07-02"),
 *                     @OA\Property(property="status_utama", type="string", example="Approved"),
 *                     @OA\Property(
 *                         property="progres",
 *                         type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="id", type="integer", example=10),
 *                             @OA\Property(property="form_id", type="integer", example=2),
 *                             @OA\Property(property="status", type="string", example="Approved"),
 *                             @OA\Property(property="form_type", type="string", example="Form 2")
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Parameter pk_id wajib diisi",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Parameter pk_id wajib diisi."),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Gagal mengambil data progres",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Gagal mengambil data progres: Kesalahan sistem"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 */


 class getProgresByAsesi {}