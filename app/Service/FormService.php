<?php

namespace App\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Form6Controller;
use App\Service\OneSignalService;
use App\Models\DataAsesorModel;
use App\Models\DaftarUser;
use App\Models\BidangModel;
use App\Models\UserRole;
use App\Models\KompetensiTrack;
use App\Models\KompetensiProgres;
use App\Models\Notification;
use App\Models\ElemenForm3;
use App\Models\Form5;
use App\Models\Form6;
use App\Models\Form7;
use App\Models\Form4a;
use App\Models\Form4b;
use App\Models\Form4c;
use App\Models\Form4d;
use App\Models\Form9;
use App\Models\Form10;
use App\Models\Form12;
use Carbon\Carbon;

class FormService
{
    protected $oneSignalService;

    public function __construct(OneSignalService $oneSignalService)
    {
        $this->oneSignalService = $oneSignalService;
    }

	public function findUser(int $id): ?DaftarUser
	{
		return DaftarUser::find($id);
	}

	public function getUserById($id)
	{
		$user = $this->findUser($id);

		if (!$user) {
			return response()->json(['message' => 'User not found'], 404);
		}

		return response()->json($user);
	}

    public function kirimNotifikasiKeUser(DaftarUser $user, string $title, string $message)
	{
		if (empty($user->device_token)) {
			Log::warning("User user_id={$user->user_id} tidak memiliki device_token.");
			// Tetap simpan notifikasi ke database walaupun device_token kosong
			Notification::create([
				'user_id'     => $user->user_id,
				'title'       => $title,
				'description' => $message,
				'is_read'     => 0,
				'created_at'  => Carbon::now(),
				'updated_at'  => Carbon::now(),
			]);
			return;
		}

		// Simpan notifikasi ke database terlebih dahulu
		Notification::create([
			'user_id'     => $user->user_id,
			'title'       => $title,
			'description' => $message,
			'is_read'     => 0,
			'created_at'  => Carbon::now(),
			'updated_at'  => Carbon::now(),
		]);

		try {
			// Kirim notifikasi ke OneSignal
			$this->oneSignalService->sendNotification(
				[$user->device_token],
				$title,
				$message
			);

			Log::info("Notifikasi dikirim ke user user_id={$user->user_id}, nama={$user->nama}");

		} catch (\Exception $e) {
			// Tetap log error, tapi notifikasi sudah tersimpan
			Log::error("Gagal mengirim notifikasi ke user.", [
				'user_id'       => $user->user_id,
				'error_message' => $e->getMessage(),
				'error_trace'   => $e->getTraceAsString(),
			]);
		}
	}


    public function getForm1ByAsesiIdAndPkId($asesiId, $pkId)
    {
        return DB::table('form_1')
            ->where('asesi_id', $asesiId)
            ->where('pk_id', $pkId)
            ->first();
    }

	public function getPkIdByForm1Id($form1Id)
    {
        $record = BidangModel::find($form1Id);

        if (!$record) {
            return null; // jika tidak ada data
        }

        return $record->pk_id;
    }

    public function isFormExist($asesiId, $pkId, $formType)
	{
		$form1 = $this->getForm1ByAsesiIdAndPkId($asesiId, $pkId);

		if (!$form1) {
			return false;
		}

		$parentProgres = $this->getProgresOnlyByParentFormId($form1->form_1_id);

		// Konversi ke collection
		$parentProgres = collect($parentProgres);

		if ($parentProgres->isEmpty()) {
			return false;
		}

		// Debug: cek isi item pertama
		Log::debug('parentProgres first item', ['item' => $parentProgres->first()]);

		// Pastikan item adalah array, kalau iya akses dengan ['form_type']
		$filtered = $parentProgres->filter(function ($item) use ($formType) {
			if (is_array($item)) {
				return isset($item['form_type']) && $item['form_type'] === $formType;
			} elseif (is_object($item)) {
				return isset($item->form_type) && $item->form_type === $formType;
			}
			return false;
		});

		return $filtered->isNotEmpty();
	}

    function getParentFormIdByFormId($formId)
	{
		$progres = KompetensiProgres::where('form_id', $formId)->first();

		return $progres?->parent_form_id; // gunakan null-safe operator jika data tidak ditemukan
	}

	function getParentFormIdByFormIdAndAsesiId($formId, $asesiId)
	{
		$progres = KompetensiProgres::where('form_id', $formId)
					->where('user_id', $asesiId)
					->first();

		return $progres?->parent_form_id; // pakai null-safe operator biar aman
	}


	function getParentDataByFormId($form1Id)
	{
		return BidangModel::find($form1Id);
	}

	function getForm5DataFromForm5Id($form5Id)
	{
		return Form5::find($form5Id);
	}


    function createProgresDanTrack($formId, $formType, $status, $userId, $parentFormId = null, $description)
	{
		$progres = KompetensiProgres::create([
			'form_id' => $formId,
			'parent_form_id' => $parentFormId,
			'user_id' => $userId,
			'status' => $status,
		]);

		KompetensiTrack::create([
			'progres_id' => $progres->id,
			'form_type' => $formType,
			'form_id' => $formId,
			'description' => $description,
			'activity' => $status,
			'updated_by' => $userId,
			'updated_at' => Carbon::now(),
		]);

		return $progres;
	}

    function updateProgresDanTrack($formId, $formType, $status, $userId, $description)
	{
		// Cari progres yang form_id dan user_id cocok, dan memiliki track dengan form_type yg sesuai
		$progres = KompetensiProgres::where('form_id', $formId)
			->where('user_id', $userId)
			->whereHas('track', function ($query) use ($formType) {
				$query->where('form_type', $formType);
			})
			->first();

		if (!$progres) {
			throw new \Exception("Data progres dengan form_id=$formId, user_id=$userId dan form_type=$formType tidak ditemukan.");
		}

		// Update status progres
		$progres->status = $status;
		$progres->save();

		// Tambah track baru
		KompetensiTrack::create([
			'progres_id'    => $progres->id,
			'form_type'     => $formType,
			'activity'      => $status, // contoh: 'Completed', 'Submitted', dll
			'activity_time' => Carbon::now(),
			'description'   => $description,
			'created_at'    => Carbon::now(),
			'updated_at'    => Carbon::now(),
		]);

		return $progres;
	}
	
	public function updateForm1($form1Id, $status = null)
	{
		$form1 = BidangModel::findOrFail($form1Id);

		$form1->update([
			'status' => $status ?? $form1->status,
		]);

		return $form1;
	}



    function inputForm6($pkId, $asesiId, $asesiName, $asesorId, $asesorName, $noReg)
	{
		return Form6::create([
			'pk_id'         => $pkId,
			'asesi_id'      => $asesiId,
			'asesi_name'    => $asesiName,
			'asesi_date'    => Carbon::now(),
			'asesor_id'     => $asesorId,
			'asesor_name'   => $asesorName,
			'asesor_date'   => Carbon::now(),
			'no_reg'        => $noReg,
			'status'        => 'Submitted',
		]);
	}

	function inputForm4a($pkId, $asesiId, $asesiName, $asesorId, $asesorName, $noReg)
	{
		return Form4a::create([
			'pk_id'         => $pkId,
			'asesi_id'      => $asesiId,
			'asesi_name'    => $asesiName,
			'asesi_date'    => Carbon::now(),
			'asesor_id'     => $asesorId,
			'asesor_name'   => $asesorName,
			'asesor_date'   => Carbon::now(),
			'no_reg'        => $noReg,
			'status'        => 'InAssessment',
		]);
	}

	function inputForm4b($pkId, $asesiId, $asesiName, $asesorId, $asesorName, $noReg)
	{
		return Form4b::create([
			'pk_id'         => $pkId,
			'asesi_id'      => $asesiId,
			'asesi_name'    => $asesiName,
			'asesi_date'    => Carbon::now(),
			'asesor_id'     => $asesorId,
			'asesor_name'   => $asesorName,
			'asesor_date'   => Carbon::now(),
			'no_reg'        => $noReg,
			'status'        => 'InAssessment',
		]);
	}

	function inputForm4c($pkId, $asesiId, $asesiName, $asesorId, $asesorName, $noReg)
	{
		return Form4c::create([
			'pk_id'         => $pkId,
			'asesi_id'      => $asesiId,
			'asesi_name'    => $asesiName,
			'asesi_date'    => Carbon::now(),
			'asesor_id'     => $asesorId,
			'asesor_name'   => $asesorName,
			'asesor_date'   => Carbon::now(),
			'no_reg'        => $noReg,
			'status'        => 'InAssessment',
		]);
	}

	function inputForm4d($pkId, $asesiId, $asesiName, $asesorId, $asesorName, $noReg)
	{
		return Form4d::create([
			'pk_id'         => $pkId,
			'asesi_id'      => $asesiId,
			'asesi_name'    => $asesiName,
			'asesi_date'    => Carbon::now(),
			'asesor_id'     => $asesorId,
			'asesor_name'   => $asesorName,
			'asesor_date'   => Carbon::now(),
			'no_reg'        => $noReg,
			'status'        => 'InAssessment',
		]);
	}

	public function inputForm10(
		$pkId,
		$daftarTilikId,
		$form_type,
		$asesiId,
		$asesiName,
		$asesorId = null,
		$asesorName = null
	) {
		DB::beginTransaction();
		try {
			// Insert ke tabel form_10
			$form10 = Form10::create([
				'pk_id'           => $pkId,
				'daftar_tilik_id' => $daftarTilikId, // ✅ sekarang disimpan
				'asesi_id'        => $asesiId,
				'asesi_name'      => $asesiName,
				'asesor_id'       => $asesorId,
				'asesor_name'     => $asesorName,
				'form_type'          => $form_type,
				'status'          => 'InAssessment', // contoh status awal
				'created_at'      => Carbon::now(),
				'updated_at'      => Carbon::now(),
			]);

			DB::commit();
			return $form10;

		} catch (\Exception $e) {
			DB::rollBack();
			Log::error("Gagal membuat Form 10: " . $e->getMessage());
			throw $e;
		}
	}

    function updateForm6($form6Id, $pkId = null, $asesiId = null, $asesiName = null, $asesorId = null, $asesorName = null, $noReg = null, $asesiDate = null, $asesorDate = null, $status)
	{
		$form6 = Form6::findOrFail($form6Id);

		$form6->update([
			'pk_id'         => $pkId         ?? $form6->pk_id,
			'asesi_id'      => $asesiId      ?? $form6->asesi_id,
			'asesi_name'    => $asesiName    ?? $form6->asesi_name,
			'asesi_date'    => $asesiDate	?? $form6->asesi_date,
			'asesor_id'     => $asesorId     ?? $form6->asesor_id,
			'asesor_name'   => $asesorName   ?? $form6->asesor_name,
			'asesor_date'   => $asesorDate	?? $form6->asesor_date,
			'no_reg'        => $noReg        ?? $form6->no_reg,
			'status'        => $status		?? $form6->status,
		]);

		return $form6;
	}

	public function updateForm9(
		$form9Id,
		$pkId = null,
		$asesiId = null,
		$asesiName = null,
		$asesiDate = null,
		$asesorId = null,
		$asesorName = null,
		$asesorDate = null,
		$noReg = null,
		$status = null
	) {
		DB::beginTransaction();
		try {
			$form9 = Form9::findOrFail($form9Id);

			$form9->update([
				'pk_id'       => $pkId       ?? $form9->pk_id,
				'asesi_id'    => $asesiId    ?? $form9->asesi_id,
				'asesi_name'  => $asesiName  ?? $form9->asesi_name,
				'asesi_date'  => $asesiDate  ?? $form9->asesi_date,
				'asesor_id'   => $asesorId   ?? $form9->asesor_id,
				'asesor_name' => $asesorName ?? $form9->asesor_name,
				'asesor_date' => $asesorDate ?? $form9->asesor_date,
				'no_reg'      => $noReg      ?? $form9->no_reg,
				'status'      => $status     ?? $form9->status,
				'updated_at'  => Carbon::now(),
			]);

			DB::commit();
			return $form9;

		} catch (\Exception $e) {
			DB::rollBack();
			Log::error("Gagal mengupdate Form 9: " . $e->getMessage());
			throw $e;
		}
	}


	public function updateForm10(
		$form10Id,
		$pkId = null,
		$daftarTilikId = null,
		$form_type = null,
		$asesiId = null,
		$asesiName = null,
		$asesorId = null,
		$asesorName = null,
		$status = null
	) {
		DB::beginTransaction();
		try {
			$form10 = Form10::findOrFail($form10Id);

			$form10->update([
				'pk_id'           => $pkId           ?? $form10->pk_id,
				'daftar_tilik_id' => $daftarTilikId   ?? $form10->daftar_tilik_id,
				'form_type'       => $form_type       ?? $form10->form_type,
				'asesi_id'        => $asesiId         ?? $form10->asesi_id,
				'asesi_name'      => $asesiName       ?? $form10->asesi_name,
				'asesor_id'       => $asesorId        ?? $form10->asesor_id,
				'asesor_name'     => $asesorName      ?? $form10->asesor_name,
				'status'          => $status          ?? $form10->status,
				'updated_at'      => Carbon::now(),
			]);

			DB::commit();
			return $form10;

		} catch (\Exception $e) {
			DB::rollBack();
			Log::error("Gagal mengupdate Form 10: " . $e->getMessage());
			throw $e;
		}
	}

	public function isFormExistSingle($asesiId, $pkId, $formType)
	{
		// Ambil data form_1 berdasarkan asesi dan pk
		$form1 = $this->getForm1ByAsesiIdAndPkId($asesiId, $pkId);

		if (!$form1) {
			return false;
		}

		// Ambil progres menggunakan relasi trackSingle (hanya 1 track per progres)
		$parentProgres = $this->getProgresSingleByParentFormId($form1->form_1_id);

		if ($parentProgres->isEmpty()) {
			return false;
		}

		// Filter progres yang memiliki form_type sesuai
		$filtered = collect($parentProgres)->filter(function ($item) use ($formType) {
			return isset($item['form_type']) && $item['form_type'] === $formType;
		});

		return $filtered->isNotEmpty();
	}


	public function getProgresSingleByParentFormId(int $formId)
	{
		return KompetensiProgres::with('trackSingle:id,progres_id,form_type')
			->where('parent_form_id', $formId)
			->get()
			->map(function ($item) {
				return [
					'id' => $item->id,
					'form_id' => $item->form_id,
					'parent_form_id' => $item->parent_form_id,
					'user_id' => $item->user_id,
					'status' => $item->status,
					'form_type' => $item->trackSingle?->form_type, // pakai relasi baru
					'created_at' => $item->created_at,
					'updated_at' => $item->updated_at,
				];
			})
			->unique(function ($item) {
				return $item['form_type'] . '-' . $item['form_id'] . '-' . $item['parent_form_id'];
			})
			->values();
	}


    public function getProgresOnlyByParentFormId(int $formId)
    {
        return KompetensiProgres::with('track:id,progres_id,form_type')
            ->where('parent_form_id', $formId)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'form_id' => $item->form_id,
                    'parent_form_id' => $item->parent_form_id,
                    'user_id' => $item->user_id,
                    'status' => $item->status,
                    'form_type' => $item->track?->form_type,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            })
            ->unique(function ($item) {
                return $item['form_type'] . '-' . $item['form_id'] . '-' . $item['parent_form_id'];
            })
            ->values(); // reset indeks array
    }

	public function getFormIdsByParentFormIdAndType(int $formId, string $formType)
	{
		return KompetensiProgres::with('track:id,progres_id,form_type')
			->where('parent_form_id', $formId)
			->whereHas('track', function ($query) use ($formType) {
				$query->where('form_type', $formType);
			})
			->get()
			->pluck('form_id')
			->unique()
			->values(); // reset indeks array
	}

	public function getStatusByParentFormIdAndType(int $formId, string $formType)
	{
		return KompetensiProgres::with('track:id,progres_id,form_type')
			->where('parent_form_id', $formId)
			->whereHas('track', function ($query) use ($formType) {
				$query->where('form_type', $formType);
			})
			->get()
			->pluck('status')  // Ambil nilai status saja
			->unique()          // Pastikan status yang duplikat dihapus
			->values();         // Reset indeks array
	}

	function isUserAsesor(?int $userId): bool
	{
		if (!$userId) {
			return false;
		}

		return DataAsesorModel::where('user_id', $userId)->exists();
	}

	public function updateForm4a(
		$form4aId,
		$pkId = null,
		$asesiId = null,
		$asesiName = null,
		$asesiDate = null,
		$asesorId = null,
		$asesorName = null,
		$asesorDate = null,
		$noReg = null,
		$status = null,
		$ket = null
	) {
		DB::beginTransaction();
		try {
			$form4a = Form4a::findOrFail($form4aId);

			$form4a->update([
				'pk_id'        => $pkId        ?? $form4a->pk_id,
				'asesi_id'     => $asesiId     ?? $form4a->asesi_id,
				'asesi_name'   => $asesiName   ?? $form4a->asesi_name,
				'asesi_date'   => $asesiDate   ?? $form4a->asesi_date,
				'asesor_id'    => $asesorId    ?? $form4a->asesor_id,
				'asesor_name'  => $asesorName  ?? $form4a->asesor_name,
				'asesor_date'  => $asesorDate  ?? $form4a->asesor_date,
				'no_reg'       => $noReg       ?? $form4a->no_reg,
				'status'       => $status      ?? $form4a->status,
				'ket'          => $ket         ?? $form4a->ket,
				'updated_at'   => Carbon::now(),
			]);

			DB::commit();
			return $form4a;

		} catch (\Throwable $e) {
			DB::rollBack();
			Log::error("Gagal mengupdate Form 4A: " . $e->getMessage());
			throw $e;
		}
	}

	public function updateForm4b(
		$form4bId,
		$pkId = null,
		$asesiId = null,
		$asesiName = null,
		$asesiDate = null,
		$asesorId = null,
		$asesorName = null,
		$asesorDate = null,
		$noReg = null,
		$status = null,
		$ket = null
	) {
		DB::beginTransaction();
		try {
			$form4b = Form4b::findOrFail($form4bId);

			$form4b->update([
				'pk_id'        => $pkId        ?? $form4b->pk_id,
				'asesi_id'     => $asesiId     ?? $form4b->asesi_id,
				'asesi_name'   => $asesiName   ?? $form4b->asesi_name,
				'asesi_date'   => $asesiDate   ?? $form4b->asesi_date,
				'asesor_id'    => $asesorId    ?? $form4b->asesor_id,
				'asesor_name'  => $asesorName  ?? $form4b->asesor_name,
				'asesor_date'  => $asesorDate  ?? $form4b->asesor_date,
				'no_reg'       => $noReg       ?? $form4b->no_reg,
				'status'       => $status      ?? $form4b->status,
				'ket'          => $ket         ?? $form4b->ket,
				'updated_at'   => Carbon::now(),
			]);

			DB::commit();
			return $form4b;

		} catch (\Throwable $e) {
			DB::rollBack();
			Log::error("Gagal mengupdate Form 4B: " . $e->getMessage());
			throw $e;
		}
	}

	public function updateForm4c(
		$form4cId,
		$pkId = null,
		$asesiId = null,
		$asesiName = null,
		$asesiDate = null,
		$asesorId = null,
		$asesorName = null,
		$asesorDate = null,
		$noReg = null,
		$status = null,
		$ket = null
	) {
		DB::beginTransaction();
		try {
			$form4c = Form4c::findOrFail($form4cId);

			$form4c->update([
				'pk_id'        => $pkId        ?? $form4c->pk_id,
				'asesi_id'     => $asesiId     ?? $form4c->asesi_id,
				'asesi_name'   => $asesiName   ?? $form4c->asesi_name,
				'asesi_date'   => $asesiDate   ?? $form4c->asesi_date,
				'asesor_id'    => $asesorId    ?? $form4c->asesor_id,
				'asesor_name'  => $asesorName  ?? $form4c->asesor_name,
				'asesor_date'  => $asesorDate  ?? $form4c->asesor_date,
				'no_reg'       => $noReg       ?? $form4c->no_reg,
				'status'       => $status      ?? $form4c->status,
				'ket'          => $ket         ?? $form4c->ket,
				'updated_at'   => Carbon::now(),
			]);

			DB::commit();
			return $form4c;

		} catch (\Throwable $e) {
			DB::rollBack();
			Log::error("Gagal mengupdate Form 4C: " . $e->getMessage());
			throw $e;
		}
	}

	public function updateForm4d(
		$form4dId,
		$pkId = null,
		$asesiId = null,
		$asesiName = null,
		$asesiDate = null,
		$asesorId = null,
		$asesorName = null,
		$asesorDate = null,
		$noReg = null,
		$status = null,
		$ket = null
	) {
		DB::beginTransaction();
		try {
			$form4d = Form4d::findOrFail($form4dId);

			$form4d->update([
				'pk_id'        => $pkId        ?? $form4d->pk_id,
				'asesi_id'     => $asesiId     ?? $form4d->asesi_id,
				'asesi_name'   => $asesiName   ?? $form4d->asesi_name,
				'asesi_date'   => $asesiDate   ?? $form4d->asesi_date,
				'asesor_id'    => $asesorId    ?? $form4d->asesor_id,
				'asesor_name'  => $asesorName  ?? $form4d->asesor_name,
				'asesor_date'  => $asesorDate  ?? $form4d->asesor_date,
				'no_reg'       => $noReg       ?? $form4d->no_reg,
				'status'       => $status      ?? $form4d->status,
				'ket'          => $ket         ?? $form4d->ket,
				'updated_at'   => Carbon::now(),
			]);

			DB::commit();
			return $form4d;

		} catch (\Throwable $e) {
			DB::rollBack();
			Log::error("Gagal mengupdate Form 4D: " . $e->getMessage());
			throw $e;
		}
	}

	public function inputForm7($pkId, $asesiId, $asesiName, $asesorId, $asesorName, $noReg)
    {
        return Form7::create([
            'pk_id'         => $pkId,
            'asesi_id'      => $asesiId,
            'asesi_name'    => $asesiName,
            'asesi_date'    => Carbon::now(),
            'asesor_id'     => $asesorId,
            'asesor_name'   => $asesorName,
            'asesor_date'   => Carbon::now(),
            'no_reg'        => $noReg,
            'status'        => 'InAssessment',
        ]);
    }

	public function inputForm12($pkId, $asesiId, $asesiName, $asesorId, $asesorName, $noReg)
	{
		return Form12::create([
			'pk_id'       => $pkId,
			'asesi_id'    => $asesiId,
			'asesi_name'  => $asesiName,
			'asesi_date'  => Carbon::now(),
			'asesor_id'   => $asesorId,
			'asesor_name' => $asesorName,
			'asesor_date' => Carbon::now(),
			'no_reg'      => $noReg,
			'status'      => 'InAssessment',
		]);
	}

	public function inputForm9($pkId, $asesiId, $asesiName, $asesorId, $asesorName, $noReg)
	{
		return Form9::create([
			'pk_id'       => $pkId,
			'asesi_id'    => $asesiId,
			'asesi_name'  => $asesiName,
			'asesi_date'  => Carbon::now(),   // otomatis tanggal saat ini
			'asesor_id'   => $asesorId,
			'asesor_name' => $asesorName,
			'asesor_date' => Carbon::now(),   // otomatis tanggal saat ini
			'no_reg'      => $noReg,
			'status'      => 'InAssessment',  // default status
		]);
	}


	public function updateForm7(
		$form7Id,
		$pkId = null,
		$asesiId = null,
		$asesiName = null,
		$asesiDate = null,
		$asesorId = null,
		$asesorName = null,
		$asesorDate = null,
		$noReg = null,
		$status = null
	) {
		DB::beginTransaction();
		try {
			$form7 = Form7::findOrFail($form7Id);

			$form7->update([
				'pk_id'       => $pkId       ?? $form7->pk_id,
				'asesi_id'    => $asesiId    ?? $form7->asesi_id,
				'asesi_name'  => $asesiName  ?? $form7->asesi_name,
				'asesi_date'  => $asesiDate  ?? $form7->asesi_date,
				'asesor_id'   => $asesorId   ?? $form7->asesor_id,
				'asesor_name' => $asesorName ?? $form7->asesor_name,
				'asesor_date' => $asesorDate ?? $form7->asesor_date,
				'no_reg'      => $noReg      ?? $form7->no_reg,
				'status'      => $status     ?? $form7->status,
				'updated_at'  => Carbon::now(),
			]);

			DB::commit();
			return $form7;

		} catch (\Throwable $e) {
			DB::rollBack();
			Log::error("Gagal mengupdate Form 7: " . $e->getMessage());
			throw $e;
		}
	}

	public function updateForm12(
		$form12Id,
		$pkId = null,
		$asesiId = null,
		$asesiName = null,
		$asesiDate = null,
		$asesorId = null,
		$asesorName = null,
		$asesorDate = null,
		$noReg = null,
		$status = null
	) {
		DB::beginTransaction();
		try {
			$form12 = Form12::findOrFail($form12Id);

			$form12->update([
				'pk_id'       => $pkId       ?? $form12->pk_id,
				'asesi_id'    => $asesiId    ?? $form12->asesi_id,
				'asesi_name'  => $asesiName  ?? $form12->asesi_name,
				'asesi_date'  => $asesiDate  ?? $form12->asesi_date,
				'asesor_id'   => $asesorId   ?? $form12->asesor_id,
				'asesor_name' => $asesorName ?? $form12->asesor_name,
				'asesor_date' => $asesorDate ?? $form12->asesor_date,
				'no_reg'      => $noReg      ?? $form12->no_reg,
				'status'      => $status     ?? $form12->status,
				'updated_at'  => Carbon::now(),
			]);

			DB::commit();
			return $form12;

		} catch (\Throwable $e) {
			DB::rollBack();
			Log::error("Gagal mengupdate Form 12: " . $e->getMessage());
			throw $e;
		}
	}

	public function getFinalResultByPkIdAndAsesiId($pkId, $asesiId)
    {
        $data = ElemenForm3::with([
            'kukForm3' => function ($q) use ($asesiId) {
                $q->orderBy('no_kuk', 'asc')
                ->with([
                    'iukForm3' => function ($q2) use ($asesiId) {
                        $q2->orderBy('no_iuk', 'asc')
                            ->with([
                                'soalForm7' => function ($q3) use ($asesiId) {
                                    $q3->select('id', 'iuk_form3_id')
                                        ->with([
                                            'jawabanForm7' => function ($q4) use ($asesiId) {
                                                $q4->select('id', 'soal_form7_id', 'keputusan', 'asesi_id')
                                                    ->where('asesi_id', $asesiId);
                                            }
                                        ]);
                                }
                            ]);
                    }
                ]);
            }
        ])
        ->where('pk_id', $pkId)
        ->whereHas('kukForm3.iukForm3.soalForm7.jawabanForm7', function ($q) use ($asesiId) {
            $q->where('asesi_id', $asesiId);
        })
        ->orderBy('no_elemen_form_3', 'asc')
        ->get();

        if ($data->isEmpty()) {
            return null;
        }

        // Hitung nilai
        $data->transform(function ($elemen) {
            $elemen->kukForm3->transform(function ($kuk) {
                $kuk->iukForm3->transform(function ($iuk) {
                    $totalSoal = $iuk->soalForm7->count();
                    $jumlahK   = 0;

                    foreach ($iuk->soalForm7 as $soal) {
                        foreach ($soal->jawabanForm7 as $jawaban) {
                            if ($jawaban->keputusan === 'K') {
                                $jumlahK++;
                            }
                        }
                    }

                    $iuk->final = ($totalSoal > 0 && ($jumlahK / $totalSoal) >= 0.5) ? 'K' : 'BK';
                    return $iuk;
                });

                $totalIuk = $kuk->iukForm3->count();
                $jumlahK  = $kuk->iukForm3->where('final', 'K')->count();

                $kuk->final = ($totalIuk > 0 && ($jumlahK / $totalIuk) >= 0.5) ? 'K' : 'BK';
                return $kuk;
            });

            $totalKuk = $elemen->kukForm3->count();
            $jumlahK  = $elemen->kukForm3->where('final', 'K')->count();

            $elemen->final = ($totalKuk > 0 && ($jumlahK / $totalKuk) >= 0.5) ? 'K' : 'BK';
            return $elemen;
        });

        return $data;
    }

}
