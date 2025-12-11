<?php

namespace App\Helpers;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FCMHelper
{
    public static function send($token, $title, $body, $data = [])
    {
        // 1. Đường dẫn đến file JSON bạn vừa tải về (để trong storage/app/)
        $credentialsPath = storage_path('app/firebase_credentials.json');
        
        // 2. Lấy Project ID từ file JSON tự động
        if (!file_exists($credentialsPath)) {
            Log::error('FCM: Không tìm thấy file credentials tại ' . $credentialsPath);
            return false;
        }
        
        $jsonKey = json_decode(file_get_contents($credentialsPath), true);
        $projectId = $jsonKey['project_id'];

        // 3. Tạo Access Token (OAuth 2.0)
        $credentials = new ServiceAccountCredentials(
            'https://www.googleapis.com/auth/firebase.messaging',
            $credentialsPath
        );
        
        $tokenArr = $credentials->fetchAuthToken();
        $accessToken = $tokenArr['access_token'];

        // 4. Chuẩn bị URL và Header
        $url = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
        
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        // 5. Cấu trúc Payload mới (HTTP v1 khác Legacy)
        // Tất cả dữ liệu phải ép về string trong 'data'
        $stringData = array_map('strval', $data);

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $stringData, // Dữ liệu tùy chỉnh (id, type...)
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                         // Quan trọng: click_action giúp Flutter nhận diện khi app chạy ngầm
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK', 
                    ]
                ],
                'apns' => [ // Cấu hình cho iOS
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                        ]
                    ]
                ]
            ]
        ];

        // 6. Gửi Request bằng CURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($result === FALSE) {
            Log::error('FCM Send Error: ' . curl_error($ch));
        } else {
            // Log kết quả để debug
            if ($httpCode != 200) {
                 Log::error('FCM Error (' . $httpCode . '): ' . $result);
            } else {
                 Log::info('FCM Success: ' . $result);
            }
        }
        
        curl_close($ch);

        return $result;
    }
}   