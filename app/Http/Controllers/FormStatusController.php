<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PkStatusModel;
use App\Models\PkProgressModel;
use App\Models\BidangModel;

        /**
     * @OA\Get(
     *     path="/get-indikator-status",
     *     summary="Ambil semua status form berdasarkan asesi",
     *     description="Mengembalikan status semua form yang terkait dengan asesi_id.",
     *     operationId="getFormStatusByAsesi",
     *     tags={"Form Status"},
     *     @OA\Parameter(
     *         name="asesi_id",
     *         in="query",
     *         required=true,
     *         description="ID Asesi",
     *         @OA\Schema(type="integer", example=123)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status data retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Status data retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 additionalProperties=@OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="asesi_id", type="integer", example=123),
     *                     @OA\Property(property="asesi_name", type="string", example="John Doe"),
     *                     @OA\Property(property="asesi_date", type="string", format="date-time", example="2025-09-03 10:00:00"),
     *                     @OA\Property(property="asesor_id", type="integer", example=45),
     *                     @OA\Property(property="asesor_name", type="string", example="Jane Smith"),
     *                     @OA\Property(property="asesor_date", type="string", format="date-time", example="2025-09-03 11:00:00"),
     *                     @OA\Property(
     *                         property="status",
     *                         type="object",
     *                         additionalProperties=@OA\Schema(type="string", example="Completed"),
     *                         description="Key adalah form_type, value adalah status"
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Parameter asesi_id wajib diisi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Parameter asesi_id wajib diisi."),
     *             @OA\Property(property="status_code", type="integer", example=400),
     *             @OA\Property(property="data", type="string", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data tidak tersedia",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Data tidak tersedia."),
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="data", type="string", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error occurred while retrieving data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred while retrieving data."),
     *             @OA\Property(property="error", type="string", example="SQL error or exception message"),
     *             @OA\Property(property="status_code", type="integer", example=500),
     *             @OA\Property(property="data", type="string", example=null)
     *         )
     *     )
     * )
     */


class FormStatusController extends Controller
{
    public function getFormStatusByAsesi(Request $request)
    {
        try {
            $asesi_id = $request->query('asesi_id');

            if (!$asesi_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter asesi_id wajib diisi.',
                    'status_code' => 400,
                    'data' => null,
                ], 400);
            }

            // Ambil satu data bidang berdasarkan asesi_id
            $item = BidangModel::where('asesi_id', $asesi_id)
                ->select([
                    'pk_id',
                    'form_1_id',
                    'asesi_name',
                    'asesi_id',
                    'asesi_date',
                    'asesor_id',
                    'asesor_name',
                    'asesor_date',
                ])
                ->first();

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak tersedia.',
                    'status_code' => 200,
                    'data' => null,
                ], 200);
            }

            // 🔹 Ambil semua daftar form_type dari KompetensiTrack (unik)
            $defaultForms = \App\Models\KompetensiTrack::distinct()
                ->pluck('form_type')
                ->filter() // buang null
                ->values()
                ->toArray();

            // 🔹 Inisialisasi semua form dengan null
            $status = array_fill_keys($defaultForms, null);

            // Ambil semua progres terkait form_1_id
            $allProgres = \App\Models\KompetensiProgres::where('form_id', $item->form_1_id)
                ->orWhere('parent_form_id', $item->form_1_id)
                ->select('id', 'status')
                ->get();

            foreach ($allProgres as $prog) {
                // Ambil form_type dari KompetensiTrack
                $form_type = \App\Models\KompetensiTrack::where('progres_id', $prog->id)
                    ->value('form_type');

                if ($form_type && array_key_exists($form_type, $status)) {
                    $status[$form_type] = $prog->status;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Status data retrieved successfully.',
                'data'    => $status,
                'status_code' => 200,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving data.',
                'error' => $e->getMessage(),
                'status_code' => 500,
                'data' => null,
            ], 500);
        }
    }
}