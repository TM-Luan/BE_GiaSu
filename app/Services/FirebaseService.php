<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\TaiKhoan;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    public static function send($userId, $title, $body, $data = [])
    {
        $user = TaiKhoan::find($userId);
        
        // 1. Lưu thông báo vào database (để hiện trong danh sách thông báo)
        $notification = Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $body,
            'type' => $data['type'] ?? 'general',
            'related_id' => $data['id'] ?? null,
            'is_read' => false,
        ]);

        // 2. Nếu user không có token thì chỉ lưu DB, không bắn noti
        if (!$user || !$user->fcm_token) {
            return $notification;
        }

        // 3. Gửi FCM
        $url = 'https://fcm.googleapis.com/fcm/send';
        $serverKey = 'DÁN_SERVER_KEY_CỦA_BẠN_VÀO_ĐÂY'; // Lấy từ Firebase Console

        $payload = [
            'to' => $user->fcm_token,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => array_merge($data, [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'notification_id' => $notification->id // Gửi kèm ID để tracking nếu cần
            ]),
            'priority' => 'high',
        ];

        $headers = [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $result = curl_exec($ch);
        if ($result === FALSE) {
            Log::error('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);

        return $result;
    }
}