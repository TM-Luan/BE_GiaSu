<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiaSu;
use App\Models\YeuCauNhanLop; // Thêm model này
use App\Models\LopHocYeuCau;  // Thêm model này
use Illuminate\Support\Facades\Auth; // Thêm model này
use Illuminate\Support\Facades\DB; // <<< QUAN TRỌNG: Thêm import DB

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
            // Bỏ withAvg/withCount ở đây vì nó gây lỗi trong show()
            // ->withAvg('danhGia', 'DiemSo') 
            // ->withCount('danhGia')
            ->where('TrangThai', 1) // CHỈ LẤY GIA SƯ ĐÃ ĐƯỢC DUYỆT
            ->whereHas('taiKhoan', fn($q) => $q->where('TrangThai', 1))
            ->paginate(6);

        // [MỚI] TÍNH TOÁN RATING CHO TỪNG GIA SƯ TRONG DANH SÁCH (cho dashboard)
        $giasuList->each(function ($gs) {
            $stats = DB::table('DanhGia')
                ->join('LopHocYeuCau', 'DanhGia.LopYeuCauID', '=', 'LopHocYeuCau.LopYeuCauID')
                ->where('LopHocYeuCau.GiaSuID', $gs->GiaSuID)
                ->selectRaw('ROUND(AVG(DiemSo), 1) as rating, COUNT(*) as count')
                ->first();
            $gs->rating_average = $stats->rating ?? 0.0;
            $gs->rating_count = $stats->count ?? 0;
        });

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
    public function show($id)
    {
        // Lấy thông tin gia sư - CHỈ LẤY GIA SƯ ĐÃ ĐƯỢC DUYỆT
        $giasu = GiaSu::with([
                'taiKhoan', 
                'lopHocYeuCau',
                'danhGia.taiKhoan.nguoiHoc' // ĐÃ SỬA: Eager load đầy đủ cho danh sách đánh giá
            ])
            ->where('TrangThai', 1) // CHỈ LẤY GIA SƯ ĐÃ ĐƯỢC DUYỆT
            ->findOrFail($id);
        
        // <<< THAY THẾ withAvg/withCount BẰNG RAW QUERY ĐỂ ĐẢM BẢO TÍNH TOÁN ĐÚNG >>>
        $stats = DB::table('DanhGia')
            ->join('LopHocYeuCau', 'DanhGia.LopYeuCauID', '=', 'LopHocYeuCau.LopYeuCauID')
            ->where('LopHocYeuCau.GiaSuID', $id)
            ->selectRaw('ROUND(AVG(DiemSo), 1) as rating, COUNT(*) as count')
            ->first();
            
        // Gán kết quả tính toán vào đối tượng $giasu
        $giasu->rating_average = $stats->rating ?? 0.0;
        $giasu->rating_count = $stats->count ?? 0;
        // <<< KẾT THÚC RAW QUERY >>>
        
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
            // Đã xóa $rating cũ vì nó được gán vào $giasu->rating_average
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

        // --- Tạo thông báo cho gia sư (giống mobile) ---
        $lopHocInfo = LopHocYeuCau::with(['nguoiHoc.taiKhoan', 'monHoc', 'khoiLop'])->find($request->lop_yeu_cau_id);
        $tenNguoiGui = Auth::user()->HoTen ?? 'Người dùng';
        $giaSuNhan = \App\Models\GiaSu::find($request->gia_su_id);

        if ($lopHocInfo && $giaSuNhan) {
            $tenLop = ($lopHocInfo->monHoc->TenMon ?? 'Lớp học') . ' - ' . ($lopHocInfo->khoiLop->TenKhoiLop ?? '');
            $message = "$tenNguoiGui đã mời bạn dạy lớp: $tenLop";

            \App\Models\Notification::create([
                'user_id' => $giaSuNhan->TaiKhoanID,
                'title' => 'Lời mời dạy mới',
                'message' => $message,
                'type' => 'invitation_received',
                'related_id' => $lopHocInfo->LopYeuCauID,
                'is_read' => false,
                'created_at' => now(),
            ]);
        }

        return back()->with('success', 'Đã gửi lời mời thành công!');
    }
}