<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; //

class LandingController extends Controller
{
    public function index()
    {
        // 1. Kiểm tra nếu là Admin đang đăng nhập -> Chuyển về Admin Dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        // 2. Kiểm tra nếu là User (Gia sư/Người học) đang đăng nhập
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            
            // Kiểm tra phân quyền để chuyển hướng đúng Dashboard
            // Logic này dựa trên code trong LoginController của bạn
            if ($user->phanquyen) {
                // Vai trò 2: Gia Sư
                if ($user->phanquyen->VaiTroID == 2) {
                    return redirect()->route('giasu.dashboard');
                }
                // Vai trò 3: Người Học
                if ($user->phanquyen->VaiTroID == 3) {
                    return redirect()->route('nguoihoc.dashboard');
                }
            }
        }

        // 3. Nếu chưa đăng nhập -> Hiển thị trang Landing như bình thường
        return view('landing.index');
    }
}