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
use Illuminate\Support\Facades\Log; // Import Log để ghi lỗi upload nếu có

class ProfileController extends Controller
{
    /**
     * Hàm hỗ trợ upload ảnh lên ImgBB (Private method)
     */
    private function uploadToImgBB($file)
    {
        // Lấy API Key từ config (cần cấu hình trong services.php)
        $apiKey = config('services.imgbb.key');
        
        if (!$apiKey) {
            Log::error('ImgBB API Key chưa được cấu hình trong services.php');
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
            } else {
                Log::error('ImgBB Upload Failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('ImgBB Exception: ' . $e->getMessage());
        }
        return null;
    }

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
        $user->update(['SoDienThoai' => $validated['SoDienThoai']]);
        
        $nguoiHocData = [
            'HoTen' => $validated['HoTen'],
            'NgaySinh' => $validated['NgaySinh'],
            'GioiTinh' => $validated['GioiTinh'],
            'DiaChi' => $validated['DiaChi'],
        ];

        // Xử lý upload ảnh cho Người Học (Đồng bộ dùng ImgBB hoặc Storage)
        if ($request->hasFile('AnhDaiDien')) {
            // Thử upload lên ImgBB trước
            $url = $this->uploadToImgBB($request->file('AnhDaiDien'));
            
            if ($url) {
                // Nếu upload ImgBB thành công
                $nguoiHocData['AnhDaiDien'] = $url;
            } else {
                // Fallback: Nếu ImgBB lỗi thì lưu vào Storage local như cũ
                if ($nguoiHoc->AnhDaiDien && !filter_var($nguoiHoc->AnhDaiDien, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($nguoiHoc->AnhDaiDien);
                }
                $path = $request->file('AnhDaiDien')->store('avatars', 'public');
                $nguoiHocData['AnhDaiDien'] = $path;
            }
        }

        $nguoiHoc->update($nguoiHocData);
        return back()->with('success_profile', 'Cập nhật thông tin thành công!');
    }

    /**
     * Cập nhật mật khẩu
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
    public function tutorProfile()
    {
        /** @var \App\Models\TaiKhoan $user */
        $user = Auth::user();
        $user->load('giaSu');
        $gs = $user->giaSu; 

        $monHocs = \App\Models\MonHoc::orderBy('TenMon')->get();

        $danhGiaStats = \App\Models\DanhGia::whereHas('lop', function ($q) use ($gs) {
            $q->where('GiaSuID', $gs->GiaSuID);
        })->selectRaw('
            ROUND(AVG(DiemSo),1) as rating,
            COUNT(*) as total
        ')->first();

        $rating = $danhGiaStats->rating ?? 0;
        $gs->danh_gia_count = $danhGiaStats->total ?? 0; 
        
        $hocPhi = number_format($gs->GiaTrungBinhMotBuoi ?? 150000, 0, ',', '.') . ' đ/buổi';

        return view('giasu.profile-index', compact('user', 'monHocs', 'danhGiaStats', 'gs', 'rating', 'hocPhi')); 
    }

    public function tutorProfileUpdate(Request $request)
    {
        $user = Auth::user();
        $giaSu = $user->giaSu;
        
        // Xử lý đổi mật khẩu
        if ($request->input('update_type') === 'password') {
            return $this->updatePassword($request); // Tái sử dụng hàm updatePassword
        }
        
        // Validate dữ liệu
        $validated = $request->validate([
            'HoTen' => 'required|string|max:150',
            'SoDienThoai' => ['required', 'string', 'max:20', Rule::unique('TaiKhoan')->ignore($user->TaiKhoanID, 'TaiKhoanID')],
            'NgaySinh' => 'nullable|date',
            'GioiTinh' => 'nullable|string|max:10',
            'DiaChi' => 'nullable|string|max:255',
            'BangCap' => 'nullable|string|max:255',
            'TruongDaoTao' => 'nullable|string|max:255',
            'ChuyenNganh' => 'nullable|string|max:255',
            'ThanhTich' => 'nullable|string|max:1000',
            'KinhNghiem' => 'nullable|string|max:255',
            'MonID' => 'nullable|integer|exists:MonHoc,MonID',
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
        
        // Xử lý upload ảnh (Sử dụng hàm chung uploadToImgBB)
        $imageFields = ['AnhDaiDien', 'AnhCCCD_MatTruoc', 'AnhCCCD_MatSau', 'AnhBangCap'];
        
        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $url = $this->uploadToImgBB($request->file($field));
                if ($url) {
                    $giaSuData[$field] = $url;
                }
            }
        }
        
        $giaSu->update($giaSuData);
        return back()->with('success_profile', 'Cập nhật thông tin thành công!');
    }
}