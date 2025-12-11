<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiaSu;
use App\Models\YeuCauNhanLop;
use App\Models\LopHocYeuCau;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <<< QUAN TRỌNG: Thêm import DB
use App\Helpers\FCMHelper; // <--- [1] THÊM DÒNG NÀY
use App\Models\TaiKhoan;
class NguoiHocDashboardController extends Controller
{
    /**
     * Hiển thị trang Dashboard với danh sách gia sư (có tìm kiếm)
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
            ->where('TrangThai', 1) // CHỈ LẤY GIA SƯ ĐÃ ĐƯỢC DUYỆT
            ->whereHas('taiKhoan', fn($q) => $q->where('TrangThai', 1))
            ->paginate(6);

        $giasuList->appends(['q' => $request->q]);

        // === [RAW QUERY ĐỂ TÍNH RATING CHO TRANG CHỦ] ===
        $giaSuIds = $giasuList->pluck('GiaSuID')->toArray();

        $ratingStats = DB::table('DanhGia')
            ->join('LopHocYeuCau', 'DanhGia.LopYeuCauID', '=', 'LopHocYeuCau.LopYeuCauID')
            ->whereIn('LopHocYeuCau.GiaSuID', $giaSuIds)
            ->select('LopHocYeuCau.GiaSuID', DB::raw('ROUND(AVG(DanhGia.DiemSo), 1) as rating_average'), DB::raw('COUNT(DanhGia.DanhGiaID) as rating_count'))
            ->groupBy('LopHocYeuCau.GiaSuID')
            ->get()
            ->keyBy('GiaSuID');
        
        // Gán lại kết quả tính toán vào từng đối tượng GiaSu
        $giasuList->each(function ($gs) use ($ratingStats) {
            $stats = $ratingStats->get($gs->GiaSuID);
            $gs->rating_average = $stats->rating_average ?? 0.0;
            $gs->rating_count = $stats->rating_count ?? 0;
        });
        // === [KẾT THÚC RAW QUERY] ===

        // 5. Lấy danh sách lớp của user để hiển thị trong Modal "Mời dạy"
        $user = Auth::user();
        $myClasses = [];
        
        if ($user && $user->nguoiHoc) {
            $myClasses = LopHocYeuCau::where('NguoiHocID', $user->nguoiHoc->NguoiHocID)
                        ->where('TrangThai', 'TimGiaSu')
                        ->with('monHoc', 'khoiLop')
                        ->get();
        }

        return view('nguoihoc.dashboard', [
            'giasuList' => $giasuList,
            'myClasses' => $myClasses
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
                'danhGia.taiKhoan.nguoiHoc' // Eager load đầy đủ cho danh sách đánh giá
            ])
            ->where('TrangThai', 1) // CHỈ LẤY GIA SƯ ĐÃ ĐƯỢC DUYỆT
            ->findOrFail($id);
        
        // <<< RAW QUERY CHO TRANG CHI TIẾT >>>
        // Tính toán điểm trung bình và số lượng đánh giá chính xác
        $stats = DB::table('DanhGia')
            ->join('LopHocYeuCau', 'DanhGia.LopYeuCauID', '=', 'LopHocYeuCau.LopYeuCauID')
            ->where('LopHocYeuCau.GiaSuID', $id)
            ->selectRaw('ROUND(AVG(DiemSo), 1) as rating, COUNT(*) as total')
            ->first();
            
        // Gán các thuộc tính cần thiết
        $rating = $stats->rating ?? 0.0;
        $giasu->danh_gia_count = $stats->total ?? 0; 
        // <<< KẾT THÚC RAW QUERY >>>
        
        $avgHocPhi = $giasu->lopHocYeuCau->avg('HocPhi');
        $hocPhi = $avgHocPhi > 0 ? number_format($avgHocPhi, 0, ',', '.') . 'đ/buổi' : 'Thỏa thuận';
        $relatedTutors = GiaSu::where('ChuyenNganh', 'LIKE', "%{$giasu->ChuyenNganh}%")
            ->where('GiaSuID', '!=', $id)
            ->where('TrangThai', 1)
            ->limit(3)
            ->get();
            
        // Lấy danh sách lớp của user để hiển thị trong Modal "Mời dạy"
        $user = Auth::user();
        $myClasses = [];
        
        if ($user && $user->nguoiHoc) {
            $myClasses = LopHocYeuCau::where('NguoiHocID', $user->nguoiHoc->NguoiHocID)
                        ->where('TrangThai', 'TimGiaSu')
                        ->with('monHoc', 'khoiLop')
                        ->get();
        }

        return view('nguoihoc.tutor-profile', [
            'gs' => $giasu,
            'rating' => $rating, // <<< TRUYỀN BIẾN RATING ĐÃ TÍNH TOÁN
            'hocPhi' => $hocPhi,
            'relatedTutors' => $relatedTutors,
            'myClasses' => $myClasses
        ]);
    }

    /**
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

        $lopHocInfo = LopHocYeuCau::with(['nguoiHoc.taiKhoan', 'monHoc', 'khoiLop'])->find($request->lop_yeu_cau_id);
        $giaSuNhan = GiaSu::find($request->gia_su_id);

        if ($lopHocInfo && $giaSuNhan) {
            $tenLop = ($lopHocInfo->monHoc->TenMon ?? 'Lớp học') . ' - ' . ($lopHocInfo->khoiLop->TenKhoiLop ?? '');
            
            // [QUAN TRỌNG] Khai báo biến $title và $message ở đây
            $title = 'Lời mời dạy mới';
            $message = "Bạn có lời mời dạy lớp: $tenLop";

            // A. Lưu vào Database (Sử dụng biến vừa tạo)
            \App\Models\Notification::create([
                'user_id' => $giaSuNhan->TaiKhoanID,
                'title' => $title, // Dùng biến $title
                'message' => $message, // Dùng biến $message
                'type' => 'invitation_received',
                'related_id' => $lopHocInfo->LopYeuCauID,
                'is_read' => false,
                'created_at' => now(),
            ]);

            // B. Gửi Push Notification sang Firebase
            $taiKhoanGiaSu = TaiKhoan::find($giaSuNhan->TaiKhoanID);
            
            if ($taiKhoanGiaSu && $taiKhoanGiaSu->fcm_token) {
                FCMHelper::send(
                    $taiKhoanGiaSu->fcm_token,
                    $title,   // Biến $title đã có giá trị
                    $message, // Biến $message đã có giá trị
                    [
                        'type' => 'invitation_received',
                        'id' => (string)$lopHocInfo->LopYeuCauID
                    ]
                );
            }
        }

        return back()->with('success', 'Đã gửi lời mời thành công!');
    }

}