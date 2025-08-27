<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/form7/keputusan/{pkId}/{form1Id}",
 *     tags={"FORM 7 (PENGUMPULAN BUKTI)"},
 *     summary="Mengisi otomatis jawaban Form 7",
 *     description="API ini digunakan untuk mengisi otomatis jawaban untuk form 7",
 *     @OA\Parameter(
 *         name="pkId",
 *         in="path",
 *         required=true,
 *         description="ID PK (paket kompetensi) untuk menentukan soal/jawaban",
 *         @OA\Schema(type="integer", example=12)
 *     ),
 *     @OA\Parameter(
 *         name="form1Id",
 *         in="path",
 *         required=true,
 *         description="ID Form 1 (induk) yang terkait dengan Form 7",
 *         @OA\Schema(type="integer", example=1001)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Jawaban Form 7 berhasil diisi otomatis",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="form4a",
 *                     type="array",
 *                     @OA\Items(type="object", example={"iuk_id": 1, "keputusan": "Kompeten"})
 *                 ),
 *                 @OA\Property(
 *                     property="form4b",
 *                     type="array",
 *                     @OA\Items(type="object", example={"iuk_id": 2, "keputusan": "Belum Kompeten"})
 *                 ),
 *                 @OA\Property(
 *                     property="form4c",
 *                     type="array",
 *                     @OA\Items(type="object", example={"iuk_id": 3, "keputusan": "Kompeten"})
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan saat memproses Form 7",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Gagal memproses Form 7: ...")
 *         )
 *     )
 * )
 */


 class getAllKeputusanForm7 {}