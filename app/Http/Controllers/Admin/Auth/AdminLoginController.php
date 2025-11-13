<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TaiKhoan;

class AdminLoginController extends Controller
{
    public function showLoginForm() {
        return view('admin.auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'Email' => 'required|email',
            'password' => 'required'
        ]);

        // Tìm tài khoản theo email
        $user = TaiKhoan::where('Email', $credentials['Email'])->first();

        if (!$user) {
            return back()->withErrors(['Email' => 'Sai email hoặc mật khẩu.'])->withInput();
        }

        // Kiểm tra mật khẩu
        $isPasswordValid = false;
        
        // Kiểm tra nếu mật khẩu đã được hash
        if (Hash::needsRehash($user->MatKhauHash) === false && Hash::check($credentials['password'], $user->MatKhauHash)) {
            $isPasswordValid = true;
        } 
        // Nếu mật khẩu chưa hash, so sánh trực tiếp và hash lại
        elseif ($user->MatKhauHash === $credentials['password']) {
            $isPasswordValid = true;
            // Tự động hash lại mật khẩu
            $user->MatKhauHash = Hash::make($credentials['password']);
            $user->save();
        }

        if (!$isPasswordValid) {
            return back()->withErrors(['Email' => 'Sai email hoặc mật khẩu.'])->withInput();
        }

        // Kiểm tra quyền admin
        if (!$user->phanquyen || $user->phanquyen->VaiTroID != 1) {
            return back()->withErrors(['Email' => 'Bạn không có quyền admin.'])->withInput();
        }

        // Kiểm tra trạng thái tài khoản
        if ($user->TrangThai == 0) {
            return back()->withErrors(['Email' => 'Tài khoản của bạn đã bị khóa.'])->withInput();
        }

        // Đăng nhập thành công
        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
