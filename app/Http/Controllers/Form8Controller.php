<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Service\OneSignalService;
use App\Service\FormService;
use App\Models\DataAsesorModel;
use App\Models\JawabanForm4c;
use App\Models\KegiatanDaftarTilik;
use App\Models\DaftarUser;
use App\Models\ElemenForm3;
use App\Models\JawabanForm4d;
use App\Models\DaftarTilik;

use App\Models\JawabanDaftarTilik;
use App\Models\Form8;
use App\Models\FormBandingAsesmen;
use App\Models\PertanyaanForm4d;
use App\Models\PertanyaanForm4c;
use App\Models\IukModel;
use App\Models\JawabanForm4b;
use App\Models\KukModel;
use App\Models\JawabanForm4a;
use App\Models\BidangModel;
use App\Models\UserRole;
use App\Models\Form9Question;
use App\Models\Form9Answer;
use App\Models\Form9;
use App\Models\KompetensiTrack;
use App\Models\KompetensiProgres;
use App\Models\Notification;
use App\Models\SoalForm7;
use App\Models\Form12;
use App\Models\JawabanForm7;
use App\Models\PoinForm4;
use Carbon\Carbon;

class Form8Controller extends BaseController
{

	protected $formService;

    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
    }

    public function storeFormBandingAsesmen(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'form_1_id'          => 'required|integer',
            'alasan_banding'     => 'required|string',
            'persetujuan_asesi'  => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Ambil data Form 1
        $form1 = $this->formService->getParentDataByFormId($request->form_1_id);

        if (!$form1 || !isset($form1->asesi_id) || !isset($form1->asesor_id) || !isset($form1->asesor_date)) {
            return response()->json([
                'success' => false,
                'message' => 'Data asesi, asesor, atau tanggal asesmen tidak ditemukan dari form_1_id',
            ], 404);
        }

        // Cek apakah form banding sudah ada (WAJIB ADA)
        $existing = FormBandingAsesmen::where('form_1_id', $request->form_1_id)->first();

        if (!$existing) {
            return response()->json([
                'success' => false,
                'message' => 'Form Banding belum diinisialisasi.',
            ], 404);
        }

        DB::beginTransaction();
        try {

            // Update data Form 8
            $existing->update([
                'alasan_banding'     => $request->alasan_banding,
                'persetujuan_asesi'  => $request->persetujuan_asesi ?? false,
                // persetujuan_asesor tidak di-update oleh asesi
            ]);

            // Update progress menjadi Submitted (Asesi sudah mengisi)
            $this->formService->createProgresDanTrack(
                $existing->banding_id,
                'form_8',
                'Submitted',
                $form1->asesi_id,
                $form1->form_1_id,
                'Asesi telah mengisi Formulir Banding.'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form banding asesmen berhasil diperbarui.',
                'data'    => $existing,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update FormBandingAsesmen: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    public function approveFormBandingAsesmen(Request $request, $bandingId)
    {
        // Validasi ID form banding (form_8)
        $validator = Validator::make(['banding_id' => $bandingId], [
            'banding_id' => 'required|integer|exists:form_banding_asesmen,banding_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Ambil data form banding
            $formBanding = FormBandingAsesmen::find($bandingId);
            Log::info('FormBandingAsesmen ditemukan: ', ['banding_id' => $bandingId]);

            if (!$formBanding) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data form banding asesmen tidak ditemukan.'
                ], 404);
            }

            // Ambil form induk (form_1)
            $form1 = $this->formService->getParentDataByFormId($formBanding->form_1_id);
            Log::info('FormInduk ditemukan untuk form_1_id: ', ['form_1_id' => $formBanding->form_1_id]);

            // Ambil Form 8 ID
            $form8id = $this->formService
                ->getFormIdsByParentFormIdAndTypeNew($form1->form_1_id, 'form_8', $form1->asesi_id)
                ->first();

            Log::info('Form_8 ID ditemukan: ', ['form_8_id' => $form8id]);

            // Ambil status form_8 (banding)
            $form8Status = $this->formService
                ->getStatusByParentAndFormIdAndType($form1->form_1_id, $form8id, 'form_8')
                ->first();

            Log::info('Status form_8 ditemukan: ', ['status' => $form8Status]);

            // 🚫 Cek apakah form 8 sudah disetujui sebelumnya
            if ($form8Status === 'Approved') {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan banding sudah disetujui sebelumnya.',
                ], 409); // HTTP 409 Conflict
            }

            // ✅ Kalau status form_8 masih "Submitted", lanjut approve
            if ($form8Status === 'Submitted') {
                // Update status form banding (form 8)
                $formBanding->update([
                    'status'         => 'Approved',
                    'approved_by'    => $form1->asesor_id,
                    'approved_at'    => Carbon::now(),
                ]);

                // Update progres & track
                $this->formService->updateProgresDanTrack(
                    $formBanding->banding_id,
                    'form_8',
                    'Approved',
                    $form1->asesi_id,
                    'Form banding asesmen (form 8) telah di-approve oleh Asesor.'
                    
                );

                // Kirim notifikasi ke ASESI
                $this->formService->kirimNotifikasiKeUser(
                    DaftarUser::find($form1->asesi_id),
                    'Form Banding Disetujui',
                    'Form banding asesmen (Form 8) Anda telah disetujui oleh Asesor.'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form banding asesmen berhasil di-approve oleh Asesor',
                'data'    => $formBanding,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal approve FormBandingAsesmen: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat approve form banding asesmen: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function getFormBandingByUser(Request $request)
    {
        try {
            // ✅ Validasi input
            $validator = Validator::make($request->all(), [
                'banding_id' => 'nullable|integer',
                'asesor_id'  => 'nullable|integer',
                'asesi_id'   => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'ERROR',
                    'message' => 'Validasi gagal',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // ✅ Query data dasar
            $query = FormBandingAsesmen::query();

            // Filter berdasarkan banding_id (jika ada)
            if ($request->filled('banding_id')) {
                $query->where('banding_id', $request->banding_id);
            }

            // Filter berdasarkan asesor_id (jika ada)
            if ($request->filled('asesor_id')) {
                $query->where('asesor_id', $request->asesor_id);
            }

            // Filter berdasarkan asesi_id (jika ada)
            if ($request->filled('asesi_id')) {
                $query->where('asesi_id', $request->asesi_id);
            }

            // Jalankan query
            $data = $query->first(); // ⚡ gunakan first() agar hasilnya 1 object, bukan array

            // ✅ Jika tidak ada data
            if (!$data) {
                return response()->json([
                    'status'  => 'SUCCESS',
                    'message' => 'Data form banding tidak ditemukan',
                    'data'    => (object)[], // hasil tetap berbentuk object kosong
                ], 200);
            }

            // ✅ Jika data ditemukan
            return response()->json([
                'status'  => 'SUCCESS',
                'message' => 'Data form banding berhasil diambil',
                'data'    => $data, // hasil berupa object JSON
            ], 200);

        } catch (\Exception $e) {
            // ✅ Catat error di log
            \Log::error('Gagal mengambil FormBandingAsesmen: ' . $e->getMessage());

            return response()->json([
                'status'  => 'ERROR',
                'message' => 'Terjadi kesalahan pada server',
                'error'   => $e->getMessage(), // kalau mau disembunyikan, hapus baris ini
            ], 500);
        }
    }






}
