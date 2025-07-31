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
use App\Models\Form6;
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
				'created_at'  => Carbon::now(),
				'updated_at'  => Carbon::now(),
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

    public function getForm1ByAsesiIdAndPkId($asesiId, $pkId)
    {
        return DB::table('form_1')
            ->where('asesi_id', $asesiId)
            ->where('pk_id', $pkId)
            ->first();
    }

    public function isFormExist($asesiId, $pkId, $formType)
    {
        $form1 = $this->getForm1ByAsesiIdAndPkId($asesiId, $pkId);

        if (is_null($form1)) {
            return false;
        }

        $parentProgres = $this->getProgresOnlyByParentFormId($form1->form_1_id);

        // Filter progres berdasarkan form_type yang diminta
        $filtered = $parentProgres->where('form_type', $formType);

        // Jika ingin mengembalikan boolean true/false:
        return $filtered->isNotEmpty();

        // Jika ingin mengembalikan datanya:
        // return $filtered->values();
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
		$progres = KompetensiProgres::where('form_id', $formId)->firstOrFail();
		$progres->status = $status;
		$progres->save();

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
}
