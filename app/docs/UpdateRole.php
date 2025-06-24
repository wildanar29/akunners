<?php

namespace App\Docs;
/**
 * @OA\Post(
 *     path="/user/update-role",
 *     summary="Ubah role user",
 *     tags={"Akun"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id", "role_id"},
 *             @OA\Property(property="user_id", type="integer", example=12, description="ID user yang akan diubah rolenya"),
 *             @OA\Property(property="role_id", type="integer", example=2, description="ID role baru (contoh: 2 = Asesor)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengubah role user",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Role user berhasil diubah."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="user_id", type="integer", example=12),
 *                 @OA\Property(property="nama", type="string", example="Siti Nur"),
 *                 @OA\Property(property="role_id_baru", type="integer", example=2)
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validasi gagal."),
 *             @OA\Property(property="errors", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=400)
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="User bukan asesor aktif",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User belum terdaftar sebagai asesor aktif."),
 *             @OA\Property(property="status_code", type="integer", example=403)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error server",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat mengubah role."),
 *             @OA\Property(property="error", type="string", example="Exception message"),
 *             @OA\Property(property="status_code", type="integer", example=500)
 *         )
 *     )
 * )
 */

 class UpdateRole {}