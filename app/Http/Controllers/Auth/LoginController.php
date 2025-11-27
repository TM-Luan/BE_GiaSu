<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TaiKhoan;

class LoginController extends Controller
{
    /**
     * Hiển thị trang form đăng nhập.
     */
    public function showLoginForm()
    {
        return view('auth.login'); // Trỏ đến file view ở Bước 3
    }

    /**
     * Xử lý logic đăng nhập.
     */
    public function login(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'Email' => 'required|email',
            'MatKhau' => 'required|string',
        ], [
            'Email.required' => 'Vui lòng nhập Email.',
            'MatKhau.required' => 'Vui lòng nhập Mật khẩu.',
        ]);

        // 2. Tìm tài khoản
        $user = TaiKhoan::where('Email', $request->Email)->first();

        // 3. Kiểm tra tài khoản và mật khẩu
        if (!$user || !Hash::check($request->MatKhau, $user->getAuthPassword())) {
            return back()
                ->withInput($request->only('Email', 'remember'))
                ->withErrors(['Email' => 'Email hoặc Mật khẩu không chính xác.'])
                ->with('auth_panel', 'login');
        }

        // --- ĐỒNG BỘ KIỂM TRA TRẠNG THÁI AN TOÀN: XÉT CẢ TaiKhoan.TrangThai VÀ GiaSu.TrangThai ---
        // ensure relation loaded (lazy load if needed)
        $user->loadMissing('giasu');
        // chặn khi account bị khóa (TaiKhoan.TrangThai == 2) HOẶC hồ sơ gia sư bị khóa (GiaSu.TrangThai == 2)
        if ((int)$user->TrangThai === 2 || ($user->giasu && (int)$user->giasu->TrangThai === 2)) {
            return back()
                ->withInput($request->only('Email', 'remember'))
                ->withErrors(['Email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên để được hỗ trợ.'])
                ->with('auth_panel', 'login');
        }

        // 5. Kiểm tra vai trò
        $vaiTroID = $user->phanquyen->VaiTroID;

        // Không cho phép Admin (ID 1) đăng nhập ở đây
        if ($vaiTroID == 1) {
            return back()->withInput($request->only('Email', 'remember'))
                         ->withErrors(['Email' => 'Tài khoản Admin vui lòng đăng nhập ở trang quản trị.']);
        }

        // 6. Đăng nhập thành công
        Auth::guard('web')->login($user, $request->filled('remember'));
        
        $request->session()->regenerate();

        // 7. Chuyển hướng theo vai trò
        if ($vaiTroID == 2) { // GiaSu
            return redirect()->intended(route('giasu.dashboard'));
        }

        if ($vaiTroID == 3) { // NguoiHoc
            return redirect()->intended(route('nguoihoc.dashboard'));
        }

        // Mặc định (nếu có vai trò khác)
        return redirect('/');
    }

    /**
     * Xử lý đăng xuất.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}