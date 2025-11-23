<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Http;
// XÓA: use Illuminate\Support\Facades\Log; để tránh lỗi xung đột

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
        $gs = $user->giaSu; 

        // Lấy danh sách môn học cho dropdown
        $monHocs = \App\Models\MonHoc::orderBy('TenMon')->get();

        // Thống kê đánh giá
        $danhGiaStats = \App\Models\DanhGia::whereHas('lop', function ($q) use ($gs) {
            $q->where('GiaSuID', $gs->GiaSuID);
        })->selectRaw('
            ROUND(AVG(DiemSo),1) as rating,
            COUNT(*) as total
        ')->first();

        // Nếu không có đánh giá (Sử dụng tên biến mới)
        $rating = $danhGiaStats->rating ?? 0;
        $gs->danh_gia_count = $danhGiaStats->total ?? 0; 
        
        // Học phí mẫu (tùy bạn)
        $hocPhi = number_format($gs->GiaTrungBinhMotBuoi ?? 150000, 0, ',', '.') . ' đ/buổi';

        return view('giasu.profile-index', compact('user', 'monHocs', 'danhGiaStats', 'gs', 'rating', 'hocPhi')); 
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
        
        // Cập nhật thông tin profile bình thường - Đồng bộ với mobile
        $validated = $request->validate([
            'HoTen' => 'required|string|max:150',
            'SoDienThoai' => ['required', 'string', 'max:20', Rule::unique('TaiKhoan')->ignore($user->TaiKhoanID, 'TaiKhoanID')],
            'NgaySinh' => 'nullable|date',
            'GioiTinh' => 'nullable|string|max:10',
            'DiaChi' => 'nullable|string|max:255',
            
            // Thông tin học vấn - Đồng bộ với mobile
            'BangCap' => 'nullable|string|max:255',
            'TruongDaoTao' => 'nullable|string|max:255',
            'ChuyenNganh' => 'nullable|string|max:255',
            'ThanhTich' => 'nullable|string|max:1000',
            'KinhNghiem' => 'nullable|string|max:255',
            'MonID' => 'nullable|integer|exists:MonHoc,MonID',
            
            // Upload ảnh - Đồng bộ với mobile
            'AnhDaiDien' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'AnhCCCD_MatTruoc' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'AnhCCCD_MatSau' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'AnhBangCap' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        /** @var \App\Models\TaiKhoan $user */
        $user->update(['SoDienThoai' => $validated['SoDienThoai']]);
        
        $giaSuData = [
            'HoTen' => $validated['HoTen'],
            'NgaySinh' => $validated['NgaySinh'],
            'GioiTinh' => $validated['GioiTinh'],
            'DiaChi' => $validated['DiaChi'],
            'BangCap' => $validated['BangCap'] ?? null,
            'TruongDaoTao' => $validated['TruongDaoTao'] ?? null,
            'ChuyenNganh' => $validated['ChuyenNganh'] ?? null,
            'ThanhTich' => $validated['ThanhTich'] ?? null,
            'KinhNghiem' => isset($validated['KinhNghiem']) ? $validated['KinhNghiem'] . ' năm' : null,
            'MonID' => $validated['MonID'] ?? null,
        ];
        
        // ĐỒNG BỘ MOBILE: Upload ảnh lên ImgBB API
        $apiKey = config('services.imgbb.key');
        
        $uploadToImgBB = function ($file) use ($apiKey) {
            if (!$apiKey) {
                return null;
            }
            try {
                $imageBase64 = base64_encode(file_get_contents($file->getRealPath()));
                $response = Http::asForm()->post('https://api.imgbb.com/1/upload', [
                    'key' => $apiKey,
                    'image' => $imageBase64
                ]);

                if ($response->successful() && isset($response->json()['data']['url'])) {
                    return $response->json()['data']['url'];
                }
            } catch (\Exception $e) {
                // SỬA LỖI: Gọi Facade Log với namespace đầy đủ
                \Illuminate\Support\Facades\Log::error('ImgBB upload failed: ' . $e->getMessage()); 
            }
            return null;
        };
        
        // Upload ảnh đại diện - ĐỒNG BỘ MOBILE
        if ($request->hasFile('AnhDaiDien')) {
            $url = $uploadToImgBB($request->file('AnhDaiDien'));
            if ($url) {
                $giaSuData['AnhDaiDien'] = $url;
            }
        }
        
        // Upload CCCD mặt trước - ĐỒNG BỘ MOBILE
        if ($request->hasFile('AnhCCCD_MatTruoc')) {
            $url = $uploadToImgBB($request->file('AnhCCCD_MatTruoc'));
            if ($url) {
                $giaSuData['AnhCCCD_MatTruoc'] = $url;
            }
        }
        
        // Upload CCCD mặt sau - ĐỒNG BỘ MOBILE
        if ($request->hasFile('AnhCCCD_MatSau')) {
            $url = $uploadToImgBB($request->file('AnhCCCD_MatSau'));
            if ($url) {
                $giaSuData['AnhCCCD_MatSau'] = $url;
            }
        }
        
        // Upload ảnh bằng cấp - ĐỒNG BỘ MOBILE
        if ($request->hasFile('AnhBangCap')) {
            $url = $uploadToImgBB($request->file('AnhBangCap'));
            if ($url) {
                $giaSuData['AnhBangCap'] = $url;
            }
        }
        
        $giaSu->update($giaSuData);
        return back()->with('success_profile', 'Cập nhật thông tin thành công!');
    }
}