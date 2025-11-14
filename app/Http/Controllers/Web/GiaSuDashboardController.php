<?php

namespace App\Http\Controllers\Web; // <-- ĐÃ THAY ĐỔI

use App\Http\Controllers\Controller; // <-- THÊM DÒNG NÀY
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GiaSuDashboardController extends Controller // <-- SỬA LẠI
{
    /**
     * Hiển thị trang dashboard cho Gia sư.
     */
    public function index()
    {
        // Kiểm tra xem đúng là Gia sư đang đăng nhập không
        $user = Auth::user();
        if (!$user || $user->phanquyen->VaiTroID != 2) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['Email' => 'Phiên đăng nhập không hợp lệ.']);
        }

        return view('giasu.dashboard'); // Trả về view
    }
}
