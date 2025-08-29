<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PkStatusModel;
use App\Models\PkProgressModel;

    /**
     * @OA\Get(
     *     path="/get-indikator-status/{user_id}",
     *     summary="List Form Status untuk membuka Indikator Pk",
     *     description="Pencarian Berdasarkan user_id",
     *     operationId="getFormStatusByUser",
     *     tags={"Form Status"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status data retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Status data retrieved successfully."),
     *             @OA\Property(
     *                 property="status",
     *                 type="object",
     *                 @OA\Property(property="form_1_status", type="string", nullable=true, example="Completed"),
     *                 @OA\Property(property="form_2_status", type="string", nullable=true, example="Open"),
     *                 @OA\Property(property="form_3_status", type="string", nullable=true, example="null"),
     *                 @OA\Property(property="form_4_status", type="string", nullable=true, example="null"),
     *                 @OA\Property(property="form_5_status", type="string", nullable=true, example=null),
     *                 @OA\Property(property="form_6_status", type="string", nullable=true, example=null),
     *                 @OA\Property(property="form_7_status", type="string", nullable=true, example="null"),
     *                 @OA\Property(property="form_8_status", type="string", nullable=true, example="null"),
     *                 @OA\Property(property="form_9_status", type="string", nullable=true, example="null"),
     *                 @OA\Property(property="form_10_status", type="string", nullable=true, example="null"),
     *                 @OA\Property(property="form_11_status", type="string", nullable=true, example="null"),
     *                 @OA\Property(property="form_12_status", type="string", nullable=true, example="null")
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No progress or status found for this user ID",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No progress found for this user ID."),
     *             @OA\Property(property="status_code", type="integer", example=404)
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
     *             @OA\Property(property="status_code", type="integer", example=500)
     *         )
     *     )
     * )
     */

class FormStatusController extends Controller
{
    public function getFormStatusByUser($user_id)
    {
        try {
            // Ambil progress_id berdasarkan user_id
            $progress = PkProgressModel::where('user_id', $user_id)->first();

            // Jika progress_id tidak ditemukan
            if (!$progress) {
                return response()->json([
                    'success' => false,
                    'message' => 'No progress found for this user ID.',
                    'status_code' => 200,
                    'data' => null
                ], 200);
            }

            // Ambil data status berdasarkan progress_id
            $statusData = PkStatusModel::where('progress_id', $progress->progress_id)->first();

            // Jika status tidak ditemukan
            if (!$statusData) {
                return response()->json([
                    'success' => false,
                    'message' => 'No status found for this user ID.',
                    'status_code' => 200,
                    'data' => null
                ], 200);
            }

            // Struktur response JSON hanya form_x_status
            return response()->json([
                'success' => true,
                'message' => 'Status data retrieved successfully.',
                'status' => [
                    'form_1_status' => $statusData->form_1_status ?? null,
                    'form_2_status' => $statusData->form_2_status ?? null,
                    'form_3_status' => $statusData->form_3_status ?? null,
                    'form_4_status' => $statusData->form_4_status ?? null,
                    'form_5_status' => $statusData->form_5_status ?? null,
                    'form_6_status' => $statusData->form_6_status ?? null,
                    'form_7_status' => $statusData->form_7_status ?? null,
                    'form_8_status' => $statusData->form_8_status ?? null,
                    'form_9_status' => $statusData->form_9_status ?? null,
                    'form_10_status' => $statusData->form_10_status ?? null,
                    'form_11_status' => $statusData->form_11_status ?? null,
                    'form_12_status' => $statusData->form_12_status ?? null,
                ],
                'status_code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving data.',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
}