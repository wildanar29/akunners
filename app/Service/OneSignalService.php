<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class OneSignalService
{
    protected $client;
    protected $appId;
    protected $apiKey;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->appId = '36c427b7-fd0c-4750-8510-20bb6c62119d'; // Ganti dengan App ID kamu
        $this->apiKey = 'os_v2_app_g3ccpn75brdvbbiqec5wyyqrtuzd5zlfoc3esvfecx7v3lxqnyqlm6nibb6bgua7cyylaj534jn3ptbykrcrdcrrcrxoyyv3cycz2di'; // Ganti dengan API Key kamu
    }

    public function sendNotification($playerIds, $title, $message)
    {
        if (empty($playerIds) || in_array(null, $playerIds) || in_array('', $playerIds)) {
            return [
                'status' => 'error',
                'message' => 'Player ID tidak valid.',
            ];
        }

        try {
            $response = $this->client->request('POST', 'https://onesignal.com/api/v1/notifications', [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Authorization' => "Basic {$this->apiKey}",
                ],
                'json' => [
                    'app_id' => $this->appId,
                    'include_player_ids' => $playerIds,
                    'contents' => ['en' => $message],
                    'headings' => ['en' => $title],
                ],
                'verify' => false, // untuk sementara saja saat SSL error lokal
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to send notification.',
                'error' => $e->getMessage(),
            ];
        }
    }
}

