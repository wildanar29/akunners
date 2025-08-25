<?php

namespace App\Docs;
// /**
//  * @OA\Post(
//  *     path="/get-form1-byasesor",
//  *     summary="Ambil data form_1 berdasarkan user asesor",
//  *     tags={"FORM 1 (PENGAJUAN ASESMEN)"},
//  *     @OA\RequestBody(
//  *         required=true,
//  *         @OA\JsonContent(
//  *             required={"user_id"},
//  *             @OA\Property(property="user_id", type="string", example="123", description="ID user asesor"),
//  *             @OA\Property(property="status", type="string", example="approved", description="(Opsional) Status form")
//  *         )
//  *     ),
//  *     @OA\Response(
//  *         response=200,
//  *         description="Berhasil mengambil data form_1",
//  *         @OA\JsonContent(
//  *             @OA\Property(property="success", type="string", example="OK"),
//  *             @OA\Property(property="message", type="string", example="Data form_1 berhasil diambil berdasarkan no_reg asesor."),
//  *             @OA\Property(property="no_reg", type="string", example="ASE-001"),
//  *             @OA\Property(
//  *                 property="data",
//  *                 type="array",
//  *                 @OA\Items(
//  *                     type="object",
//  *                     example={"id": 1, "no_reg": "ASE-001", "status": "approved", "created_at": "2025-06-01T00:00:00"}
//  *                 )
//  *             )
//  *         )
//  *     ),
//  *     @OA\Response(
//  *         response=400,
//  *         description="Parameter user_id wajib diisi",
//  *         @OA\JsonContent(
//  *             @OA\Property(property="success", type="string", example="ERR"),
//  *             @OA\Property(property="message", type="string", example="Parameter user_id wajib diisi.")
//  *         )
//  *     ),
//  *     @OA\Response(
//  *         response=403,
//  *         description="User ini bukan asesor",
//  *         @OA\JsonContent(
//  *             @OA\Property(property="success", type="string", example="OK"),
//  *             @OA\Property(property="message", type="string", example="User ini bukan asesor.")
//  *         )
//  *     ),
//  *     @OA\Response(
//  *         response=404,
//  *         description="No Registrasi tidak ditemukan",
//  *         @OA\JsonContent(
//  *             @OA\Property(property="success", type="string", example="ERR"),
//  *             @OA\Property(property="message", type="string", example="No Registrasi tidak ditemukan untuk asesor ini.")
//  *         )
//  *     )
//  * )
//  */



 class GetForm1ByAsesor {}