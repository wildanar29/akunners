<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use App\Service\OneSignalService;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $oneSignalService;

    public function __construct(OneSignalService $oneSignalService)
    {
        $this->oneSignalService = $oneSignalService;
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

		$userId = $user->user_id; // atau $user->id tergantung struktur auth-mu
		$isRead = $request->input('is_read'); // Optional filter

		// Query notifikasi berdasarkan user_id login
		$query = Notification::where('user_id', $userId);

		if (!is_null($isRead)) {
			$query->where('is_read', $isRead);
		}

		$notifications = $query->orderBy('created_at', 'desc')->get()->map(function ($notification) {
			$notification->is_read = (bool) $notification->is_read;
			return $notification;
		});

		return response()->json([
			'status' => 'OK',
			'errorCode' => '',
			'message' => 'Notifikasi berhasil diambil.',
			'errorMessages' => '',
			'data' => [
				'notifications' => $notifications
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
}
