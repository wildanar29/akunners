<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JabatanModel;

class JabatanController extends Controller
{
    /**
     * Menampilkan semua data jabatan.
     */

     /**
 * @OA\Get(
 *     path="/get-list-jabatan",
 *     summary="Menampilkan semua data jabatan",
 *     description="Mengambil daftar semua jabatan yang tersedia dalam database.",
 *     tags={"List Jabatan"},
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil daftar jabatan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="List of Jabatan"),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="jabatan_id", type="integer", example=1),
 *                     @OA\Property(property="nama_jabatan", type="string", example="Perawat"),
 *                     @OA\Property(property="deskripsi", type="string", example="Bertanggung jawab atas perawatan pasien.")
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
    public function getAllJabatan()
    {
        $jabatan = JabatanModel::all();
        
        return response()->json([
            'success' => true,
            'message' => 'List of Jabatan',
            'data' => $jabatan
        ], 200);
    }

    /**
     * Menampilkan jabatan berdasarkan ID.
     */
    public function getJabatanById($id)
    {
        $jabatan = JabatanModel::find($id);
        
        if (!$jabatan) {
            return response()->json([
                'success' => false,
                'message' => 'Jabatan not found',
                'data' => null
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Jabatan detail',
            'data' => $jabatan
        ], 200);
    }
}