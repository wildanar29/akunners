<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PkStatusModel;
use App\Models\PkProgressModel;
use Illuminate\Support\Facades\Log;
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
            $pk_id = $request->query('pk_id');

            if (!$asesi_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter asesi_id wajib diisi.',
                    'status_code' => 400,
                    'data' => null,
                ], 400);
            }

            // ---------------------------------------------------------
            // 🔍 Jika pk_id tidak diisi → cari pk_id atau parent_form_id
            //     yang status progresnya belum Completed
            // ---------------------------------------------------------
            if (!$pk_id) {

                $pk_id = BidangModel::where('asesi_id', $asesi_id)
                ->where(function ($q) {
                    $q->whereIn('pk_id', function ($sub) {
                        $sub->select('pk_id')
                            ->from('kompetensi_progres')
                            ->where(function ($s) {
                                $s->where('status', '!=', 'Completed')
                                ->orWhereNull('status');
                            });
                    });
                })
                ->orderBy('pk_id')
                ->value('pk_id');


                \Log::info('🔄 Auto-detect pk_id untuk asesi', [
                    'asesi_id' => $asesi_id,
                    'pk_id_terdeteksi' => $pk_id
                ]);

                if (!$pk_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada PK aktif (belum completed) untuk asesi ini.',
                        'status_code' => 404,
                        'data' => null,
                    ], 404);
                }
            }

            // ---------------------------------------------------------
            // 🔍 Ambil item BidangModel sesuai pk_id (baik manual atau auto)
            // ---------------------------------------------------------
            $item = BidangModel::where('asesi_id', $asesi_id)
                ->where('pk_id', $pk_id)
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

            // -----------------------------------------------------------------
            //  🚀 Logika asli Anda untuk mapping status tetap sama
            // -----------------------------------------------------------------

            $defaultForms = \App\Models\KompetensiTrack::distinct()
                ->pluck('form_type')
                ->filter()
                ->values()
                ->toArray();

            $status = array_fill_keys($defaultForms, null);

            $allProgres = \App\Models\KompetensiProgres::where(function ($q) use ($item) {
                    $q->where(function ($q2) use ($item) {
                            $q2->where('form_id', $item->form_1_id)
                            ->whereNull('parent_form_id');
                        })
                        ->orWhere('parent_form_id', $item->form_1_id);
                })
                ->select('id', 'form_id', 'parent_form_id', 'status', 'created_at')
                ->orderByRaw('CASE WHEN parent_form_id IS NULL THEN 0 ELSE 1 END')
                ->orderBy('id')
                ->get();

            foreach ($allProgres as $prog) {

                $form_type = \App\Models\KompetensiTrack::where('progres_id', $prog->id)
                    ->value('form_type');

                if (!$form_type || !array_key_exists($form_type, $status)) {
                    continue;
                }

                $status[$form_type] = $prog->status;
            }

            return response()->json([
                'success' => true,
                'message' => 'Status data retrieved successfully.',
                'data'    => $status,
                'status_code' => 200,
            ], 200);

        } catch (\Exception $e) {

            Log::error('❌ Error getFormStatusByAsesi', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

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