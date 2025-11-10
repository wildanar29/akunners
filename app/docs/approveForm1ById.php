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
 *     dan progres/track akan diperbarui. 
 *     Dokumen opsional seperti Ijazah, STR, SIP, dan SPK juga dapat diperbarui bersamaan.",
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
 *         description="Data dokumen opsional yang dapat diperbarui bersamaan dengan approval",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="ijazah",
 *                 type="object",
 *                 nullable=true,
 *                 example={"nomor": "IJZ-2025-001", "tanggal": "2025-11-10"}
 *             ),
 *             @OA\Property(
 *                 property="str",
 *                 type="object",
 *                 nullable=true,
 *                 example={"nomor": "STR-778822", "masa_berlaku": "2027-11-10"}
 *             ),
 *             @OA\Property(
 *                 property="sip",
 *                 type="object",
 *                 nullable=true,
 *                 example={"nomor": "SIP-88990", "masa_berlaku": "2026-12-31"}
 *             ),
 *             @OA\Property(
 *                 property="spk",
 *                 type="object",
 *                 nullable=true,
 *                 example={"nomor": "SPK-7788", "penerbit": "RS Umum Jakarta"}
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
 *                         "ijazah": {"nomor": "IJZ-2025-001"},
 *                         "str": {"nomor": "STR-778822"}
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
