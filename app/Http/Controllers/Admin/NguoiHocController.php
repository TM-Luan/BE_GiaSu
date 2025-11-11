<?php
namespace App\Http\Controllers\Admin; // Namespace Admin

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use App\Models\NguoiHoc; // <-- Thêm dòng này
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- Thêm dòng này
use Illuminate\Validation\Rule; // <-- Thêm dòng này

class NguoiHocController extends Controller
{
    private const NGUOIHOC_ROLE_ID = 3;

    public function index(Request $request)
    {
        $query = TaiKhoan::with('nguoihoc', 'phanquyen')
            ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::NGUOIHOC_ROLE_ID))
            ->orderByDesc('TaiKhoanID');

        // Xử lý Tìm kiếm
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('Email', 'like', "%$search%")
                  ->orWhere('SoDienThoai', 'like', "%$search%")
                  ->orWhereHas('nguoihoc', fn($q) => $q->where('HoTen', 'like', "%$search%"));
            });
        }

        // Lọc theo Trạng thái
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
        // Tìm tài khoản với vai trò Người học, 
        // và tải kèm thông tin 'nguoihoc'
        $taiKhoan = TaiKhoan::with('nguoihoc')
            ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::NGUOIHOC_ROLE_ID))
            ->findOrFail($id); // findOrFail sẽ báo lỗi 404 nếu không tìm thấy

        // Trả về view 'edit' và truyền dữ liệu $taiKhoan
        return view('admin.nguoihoc.edit', [
            'taiKhoan' => $taiKhoan
        ]);
    }
    public function update(Request $request, string $id)
    {
        // 1. Tìm tài khoản người học
        $taiKhoan = TaiKhoan::whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::NGUOIHOC_ROLE_ID))
            ->findOrFail($id);

        // 2. Validate dữ liệu
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

            // Bảng NguoiHoc
            'HoTen' => 'required|string|max:150',
            'GioiTinh' => 'nullable|string|in:Nam,Nữ,Khác',
            'NgaySinh' => 'nullable|date',
            'DiaChi' => 'nullable|string|max:255',
        ]);

        // 3. Sử dụng DB Transaction để đảm bảo an toàn
        try {
            DB::transaction(function () use ($taiKhoan, $validated) {

                // Cập nhật bảng TaiKhoan
                $taiKhoan->update([
                    'Email' => $validated['Email'],
                    'SoDienThoai' => $validated['SoDienThoai'],
                    'TrangThai' => $validated['TrangThai'],
                ]);

                // Cập nhật hoặc Tạo mới bảng NguoiHoc
                // (Dùng updateOrCreate phòng trường hợp người học chưa có record profile)
                NguoiHoc::updateOrCreate(
                    ['TaiKhoanID' => $taiKhoan->TaiKhoanID], // Điều kiện tìm
                    [ // Dữ liệu cập nhật/tạo mới
                        'HoTen' => $validated['HoTen'],
                        'GioiTinh' => $validated['GioiTinh'],
                        'NgaySinh' => $validated['NgaySinh'],
                        'DiaChi' => $validated['DiaChi'],
                    ]
                );
            });
        } catch (\Exception $e) {
            // Nếu có lỗi, quay lại và báo lỗi
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi cập nhật: ' . $e->getMessage());
        }

        // 4. Quay về trang danh sách
        return redirect()->route('admin.nguoihoc.index')->with('success', 'Cập nhật người học thành công!');
    }
}