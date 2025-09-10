<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PkStatusModel;
use App\Models\PkProgressModel;
use App\Models\BidangModel;
use App\Models\Form3Model;

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
                    'data' => [],
                ], 422);
            }

            $pk_id     = $request->input('pk_id');
            $asesor_id = $request->input('asesor_id');
            $key       = $request->input('key');

            $data = collect(); // default sebagai collection kosong

            // 🔹 Ambil data sesuai key (termasuk form_10.xxx)
            if (in_array($key, [
                'form_1', 'form_2', 'form_3', 'intv_pra_asesmen',
                'form_5', 'form_4a', 'form_4b', 'form_4c', 'form_4d',
                'form_6', 'form_7', 'form_8', 'form_9', 'form_12'
            ]) || str_starts_with($key, 'form_10')) {
                $data = $this->getSubmittedForm($key, $pk_id, $asesor_id);

                // Jika hasilnya object tunggal, ubah jadi array object
                if ($data && !($data instanceof \Illuminate\Support\Collection)) {
                    $data = collect([$data]);
                }
            }

            if (!$data || $data->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada data yang ditemukan.',
                    'status_code' => 200,
                    'data' => [],
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully.',
                'status_code' => 200,
                'data' => $data->values()->toArray(), // ✅ selalu array of object
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving data.',
                'error' => $e->getMessage(),
                'status_code' => 500,
                'data' => [],
            ], 500);
        }
    }


    private function getSubmittedForm($formType, $pk_id, $asesor_id)
    {
        $formConfig = [
            'form_1' => [
                'model' => \App\Models\BidangModel::class,
                'pk'    => 'form_1_id',
                'filters' => [
                    ['pk_id', '=', $pk_id],
                    ['asesor_id', '=', $asesor_id],
                ],
                'status' => 'Submitted',
            ],
            'form_2' => [
                'model' => \App\Models\Form2::class,
                'pk'    => 'form_2_id',
                'filters' => [
                    ['pk_id', '=', $pk_id],
                    ['no_reg', '=', optional(\App\Models\DataAsesorModel::find($asesor_id))->no_reg],
                ],
                'status' => 'Submitted',
            ],
            'form_3' => [
                'model' => \App\Models\Form3Model::class,
                'pk'    => 'form_3_id',
                'filters' => [
                    ['no_reg', '=', optional(\App\Models\DataAsesorModel::find($asesor_id))->no_reg],
                ],
                'status' => 'Submitted',
            ],
            'intv_pra_asesmen' => [
                'model' => \App\Models\InterviewModel::class,
                'pk'    => 'interview_id',
                'filters' => [
                    ['pk_id', '=', $pk_id],
                    ['asesor_id', '=', $asesor_id],
                ],
                'status' => 'Submitted',
            ],
            'form_5' => [
                'model' => \App\Models\Form5::class,
                'pk'    => 'form_5_id',
                'filters' => [
                    ['pk_id', '=', $pk_id],
                    ['asesor_id', '=', $asesor_id],
                ],
                'status' => 'Submitted',
            ],
            'form_4a' => [
                'model' => \App\Models\Form4a::class,
                'pk'    => 'form_4a_id',
                'filters' => [
                    ['pk_id', '=', $pk_id],
                    ['asesor_id', '=', $asesor_id],
                ],
                'status' => 'Submitted',
            ],
            'form_4b' => [
                'model' => \App\Models\Form4b::class,
                'pk'    => 'form_4b_id',
                'filters' => [
                    ['pk_id', '=', $pk_id],
                    ['asesor_id', '=', $asesor_id],
                ],
                'status' => 'Submitted',
            ],
            'form_4c' => [
                'model' => \App\Models\Form4c::class,
                'pk'    => 'form_4c_id',
                'filters' => [
                    ['pk_id', '=', $pk_id],
                    ['asesor_id', '=', $asesor_id],
                ],
                'status' => 'Submitted',
            ],
            'form_4d' => [
                'model' => \App\Models\Form4d::class,
                'pk'    => 'form_4d_id',
                'filters' => [
                    ['pk_id', '=', $pk_id],
                    ['asesor_id', '=', $asesor_id],
                ],
                'status' => 'Submitted',
            ],
            'form_6' => [
                'model' => \App\Models\Form4d::class,
                'pk'    => 'form_6_id',
                'filters' => [
                    ['pk_id', '=', $pk_id],
                    ['asesor_id', '=', $asesor_id],
                ],
                'status' => 'Submitted',
            ],
            'form_7' => [
                'model' => \App\Models\Form4d::class,
                'pk'    => 'form_7_id',
                'filters' => [
                    ['pk_id', '=', $pk_id],
                    ['asesor_id', '=', $asesor_id],
                ],
                'status' => 'Submitted',
            ],
            'form_8' => [
                'model' => \App\Models\Form8::class,
                'pk'    => 'form_8_id',
                'filters' => [
                    ['pk_id', '=', $pk_id],
                    ['asesor_id', '=', $asesor_id],
                ],
                'status' => 'Submitted',
            ],
            'form_9' => [
                'model' => \App\Models\Form9::class,
                'pk'    => 'form_9_id',
                'filters' => [
                    ['pk_id', '=', $pk_id],
                    ['asesor_id', '=', $asesor_id],
                ],
                'status' => 'Submitted',
            ],
            'form_12' => [
                'model' => \App\Models\Form12::class,
                'pk'    => 'form_12_id',
                'filters' => [
                    ['pk_id', '=', $pk_id],
                    ['asesor_id', '=', $asesor_id],
                ],
                'status' => 'Submitted',
            ],
            // 🔹 Generic config untuk semua form_10.xxx
            'form_10' => [
                'model' => \App\Models\Form10::class,
                'pk'    => 'form_10_id',
                'filters' => [
                    ['pk_id', '=', $pk_id],
                    ['asesor_id', '=', $asesor_id],
                    // nanti tambah filter form_type dinamis
                ],
                'status' => 'Submitted',
            ],
        ];

        // cek apakah ini form_10.xxx
        $baseType = explode('.', $formType)[0]; // ex: "form_10"
        $subType  = explode('.', $formType)[1] ?? null;

        if (!isset($formConfig[$baseType])) {
            \Log::warning("getSubmittedForm: Form type [$formType] tidak dikenali.");
            return collect();
        }

        $config = $formConfig[$baseType];

        // kalau ada subtype, tambahkan filter form_type
        if ($subType) {
            $config['filters'][] = ['form_type', '=', $formType];
        }

        // 🔹 Ambil data awal
        $query = $config['model']::query();
        foreach ($config['filters'] as $filter) {
            if ($filter[2] !== null) {
                $query->where($filter[0], $filter[1], $filter[2]);
            }
        }

        $formList = $query->get();
        if ($formList->isEmpty()) {
            return collect();
        }

        // 🔹 Ambil semua form_id
        $formIds = $formList->pluck($config['pk'])->toArray();

        // 🔹 Ambil status langsung dari KompetensiProgres
        $progressData = \App\Models\KompetensiProgres::whereIn('form_id', $formIds)
            ->where('status', $config['status'])
            ->pluck('status', 'form_id')
            ->toArray();

        if (empty($progressData)) {
            return collect();
        }

        // 🔹 Tempelkan status ke data
        $validData = $formList->filter(function ($item) use ($config, $progressData) {
            return isset($progressData[$item->{$config['pk']}]);
        })->map(function ($item) use ($config, $progressData) {
            $formId = $item->{$config['pk']};
            $item->status = $progressData[$formId] ?? null;
            return $item;
        });

        return $validData;
    }


}