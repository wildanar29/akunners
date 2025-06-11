<?php

namespace App\Http\Controllers;

use App\Models\DaftarUser; // Import model DaftarUser
use App\Service\OneSignalService;
use Illuminate\Http\Request;


class Notification extends Controller
{
    protected $oneSignalService;

    public function __construct(OneSignalService $oneSignalService)
    {
        $this->oneSignalService = $oneSignalService;
    }

    public function send(Request $request)
    {
        // Ambil pengguna yang sedang login berdasarkan token autentikasi
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized. Please log in first.',
            ], 401);
        }

        // Ambil Player ID (OneSignal) dari device_token
        $playerId = [$user->device_token];

        // Ambil nama pengguna dari DaftarUser
        $nama = $user->nama ?? 'Pengguna';

        if (!$playerId) {
            return response()->json([
                'status' => 404,
                'message' => 'Player ID not found for this user.',
                'solution' => 'Ensure the user has registered their device with OneSignal.',
            ], 404);
        }

        $title = 'AkuNurse';
        $message = "Permohonan Pengajuan Dari $nama (Asesi)";


        // Kirim notifikasi ke OneSignal
        $response = $this->oneSignalService->sendNotification($playerId, $title, $message);

        return response()->json($response);
    }
}
