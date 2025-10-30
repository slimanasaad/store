<?php

namespace App\Services;

require_once app_path('Libraries/php-jwt/src/JWT.php');
require_once app_path('Libraries/php-jwt/src/Key.php');


use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FCMHttpV1Service
{
    protected $credentials;

    public function __construct()
    {
        $path = storage_path('app/firebase/firebase-credentials.json');
        $this->credentials = json_decode(file_get_contents($path), true);
    }

    protected function getAccessToken()
    {
        $now = time();

        $payload = [
            'iss' => $this->credentials['client_email'],
            'sub' => $this->credentials['client_email'],
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        ];

        $jwt = JWT::encode($payload, $this->credentials['private_key'], 'RS256');
		
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        return $response->json('access_token');
    }

    public function sendMessage($deviceToken, $title, $body)
    {
        $accessToken = $this->getAccessToken();
        $projectId = $this->credentials['project_id'];

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $response = Http::withToken($accessToken)->post($url, [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'android' => [
                    'priority' => 'high',
                ],
            ]
        ]);

        return $response->json();
    }
}
