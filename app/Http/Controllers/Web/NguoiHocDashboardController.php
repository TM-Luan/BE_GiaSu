<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiaSu;
use App\Models\YeuCauNhanLop; // Thêm model này
use App\Models\LopHocYeuCau;  // Thêm model này
use Illuminate\Support\Facades\Auth; // Thêm model này

class NguoiHocDashboardController extends Controller
{
    /**
     * Hiển thị trang Dashboard với danh sách gia sư (có tìm kiếm)
     * VÀ lấy danh sách lớp của người học để chuẩn bị cho modal "Mời dạy"
     */
    public function index(Request $request)
    {
        // 1. Khởi tạo Query
        $query = GiaSu::query()->select('GiaSu.*');

        // 2. TÌM KIẾM
        $query->search($request->q); 

        // 3. SẮP XẾP
        $query->orderBy('GiaSuID', 'desc');

        // 4. LẤY DỮ LIỆU GIA SƯ
        $giasuList = $query->with('taiKhoan')
            ->with(['lopHocYeuCau' => function($q) { 
                $q->select('GiaSuID', 'HocPhi'); 
            }])
            ->withAvg('danhGia', 'DiemSo') 
            ->withCount('danhGia')
            ->where('TrangThai', 1) // CHỈ LẤY GIA SƯ ĐÃ ĐƯỢC DUYỆT
            ->whereHas('taiKhoan', fn($q) => $q->where('TrangThai', 1))
            ->paginate(6);

        $giasuList->appends(['q' => $request->q]);

        // === [PHẦN MỚI] ===
        // 5. Lấy danh sách lớp của user để hiển thị trong Modal "Mời dạy"
        $user = Auth::user();
        $myClasses = [];
        
        // Phải kiểm tra user đã có hồ sơ NguoiHoc chưa
        if ($user && $user->nguoiHoc) {
            $myClasses = LopHocYeuCau::where('NguoiHocID', $user->nguoiHoc->NguoiHocID)
                        ->where('TrangThai', 'TimGiaSu') // Chỉ lấy lớp đang tìm gia sư
                        ->with('monHoc', 'khoiLop') // Lấy tên môn/khối lớp để hiển thị
                        ->get();
        }
        // === [HẾT PHẦN MỚI] ===

        return view('nguoihoc.dashboard', [
            'giasuList' => $giasuList,
            'myClasses' => $myClasses // <-- Truyền biến này sang View
        ]);
    }

    /**
     * Hiển thị trang chi tiết hồ sơ gia sư
     */
   /**
     * Hiển thị trang chi tiết hồ sơ gia sư
     */
    public function show($id)
    {
        // Lấy thông tin gia sư - CHỈ LẤY GIA SƯ ĐÃ ĐƯỢC DUYỆT
        $giasu = GiaSu::with(['taiKhoan', 'danhGia.taiKhoan', 'lopHocYeuCau'])
            ->where('TrangThai', 1) // CHỈ LẤY GIA SƯ ĐÃ ĐƯỢC DUYỆT
            ->withAvg('danhGia', 'DiemSo')
            ->withCount('danhGia')
            ->findOrFail($id);
        
        $rating = round($giasu->danh_gia_avg_diem_so ?? 0, 1);
        $avgHocPhi = $giasu->lopHocYeuCau->avg('HocPhi');
        $hocPhi = $avgHocPhi > 0 ? number_format($avgHocPhi, 0, ',', '.') . 'đ/buổi' : 'Thỏa thuận';
        $relatedTutors = GiaSu::where('ChuyenNganh', 'LIKE', "%{$giasu->ChuyenNganh}%")
            ->where('GiaSuID', '!=', $id)
            ->where('TrangThai', 1) // CHỈ LẤY GIA SƯ ĐÃ ĐƯỢC DUYỆT
            ->limit(3)
            ->get();
            
        // === [PHẦN MỚI THÊM VÀO] ===
        // Lấy danh sách lớp của user để hiển thị trong Modal "Mời dạy"
        $user = Auth::user();
        $myClasses = [];
        
        // Phải kiểm tra user đã có hồ sơ NguoiHoc chưa
        if ($user && $user->nguoiHoc) {
            $myClasses = LopHocYeuCau::where('NguoiHocID', $user->nguoiHoc->NguoiHocID)
                        ->where('TrangThai', 'TimGiaSu') // Chỉ lấy lớp đang tìm gia sư
                        ->with('monHoc', 'khoiLop') // Lấy tên môn/khối lớp để hiển thị
                        ->get();
        }
        // === [HẾT PHẦN MỚI] ===

        return view('nguoihoc.tutor-profile', [
            'gs' => $giasu,
            'rating' => $rating,
            'hocPhi' => $hocPhi,
            'relatedTutors' => $relatedTutors,
            'myClasses' => $myClasses // <-- Truyền biến này sang View
        ]);
    }

    /**
     * === [PHƯƠNG THỨC MỚI] ===
     * Xử lý khi Người học bấm "Gửi lời mời" từ Modal
     */
    public function moiDay(Request $request)
    {
        $request->validate([
            'gia_su_id' => 'required|exists:GiaSu,GiaSuID',
            'lop_yeu_cau_id' => 'required|exists:LopHocYeuCau,LopYeuCauID',
        ]);

        // 1. Kiểm tra xem người học có sở hữu lớp này không
        $lopHoc = LopHocYeuCau::find($request->lop_yeu_cau_id);
        if ($lopHoc->nguoiHoc->TaiKhoanID != Auth::id()) {
            return back()->with('error', 'Lỗi: Bạn không sở hữu lớp học này.');
        }

        // 2. Kiểm tra xem đã mời gia sư này vào lớp này chưa (tránh spam)
        $exists = YeuCauNhanLop::where('LopYeuCauID', $request->lop_yeu_cau_id)
                    ->where('GiaSuID', $request->gia_su_id)
                    ->where('VaiTroNguoiGui', 'NguoiHoc')
                    ->where('TrangThai', 'Pending')
                    ->exists();

        if ($exists) {
            return back()->with('error', 'Bạn đã gửi lời mời cho gia sư này rồi, vui lòng chờ phản hồi.');
        }

        // 3. Tạo lời mời (dựa trên CSDL sql.sql)
        YeuCauNhanLop::create([
            'LopYeuCauID' => $request->lop_yeu_cau_id,
            'GiaSuID' => $request->gia_su_id,
            'NguoiGuiTaiKhoanID' => Auth::id(), 
            'VaiTroNguoiGui' => 'NguoiHoc',     
            'TrangThai' => 'Pending',           
            'NgayTao' => now(),
            'NgayCapNhat' => now()
        ]);

        return back()->with('success', 'Đã gửi lời mời thành công!');
    }
}