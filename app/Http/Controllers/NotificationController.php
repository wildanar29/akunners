<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use App\Service\OneSignalService;
use App\Models\Notification;
use App\Models\BidangModel;
use Illuminate\Http\Request;
use App\Service\FormService;
use Illuminate\Support\Facades\Validator;
use App\Models\DaftarUser;

class NotificationController extends Controller
{
    protected $oneSignalService;
	protected $formService;

    public function __construct(OneSignalService $oneSignalService, FormService $formService)
    {
        $this->oneSignalService = $oneSignalService;
		$this->formService = $formService;
    }

   public function notifikasiPengajuankeBidang(Request $request)
	{
		$user = auth()->user();

		if (!$user) {
			Log::warning('Notifikasi gagal: user belum login.');
			return response()->json([
				'status' => 401,
				'message' => 'Unauthorized. Please log in first.',
			], 401);
		}

		$deviceToken = trim($user->device_token ?? '');

		if (empty($deviceToken)) {
			Log::warning("Notifikasi gagal: User ID {$user->user_id} belum subscribe ke OneSignal (device_token kosong).");

			return response()->json([
				'status' => 404,
				'message' => 'Player ID (device_token) tidak ditemukan.',
				'solution' => 'Pastikan user telah mendaftarkan perangkatnya ke OneSignal.',
			], 404);
		}

		$playerIds = [$deviceToken];
		$nama = $user->nama ?? 'Pengguna';
		$title = 'AkuNurse';
		$message = "Permohonan Pengajuan Dari $nama (Asesi)";

		// Kirim notifikasi
		$response = $this->oneSignalService->sendNotification($playerIds, $title, $message);

		// Logging hasil kirim
		if (isset($response['id'])) {
			Log::info("Notifikasi berhasil dikirim ke {$user->user_id} ({$user->nama}) dengan Player ID: $deviceToken");
		} else {
			Log::error("Gagal kirim notifikasi ke {$user->user_id} ({$user->nama}). Respon: " . json_encode($response));
		}

		return response()->json($response);
	}

	public function getNotifications(Request $request)
	{
		// Ambil user yang sedang login
		$user = auth()->user();

		if (!$user) {
			return response()->json([
				'status' => 'ERROR',
				'errorCode' => '401',
				'message' => 'Unauthorized. Anda harus login terlebih dahulu.',
				'errorMessages' => 'Token tidak valid atau belum login.',
				'data' => null
			], 401);
		}

		$userId = $user->user_id ?? $user->id; // fallback ke id jika kolomnya beda
		$isRead = $request->input('is_read'); // Optional filter (0/1)
		$perPage = $request->input('per_page', 10); // Default 10 item per halaman

		// Query notifikasi berdasarkan user_id login
		$query = Notification::where('user_id', $userId);

		if (!is_null($isRead)) {
			$query->where('is_read', $isRead);
		}

		// Gunakan pagination
		$notifications = $query
			->orderBy('created_at', 'desc')
			->paginate($perPage);

		// Konversi is_read ke boolean agar seragam di frontend
		$notifications->getCollection()->transform(function ($notification) {
			$notification->is_read = (bool) $notification->is_read;
			return $notification;
		});

		return response()->json([
			'status' => 'OK',
			'errorCode' => '',
			'message' => 'Notifikasi berhasil diambil.',
			'errorMessages' => '',
			'data' => [
				'notifications' => $notifications->items(), // data notifikasi
				'pagination' => [
					'current_page' => $notifications->currentPage(),
					'last_page' => $notifications->lastPage(),
					'per_page' => $notifications->perPage(),
					'total' => $notifications->total(),
					'has_more_pages' => $notifications->hasMorePages(),
				]
			]
		], 200);
	}


	public function markAsRead(Request $request)
	{
		$notificationId = $request->input('notification_id');

		if (!$notificationId) {
			return response()->json([
				'status' => 'ERROR',
				'errorCode' => '400',
				'message' => 'Notification ID is required',
				'errorMessages' => 'The field notification_id is missing.',
				'data' => null
			], 400);
		}

		$notification = Notification::find($notificationId);

		if (!$notification) {
			return response()->json([
				'status' => 'ERROR',
				'errorCode' => '404',
				'message' => 'Notification not found',
				'errorMessages' => 'The notification with the given ID does not exist.',
				'data' => null
			], 404);
		}

		// Update is_read menjadi true
		$notification->is_read = true;
		$notification->save();

		return response()->json([
			'status' => 'OK',
			'errorCode' => '',
			'message' => 'Notification marked as read successfully.',
			'errorMessages' => '',
			'data' => [
				'notification_id' => $notificationId,
				'is_read' => $notification->is_read
			]
		], 200);
	}

	public function sendNotifyReject(Request $request, $form_1_id)
	{
		try {
			// ==============================
			// 1️⃣ Validasi awal parameter
			// ==============================
			if (!$form_1_id) {
				Log::warning('sendNotifyReject dipanggil tanpa form_1_id');
				return response()->json([
					'status' => 'error',
					'message' => 'Parameter form_1_id wajib diisi.',
				], 400);
			}

			// ==============================
			// 2️⃣ Ambil data Form 1
			// ==============================
			$form1 = BidangModel::find($form_1_id);
			if (!$form1) {
				Log::warning('Form 1 tidak ditemukan untuk ID: ' . $form_1_id);
				return response()->json([
					'status' => 'error',
					'message' => 'Data Form 1 tidak ditemukan.',
					'form_1_id' => $form_1_id,
				], 404);
			}

			// ==============================
			// 3️⃣ Ambil user penerima notifikasi
			// ==============================
			$user = DaftarUser::where('user_id', $form1->asesi_id)->first();
			if (!$user) {
				Log::warning('User penerima notifikasi tidak ditemukan untuk asesi_id: ' . $form1->asesi_id);
				return response()->json([
					'status' => 'error',
					'message' => 'User penerima notifikasi tidak ditemukan.',
					'asesi_id' => $form1->asesi_id,
				], 404);
			}

			// ==============================
			// 4️⃣ Validasi pesan (pakai Validator manual untuk Lumen)
			// ==============================
			$validator = Validator::make($request->all(), [
				'message' => 'required|string|max:1000',
			]);

			if ($validator->fails()) {
				Log::warning('Validasi gagal saat kirim notifikasi feedback dokumen', [
					'errors' => $validator->errors(),
				]);
				return response()->json([
					'status' => 'validation_error',
					'message' => 'Pesan notifikasi wajib diisi dengan format teks.',
					'errors' => $validator->errors(),
				], 422);
			}

			$title = 'AkuNurse - Feedback Dokumen';
			$message = $request->input('message');

			Log::info('Mengirim notifikasi feedback dokumen', [
				'form_1_id' => $form_1_id,
				'asesi_id' => $form1->asesi_id,
				'user' => $user->nama ?? $user->user_id,
				'message' => $message,
			]);

			// ==============================
			// 5️⃣ Kirim notifikasi via service
			// ==============================
			$notification = $this->formService->kirimNotifikasiKeUser($user, $title, $message);

			// ==============================
			// 6️⃣ Return sukses
			// ==============================
			return response()->json([
				'status' => 'success',
				'message' => 'Notifikasi feedback dokumen berhasil dikirim.',
				'data' => [
					'form_1_id' => $form_1_id,
					'asesi_id' => $form1->asesi_id,
					'penerima' => $user->nama ?? $user->user_id,
					'notifikasi' => $notification ?? 'Terkirim',
				],
			], 200);

		} catch (\Exception $e) {
			// ==============================
			// 🚨 Error tak terduga
			// ==============================
			Log::error('Terjadi kesalahan saat kirim notifikasi feedback dokumen', [
				'form_1_id' => $form_1_id,
				'error_message' => $e->getMessage(),
				'error_trace' => $e->getTraceAsString(),
			]);

			return response()->json([
				'status' => 'error',
				'message' => 'Terjadi kesalahan saat mengirim notifikasi feedback dokumen.',
				'error' => $e->getMessage(),
			], 500);
		}
	}


}
