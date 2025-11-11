<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use App\Models\NguoiHoc; // <-- Đảm bảo có
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;    // <-- Thêm
use Illuminate\Support\Facades\Storage; // <-- Thêm (Dùng cho xóa ảnh cũ nếu là storage)
use Illuminate\Support\Facades\Hash; // <-- Đã có

class NguoiHocController extends Controller
{
    private const NGUOIHOC_ROLE_ID = 3;

    public function index(Request $request)
    {
        $query = TaiKhoan::with('nguoihoc', 'phanquyen')
            ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::NGUOIHOC_ROLE_ID))
            ->orderByDesc('TaiKhoanID');

        // (Code index... của bạn)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('Email', 'like', "%$search%")
                  ->orWhere('SoDienThoai', 'like', "%$search%")
                  ->orWhereHas('nguoihoc', fn($q) => $q->where('HoTen', 'like', "%$search%"));
            });
        }
        if ($trangthai = $request->input('trangthai')) {
            if ($trangthai === '0' || $trangthai === '1') {
                $query->where('TrangThai', (int)$trangthai);
            }
        }
        
        $nguoihocList = $query->paginate(10)->withQueryString(); 

        return view('admin.nguoihoc.index', [
            'nguoihocList' => $nguoihocList
        ]);
    }

    public function edit(string $id)
    {
        $taiKhoan = TaiKhoan::with('nguoihoc')
            ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::NGUOIHOC_ROLE_ID))
            ->findOrFail($id); 
        return view('admin.nguoihoc.edit', [
            'taiKhoan' => $taiKhoan
        ]);
    }

    /**
     * Cập nhật thông tin người học trong CSDL.
     */
    public function update(Request $request, string $id)
    {
        // 1. Tìm tài khoản người học
        $taiKhoan = TaiKhoan::with('nguoihoc')->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::NGUOIHOC_ROLE_ID))
            ->findOrFail($id);

        // 2. Validate dữ liệu
        $validated = $request->validate([
            // Bảng TaiKhoan
            'Email' => [
                'required', 'email', 'max:100',
                Rule::unique('TaiKhoan', 'Email')->ignore($taiKhoan->TaiKhoanID, 'TaiKhoanID')
            ],
            'SoDienThoai' => [
                'nullable', 'string', 'max:20',
                Rule::unique('TaiKhoan', 'SoDienThoai')->ignore($taiKhoan->TaiKhoanID, 'TaiKhoanID')
            ],
            'TrangThai' => 'required|boolean',

            // ===== THÊM VALIDATION MẬT KHẨU =====
            'MatKhau' => 'nullable|string|min:8|confirmed',

            // Bảng NguoiHoc
            'HoTen' => 'required|string|max:150',
            'GioiTinh' => 'nullable|string|in:Nam,Nữ,Khác',
            'NgaySinh' => 'nullable|date',
            'DiaChi' => 'nullable|string|max:255',
            
            'AnhDaiDien' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // 3. Tách dữ liệu cho 2 bảng
        $taiKhoanData = [
            'Email' => $validated['Email'],
            'SoDienThoai' => $validated['SoDienThoai'],
            'TrangThai' => $validated['TrangThai'],
        ];

        $nguoiHocData = [
            'HoTen' => $validated['HoTen'],
            'GioiTinh' => $validated['GioiTinh'],
            'NgaySinh' => $validated['NgaySinh'],
            'DiaChi' => $validated['DiaChi'],
        ];

        // 4. Sử dụng DB Transaction
        try {
            // ===== SỬA DÒNG NÀY (thêm $validated) =====
            DB::transaction(function () use ($request, $taiKhoan, $taiKhoanData, $nguoiHocData, $validated) {
                
                // ===== THÊM LOGIC HASH MẬT KHẨU =====
                if (!empty($validated['MatKhau'])) {
                    $taiKhoanData['MatKhauHash'] = Hash::make($validated['MatKhau']);
                }
                // ===================================

                // Cập nhật bảng TaiKhoan (Cần $fillable trong Model TaiKhoan)
                $taiKhoan->update($taiKhoanData);

                // 4.1. XỬ LÝ ẢNH ĐẠI DIỆN (Dùng ImgBB)
                if ($request->hasFile('AnhDaiDien')) {
                    
                    // Nếu ảnh cũ là ảnh storage (không phải link http), thì xóa đi
                    if ($taiKhoan->nguoihoc?->AnhDaiDien && !str_starts_with($taiKhoan->nguoihoc->AnhDaiDien, 'http')) {
                        Storage::disk('public')->delete($taiKhoan->nguoihoc->AnhDaiDien);
                    }

                    $apiKey = env('IMAGEBB_API_KEY'); // Đọc key từ file .env
                    $response = Http::attach(
                        'image', file_get_contents($request->file('AnhDaiDien')->getRealPath()), $request->file('AnhDaiDien')->getClientOriginalName()
                    )->post('https://api.imgbb.com/1/upload', ['key' => $apiKey]);
                    
                    if ($response->successful()) {
                        $nguoiHocData['AnhDaiDien'] = $response->json()['data']['url']; // Lưu link ImgBB (http)
                    }
                }

                // 4.2. Cập nhật hoặc Tạo mới bảng NguoiHoc (Cần $fillable trong Model NguoiHoc)
                NguoiHoc::updateOrCreate(
                    ['TaiKhoanID' => $taiKhoan->TaiKhoanID], 
                    $nguoiHocData
                );
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi cập nhật: ' . $e->getMessage());
        }

        // 5. Quay về trang danh sách
        return redirect()->route('admin.nguoihoc.index')->with('success', 'Cập nhật người học thành công!');
    }
}