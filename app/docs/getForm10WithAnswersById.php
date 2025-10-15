<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form10/soal-jawab/{form10Id}",
 *     tags={"FORM 10 (DAFTAR TILIK)"},
 *     summary="Ambil data Form 10 beserta jawaban dan kegiatan daftar tilik",
 *     description="Mengambil data lengkap Form 10 berdasarkan ID, termasuk kegiatan daftar tilik, subkegiatan, dan jawaban asesor.",
 *
 *     @OA\Parameter(
 *         name="form10Id",
 *         in="path",
 *         required=true,
 *         description="ID Form 10 yang ingin diambil",
 *         @OA\Schema(type="integer", example=7)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Data Form 10 berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Data Form 10 berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="form_10_id", type="integer", example=7),
 *                 @OA\Property(property="pk_id", type="integer", example=12),
 *                 @OA\Property(property="asesi_id", type="integer", example=45),
 *                 @OA\Property(property="asesor_id", type="integer", example=8),
 *                 @OA\Property(
 *                     property="soal",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=15),
 *                         @OA\Property(property="kegiatan", type="string", example="Melakukan pemeriksaan tanda vital pasien"),
 *                         @OA\Property(property="isTitle", type="boolean", example=false),
 *                         @OA\Property(
 *                             property="jawaban",
 *                             type="array",
 *                             @OA\Items(
 *                                 type="object",
 *                                 @OA\Property(property="dilakukan", type="boolean", example=true),
 *                                 @OA\Property(property="catatan", type="string", example="Sudah dilakukan dengan benar")
 *                             )
 *                         ),
 *                         @OA\Property(
 *                             property="children",
 *                             type="array",
 *                             @OA\Items(
 *                                 type="object",
 *                                 @OA\Property(property="id", type="integer", example=16),
 *                                 @OA\Property(property="kegiatan", type="string", example="Mengukur tekanan darah"),
 *                                 @OA\Property(property="isTitle", type="boolean", example=false),
 *                                 @OA\Property(
 *                                     property="jawaban",
 *                                     type="array",
 *                                     @OA\Items(
 *                                         type="object",
 *                                         @OA\Property(property="dilakukan", type="boolean", example=true),
 *                                         @OA\Property(property="catatan", type="string", example="Nilai tekanan darah normal")
 *                                     )
 *                                 )
 *                             )
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Form 10 tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Data Form 10 tidak ditemukan"),
 *             @OA\Property(property="data", type="string", example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat mengambil data Form 10",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat mengambil data Form 10"),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[42S22]: Column not found: ..."),
 *             @OA\Property(property="data", type="string", example=null)
 *         )
 *     )
 * )
 */



 class getForm10WithAnswersById {}