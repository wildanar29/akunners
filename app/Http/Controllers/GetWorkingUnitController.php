<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkingUnit;

class GetWorkingUnitController extends Controller
{

    /**
 * @OA\Get(
 *     path="/get-list-working-units",
 *     summary="Menampilkan semua data Working Unit",
 *     description="Mengambil daftar semua Working Unit yang tersedia dalam database.",
 *     tags={"Working Unit"},
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil daftar Working Unit",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Data Working Unit berhasil diambil"),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="nama_unit", type="string", example="Unit Keperawatan"),
 *                     @OA\Property(property="deskripsi", type="string", example="Unit yang bertanggung jawab atas perawatan pasien.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Terjadi kesalahan server",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Internal Server Error")
 *         )
 *     )
 * )
 */

    // Ambil semua data
    public function index()
    {
        $data = WorkingUnit::all()->makeHidden(['working_area_id']); // Sembunyikan working_area_id
        return response()->json([
            'success' => true,
            'message' => 'Data Working Unit Accept',
            'data' => $data
        ], 200);
    }


}
