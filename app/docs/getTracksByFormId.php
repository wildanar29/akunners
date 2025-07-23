<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/api/tracks-by-form",
 *     summary="Ambil log aktivitas asesmen berdasarkan form_id",
 *     description="API ini digunakan untuk mengambil log atau jejak aktivitas asesmen berdasarkan form_id. Data yang dikembalikan berupa daftar aktivitas dari tabel kompetensi_tracks berdasarkan progres_id yang diambil dari tabel kompetensi_progres.",
 *     tags={"PROGRESS"},
 *     @OA\Parameter(
 *         name="form_id",
 *         in="query",
 *         required=true,
 *         description="ID form yang digunakan untuk mencari progres dan track aktivitas",
 *         @OA\Schema(
 *             type="integer",
 *             example=123
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data tracks",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="OK"),
 *             @OA\Property(property="message", type="string", example="Data tracks berhasil diambil."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="progres_id", type="integer", example=1),
 *                     @OA\Property(property="form_type", type="string", example="form_3"),
 *                     @OA\Property(property="activity", type="string", example="Completed"),
 *                     @OA\Property(property="activity_time", type="string", format="date-time", example="2025-07-22T12:00:00"),
 *                     @OA\Property(property="description", type="string", example="Selesai dinilai"),
 *                     @OA\Property(property="created_at", type="string", format="date-time"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Parameter form_id tidak diisi",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Parameter form_id wajib diisi."),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data progres tidak ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Data progres tidak ditemukan untuk form_id tersebut."),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Kesalahan internal server",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ERROR"),
 *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat mengambil data tracks."),
 *             @OA\Property(property="error", type="string", example="Exception message")
 *         )
 *     )
 * )
 */

 class getTracksByFormId {}