<?php

namespace App\Service;

use GuzzleHttp\Client;

class WablasService
{
    protected $client;
    protected $apiKey;
	protected $secretKey;

    public function __construct($apiKey)
    {
        $this->client = new Client();
        $this->apiKey = 'a869qeQFHi7r6vfThDBggM2xvG4pE97DuS5fTMFAAA53hr2JhwbFUPN8rgYg877B';
		$this->secretKey = '0st8ewQU';
    }

    public function sendMessage($payload)
    {
        $url = 'https://bdg.wablas.com/api/v2/send-message';

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => "a869qeQFHi7r6vfThDBggM2xvG4pE97DuS5fTMFAAA53hr2JhwbFUPN8rgYg877B.0st8ewQU",
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload, // Mengirim payload dalam format JSON
                'verify' => false, // Nonaktifkan SSL hanya untuk testing
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}