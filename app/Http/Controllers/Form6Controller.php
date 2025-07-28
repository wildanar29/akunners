<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Service\OneSignalService;
use App\Models\DataAsesorModel;
use App\Models\DaftarUser;
use App\Models\UserRole;
use App\Models\KompetensiTrack;
use App\Models\KompetensiProgres;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
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

	function simpanProgresDanTrack($formId, $formType, $status, $userId, $parentFormId = null)
	{
		$progres = KompetensiProgres::firstOrNew([
			'form_id' => $formId
		]);

		// Jika data baru, set parent_form_id dan user_id
		if (!$progres->exists) {
			$progres->parent_form_id = $parentFormId;
			$progres->user_id = $userId;
		}

		$progres->status = $status;
		$progres->save();

		// Buat track aktivitas
		KompetensiTrack::create([
			'progres_id' => $progres->id,
			'form_type' => $formType,
			'form_id' => $formId,
			'activity' => $status,
			'updated_by' => $userId,
			'updated_at' => Carbon::now()
		]);

		return $progres;
	}

	function isUserAsesor(?int $userId): bool
	{
		if (!$userId) {
			return false;
		}

		return DataAsesorModel::where('user_id', $userId)->exists();
	}

	
}
