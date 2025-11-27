<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckGiaSuApproved
{
    /**
     * Kiểm tra gia sư đã được admin duyệt chưa (TrangThai = 1)
     * Đồng bộ với logic mobile API
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Nếu chưa đăng nhập, middleware auth sẽ xử lý
        if (!$user) {
            return redirect()->route('login');
        }

        // Nếu tài khoản bị đánh dấu bị khóa ở cấp TaiKhoan -> chặn ngay
        if ((int)$user->TrangThai === 2) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tài khoản của bạn đã bị khóa. Liên hệ quản trị viên.'
                ], 403);
            }
            return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị khóa. Liên hệ quản trị viên.');
        }

        // Kiểm tra user có phải gia sư không
        $giaSu = $user->giaSu;
        if (!$giaSu) {
            return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        // Nếu hồ sơ gia sư tồn tại nhưng chưa được duyệt (GiaSu.TrangThai != 1) -> chặn truy cập tính năng cần duyệt
        if ((int)$giaSu->TrangThai !== 1) {
            // Nếu là request API (JSON), trả về JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tài khoản của bạn chưa được duyệt. Vui lòng cập nhật đầy đủ thông tin hồ sơ và chờ admin duyệt.'
                ], 403);
            }

            // Nếu là request web, redirect về trang profile với thông báo
            return redirect()->route('giasu.profile.index')
                ->with('warning', 'Tài khoản của bạn đang chờ duyệt. Vui lòng cập nhật đầy đủ thông tin hồ sơ (bằng cấp, CCCD, v.v.) để được admin duyệt sớm hơn.');
        }

        return $next($request);
    }
}
