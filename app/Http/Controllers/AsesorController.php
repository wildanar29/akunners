<?php  
  
namespace App\Http\Controllers;  
  
use Illuminate\Http\Request;  
use App\Models\BidangModel;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;  
use App\Models\HistoryJabatan;
use App\Models\PenilaianForm2Model;
use App\Models\DaftarUser; // Pastikan untuk mengimpor model User  
use App\Models\IjazahModel; // Model untuk users_ijazah_file  
use App\Models\TranskripModel; // Model untuk users_transkrip_file  
use App\Models\SipModel; // Model untuk users_sip_file  
use App\Models\StrModel; // Model untuk users_str_file  
use App\Models\UjikomModel; // Model untuk users_str_file  
use App\Models\DataAsesorModel; // Model untuk users_str_file  
use App\Models\PkProgressModel; // Model untuk users_str_file  
use App\Models\PkStatusModel; // Model untuk users_str_file  
use App\Models\JawabanForm2Model;
use App\Models\SoalForm2Model;
use App\Models\KompetensiTrack;
use App\Models\KompetensiProgres;
use App\Models\User; // Model untuk user  
use Illuminate\Support\Facades\Validator;  
use Illuminate\Support\Facades\DB; // Tambahkan DB untuk query manual
use Carbon\Carbon; // Pastikan untuk mengimpor Carbon  
use App\Service\OneSignalService;
use App\Service\FormService;
  
class AsesorController extends Controller  
{  
	protected $oneSignalService;
	protected $formService;

	public function __construct(OneSignalService $oneSignalService, FormService $formService)
    {
        $this->oneSignalService = $oneSignalService;
        $this->formService = $formService;
    }

	public function getForm1ByAsesorName($asesorName)
	{
		// Ambil semua data dari form_1 berdasarkan nama asesor dan status = 'Waiting'
		$form1Data = DB::table('form_1')
			->where('asesor_name', $asesorName)
			->where('status', 'Waiting')
			->get();

		// Return JSON response
		return response()->json([
			'status' => 200,
			'message' => 'Data form_1 berdasarkan asesor_name dan status Waiting berhasil diambil.',
			'data' => $form1Data
		]);
	}
	
	public function approveForm1ById($form_1_id)
	{
		$form_1_id = (int) $form_1_id;
		$user = auth()->user();

		Log::info('Memulai proses approveForm1ById', [
			'form_1_id' => $form_1_id,
			'user_id' => $user->user_id ?? null,
		]);

		DB::beginTransaction();

		try {
			$formDebug = BidangModel::find($form_1_id);

			if ($formDebug) {
				Log::debug('Data form_1 ditemukan', [
					'form_1_id' => $form_1_id,
					'status' => $formDebug->status ?? null,
					'asesor_id' => $formDebug->asesor_id ?? null,
					'user_id_pengaju' => $formDebug->asesi_id ?? null,
					'current_user_id' => $user->user_id ?? null,
				]);
			}

			$form = BidangModel::where('form_1_id', $form_1_id)
				->where('status', 'Assigned')
				->where('asesor_id', $user->user_id)
				->first();

			if (!$form) {
				Log::warning('Form tidak ditemukan atau bukan asesor yang sesuai', [
					'form_1_id' => $form_1_id,
					'user_id' => $user->user_id,
				]);

				DB::rollBack();

				return response()->json([
					'status' => 403,
					'message' => 'Data tidak ditemukan atau Anda bukan asesor yang ditugaskan.'
				], 403);
			}

			Log::debug('Update status form_1 ke Approved', ['form_1_id' => $form_1_id]);
			$form->status = 'InAssessment';
			$form->updated_at = Carbon::now();
			$form->save();

			Log::info('Form berhasil disetujui', [
				'form_1_id' => $form_1_id,
				'user_id' => $user->user_id,
			]);

			// Update atau create progres
			Log::debug('Cek progres form_1', ['form_id' => $form->form_1_id]);
			$progres = KompetensiProgres::where('form_id', $form->form_1_id)->first();

			if ($progres) {
				$progres->status = 'Approved';
				$progres->save();

				Log::info('Status kompetensi_progres diperbarui menjadi Approved', [
					'form_id' => $form->form_1_id,
					'progres_id' => $progres->id,
				]);
			} else {
				$progres = KompetensiProgres::create([
					'form_id' => $form->form_1_id,
					'status' => 'Approved',
				]);

				Log::info('Data kompetensi_progres baru dibuat dengan status Approved', [
					'form_id' => $form->form_1_id,
					'progres_id' => $progres->id,
				]);
			}

			// Tambahkan track
			Log::debug('Menambahkan track untuk form_1', ['form_id' => $form->form_1_id]);
			KompetensiTrack::create([
				'progres_id' => $progres->id,
				'form_type' => 'form_1',
				'activity' => 'Approved',
				'activity_time' => Carbon::now(),
				'description' => 'Pengajuan Asesmen disetujui oleh Asesor.',
			]);

			// Inisialisasi jawaban form_2
			Log::debug('Memulai initJawabanForm2', ['form_id' => $form->form_1_id]);
			$this->initJawabanForm2($user, $form);
			Log::debug('Selesai initJawabanForm2');

			Log::debug('Memulai buatForm2DariForm1', ['form_id' => $form->form_1_id]);
			$form2 = $this->buatForm2DariForm1($form);
			Log::debug('Selesai buatForm2DariForm1', ['form_2_id' => $form2->form_2_id ?? null]);

			$progresForm2 = KompetensiProgres::create([
				'form_id' => $form2->form_2_id,
				'parent_form_id' => $form->form_1_id,
				'user_id' => $user->user_id,
				'status' => 'InAssessment',
			]);

			Log::info('Progres form_2 dibuat', [
				'form_2_id' => $form2->form_2_id ?? null,
				'progres_id' => $progresForm2->id,
			]);

			KompetensiTrack::create([
				'progres_id' => $progresForm2->id,
				'form_type' => 'form_2',
				'activity' => 'InAssessment',
				'activity_time' => Carbon::now(),
				'description' => 'Penilaian Mandiri sudah dapat dikerjaan oleh asesi.',
			]);

			Log::debug('Mengirim notifikasi approval ke pengaju');
			$this->kirimNotifikasiApprovalKePengaju($formDebug);

			DB::commit();

			return response()->json([
				'status' => 200,
				'message' => 'Status berhasil diperbarui menjadi Approved dan notifikasi dikirim.'
			]);
		} catch (\Exception $e) {
			DB::rollBack();

			Log::error('Terjadi error saat approveForm1ById', [
				'form_1_id' => $form_1_id,
				'user_id' => $user->user_id ?? null,
				'error_message' => $e->getMessage(),
				'error_trace' => $e->getTraceAsString(),
			]);

			return response()->json([
				'status' => 500,
				'message' => 'Terjadi kesalahan saat memproses data.',
				'error' => $e->getMessage(),
			], 500);
		}
	}



	private function buatForm2DariForm1($form)
	{
		Log::debug('Mulai proses buatForm2DariForm1', [
			'form_1_id' => $form->form_1_id ?? null,
			'asesi_id' => $form->asesi_id ?? null,
			'asesi_name' => $form->asesi_name ?? null,
		]);

		try {
			$form2 = new PenilaianForm2Model();

			// Catat nilai yang akan dimasukkan sebelum disave
			Log::debug('Data yang akan disimpan ke form_2', [
				'user_jawab_form_2_id' => $form->asesi_id,
				'penilaian_asesi' => 0,
				'asesi_date' => null,
				'asesor_date' => null,
				'no_reg' => null,
				'asesi_name' => $form->asesi_name,
				'asesor_name' => null,
				'status' => null,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
			]);

			$form2->user_jawab_form_2_id = $form->asesi_id;
			$form2->penilaian_asesi = 0;
			$form2->pk_id = $form->pk_id;
			$form2->asesi_date = null;
			$form2->asesor_date = null;
			$form2->no_reg = null;
			$form2->asesi_name = $form->asesi_name;
			$form2->asesor_name = null;
			$form2->status = null;
			$form2->created_at = Carbon::now();
			$form2->updated_at = Carbon::now();
			$form2->save();

			Log::info('Form_2 berhasil dibuat saat approval', [
				'form_1_id' => $form->form_1_id,
				'form_2_id' => $form2->form_2_id,
				'user_jawab_form_2_id' => $form2->user_jawab_form_2_id,
			]);

			return $form2;
		} catch (\Exception $e) {
			Log::error('Gagal membuat Form_2', [
				'form_1_id' => $form->form_1_id ?? null,
				'error_message' => $e->getMessage(),
				'trace' => $e->getTraceAsString(),
			]);
			throw $e;
		}
	}

	private function kirimNotifikasiApprovalKePengaju($formData)
	{
		if (!$formData || empty($formData->asesi_id)) {
			Log::warning('Gagal kirim notifikasi: user_id pengaju kosong');
			return;
		}

		$pengaju = DaftarUser::where('user_id', $formData->asesi_id)->first();

		if (!$pengaju) {
			Log::warning('User pengaju tidak ditemukan', ['user_id' => $formData->asesi_id]);
			return;
		}

		if (empty($pengaju->device_token)) {
			Log::warning("User pengaju tidak memiliki device_token", ['user_id' => $pengaju->user_id]);
			return;
		}

		try {
			$title = 'Pengajuan Disetujui';
			$message = "Pengajuan Asesmen Anda telah disetujui oleh asesor. Lakukan pengisian Form 2 segera.";

			// Kirim notifikasi ke OneSignal
			$this->oneSignalService->sendNotification(
				[$pengaju->device_token],
				$title,
				$message
			);

			Log::info('Notifikasi berhasil dikirim ke pengaju', [
				'user_id' => $pengaju->user_id,
				'nama' => $pengaju->nama ?? null,
			]);

			// Simpan ke tabel notification
			Notification::create([
				'user_id' => $pengaju->user_id,
				'title' => $title,
				'description' => $message,
				'is_read' => 0,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
			]);

		} catch (\Exception $e) {
			Log::error('Gagal kirim notifikasi ke pengaju', [
				'user_id' => $pengaju->user_id,
				'error' => $e->getMessage(),
			]);
		}
	}

	private function kirimNotifikasiRejectKePengaju($formData)
	{
		if (!$formData || empty($formData->asesi_id)) {
			Log::warning('Gagal kirim notifikasi reject: user_id pengaju kosong');
			return;
		}

		$pengaju = DaftarUser::where('user_id', $formData->asesi_id)->first();

		if (!$pengaju) {
			Log::warning('User pengaju tidak ditemukan untuk reject', ['user_id' => $formData->asesi_id]);
			return;
		}

		if (empty($pengaju->device_token)) {
			Log::warning("User pengaju tidak memiliki device_token", ['user_id' => $pengaju->user_id]);
			return;
		}

		try {
			$title = 'Pengajuan Ditolak';
			$message = "Pengajuan Asesmen Anda ditolak oleh asesor. Silakan periksa kembali persyaratan dan lakukan pengajuan ulang.";

			// Kirim notifikasi ke OneSignal
			$this->oneSignalService->sendNotification(
				[$pengaju->device_token],
				$title,
				$message
			);

			Log::info('Notifikasi penolakan berhasil dikirim ke pengaju', [
				'user_id' => $pengaju->user_id,
				'nama' => $pengaju->nama ?? null,
			]);

			// Simpan ke tabel notification
			Notification::create([
				'user_id' => $pengaju->user_id,
				'title' => $title,
				'description' => $message,
				'is_read' => 0,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
			]);

		} catch (\Exception $e) {
			Log::error('Gagal kirim notifikasi penolakan ke pengaju', [
				'user_id' => $pengaju->user_id,
				'error' => $e->getMessage(),
			]);
		}
	}

	public function rejectForm1ById($form_1_id)
	{
		$form_1_id = (int) $form_1_id;
		$user = auth()->user();

		Log::info('Memulai proses rejectForm1ById', [
			'form_1_id' => $form_1_id,
			'user_id' => $user->user_id ?? null,
		]);

		DB::beginTransaction();

		try {
			$formDebug = BidangModel::find($form_1_id);

			if ($formDebug) {
				Log::debug('Data form_1 ditemukan untuk reject', [
					'form_1_id' => $form_1_id,
					'status' => $formDebug->status ?? null,
					'asesor_id' => $formDebug->asesor_id ?? null,
					'user_id_pengaju' => $formDebug->asesi_id ?? null,
					'current_user_id' => $user->user_id ?? null,
				]);
			}

			$form = BidangModel::where('form_1_id', $form_1_id)
				->where('status', 'Assigned')
				->where('asesor_id', $user->user_id)
				->first();

			if (!$form) {
				Log::warning('Form tidak ditemukan atau bukan asesor yang sesuai saat reject', [
					'form_1_id' => $form_1_id,
					'user_id' => $user->user_id,
				]);

				DB::rollBack();

				return response()->json([
					'status' => 403,
					'message' => 'Data tidak ditemukan atau Anda bukan asesor yang ditugaskan.'
				], 403);
			}

			// Update form_1 menjadi Canceled
			$form->status = 'Canceled';
			$form->updated_at = Carbon::now();
			$form->save();

			Log::info('Form berhasil ditolak', [
				'form_1_id' => $form_1_id,
				'user_id' => $user->user_id,
			]);

			// Update atau create progres
			$progres = KompetensiProgres::where('form_id', $form->form_1_id)->first();

			if ($progres) {
				$progres->status = 'Canceled';
				$progres->save();

				Log::info('Status kompetensi_progres diperbarui menjadi Canceled', [
					'form_id' => $form->form_1_id,
					'progres_id' => $progres->id,
				]);
			} else {
				$progres = KompetensiProgres::create([
					'form_id' => $form->form_1_id,
					'status' => 'Canceled',
				]);

				Log::info('Data kompetensi_progres baru dibuat dengan status Canceled', [
					'form_id' => $form->form_1_id,
					'progres_id' => $progres->id,
				]);
			}

			// Tambahkan track Canceled
			KompetensiTrack::create([
				'progres_id' => $progres->id,
				'form_type' => 'form_1',
				'activity' => 'Canceled',
				'activity_time' => Carbon::now(),
				'description' => 'Form 1 ditolak oleh Asesor.',
			]);

			// Kirim notifikasi penolakan
			$this->kirimNotifikasiRejectKePengaju($formDebug);

			DB::commit();

			return response()->json([
				'status' => 200,
				'message' => 'Status berhasil diperbarui menjadi Canceled dan notifikasi dikirim.'
			]);
		} catch (\Exception $e) {
			DB::rollBack();

			Log::error('Terjadi error saat rejectForm1ById', [
				'form_1_id' => $form_1_id,
				'user_id' => $user->user_id ?? null,
				'error_message' => $e->getMessage(),
			]);

			return response()->json([
				'status' => 500,
				'message' => 'Terjadi kesalahan saat memproses data.',
				'error' => $e->getMessage(),
			], 500);
		}
	}


	private function initJawabanForm2($user, $form1)
	{
		Log::debug('Mulai initJawabanForm2', [
			'form_1_id' => $form1->form_1_id ?? null,
			'pk_id' => $form1->pk_id ?? null,
			'asesi_id' => $form1->asesi_id ?? null,
			'current_user_id' => $user->user_id ?? null,
		]);

		try {
			$existingCount = JawabanForm2Model::where('user_jawab_form_2_id', $form1->asesi_id)->count();

			Log::debug('Cek existing jawaban_form_2', [
				'user_jawab_form_2_id' => $user->user_id,
				'existingCount' => $existingCount,
			]);

			if ($existingCount === 0) {
				$soalList = SoalForm2Model::where('pk_id', $form1->pk_id)->get();

				Log::info('Soal ditemukan untuk inisialisasi jawaban_form_2', [
					'pk_id' => $form1->pk_id,
					'jumlah_soal' => $soalList->count(),
				]);

				$jawabanToInit = [];
				foreach ($soalList as $soal) {
					$jawabanToInit[] = [
						'user_jawab_form_2_id' => $form1->asesi_id,
						'no_id' => $soal->no_id,
						'k' => null,
						'bk' => null,
						'created_at' => Carbon::now(),
						'updated_at' => Carbon::now(),
					];
				}

				if (!empty($jawabanToInit)) {
					JawabanForm2Model::insert($jawabanToInit);

					Log::info('Jawaban form_2 berhasil diinisialisasi', [
						'user_jawab_form_2_id' => $form1->asesi_id,
						'jumlah_jawaban' => count($jawabanToInit),
					]);
				} else {
					Log::warning('Tidak ada soal untuk diinisialisasi pada jawaban_form_2', [
						'pk_id' => $form1->pk_id,
					]);
				}
			} else {
				Log::info('Jawaban form_2 sudah ada, skip inisialisasi', [
					'user_jawab_form_2_id' => $form1->asesi_id,
				]);
			}
		} catch (\Exception $e) {
			Log::error('Gagal inisialisasi jawaban_form_2', [
				'user_jawab_form_2_id' => $form1->asesi_id ?? null,
				'form_1_id' => $form1->form_1_id ?? null,
				'error_message' => $e->getMessage(),
				'trace' => $e->getTraceAsString(),
			]);
			throw $e;
		}
	}


	/**
	 * @OA\Post(
	 *     path="/jawaban-form2/update/{no_id}",
	 *     summary="Update k_asesor dan bk_asesor berdasarkan no_id",
	 *     tags={"Asesor"},
	 *     @OA\Parameter(
	 *         name="no_id",
	 *         in="path",
	 *         description="Nomor ID dari jawaban_form_2 yang akan diperbarui",
	 *         required=true,
	 *         @OA\Schema(type="integer", example=12)
	 *     ),
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\JsonContent(
	 *             required={"k_asesor", "bk_asesor"},
	 *             @OA\Property(property="k_asesor", type="string", example="Penilaian asesor"),
	 *             @OA\Property(property="bk_asesor", type="string", example="Bukti kompetensi dari asesor")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Berhasil memperbarui data k_asesor dan bk_asesor",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="status", type="integer", example=200),
	 *             @OA\Property(property="message", type="string", example="Data k_asesor dan bk_asesor berhasil diperbarui.")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="Data tidak ditemukan",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="status", type="integer", example=404),
	 *             @OA\Property(property="message", type="string", example="Data jawaban_form_2 dengan no_id tersebut tidak ditemukan.")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=422,
	 *         description="Validasi gagal",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
	 *             @OA\Property(property="errors", type="object")
	 *         )
	 *     )
	 * )
	 */
	 
	public function updateJawabanForm2ByNoId(Request $request)
	{
		$user = auth()->user();

		$isAsesor = $user && $user->roles->contains('role_id', 2);

		if (!$isAsesor) {
			return response()->json([
				'status' => 403,
				'message' => 'Akses ditolak. Hanya pengguna dengan role Asesor yang dapat mengupdate data.'
			], 403);
		}

		// Validasi input
		$this->validate($request, [
			'form_2_id' => 'required|integer|exists:kompetensi_progres,form_id',
			'pk_id' => 'nullable|integer',
			'data' => 'required|array',
			'data.*.jawab_form_2_id' => 'required|integer|exists:jawaban_form_2,jawab_form_2_id',
			'data.*.k_asesor' => 'required|boolean',
			'data.*.bk_asesor' => 'required|boolean',
		]);

		$updated = 0;
		$notFound = [];

		foreach ($request->data as $item) {
			$jawabForm2Id = $item['jawab_form_2_id'];

			$exists = DB::table('jawaban_form_2')
				->where('jawab_form_2_id', $jawabForm2Id)
				->exists();

			if (!$exists) {
				$notFound[] = ['jawab_form_2_id' => $jawabForm2Id];
				continue;
			}

			DB::table('jawaban_form_2')
				->where('jawab_form_2_id', $jawabForm2Id)
				->update([
					'k_asesor' => (int) $item['k_asesor'],
					'bk_asesor' => (int) $item['bk_asesor'],
				]);

			$updated++;
		}

		// ✅ Update status KompetensiProgres menjadi "Approved"
		$id = $request->form_2_id;
		$pk_id = $request->pk_id;

		$progres = KompetensiProgres::where('form_id', $id)->first();

		if ($progres) {
			$progres->update([
				'status' => 'Approved',
				'updated_at' => Carbon::now(),
			]);

			Log::info('Status KompetensiProgres berhasil diupdate ke Approved', [
				'id' => $id,
				'pk_id' => $pk_id ?? null
			]);

			// Tambahkan ke KompetensiTrack
			KompetensiTrack::create([
				'progres_id'    => $progres->id,
				'form_type'     => 'form_2',
				'activity'      => 'Approved',
				'activity_time' => Carbon::now(),
				'description'   => 'Form 2 telah disetujui dan dinilai oleh asesor.',
			]);
		}

		$this->kirimNotifikasiKeAsesiSetelahDinilai($progres);

		return response()->json([
			'status' => 200,
			'message' => 'Update selesai.',
			'updated_count' => $updated,
			'not_found' => $notFound,
		]);
	}

	private function kirimNotifikasiKeAsesiSetelahDinilai($formData)
	{
		if (!$formData || empty($formData->user_id)) {
			Log::warning('Gagal kirim notifikasi: asesi_id kosong');
			return;
		}

		$asesi = DaftarUser::where('user_id', $formData->user_id)->first();

		if (!$asesi) {
			Log::warning('User asesi tidak ditemukan', ['user_id' => $formData->user_id]);
			return;
		}

		if (empty($asesi->device_token)) {
			Log::warning("User asesi tidak memiliki device_token", ['user_id' => $asesi->user_id]);
			return;
		}

		try {
			$title = 'Form 2 Dinilai';
			$message = "Form 2 Anda telah dinilai oleh asesor.";

			$this->oneSignalService->sendNotification(
				[$asesi->device_token],
				$title,
				$message
			);

			Log::info('Notifikasi berhasil dikirim ke asesi', [
				'user_id' => $asesi->user_id,
				'nama' => $asesi->nama ?? null,
			]);

			Notification::create([
				'user_id' => $asesi->user_id,
				'title' => $title,
				'description' => $message,
				'is_read' => 0,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
			]);

		} catch (\Exception $e) {
			Log::error('Gagal kirim notifikasi ke asesi', [
				'user_id' => $asesi->user_id,
				'error' => $e->getMessage(),
			]);
		}
	}




public function updateIfEmptyByUserJawabForm2Id($user_jawab_form2_id)
{
    // Ambil semua baris dengan user_jawab_form2_id yang sama
    $records = DB::table('jawaban_form_2')
        ->where('user_jawab_form_2_id', $user_jawab_form2_id)
        ->get();

    // Jika tidak ada data ditemukan
    if ($records->isEmpty()) {
        return response()->json([
            'status' => 404,
            'message' => 'Tidak ada data ditemukan untuk user_jawab_form2_id tersebut.'
        ]);
    }

    $updatedRows = 0;

    foreach ($records as $record) {
        $updateData = [];

        if (is_null($record->k_asesor) || $record->k_asesor === '') {
            $updateData['k_asesor'] = $record->k;
        }

        if (is_null($record->bk_asesor) || $record->bk_asesor === '') {
            $updateData['bk_asesor'] = $record->bk;
        }

        if (!empty($updateData)) {
            DB::table('jawaban_form_2')
                ->where('no_id', $record->no_id)
                ->update($updateData);

            $updatedRows++;
        }
    }

    return response()->json([
        'status' => 200,
        'message' => $updatedRows > 0
            ? "$updatedRows baris berhasil diperbarui."
            : "Semua data sudah lengkap, tidak ada yang diperbarui.",
        'updated_count' => $updatedRows
    ]);
}



}