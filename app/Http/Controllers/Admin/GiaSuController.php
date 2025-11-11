<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use App\Models\GiaSu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;    // Dùng cho ImgBB
use Illuminate\Support\Facades\Hash;    // <-- THÊM DÒNG NÀY

class GiaSuController extends Controller
{
    private const GIASU_ROLE_ID = 2;

    public function index(Request $request)
    {
        // ... (Code index của bạn) ...
        $query = TaiKhoan::with('giasu', 'phanquyen')
            ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::GIASU_ROLE_ID))
            ->orderByDesc('TaiKhoanID');
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('Email', 'like', "%$search%")
                  ->orWhere('SoDienThoai', 'like', "%$search%")
                  ->orWhereHas('giasu', fn($q) => $q->where('HoTen', 'like', "%$search%"));
            });
        }
        if ($trangthai = $request->input('trangthai')) {
            if ($trangthai === '0' || $trangthai === '1') {
                $query->where('TrangThai', (int)$trangthai);
            }
        }
        $giasuList = $query->paginate(10)->withQueryString(); 
        return view('admin.giasu.index', [ 'giasuList' => $giasuList ]);
    }

    public function edit(string $id)
    {
        $taiKhoan = TaiKhoan::with('giasu')
            ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::GIASU_ROLE_ID))
            ->findOrFail($id); 
        return view('admin.giasu.edit', [ 'taiKhoan' => $taiKhoan ]);
    }

    /**
     * Cập nhật thông tin gia sư trong CSDL.
     */
    public function update(Request $request, string $id)
    {
        // 1. Tìm tài khoản gia sư
        $taiKhoan = TaiKhoan::with('giasu')->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::GIASU_ROLE_ID))
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

            // Bảng GiaSu
            'HoTen' => 'required|string|max:150',
            'GioiTinh' => 'nullable|string|in:Nam,Nữ,Khác',
            'NgaySinh' => 'nullable|date',
            'DiaChi' => 'nullable|string|max:255',
            'BangCap' => 'nullable|string|max:255',
            'TruongDaoTao' => 'nullable|string|max:255',
            'ChuyenNganh' => 'nullable|string|max:255',
            'KinhNghiem' => 'nullable|string|max:255',
            'ThanhTich' => 'nullable|string',
            
            // 4 Trường ảnh
            'AnhDaiDien' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'AnhCCCD_MatTruoc' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'AnhCCCD_MatSau' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'AnhBangCap' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // 3. Tách dữ liệu cho 2 bảng
        $taiKhoanData = [
            'Email' => $validated['Email'],
            'SoDienThoai' => $validated['SoDienThoai'],
            'TrangThai' => $validated['TrangThai'],
        ];

        $giaSuData = [
            'HoTen' => $validated['HoTen'],
            'GioiTinh' => $validated['GioiTinh'],
            'NgaySinh' => $validated['NgaySinh'],
            'DiaChi' => $validated['DiaChi'],
            'BangCap' => $validated['BangCap'],
            'TruongDaoTao' => $validated['TruongDaoTao'],
            'ChuyenNganh' => $validated['ChuyenNganh'],
            'KinhNghiem' => $validated['KinhNghiem'],
            'ThanhTich' => $validated['ThanhTich'],
        ];

        // 4. Sử dụng DB Transaction
        try {
            // ===== SỬA DÒNG NÀY (thêm $validated) =====
            DB::transaction(function () use ($request, $taiKhoan, $taiKhoanData, $giaSuData, $validated) {
                
                // ===== THÊM LOGIC HASH MẬT KHẨU =====
                if (!empty($validated['MatKhau'])) {
                    // Cột trong CSDL của bạn là MatKhauHash
                    $taiKhoanData['MatKhauHash'] = Hash::make($validated['MatKhau']);
                }
                // ===================================

                // Cập nhật bảng TaiKhoan (Đã có MatKhauHash trong $fillable của Model)
                $taiKhoan->update($taiKhoanData);

                // --- XỬ LÝ ẢNH (TẤT CẢ LÊN IMGBB) ---
                $apiKey = env('IMAGEBB_API_KEY');

                // 4.1. ẢNH ĐẠI DIỆN
                if ($request->hasFile('AnhDaiDien')) {
                    $response = Http::attach( 'image', file_get_contents($request->file('AnhDaiDien')), $request->file('AnhDaiDien')->getClientOriginalName()
                    )->post('https://api.imgbb.com/1/upload', ['key' => $apiKey]);
                    
                    if ($response->successful()) {
                        $giaSuData['AnhDaiDien'] = $response->json()['data']['url']; 
                    }
                }

                // 4.2. CCCD MẶT TRƯỚC
                if ($request->hasFile('AnhCCCD_MatTruoc')) {
                    $response = Http::attach( 'image', file_get_contents($request->file('AnhCCCD_MatTruoc')), $request->file('AnhCCCD_MatTruoc')->getClientOriginalName()
                    )->post('https://api.imgbb.com/1/upload', ['key' => $apiKey]);
                    
                    if ($response->successful()) {
                        $giaSuData['AnhCCCD_MatTruoc'] = $response->json()['data']['url']; 
                    }
                }
                
                // 4.3. CCCD MẶT SAU
                if ($request->hasFile('AnhCCCD_MatSau')) {
                    $response = Http::attach( 'image', file_get_contents($request->file('AnhCCCD_MatSau')), $request->file('AnhCCCD_MatSau')->getClientOriginalName()
                    )->post('https://api.imgbb.com/1/upload', ['key' => $apiKey]);
                    
                    if ($response->successful()) {
                        $giaSuData['AnhCCCD_MatSau'] = $response->json()['data']['url']; 
                    }
                }

                // 4.4. ẢNH BẰNG CẤP
                if ($request->hasFile('AnhBangCap')) {
                    $response = Http::attach( 'image', file_get_contents($request->file('AnhBangCap')), $request->file('AnhBangCap')->getClientOriginalName()
                    )->post('https://api.imgbb.com/1/upload', ['key' => $apiKey]);
                    
                    if ($response->successful()) {
                        $giaSuData['AnhBangCap'] = $response->json()['data']['url']; 
                    }
                }

                // 4.5. Cập nhật hoặc Tạo mới bảng GiaSu
                GiaSu::updateOrCreate(
                    ['TaiKhoanID' => $taiKhoan->TaiKhoanID], 
                    $giaSuData
                );
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi cập nhật: ' . $e->getMessage());
        }

        // 5. Quay về trang danh sách
        return redirect()->route('admin.giasu.index')->with('success', 'Cập nhật gia sư thành công!');
    }
}