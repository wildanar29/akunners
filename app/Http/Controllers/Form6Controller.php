<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
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
use App\Models\Form6;
use Carbon\Carbon;

class Form6Controller extends BaseController
{

	protected $oneSignalService;

    public function __construct(OneSignalService $oneSignalService)
    {
        $this->oneSignalService = $oneSignalService;
    }

	private function kirimNotifikasiKeUser(DaftarUser $user, string $title, string $message)
	{
		if (empty($user->device_token)) {
			Log::warning("User user_id={$user->user_id} tidak memiliki device_token.");
			return;
		}

		try {
			// Kirim notifikasi ke OneSignal
			$this->oneSignalService->sendNotification(
				[$user->device_token],
				$title,
				$message
			);

			// Simpan notifikasi ke database
			Notification::create([
				'user_id'     => $user->user_id,
				'title'       => $title,
				'description' => $message,
				'is_read'     => 0,
				'created_at'  => now(),
				'updated_at'  => now(),
			]);

			Log::info("Notifikasi dikirim ke user user_id={$user->user_id}, nama={$user->nama}");

		} catch (\Exception $e) {
			Log::error("Gagal mengirim notifikasi ke user.", [
				'user_id'       => $user->user_id,
				'error_message' => $e->getMessage(),
				'error_trace'   => $e->getTraceAsString(),
			]);
		}
	}

	function createProgresDanTrack($formId, $formType, $status, $userId, $parentFormId = null)
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
			'activity' => $status,
			'updated_by' => $userId,
			'updated_at' => Carbon::now(),
		]);

		return $progres;
	}

	function updateProgresDanTrack($formId, $formType, $status, $userId)
	{
		$progres = KompetensiProgres::where('form_id', $formId)->firstOrFail();
		$progres->status = $status;
		$progres->save();

		KompetensiTrack::create([
			'progres_id' => $progres->id,
			'form_type' => $formType,
			'form_id' => $formId,
			'activity' => $status,
			'updated_by' => $userId,
			'updated_at' => Carbon::now(),
		]);

		return $progres;
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

	function updateForm6($form6Id, $pkId = null, $asesiId = null, $asesiName = null, $asesorId = null, $asesorName = null, $noReg = null)
	{
		$form6 = Form6::findOrFail($form6Id);

		$form6->update([
			'pk_id'         => $pkId         ?? $form6->pk_id,
			'asesi_id'      => $asesiId      ?? $form6->asesi_id,
			'asesi_name'    => $asesiName    ?? $form6->asesi_name,
			'asesi_date'    => Carbon::now(),
			'asesor_id'     => $asesorId     ?? $form6->asesor_id,
			'asesor_name'   => $asesorName   ?? $form6->asesor_name,
			'asesor_date'   => Carbon::now(),
			'no_reg'        => $noReg        ?? $form6->no_reg,
			'status'        => 'Submitted',
		]);

		return $form6;
	}

	function getParentFormIdByFormId($formId)
	{
		$progres = KompetensiProgres::where('form_id', $formId)->first();

		return $progres?->parent_form_id; // gunakan null-safe operator jika data tidak ditemukan
	}

	function getParentDataByFormId($form1Id)
	{
		return BidangModel::find($form1Id);
	}

	function isUserAsesor(?int $userId): bool
	{
		if (!$userId) {
			return false;
		}

		return DataAsesorModel::where('user_id', $userId)->exists();
	}
	
}
