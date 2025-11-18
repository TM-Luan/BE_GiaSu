<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\LopHocYeuCau;
use App\Models\YeuCauNhanLop;

class GiaSuDashboardController extends Controller
{
    /**
     * Hiển thị dashboard cho gia sư
     * Tương tự student dashboard nhưng hiển thị danh sách lớp học thay vì gia sư
     */
    public function index(Request $request)
    {
        $giaSu = Auth::guard('giasu')->user();
        
        if (!$giaSu) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        // Lấy các tham số tìm kiếm từ request
        $search = $request->input('search');
        $monHoc = $request->input('mon_hoc');
        $lopHoc = $request->input('lop_hoc');
        $tinhThanh = $request->input('tinh_thanh');
        $quanHuyen = $request->input('quan_huyen');
        $sortBy = $request->input('sort_by', 'newest'); // newest, fee_high, fee_low

        // Query lấy danh sách lớp học đang cần tìm gia sư (TrangThai = 'TimGiaSu')
        $query = LopHocYeuCau::with('nguoiHoc.taiKhoan')
            ->where('TrangThai', 'TimGiaSu');

        // Tìm kiếm theo tên lớp học hoặc mô tả
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('TieuDeLop', 'LIKE', "%{$search}%")
                  ->orWhere('MoTa', 'LIKE', "%{$search}%");
            });
        }

        // Lọc theo môn học
        if ($monHoc) {
            $query->where('MonHoc', $monHoc);
        }

        // Lọc theo lớp học
        if ($lopHoc) {
            $query->where('LopHoc', $lopHoc);
        }

        // Lọc theo tỉnh thành
        if ($tinhThanh) {
            $query->where('TinhThanh', $tinhThanh);
        }

        // Lọc theo quận huyện
        if ($quanHuyen) {
            $query->where('QuanHuyen', $quanHuyen);
        }

        // Sắp xếp
        switch ($sortBy) {
            case 'fee_high':
                $query->orderBy('HocPhiMuonNhan', 'desc');
                break;
            case 'fee_low':
                $query->orderBy('HocPhiMuonNhan', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('NgayTao', 'desc');
                break;
        }

        $lopHocList = $query->paginate(12)->withQueryString();

        // Lấy danh sách options cho filter dropdowns
        $monHocList = DB::table('LopHocYeuCau')
            ->select('MonHoc')
            ->distinct()
            ->whereNotNull('MonHoc')
            ->where('TrangThai', 'TimGiaSu')
            ->orderBy('MonHoc')
            ->pluck('MonHoc');

        $lopHocLevels = DB::table('LopHocYeuCau')
            ->select('LopHoc')
            ->distinct()
            ->whereNotNull('LopHoc')
            ->where('TrangThai', 'TimGiaSu')
            ->orderBy('LopHoc')
            ->pluck('LopHoc');

        $tinhThanhList = DB::table('LopHocYeuCau')
            ->select('TinhThanh')
            ->distinct()
            ->whereNotNull('TinhThanh')
            ->where('TrangThai', 'TimGiaSu')
            ->orderBy('TinhThanh')
            ->pluck('TinhThanh');

        return view('giasu.dashboard', compact(
            'giaSu',
            'lopHocList',
            'monHocList',
            'lopHocLevels',
            'tinhThanhList',
            'search',
            'monHoc',
            'lopHoc',
            'tinhThanh',
            'quanHuyen',
            'sortBy'
        ));
    }

    /**
     * Gửi đề nghị dạy lớp học
     * Tương tự student's "mời dạy" nhưng ngược lại - gia sư gửi đề nghị cho lớp học
     */
    public function deNghiDay(Request $request)
    {
        $giaSu = Auth::guard('giasu')->user();
        
        if (!$giaSu) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập.'
            ], 401);
        }

        $validated = $request->validate([
            'lop_hoc_id' => 'required|exists:LopHocYeuCau,MaLop',
            'ghi_chu' => 'nullable|string|max:500'
        ]);

        $lopHoc = LopHocYeuCau::find($validated['lop_hoc_id']);

        // Kiểm tra lớp học có đang cần tìm gia sư không
        if ($lopHoc->TrangThai !== 'TimGiaSu') {
            return response()->json([
                'success' => false,
                'message' => 'Lớp học này không còn cần tìm gia sư.'
            ], 400);
        }

        // Kiểm tra đã gửi đề nghị cho lớp học này chưa
        $existing = YeuCauNhanLop::where('LopHocYeuCauID', $validated['lop_hoc_id'])
            ->where('GiaSuID', $giaSu->MaGiaSu)
            ->whereIn('TrangThai', ['ChoDuyet', 'DaDuyet'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã gửi đề nghị dạy lớp học này rồi.'
            ], 400);
        }

        try {
            // Tạo yêu cầu nhận lớp với VaiTroNguoiGui = 'GiaSu'
            YeuCauNhanLop::create([
                'LopHocYeuCauID' => $validated['lop_hoc_id'],
                'GiaSuID' => $giaSu->MaGiaSu,
                'NguoiGuiTaiKhoanID' => $giaSu->TaiKhoanID,
                'VaiTroNguoiGui' => 'GiaSu', // Gia sư gửi đề nghị
                'GhiChu' => $validated['ghi_chu'],
                'TrangThai' => 'ChoDuyet',
                'NgayTao' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi đề nghị dạy lớp "' . $lopHoc->TieuDeLop . '" thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
