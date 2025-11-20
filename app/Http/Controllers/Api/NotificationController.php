<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->TaiKhoanID) {
            return response()->json([
                'success' => false,
                'message' => 'Không xác định được người dùng.'
            ], 401);
        }

        try {
            $notifications = Notification::where('user_id', $user->TaiKhoanID)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['data' => $notifications]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $notification = Notification::where('user_id', $user->TaiKhoanID)->find($id);
        
        if ($notification) {
            $notification->update(['is_read' => true]);
            return response()->json(['success' => true, 'message' => 'Đã đánh dấu đã đọc']);
        }

        return response()->json(['success' => false, 'message' => 'Không tìm thấy thông báo'], 404);
    }
}