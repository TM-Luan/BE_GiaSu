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
    // ===== NGƯỜI HỌC =====
    public function index()
    {
        /** @var \App\Models\TaiKhoan $user */
        $user = Auth::user();
        $user->load('nguoiHoc');
        return view('nguoihoc.profile-index', compact('user'));
    }

    public function update(Request $request)
    {
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
        /** @var \App\Models\TaiKhoan $user */
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
     * Cập nhật mật khẩu người học
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Bạn phải nhập mật khẩu hiện tại.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.'
        ]);

        if (!Hash::check($request->current_password, $user->MatKhauHash)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
        }
        /** @var \App\Models\TaiKhoan $user */
        $user->update([
            'MatKhauHash' => Hash::make($validated['password']),
        ]);

        return back()->with('success_password', 'Đổi mật khẩu thành công!');
    }

    // ===== GIA SƯ =====
    /**
     * Hiển thị trang thông tin cá nhân gia sư
     */
    public function tutorProfile()
    {
        /** @var \App\Models\TaiKhoan $user */
        $user = Auth::user();
        $user->load('giaSu');
        return view('giasu.profile-index', compact('user'));
    }

    /**
     * Cập nhật thông tin cá nhân gia sư
     */
    public function tutorProfileUpdate(Request $request)
    {
        $user = Auth::user();
        $giaSu = $user->giaSu;
        
        // Kiểm tra nếu đang cập nhật mật khẩu
        if ($request->input('update_type') === 'password') {
            $validated = $request->validate([
                'current_password' => 'required|string',
                'password' => ['required', 'string', 'confirmed', Password::min(8)],
            ], [
                'current_password.required' => 'Bạn phải nhập mật khẩu hiện tại.',
                'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
                'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.'
            ]);

            if (!Hash::check($request->current_password, $user->MatKhauHash)) {
                return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
            }
            /** @var \App\Models\TaiKhoan $user */
            $user->update([
                'MatKhauHash' => Hash::make($validated['password']),
            ]);

            return back()->with('success_password', 'Đổi mật khẩu thành công!');
        }
        
        // Cập nhật thông tin profile bình thường
        $validated = $request->validate([
            'HoTen' => 'required|string|max:150',
            'SoDienThoai' => ['required', 'string', 'max:20', Rule::unique('TaiKhoan')->ignore($user->TaiKhoanID, 'TaiKhoanID')],
            'NgaySinh' => 'nullable|date',
            'GioiTinh' => 'nullable|string|max:10',
            'DiaChi' => 'nullable|string|max:255',
            'ChuyenNganh' => 'nullable|string|max:255',
            'TrinhDo' => 'nullable|string|max:100',
            'KinhNghiem' => 'nullable|integer|min:0',
            'GioiThieu' => 'nullable|string|max:1000',
            'AnhDaiDien' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        /** @var \App\Models\TaiKhoan $user */
        $user->update(['SoDienThoai' => $validated['SoDienThoai']]);
        
        $giaSuData = [
            'HoTen' => $validated['HoTen'],
            'NgaySinh' => $validated['NgaySinh'],
            'GioiTinh' => $validated['GioiTinh'],
            'DiaChi' => $validated['DiaChi'],
            'ChuyenNganh' => $validated['ChuyenNganh'] ?? null,
            'TrinhDo' => $validated['TrinhDo'] ?? null,
            'KinhNghiem' => $validated['KinhNghiem'] ?? null,
            'GioiThieu' => $validated['GioiThieu'] ?? null,
        ];
        
        if ($request->hasFile('AnhDaiDien')) {
            if ($giaSu->AnhDaiDien) {
                Storage::disk('public')->delete($giaSu->AnhDaiDien);
            }
            $path = $request->file('AnhDaiDien')->store('avatars', 'public');
            $giaSuData['AnhDaiDien'] = $path;
        }
        
        $giaSu->update($giaSuData);
        return back()->with('success_profile', 'Cập nhật thông tin thành công!');
    }
}