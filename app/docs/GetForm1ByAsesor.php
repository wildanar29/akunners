<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/get-form1-byasesor",
 *     summary="Ambil daftar Form1 berdasarkan user_id asesor",
 *     tags={"FORM 1"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 required={"user_id"},
 *                 @OA\Property(
 *                     property="user_id",
 *                     type="integer",
 *                     example=123,
 *                     description="ID dari user asesor"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data form_1 berhasil diambil",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="string", example="OK"),
 *             @OA\Property(property="message", type="string", example="Data form_1 berhasil diambil berdasarkan no_reg asesor."),
 *             @OA\Property(property="no_reg", type="string", example="REG-2024-001"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="form_1_id", type="integer", example=1),
 *                     @OA\Property(property="user_id", type="integer", example=123),
 *                     @OA\Property(property="asesi_name", type="string", example="John Doe"),
 *                     @OA\Property(property="asesi_date", type="string", format="date", example="2024-05-01"),
 *                     @OA\Property(property="asesor_name", type="string", example="Jane Asesor"),
 *                     @OA\Property(property="asesor_date", type="string", format="date", example="2024-05-02"),
 *                     @OA\Property(property="no_reg", type="string", example="REG-2024-001"),
 *                     @OA\Property(property="status", type="string", enum={"Waiting", "Approved", "Cancel"}, example="Approved"),
 *                     @OA\Property(property="ijazah_id", type="integer", nullable=true),
 *                     @OA\Property(property="spk_id", type="integer", nullable=true),
 *                     @OA\Property(property="sip_id", type="integer", nullable=true),
 *                     @OA\Property(property="str_id", type="integer", nullable=true),
 *                     @OA\Property(property="ujikom_id", type="integer", nullable=true),
 *                     @OA\Property(property="sertifikat_id", type="integer", nullable=true),
 *                     @OA\Property(property="created_at", type="string", format="date-time"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time"),
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Parameter user_id tidak diisi",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="string", example="ERR"),
 *             @OA\Property(property="message", type="string", example="Parameter user_id wajib diisi.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="User bukan asesor",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="string", example="OK"),
 *             @OA\Property(property="message", type="string", example="User ini bukan asesor.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No Registrasi tidak ditemukan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="string", example="ERR"),
 *             @OA\Property(property="message", type="string", example="No Registrasi tidak ditemukan untuk asesor ini.")
 *         )
 *     )
 * )
 */



 class GetForm1ByAsesor {}