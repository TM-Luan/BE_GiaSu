<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // <-- Thêm Hash
use App\Models\TaiKhoan;

class AdminLoginController extends Controller
{
    public function showLoginForm() {
        // Nếu admin đã đăng nhập, chuyển thẳng vào dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request) {
        // 1. Validate
        $credentials = $request->validate([
            'Email' => 'required|email',
            'password' => 'required'
        ],[
            'Email.required' => 'Vui lòng nhập Email.',
            'password.required' => 'Vui lòng nhập Mật khẩu.'
        ]);

        // 2. Tìm tài khoản bằng tay (Để kiểm tra vai trò và trạng thái TRƯỚC KHI đăng nhập)
        $user = TaiKhoan::where('Email', $credentials['Email'])->first();

        // 3. Kiểm tra mật khẩu hoặc không có user
        if (!$user || !Hash::check($credentials['password'], $user->getAuthPassword())) {
            return back()->withInput($request->only('Email'))
                         ->withErrors(['Email' => 'Sai email hoặc mật khẩu.']);
        }

        // 4. Kiểm tra tài khoản bị khóa
        if ($user->TrangThai == 0) {
            return back()->withInput($request->only('Email'))
                         ->withErrors(['Email' => 'Tài khoản này đã bị khóa.']);
        }

        // 5. KIỂM TRA QUYỀN ADMIN (Quan trọng)
        // Dựa trên sql.sql, VaiTroID 1 là Admin
        if (!$user->phanquyen || $user->phanquyen->VaiTroID != 1) {
            return back()->withInput($request->only('Email'))
                         ->withErrors(['Email' => 'Bạn không có quyền truy cập trang quản trị.']);
        }

        // 6. Đăng nhập vào đúng guard 'admin'
        // Auth::guard('admin')->attempt(...) // Cách này cũng được, nhưng làm thủ công ở trên tốt hơn
        
        Auth::guard('admin')->login($user); // Đăng nhập người dùng này vào 'cửa' admin

        $request->session()->regenerate();

        // 7. Chuyển hướng
        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request) {
        // Chỉ định rõ guard 'admin' khi đăng xuất
        Auth::guard('admin')->logout(); 

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}