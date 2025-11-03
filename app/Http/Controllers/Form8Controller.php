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
        // Validasi awal untuk form_1_id + field lain
        $validator = Validator::make($request->all(), [
            'form_1_id'          => 'required|integer',
            'alasan_banding'     => 'required|string',
            'persetujuan_asesi'  => 'nullable|boolean',
            'persetujuan_asesor' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Ambil parent data berdasarkan form_1_id
        $form1 = $this->formService->getParentDataByFormId($request->form_1_id);

        if (
            !$form1 ||
            !isset($form1->asesi_id) ||
            !isset($form1->asesor_id) ||
            !isset($form1->asesor_date)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Data asesi, asesor, atau tanggal asesmen tidak ditemukan dari form_1_id',
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Simpan data
            $formBanding = FormBandingAsesmen::create([
                'form_1_id'         => $request->form_1_id,   // << simpan juga form_1_id
                'asesi_id'          => $form1->asesi_id,
                'asesor_id'         => $form1->asesor_id,
                'tanggal_asesmen'   => $form1->asesor_date,
                'alasan_banding'    => $request->alasan_banding,
                'persetujuan_asesi' => $request->persetujuan_asesi ?? false,
                'persetujuan_asesor'=> $request->persetujuan_asesor ?? false,
            ]);

            $this->formService->createProgresDanTrack(
                $formBanding->banding_id,
                'form_8',
                'Submitted',
                $form1->asesi_id,
                $form1->form_1_id,
                'Form 8 diisi oleh asesi.'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form banding asesmen berhasil disimpan',
                'data'    => $formBanding
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan FormBandingAsesmen: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data',
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
            if (!$formBanding) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data form banding asesmen tidak ditemukan.'
                ], 404);
            }

            // Ambil form induk (form_1)
            $form1 = $this->formService->getParentDataByFormId($formBanding->form_1_id);

            // Ambil status form_8 (banding)
            $form8Status = $this->formService
                ->getStatusByParentFormIdAndType($form1->form_1_id, 'form_8')
                ->first();

            if ($form8Status === 'Submitted') {
                // Update status form banding (form 8)
                $formBanding->update([
                    'status'         => 'Approved',
                    'approved_by'    => Auth::id(),   // id asesor yang approve
                    'approved_at'    => Carbon::now(),
                ]);

                // Update progres & track
                $this->formService->updateProgresDanTrack(
                    $formBanding->banding_id,
                    'form_8',
                    'Approved',
                    Auth::id(),
                    $form1->form_1_id,
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
                'asesor_id' => 'nullable|integer',
                'asesi_id'  => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'ERROR',
                    'message' => 'Validasi gagal',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // ✅ Query data
            $query = FormBandingAsesmen::query();

            if ($request->filled('asesor_id')) {
                $query->where('asesor_id', $request->asesor_id);
            }

            if ($request->filled('asesi_id')) {
                $query->where('asesi_id', $request->asesi_id);
            }

            $data = $query->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'status'  => 'SUCCESS',
                    'message' => 'Data form banding tidak ditemukan',
                    'data'    => [],
                ], 200);
            }

            return response()->json([
                'status'  => 'SUCCESS',
                'message' => 'Data form banding berhasil diambil',
                'data'    => $data,
            ], 200);

        } catch (\Exception $e) {
            // ✅ Catat error di log
            \Log::error('Gagal mengambil FormBandingAsesmen: ' . $e->getMessage());

            return response()->json([
                'status'  => 'ERROR',
                'message' => 'Terjadi kesalahan pada server',
                'error'   => $e->getMessage(), // kalau mau sembunyikan detail, bisa dihapus
            ], 500);
        }
    }





}
