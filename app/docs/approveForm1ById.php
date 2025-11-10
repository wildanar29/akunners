<?php

namespace App\Docs;

/**
 * @OA\Put(
 *     path="/form1/approve/{form_1_id}",
 *     tags={"FORM 1 (PENGAJUAN ASESMEN)"},
 *     summary="Menyetujui Form 1 oleh asesor yang ditugaskan",
 *     operationId="approveForm1ById",
 *     description="Endpoint ini digunakan oleh asesor untuk menyetujui Form 1 yang berstatus 'Assigned'. 
 *     Setelah disetujui, status Form 1 akan berubah menjadi 'InAssessment', Form 2 otomatis dibuat, 
 *     progres dan track akan diperbarui, serta dokumen opsional (Ijazah, STR, SIP, SPK) dapat diperbarui
 *     dengan status validasinya. Setiap dokumen menggunakan field `id` sebagai pengenal universal.",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="form_1_id",
 *         in="path",
 *         required=true,
 *         description="ID Form 1 yang akan disetujui",
 *         @OA\Schema(type="integer", example=123)
 *     ),
 *
 *     @OA\RequestBody(
 *         required=false,
 *         description="Data validasi dokumen opsional yang dapat diperbarui bersamaan dengan approval",
 *         @OA\JsonContent(
 *             type="object",
 *
 *             @OA\Property(
 *                 property="ijazah",
 *                 type="object",
 *                 nullable=true,
 *                 @OA\Property(property="id", type="integer", example=45, description="ID dokumen Ijazah"),
 *                 @OA\Property(property="valid", type="boolean", example=true),
 *                 @OA\Property(property="authentic", type="boolean", example=true),
 *                 @OA\Property(property="current", type="boolean", example=false),
 *                 @OA\Property(property="sufficient", type="boolean", example=true)
 *             ),
 *
 *             @OA\Property(
 *                 property="str",
 *                 type="object",
 *                 nullable=true,
 *                 @OA\Property(property="id", type="integer", example=21, description="ID dokumen STR"),
 *                 @OA\Property(property="valid", type="boolean", example=true),
 *                 @OA\Property(property="authentic", type="boolean", example=true),
 *                 @OA\Property(property="current", type="boolean", example=true),
 *                 @OA\Property(property="sufficient", type="boolean", example=true)
 *             ),
 *
 *             @OA\Property(
 *                 property="sip",
 *                 type="object",
 *                 nullable=true,
 *                 @OA\Property(property="id", type="integer", example=14, description="ID dokumen SIP"),
 *                 @OA\Property(property="valid", type="boolean", example=true),
 *                 @OA\Property(property="authentic", type="boolean", example=false),
 *                 @OA\Property(property="current", type="boolean", example=true),
 *                 @OA\Property(property="sufficient", type="boolean", example=true)
 *             ),
 *
 *             @OA\Property(
 *                 property="spk",
 *                 type="object",
 *                 nullable=true,
 *                 @OA\Property(property="id", type="integer", example=8, description="ID dokumen SPK"),
 *                 @OA\Property(property="valid", type="boolean", example=true),
 *                 @OA\Property(property="authentic", type="boolean", example=true),
 *                 @OA\Property(property="current", type="boolean", example=true),
 *                 @OA\Property(property="sufficient", type="boolean", example=false)
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Form 1 berhasil disetujui dan Form 2 dimulai",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Form 1 disetujui dan dokumen diperbarui (jika ada)."),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="form_1_id", type="integer", example=123),
 *                 @OA\Property(
 *                     property="dokumen",
 *                     type="object",
 *                     example={
 *                         "ijazah": {
 *                             "id": 45,
 *                             "valid": true,
 *                             "authentic": true,
 *                             "current": false,
 *                             "sufficient": true
 *                         },
 *                         "str": {
 *                             "id": 21,
 *                             "valid": true,
 *                             "authentic": true,
 *                             "current": true,
 *                             "sufficient": true
 *                         }
 *                     }
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=403,
 *         description="Form tidak ditemukan atau bukan asesor yang ditugaskan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="Data tidak ditemukan atau Anda bukan asesor yang ditugaskan.")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat memproses data",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat memproses data."),
 *             @OA\Property(property="error", type="string", example="Exception message here")
 *         )
 *     )
 * )
 */
class approveForm1ById {}
