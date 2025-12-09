<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\TaiKhoan;
use App\Models\GiaSu;
use App\Models\NguoiHoc;
use App\Models\PhanQuyen;

class RegisterController extends Controller
{
    /**
     * Hiển thị form đăng ký
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký tài khoản mới (CHỈ Gia sư hoặc Người học)
     */
    public function register(Request $request)
    {
        // 1. Validate dữ liệu
        try {
            $validated = $request->validate([
                'VaiTro' => 'required|in:2,3', // CHỈ cho phép 2=Gia sư, 3=Người học
                'HoTen' => 'required|string|max:255',
                'SoDienThoai' => 'required|string|regex:/^[0-9]{10,11}$/|unique:TaiKhoan,SoDienThoai',
                'Email' => 'required|email|unique:TaiKhoan,Email',
                'MatKhau' => 'required|string|min:6|confirmed',
                'agree_terms' => 'required|accepted'
            ], [
                'VaiTro.required' => 'Vui lòng chọn vai trò.',
                'VaiTro.in' => 'Vai trò không hợp lệ.',
                'HoTen.required' => 'Vui lòng nhập họ tên.',
                'SoDienThoai.required' => 'Vui lòng nhập số điện thoại.',
                'SoDienThoai.regex' => 'Số điện thoại phải từ 10-11 chữ số.',
                'SoDienThoai.unique' => 'Số điện thoại đã được đăng ký.',
                'Email.required' => 'Vui lòng nhập email.',
                'Email.email' => 'Email không hợp lệ.',
                'Email.unique' => 'Email đã được đăng ký.',
                'MatKhau.required' => 'Vui lòng nhập mật khẩu.',
                'MatKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
                'MatKhau.confirmed' => 'Xác nhận mật khẩu không khớp.',
                'agree_terms.required' => 'Vui lòng đồng ý với điều khoản.',
                'agree_terms.accepted' => 'Vui lòng đồng ý với điều khoản.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('auth_panel', 'register');
        }

        try {
            DB::beginTransaction();

            // 2. Tạo tài khoản
            $taiKhoan = TaiKhoan::create([
                'Email' => $validated['Email'],
                'MatKhauHash' => Hash::make($validated['MatKhau']), // Sửa: MatKhauHash thay vì MatKhau
                'SoDienThoai' => $validated['SoDienThoai'],
                'TrangThai' => 1 // Kích hoạt ngay
            ]);

            // 3. Tạo phân quyền
            PhanQuyen::create([
                'TaiKhoanID' => $taiKhoan->TaiKhoanID,
                'VaiTroID' => $validated['VaiTro']
            ]);

            // 4. Tạo hồ sơ tương ứng dựa trên vai trò
            if ($validated['VaiTro'] == 2) {
                // Tạo hồ sơ Gia sư - TrangThai = 2 (Chờ duyệt) - Đồng bộ với mobile
                GiaSu::create([
                    'TaiKhoanID' => $taiKhoan->TaiKhoanID,
                    'HoTen' => $validated['HoTen'],
                    'NgaySinh' => null,
                    'GioiTinh' => null,
                    'DiaChi' => null,
                    'TrangThai' => 0 // Chờ admin duyệt (Đồng bộ với mobile)
                ]);
            } else {
                // Tạo hồ sơ Người học - TrangThai = 1 (Đã kích hoạt)
                NguoiHoc::create([
                    'TaiKhoanID' => $taiKhoan->TaiKhoanID,
                    'HoTen' => $validated['HoTen'],
                    'NgaySinh' => null,
                    'GioiTinh' => null,
                    'DiaChi' => null,
                    'TrangThai' => 1 // Kích hoạt ngay
                ]);
            }

            DB::commit();

            // 4. Redirect về trang đăng nhập với thông báo thành công
            return redirect()->route('login')
                ->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
