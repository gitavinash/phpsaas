<?php

/**
 * Firebase Cloud Messaging (FCM) HTTP v1 Helper
 * Handles OAuth 2.0 JWT assertion token generation and HTTP v1 message dispatch.
 */
class FcmV1
{
    private static $accessToken = null;
    private static $tokenExpiry = 0;

    /**
     * Locate the Firebase service account JSON file.
     */
    public static function getServiceAccountPath()
    {
        $possiblePaths = [
            defined('FIREBASE_SERVICE_ACCOUNT_PATH') ? FIREBASE_SERVICE_ACCOUNT_PATH : '',
            __DIR__ . '/../../configuration/firebase-service-account.json',
            __DIR__ . '/../../configuration/service-account.json',
            __DIR__ . '/../../../firebase-service-account.json',
        ];

        foreach ($possiblePaths as $path) {
            if (!empty($path) && file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Get OAuth 2.0 Access Token from Google OAuth endpoint using JWT RS256.
     */
    public static function getAccessToken()
    {
        if (self::$accessToken && time() < (self::$tokenExpiry - 60)) {
            return self::$accessToken;
        }

        $jsonPath = self::getServiceAccountPath();
        if (!$jsonPath) {
            return null;
        }

        $serviceAccount = json_decode(file_get_contents($jsonPath), true);
        if (!$serviceAccount || empty($serviceAccount['client_email']) || empty($serviceAccount['private_key'])) {
            return null;
        }

        $clientEmail = $serviceAccount['client_email'];
        $privateKey  = $serviceAccount['private_key'];
        $now = time();

        // Build JWT Header & Payload
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'iss'   => $clientEmail,
            'sub'   => $clientEmail,
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        ]));

        // Base64Url encode
        $base64UrlHeader  = str_replace(['+', '/', '='], ['-', '_', ''], $header);
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], $payload);
        $signingInput     = $base64UrlHeader . '.' . $base64UrlPayload;

        // Sign with private key
        $signature = '';
        $binarySignature = '';
        if (!openssl_sign($signingInput, $binarySignature, $privateKey, OPENSSL_ALGO_SHA256)) {
            return null;
        }

        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($binarySignature));
        $jwt = $signingInput . '.' . $base64UrlSignature;

        // Exchange JWT for access token
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            if (!empty($data['access_token'])) {
                self::$accessToken = $data['access_token'];
                self::$tokenExpiry  = $now + (!empty($data['expires_in']) ? (int)$data['expires_in'] : 3600);
                return self::$accessToken;
            }
        }

        return null;
    }

    /**
     * Send an FCM message to a single device token using HTTP v1.
     */
    public static function sendToToken($deviceToken, $title, $body, $targetUrl = '', $icon = '/favicon.ico', $projectId = 'aleya-notification')
    {
        $accessToken = self::getAccessToken();
        if (!$accessToken) {
            return [
                'success' => false,
                'error'   => 'FCM v1 Service Account JSON not found or invalid. Place service account in lib/configuration/firebase-service-account.json',
            ];
        }

        $message = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => [
                    'title'        => (string)$title,
                    'body'         => (string)$body,
                    'click_action' => (string)$targetUrl,
                    'url'          => (string)$targetUrl,
                ],
                'webpush' => [
                    'headers' => [
                        'Urgency' => 'high',
                    ],
                    'notification' => [
                        'title'              => $title,
                        'body'               => $body,
                        'icon'               => $icon,
                        'badge'              => $icon,
                        'requireInteraction' => true,
                    ],
                    'fcm_options' => [
                        'link' => (string)$targetUrl,
                    ],
                ],
            ],
        ];

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json; UTF-8',
        ]);

        $resBody = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decoded = json_decode($resBody, true);

        return [
            'success'   => ($httpCode === 200),
            'http_code' => $httpCode,
            'response'  => $decoded,
        ];
    }

    /**
     * Send to multiple device tokens.
     */
    public static function sendToMultipleTokens(array $tokens, $title, $body, $targetUrl = '', $icon = '/favicon.ico', $projectId = 'aleya-notification')
    {
        $results = [];
        $successCount = 0;

        foreach ($tokens as $token) {
            $res = self::sendToToken($token, $title, $body, $targetUrl, $icon, $projectId);
            $results[] = $res;
            if (!empty($res['success'])) {
                $successCount++;
            }
        }

        return [
            'total'   => count($tokens),
            'success' => $successCount,
            'details' => $results,
        ];
    }
}
