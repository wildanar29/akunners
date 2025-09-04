<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PkStatusModel;
use App\Models\PkProgressModel;
use App\Models\BidangModel;

class MenuController extends Controller
{
    public function getMenu(Request $request)
    {
        try {
            // 🔹 Validasi input pk_id & asesor_id
            $validator = \Validator::make($request->all(), [
                'pk_id'      => 'required|integer',
                'asesor_id'  => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors'  => $validator->errors(),
                    'status_code' => 422,
                    'data' => null,
                ], 422);
            }

            $pk_id     = $request->input('pk_id');
            $asesor_id = $request->input('asesor_id');

            // 🔹 Cari form_1_id berdasarkan pk_id di BidangModel
            $bidang = \App\Models\BidangModel::where('pk_id', $pk_id)
                ->select('form_1_id')
                ->first();

            if (!$bidang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Bidang tidak ditemukan untuk pk_id ini.',
                    'status_code' => 200,
                    'data' => [],
                ], 200);
            }

            $form_1_id = $bidang->form_1_id;

            // 🔹 Ambil semua progres (parent & child) berdasarkan form_1_id
            $progresIds = \App\Models\KompetensiProgres::where('form_id', $form_1_id)
                ->orWhere('parent_form_id', $form_1_id)
                ->pluck('id')
                ->toArray();

            if (empty($progresIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada progres terkait form_1_id ini.',
                    'status_code' => 200,
                    'data' => [],
                ], 200);
            }

            // 🔹 Ambil form_type dari KompetensiTrack berdasarkan progres_id
            $defaultForms = \App\Models\KompetensiTrack::whereIn('progres_id', $progresIds)
                ->distinct()
                ->pluck('form_type')
                ->filter()
                ->values()
                ->toArray();

            // 🔹 Bentuk hasil menu + sertakan pk_id & asesor_id
            $menus = [];
            foreach ($defaultForms as $form) {
                $menus[] = [
                    'pk_id'     => $pk_id,
                    'asesor_id' => $asesor_id,
                    'key'       => $form,
                    'menu_name' => str_replace('_', ' ', $form),
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Menu retrieved successfully.',
                'data'    => $menus,
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

    public function getSubmittedData(Request $request)
    {
        try {
            // 🔹 Validasi input
            $validator = \Validator::make($request->all(), [
                'pk_id'     => 'required|integer',
                'asesor_id' => 'required|integer',
                'key'       => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors'  => $validator->errors(),
                    'status_code' => 422,
                    'data' => null,
                ], 422);
            }

            $pk_id     = $request->input('pk_id');
            $asesor_id = $request->input('asesor_id');
            $key       = $request->input('key');

            $data = null;

            // 🔹 Jika key adalah form_1 → ambil dari BidangModel
            if ($key === 'form_1') {
                $data = \App\Models\BidangModel::where('pk_id', $pk_id)
                    ->where('asesor_id', $asesor_id)
                    ->where('status', 'Submitted')
                    ->get();
            }

            // 🔹 Jika key adalah form_2 → gunakan fungsi helper
            else if ($key === 'form_2') {
                $data = $this->getSubmittedForm2($pk_id, $asesor_id);
            }

            if (!$data || $data->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada data dengan status Submitted.',
                    'status_code' => 200,
                    'data' => [],
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully.',
                'status_code' => 200,
                'data' => $data,
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

    private function getSubmittedForm2($pk_id, $asesor_id)
    {
        // Ambil data di Form2 dengan status Submitted
        $form2 = \App\Models\Form2::where('asesor_id', $asesor_id)
            ->where('status', 'Submitted')
            ->get();

        if ($form2->isEmpty()) {
            return collect(); // kembalikan collection kosong biar konsisten
        }

        // Ambil user_jawab_form_2_id
        $userJawabIds = $form2->pluck('user_jawab_form_2_id')->toArray();

        if (empty($userJawabIds)) {
            return collect();
        }

        // Ambil no_id dari JawabanForm2Model
        $noIds = \App\Models\JawabanForm2Model::whereIn('user_jawab_form_2_id', $userJawabIds)
            ->pluck('no_id')
            ->toArray();

        if (empty($noIds)) {
            return collect();
        }

        // Cek pk_id dari SoalForm2Model
        $validSoal = \App\Models\SoalForm2Model::whereIn('no_id', $noIds)
            ->where('pk_id', $pk_id)
            ->get();

        return $validSoal;
    }




}