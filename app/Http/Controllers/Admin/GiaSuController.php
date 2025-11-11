<?php
namespace App\Http\Controllers\Admin; // Namespace Admin

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use App\Models\GiaSu; // <-- Thêm dòng này
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- Thêm dòng này
use Illuminate\Validation\Rule; // <-- Thêm dòng này

class GiaSuController extends Controller
{
    private const GIASU_ROLE_ID = 2;

    public function index(Request $request)
    {
        $query = TaiKhoan::with('giasu', 'phanquyen')
            ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::GIASU_ROLE_ID))
            ->orderByDesc('TaiKhoanID');

        // Xử lý Tìm kiếm
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('Email', 'like', "%$search%")
                  ->orWhere('SoDienThoai', 'like', "%$search%")
                  ->orWhereHas('giasu', fn($q) => $q->where('HoTen', 'like', "%$search%"));
            });
        }

        // Lọc theo Trạng thái
        if ($trangthai = $request->input('trangthai')) {
            if ($trangthai === '0' || $trangthai === '1') {
                $query->where('TrangThai', (int)$trangthai);
            }
        }
        
        $giasuList = $query->paginate(10)->withQueryString(); 

        return view('admin.giasu.index', [
            'giasuList' => $giasuList
        ]);
    }
    public function edit(string $id)
    {
        // Tìm tài khoản với vai trò Gia sư,
        // và tải kèm thông tin 'giasu'
        $taiKhoan = TaiKhoan::with('giasu')
            ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::GIASU_ROLE_ID))
            ->findOrFail($id); // findOrFail sẽ báo lỗi 404 nếu không tìm thấy

        // Trả về view 'edit' và truyền dữ liệu $taiKhoan
        return view('admin.giasu.edit', [
            'taiKhoan' => $taiKhoan
        ]);
    }

/**
 * Cập nhật thông tin gia sư trong CSDL.
 */
    public function update(Request $request, string $id)
    {
        // 1. Tìm tài khoản gia sư
        $taiKhoan = TaiKhoan::whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::GIASU_ROLE_ID))
            ->findOrFail($id);

        // 2. Validate dữ liệu (Thêm các trường của Gia sư)
        $validated = $request->validate([
            // Bảng TaiKhoan
            'Email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('TaiKhoan', 'Email')->ignore($taiKhoan->TaiKhoanID, 'TaiKhoanID')
            ],
            'SoDienThoai' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('TaiKhoan', 'SoDienThoai')->ignore($taiKhoan->TaiKhoanID, 'TaiKhoanID')
            ],
            'TrangThai' => 'required|boolean',

            // Bảng GiaSu (Lấy từ file sql.sql)
            'HoTen' => 'required|string|max:150',
            'GioiTinh' => 'nullable|string|in:Nam,Nữ,Khác',
            'NgaySinh' => 'nullable|date',
            'DiaChi' => 'nullable|string|max:255',
            'TruongDaoTao' => 'nullable|string|max:255',
            'ChuyenNganh' => 'nullable|string|max:255',
            'KinhNghiem' => 'nullable|string|max:255',
            'ThanhTich' => 'nullable|string',
        ]);

        // 3. Sử dụng DB Transaction
        try {
            DB::transaction(function () use ($taiKhoan, $validated) {

                // Cập nhật bảng TaiKhoan
                $taiKhoan->update([
                    'Email' => $validated['Email'],
                    'SoDienThoai' => $validated['SoDienThoai'],
                    'TrangThai' => $validated['TrangThai'],
                ]);

                // Cập nhật hoặc Tạo mới bảng GiaSu
                GiaSu::updateOrCreate(
                    ['TaiKhoanID' => $taiKhoan->TaiKhoanID], // Điều kiện tìm
                    [ // Dữ liệu cập nhật/tạo mới
                        'HoTen' => $validated['HoTen'],
                        'GioiTinh' => $validated['GioiTinh'],
                        'NgaySinh' => $validated['NgaySinh'],
                        'DiaChi' => $validated['DiaChi'],
                        'TruongDaoTao' => $validated['TruongDaoTao'],
                        'ChuyenNganh' => $validated['ChuyenNganh'],
                        'KinhNghiem' => $validated['KinhNghiem'],
                        'ThanhTich' => $validated['ThanhTich'],
                    ]
                );
            });
        } catch (\Exception $e) {
            // Nếu có lỗi, quay lại và báo lỗi
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi cập nhật: ' . $e->getMessage());
        }

        // 4. Quay về trang danh sách
        return redirect()->route('admin.giasu.index')->with('success', 'Cập nhật gia sư thành công!');
    }
}