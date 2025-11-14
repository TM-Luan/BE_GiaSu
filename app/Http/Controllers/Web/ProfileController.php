<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;         // <-- THÊM DÒNG NÀY
use Illuminate\Validation\Rules\Password;   // <-- THÊM DÒNG NÀY

class ProfileController extends Controller
{
    // ... (Hàm index() và update() của bạn giữ nguyên) ...
    public function index()
    {
        /** @var \App\Models\TaiKhoan $user */ // <--- THÊM DÒNG NÀY
        $user = Auth::user();
        $user->load('nguoiHoc');
        return view('nguoihoc.profile-index', compact('user'));
    }

    public function update(Request $request)
    {
        // ... (Code cũ của bạn giữ nguyên) ...
        $user = Auth::user();
        $nguoiHoc = $user->nguoiHoc;
        $validated = $request->validate([
            'HoTen' => 'required|string|max:150',
            'SoDienThoai' => ['required', 'string', 'max:20', Rule::unique('TaiKhoan')->ignore($user->TaiKhoanID, 'TaiKhoanID')],
            'NgaySinh' => 'nullable|date',
            'GioiTinh' => 'nullable|string|max:10',
            'DiaChi' => 'nullable|string|max:255',
            'AnhDaiDien' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        /** @var \App\Models\TaiKhoan $user */ // <--- THÊM DÒNG NÀY
        $user->update(['SoDienThoai' => $validated['SoDienThoai'],]);
        $nguoiHocData = [
            'HoTen' => $validated['HoTen'],
            'NgaySinh' => $validated['NgaySinh'],
            'GioiTinh' => $validated['GioiTinh'],
            'DiaChi' => $validated['DiaChi'],
        ];
        if ($request->hasFile('AnhDaiDien')) {
            if ($nguoiHoc->AnhDaiDien) {
                Storage::disk('public')->delete($nguoiHoc->AnhDaiDien);
            }
            $path = $request->file('AnhDaiDien')->store('avatars', 'public');
            $nguoiHocData['AnhDaiDien'] = $path;
        }
        $nguoiHoc->update($nguoiHocData);
        return back()->with('success_profile', 'Cập nhật thông tin thành công!');
    }

    /**
     * THÊM HÀM MỚI NÀY VÀO
     * Cập nhật mật khẩu
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // 1. Validate input
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Bạn phải nhập mật khẩu hiện tại.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.'
        ]);

        // 2. Check mật khẩu hiện tại (dựa trên CSDL sql.sql)
        if (!Hash::check($request->current_password, $user->MatKhauHash)) {
            // Trả về lỗi nếu mật khẩu cũ không đúng
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
        }
        /** @var \App\Models\TaiKhoan $user */ // <--- THÊM DÒNG NÀY
        // 3. Cập nhật mật khẩu mới
        $user->update([
            'MatKhauHash' => Hash::make($validated['password']),
        ]);

        // Trả về thông báo thành công (với key khác)
        return back()->with('success_password', 'Đổi mật khẩu thành công!');
    }
}