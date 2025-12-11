<?php

namespace App\Docs;
/**
 * @OA\Get(
 *     path="/progress/assessment",
 *     summary="Mengambil data progres asesmen berdasarkan asesi dan pk_id",
 *     tags={"PROGRESS"},
 *
 *     @OA\Parameter(
 *         name="asesi_id",
 *         in="query",
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
 *
 *     @OA\Response(
 *         response=200,
 *         description="Data progres berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="SUCCESS"),
 *             @OA\Property(property="message", type="string", example="Data progres berhasil diambil."),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="form_1_id", type="integer", example=115),
 *
 *                 @OA\Property(property="asesi_name", type="string", example="Amin5"),
 *                 @OA\Property(property="asesi_id", type="integer", example=102),
 *                 @OA\Property(property="asesi_date", type="string", format="date-time", example="2025-12-03T00:00:00.000000Z"),
 *
 *                 @OA\Property(property="end_date", type="string", format="date", example="2026-01-02"),
 *                 @OA\Property(property="end_date_status", type="boolean", example=true),
 *
 *                 @OA\Property(property="asesi_email", type="string", example="testing_amin5@mailnesia.com"),
 *                 @OA\Property(property="asesi_no_telp", type="string", example="+62898981543"),
 *                 @OA\Property(property="asesi_foto", type="string", example="http://localhost/storage/foto.jpg"),
 *
 *                 @OA\Property(property="asesor_name", type="string", example="asesor_testing"),
 *                 @OA\Property(property="asesor_id", type="integer", example=24),
 *                 @OA\Property(property="asesor_email", type="string", example="123@gmail.com"),
 *                 @OA\Property(property="asesor_no_telp", type="string", example="08966579000"),
 *                 @OA\Property(property="asesor_foto", type="string", example="http://localhost/storage/asesor.jpg"),
 *
 *                 @OA\Property(property="pk_id", type="integer", example=1),
 *                 @OA\Property(property="status_utama", type="string", example="Approved"),
 *
 *                 @OA\Property(
 *                     property="tracks_utama",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1575),
 *                         @OA\Property(property="progres_id", type="integer", example=303),
 *                         @OA\Property(property="form_type", type="string", example="form_1"),
 *                         @OA\Property(property="activity", type="string", example="Assigned"),
 *                         @OA\Property(property="activity_time", type="string", format="date-time", example="2025-12-03 08:31:42"),
 *                         @OA\Property(property="description", type="string", example="Pengajuan Asesmen diterima."),
 *                         @OA\Property(property="created_at", type="string", example="2025-12-03 08:31:42"),
 *                         @OA\Property(property="updated_at", type="string", example="2025-12-03 08:31:42")
 *                     )
 *                 ),
 *
 *                 @OA\Property(
 *                     property="progres",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=500),
 *                         @OA\Property(property="form_id", type="integer", example=10),
 *                         @OA\Property(property="status", type="string", example="Approved"),
 *                         @OA\Property(property="pk_id", type="integer", example=1),
 *                         @OA\Property(property="form_type", type="string", example="form_4b"),
 *
 *                         @OA\Property(
 *                             property="tracks",
 *                             type="array",
 *                             @OA\Items(
 *                                 @OA\Property(property="id", type="integer", example=1900),
 *                                 @OA\Property(property="progres_id", type="integer", example=500),
 *                                 @OA\Property(property="form_type", type="string", example="form_4b"),
 *                                 @OA\Property(property="activity", type="string", example="Submitted"),
 *                                 @OA\Property(property="activity_time", type="string", example="2025-12-03 09:00:00"),
 *                                 @OA\Property(property="description", type="string", example="Form dikumpulkan")
 *                             )
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=400,
 *         description="Parameter asesi_id dan pk_id wajib diisi",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Parameter asesi_id dan pk_id wajib diisi."),
 *             @OA\Property(property="data", type="null", example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Gagal mengambil data progres",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Gagal mengambil data progres: Kesalahan sistem"),
 *             @OA\Property(property="data", type="null", example=null)
 *         )
 *     )
 * )
 */


 class getProgresByAsesi {}