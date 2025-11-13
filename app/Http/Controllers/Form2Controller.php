<?php

namespace App\Http\Controllers;

use App\Models\ElemenKompetensiForm2Model;
use App\Models\JawabanForm2Model;
use App\Models\SoalForm2Model;
use App\Models\Form3Model;
use App\Models\Form2;
use App\Models\DaftarUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\PenilaianForm2Model;
use App\Models\PkProgressModel;
use Carbon\Carbon; // Pastikan untuk mengimpor Carbon  
use App\Models\BidangModel;
use App\Models\KompetensiTrack;
use App\Models\KompetensiProgres;
use Illuminate\Support\Facades\Redis; // Tambahkan Redis facade
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;  
use App\Service\OneSignalService;
use App\Service\FormService;
use App\Models\Notification;


/**
 * @OA\Tag(
 *     name="Form 2",
 *     description="API terkait Form 2"
 * )
 */

class Form2Controller extends Controller
{
    protected $oneSignalService;
    protected $formService;

	public function __construct(OneSignalService $oneSignalService, FormService $formService)
	{
		$this->oneSignalService = $oneSignalService;
        $this->formService = $formService;
	}

    public function getSoals(Request $request)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'pk_id'     => 'required|integer',
            'asesi_id'  => 'required|integer',
            'no_elemen' => 'nullable|integer',
            'no_id'     => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'ERROR',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $pk_id     = $request->input('pk_id');
        $asesi_id  = $request->input('asesi_id');
        $no_elemen = $request->input('no_elemen');
        $no_id     = $request->input('no_id');

        Log::debug('getSoals() called with:', compact('pk_id', 'asesi_id', 'no_elemen', 'no_id'));

        // Cari form_2_id berdasarkan asesi_id
        $form2 = Form2::where('user_jawab_form_2_id', $asesi_id)->first();

        if (!$form2) {
            return response()->json([
                'status'  => 'ERROR',
                'message' => "Form 2 tidak ditemukan untuk asesi_id {$asesi_id} dan pk_id {$pk_id}",
                'data'    => null
            ], 404);
        }

        $form_2_id = $form2->form_2_id;

        // Query join tabel
        $query = DB::table('soal_form_2 as s')
            ->join('komponen_form_2 as k', function ($join) {
                $join->on('s.komponen_id', '=', 'k.komponen_id')
                    ->on('s.pk_id', '=', 'k.pk_id');
            })
            ->join('elemen_kompetensi_form_2 as e', function ($join) {
                $join->on('k.no_elemen', '=', 'e.no_elemen')
                    ->on('k.pk_id', '=', 'e.pk_id');
            })
            ->where('s.pk_id', $pk_id)
            ->select(
                'e.pk_id',
                'e.no_elemen',
                'e.nama_elemen',
                'k.komponen_id',
                'k.nama_komponen',
                's.no_id',
                's.sub_komponen_id',
                's.daftar_pertanyaan'
            )
            ->orderBy('e.no_elemen')
            ->orderBy('k.komponen_id');

        // Filter opsional
        if ($no_elemen !== null) {
            $query->where('e.no_elemen', $no_elemen);
        }
        if ($no_id !== null) {
            $query->where('s.no_id', $no_id);
        }

        $questions = $query->get();

        Log::debug('Total hasil akhir:', ['count' => $questions->count()]);

        return response()->json([
            'status'  => 'SUCCESS',
            'message' => "Data taken from database for PK {$pk_id}",
            'data'    => [
                'form_2_id' => $form_2_id,
                'questions' => $questions
            ]
        ]);
    }

     public function getForm2Data(Request $request)
     {
         // Ambil parameter filter dari query string
         $form_2_id = $request->query('form_2_id');
         $date      = $request->query('date');
         $no_reg    = $request->query('no_reg');
     
         // Inisialisasi query builder untuk model PenilaianForm2Model
         $query = PenilaianForm2Model::query();
     
         // Filter berdasarkan form_2_id jika disediakan
         if ($form_2_id !== null) {
             $query->where('form_2_id', $form_2_id);
         }
     
         // Filter berdasarkan date jika disediakan (misalnya, kolom bernama 'date')
         if ($date !== null) {
             $query->whereDate('date', $date);
         }
     
         // Filter berdasarkan no_reg jika disediakan
         if ($no_reg !== null) {
             $query->where('no_reg', $no_reg);
         }
     
         // Eksekusi query
         $data = $query->get();
     
         // Jika data kosong
         if ($data->isEmpty()) {
             return response()->json(['message' => 'Data not found'], 404);
         }
     
         // Kembalikan data dalam format JSON
         return response()->json([
             'message' => 'Data retrieved successfully',
             'data'    => $data
         ]);
     }

     public function getDataSoalJawaban($user_jawab_form_2_id)
     {
         // Ambil semua jawaban berdasarkan user_jawab_form_2_id
         $jawaban = JawabanForm2Model::with(['soal.komponen'])
             ->where('user_jawab_form_2_id', $user_jawab_form_2_id)
             ->get();

        // Ambil penilaian_asesi dan asesi_date berdasarkan user_jawab_form_2_id
        $penilaian = PenilaianForm2Model::where('user_jawab_form_2_id', $user_jawab_form_2_id)->first();
     
        // Jika penilaian tidak ditemukan, beri response error
        if (!$penilaian) {
            return response()->json(['error' => 'Penilaian not found'], 404);
        }

        // Format respons dan urutkan
		$response = $jawaban->map(function ($item) {
			return $item->soal->sortBy('sub_komponen_id')->map(function ($soal) use ($item) {
				return [
					'komponen_id' => $soal->komponen_id,
					'nama_komponen' => $soal->komponen->nama_komponen,
					'no_id' => $soal->no_id,
					'sub_komponen_id' => $soal->sub_komponen_id,
					'daftar_pertanyaan' => $soal->daftar_pertanyaan,
					'k' => $item->k,
					'bk' => $item->bk,
				];
			});
		})
		->flatten(1) // Menghilangkan array bersarang
		->sortBy('komponen_id') // Tetap urut berdasarkan komponen_id global
		->values(); // Reset index array

        // Menambahkan data penilaian sekali saja di luar loop
        $response = [
            'user_jawab_form_2_id' => $penilaian->user_jawab_form_2_id,
            'asesi_name' => $penilaian->asesi_name,
            'penilaian_asesi' => $penilaian->penilaian_asesi,
            'asesi_date' => $penilaian->asesi_date,
            'soal' => $response,
        ];

        return response()->json([
			'status' => 'SUCCESS',
			'message' => 'Data retrieved successfully',
			'data' => $response
		]);
    }
     
     public function inputPenilaianAsesor(Request $request)
     {
         // Validasi input hanya untuk status
         $validator = Validator::make($request->all(), [
             'form_2_id' => 'required|exists:form_2,form_2_id', // Pastikan form_2_id ada
             'status' => 'required|string|in:Approved,Cancel', // Hanya boleh Approved atau Cancel
         ]);
     
         if ($validator->fails()) {
             return response()->json(['error' => $validator->errors()], 400);
         }
     
         // Cari data penilaian berdasarkan form_2_id
         $penilaian = PenilaianForm2Model::where('form_2_id', $request->form_2_id)->first();
     
         // Jika tidak ada, buat baru
         if (!$penilaian) {
             $penilaian = new PenilaianForm2Model();
             $penilaian->form_2_id = $request->form_2_id;
         }
     
         // Set status baru
         $penilaian->status = $request->status;
         $penilaian->asesor_date = Carbon::now(); // Tambahkan timestamp asesor_date
     
         // Jika status Cancel, null-kan penilaian_asesi & asesi_date di form_2
         if ($request->status === 'Cancel') {
             DB::table('form_2')
                 ->where('form_2_id', $request->form_2_id)
                 ->update(['penilaian_asesi' => null, 'asesi_date' => null]);
     
             // Kosongkan jawaban di tabel jawaban_form_2
             DB::table('jawaban_form_2')
                 ->where('user_jawab_form_2_id', function ($query) use ($request) {
                     $query->select('user_jawab_form_2_id')
                           ->from('form_2')
                           ->where('form_2_id', $request->form_2_id);
                 })
                 ->update(['no_id' => null, 'k' => null, 'bk' => null]);

             } elseif ($request->status === 'Approved') {

                $this->formService->updateProgresDanTrack(
                    $request->form_2_id,
                    'form_2',
                    'Completed',
                    $penilaian->user_jawab_form_2_id,
                    'Form 2 telah di setujui Asesor'
                );

                $form2 = Form2::where('form_2_id', $request->form_2_id)->first();

                $this->formService->kirimNotifikasiKeUser(
                    DaftarUser::find($form2->asesor_id),
                    'Self Assessment Approved',
                    'Self Assessment telah selesai.'
                );

                // Cari progress_id dari pk_progress berdasarkan form_2_id
                // $progress = PkProgressModel::where('form_2_id', $request->form_2_id)->first();

                // if ($progress) {
                //     // Update pk_status dengan progress_id yang sesuai
                //     DB::table('pk_status')
                //         ->where('progress_id', $progress->progress_id)
                //         ->update([
                //             'form_2_status' => 'Completed',
                //             'form_3_status' => 'Open',
                //         ]);
                // }
            }
     
         // Simpan perubahan status
         $penilaian->save();
     
         return response()->json([
             'message' => 'Status updated successfully',
             'data' => $penilaian
         ]);
     }
     
    
    private function kirimNotifikasiKeAsesor(DaftarUser $userAsesor, $formId)
    {
        if (empty($userAsesor->device_token)) {
            Log::warning("Asesor user_id={$userAsesor->user_id} tidak memiliki device_token.");
            return;
        }

        try {
            DB::transaction(function () use ($userAsesor, $formId) {
                $title = 'Update Asesmen';
                $message = "Asesi telah selesai mengerjakan Form 2.";

                // Log sebelum pengiriman notifikasi
                Log::info("Mengirim notifikasi ke OneSignal untuk user_id={$userAsesor->user_id}, form_id={$formId}");

                // Kirim notifikasi ke OneSignal
                $this->oneSignalService->sendNotification(
                    [$userAsesor->device_token],
                    $title,
                    $message
                );

                Log::info("Notifikasi berhasil dikirim ke OneSignal untuk user_id={$userAsesor->user_id}");

                // Simpan notifikasi ke database
                Notification::create([
                    'user_id' => $userAsesor->user_id,
                    'title' => $title,
                    'description' => $message,
                    'is_read' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                Log::info("Notifikasi berhasil disimpan di database untuk user_id={$userAsesor->user_id}, form_id={$formId}");
            });

        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi ke asesor.", [
                'user_id' => $userAsesor->user_id,
                'form_id' => $formId,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function JawabanAsesi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'form_2_id' => 'required|integer|exists:form_2,form_2_id',
            'jawaban' => 'required|array',
            'jawaban.*.no_id' => 'required|integer|exists:soal_form_2,no_id',
            'jawaban.*.k' => 'boolean',
            'jawaban.*.bk' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'data'    => $validator->errors(),
            ], 422);
        }

        $user = auth()->user();
        $form2 = Form2::where('form_2_id', $request->form_2_id)->first();
        $asesiId = $form2 ? $form2->user_jawab_form_2_id : null;
        $form1 = $this->formService->getParentFormIdByFormIdAndAsesiId($request->form_2_id, $asesiId);
        $form = $this->formService->getParentDataByFormId($form1);

        if (!$form) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data form_1 tidak ditemukan untuk user ini',
                'data'    => [],
            ], 404);
        }

        DB::beginTransaction();

        try {
            foreach ($request->jawaban as $jawaban) {
                JawabanForm2Model::updateOrCreate(
                    [
                        'user_jawab_form_2_id' => $user->user_id,
                        'no_id' => $jawaban['no_id']
                    ],
                    [
                        'k' => $jawaban['k'] ?? false,
                        'bk' => $jawaban['bk'] ?? false
                    ]
                );
            }

            $total_soal = SoalForm2Model::count();
            $jawabanUser = JawabanForm2Model::where('user_jawab_form_2_id', $form2->user_jawab_form_2_id)->get();

            $k_count = $jawabanUser->where('k', true)->count();
            $bk_count = $jawabanUser->where('bk', true)->count();
            $penilaian_asesi = ($k_count / $total_soal) * 100;

            $penilaian = PenilaianForm2Model::updateOrCreate(
                [
                    'form_2_id' => $request->form_2_id,
                    'user_jawab_form_2_id' => $form2->user_jawab_form_2_id,
                ],
                [
                    'k' => $k_count,
                    'bk' => $bk_count,
                    'penilaian_asesi' => $penilaian_asesi,
                    'asesi_name' => $form->asesi_name,
                    'no_reg' => $form->no_reg,
                    'asesor_name' => $form->asesor_name,
                    'asesi_date' => Carbon::now(),
                ]
            );

            DB::commit();

            $this->formService->updateProgresDanTrack(
                $request->form_2_id,
                'form_2',
                'Submitted',
                $form2->user_jawab_form_2_id,
                'Form 2 telah di isi oleh Asesi'
            );

            $asesor = DaftarUser::find($form->asesor_id);
            if ($asesor) {
                $this->kirimNotifikasiKeAsesor($asesor, $penilaian->form_2_id);
            }

            $form2 = Form2::where('form_2_id', $request->form_2_id)->first();
            $asesiId = $form2 ? $form2->user_jawab_form_2_id : null;
            $parentFormId = $this->formService->getParentFormIdByFormIdAndAsesiId($request->form_2_id, $asesiId);
            $form1Data = $this->formService->getParentDataByFormId($parentFormId);
            $isForm3Exist = $this->formService->isFormExistSingle(
                $form1Data->asesi_id,
                $form1Data->pk_id,
                'form_3'
            );

            if (!$isForm3Exist) {
                Log::info("Form 3a belum ada, membuat form 3...");

                $form3 = $this->formService->inputForm3(
                    $form1Data->pk_id,
                    $form1Data->asesi_id,
                    $form1Data->asesi_name,
                    $form1Data->asesor_id,
                    $form1Data->asesor_name,
                    $form1Data->no_reg
                );

                $this->formService->createProgresDanTrack(
                    $form3->form_3_id,
                    'form_3',
                    'InAssessment',
                    $form1Data->asesi_id,
                    $form1Data->form_1_id,
                    'Form 3 sudah dapat disetujui oleh Asesi.'
                );
            } else {
                Log::info("Form 3 sudah ada, tidak membuat ulang.");
            }

            // ✅ Tambahan: tentukan apakah lulus atau tidak
            $is_pass = $penilaian_asesi >= 80;

            return response()->json([
                'status'  => 'success',
                'message' => 'Jawaban berhasil disimpan atau diperbarui.',
                'data'    => [
                    'penilaian_asesi' => $penilaian_asesi,
                    'total_k'         => $k_count,
                    'total_bk'        => $bk_count,
                    'form_2_id'       => $penilaian->form_2_id,
                    'is_pass'         => $is_pass, // ✅ nilai true/false sesuai kondisi
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan jawaban.',
                'data'    => ['error' => $e->getMessage()],
            ], 500);
        }
    }

    public function getSoalDanJawaban(Request $request)
    {
        // Ambil parameter dari query string
        $validator = Validator::make($request->query(), [
            'pk_id' => 'required|integer',
            'user_id' => 'required|integer', // user_id yang menjawab
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $pk_id = $request->query('pk_id');
        $user_id = $request->query('user_id');

        Log::debug('getSoalDanJawaban() called with:', compact('pk_id', 'user_id'));

        $query = DB::table('soal_form_2 as s')
            ->join('komponen_form_2 as k', function ($join) {
                $join->on('s.komponen_id', '=', 'k.komponen_id')
                    ->on('s.pk_id', '=', 'k.pk_id');
            })
            ->join('elemen_kompetensi_form_2 as e', function ($join) {
                $join->on('k.no_elemen', '=', 'e.no_elemen')
                    ->on('k.pk_id', '=', 'e.pk_id');
            })
            ->leftJoin('jawaban_form_2 as j', function ($join) use ($user_id) {
                $join->on('s.no_id', '=', 'j.no_id')
                    ->where('j.user_jawab_form_2_id', '=', $user_id);
            })
            ->where('s.pk_id', $pk_id)
            ->select(
                'e.pk_id',
                'e.no_elemen',
                'e.nama_elemen',
                'k.komponen_id',
                'k.nama_komponen',
                's.no_id',
                's.sub_komponen_id',
                's.daftar_pertanyaan',
                'j.jawab_form_2_id as jawab_form_2_id',
                'j.k as jawaban_k',
                'j.bk as jawaban_bk'
            )
            ->orderBy('e.no_elemen')
            ->orderBy('k.komponen_id');

        $result = $query->get()->map(function ($item) {
            $item->jawaban_k  = is_null($item->jawaban_k) ? null : (bool) $item->jawaban_k;
            $item->jawaban_bk = is_null($item->jawaban_bk) ? null : (bool) $item->jawaban_bk;
            return $item;
        });

        return response()->json([
            'status'  => 'SUCCESS',
            'message' => 'Data soal dan jawaban berhasil diambil',
            'data'    => $result
        ]);

    }
}



