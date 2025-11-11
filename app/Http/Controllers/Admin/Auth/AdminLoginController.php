<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        if (Auth::attempt(['Email' => $credentials['Email'], 'password' => $credentials['password']])) {
            $user = Auth::user();
            if ($user->phanquyen && $user->phanquyen->VaiTroID == 1) {
                return redirect()->route('admin.dashboard');
            }
            Auth::logout();
            return back()->withErrors(['Email' => 'Bạn không có quyền admin.']);
        }
        return back()->withErrors(['Email' => 'Sai email hoặc mật khẩu.']);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
