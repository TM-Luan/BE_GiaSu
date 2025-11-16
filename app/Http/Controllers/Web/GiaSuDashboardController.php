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
     * Hiển thị trang dashboard cho Gia sư.
     */
    public function index(Request $request)
    {
        // Lấy user đang đăng nhập (dùng guard web mặc định)
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        // Kiểm tra phải là gia sư (VaiTroID = 2)
        $giaSu = $user->giaSu;
        if (!$giaSu) {
            return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        // Lấy các tham số tìm kiếm từ request
        $search = $request->input('search');
        $monId = $request->input('mon_id'); // Đổi từ mon_hoc sang mon_id
        $khoiLopId = $request->input('khoi_lop_id'); // Đổi từ lop_hoc sang khoi_lop_id
        $sortBy = $request->input('sort_by', 'newest');

        // Query lấy danh sách lớp học đang cần tìm gia sư
        $query = LopHocYeuCau::with(['nguoiHoc.taiKhoan', 'monHoc', 'khoiLop'])
            ->where('TrangThai', 'TimGiaSu');

        // Tìm kiếm theo mô tả hoặc tên môn học
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('MoTa', 'LIKE', "%{$search}%")
                  ->orWhereHas('monHoc', function($mq) use ($search) {
                      $mq->where('TenMon', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Lọc theo môn học
        if ($monId) {
            $query->where('MonID', $monId);
        }

        // Lọc theo khối lớp
        if ($khoiLopId) {
            $query->where('KhoiLopID', $khoiLopId);
        }

        // Sắp xếp
        switch ($sortBy) {
            case 'fee_high':
                $query->orderBy('HocPhi', 'desc');
                break;
            case 'fee_low':
                $query->orderBy('HocPhi', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('NgayTao', 'desc');
                break;
        }

        $lopHocList = $query->paginate(12)->withQueryString();

        // Lấy danh sách môn học
        $monHocList = DB::table('MonHoc')
            ->orderBy('TenMon')
            ->get();

        // Lấy danh sách khối lớp
        $khoiLopList = DB::table('KhoiLop')
            ->orderBy('BacHoc')
            ->get();

        return view('giasu.dashboard', compact(
            'giaSu',
            'lopHocList',
            'monHocList',
            'khoiLopList',
            'search',
            'monId',
            'khoiLopId',
            'sortBy'
        ));
    }

    /**
     * Gửi đề nghị dạy lớp học
     * Tương tự student's "mời dạy" nhưng ngược lại - gia sư gửi đề nghị cho lớp học
     */
    public function deNghiDay(Request $request)
    {
        // Lấy user đang đăng nhập
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập.'
            ], 401);
        }

        // Lấy thông tin gia sư
        $giaSu = $user->giaSu;
        if (!$giaSu) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ gia sư mới có thể gửi đề nghị dạy.'
            ], 403);
        }

        $validated = $request->validate([
            'lop_hoc_id' => 'required|exists:LopHocYeuCau,LopYeuCauID',
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
        $existing = YeuCauNhanLop::where('LopYeuCauID', $validated['lop_hoc_id'])
            ->where('GiaSuID', $giaSu->GiaSuID)
            ->whereIn('TrangThai', ['Pending', 'Accepted'])
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
                'LopYeuCauID' => $validated['lop_hoc_id'],
                'GiaSuID' => $giaSu->GiaSuID,
                'NguoiGuiTaiKhoanID' => $user->TaiKhoanID,
                'VaiTroNguoiGui' => 'GiaSu',
                'TrangThai' => 'Pending',
                'GhiChu' => $validated['ghi_chu'],
                'NgayTao' => now(),
                'NgayCapNhat' => now()
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

    /**
     * Hiển thị danh sách lớp học đã được chấp nhận của gia sư
     */
    public function myClasses(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        $giaSu = $user->giaSu;
        if (!$giaSu) {
            return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        // Lấy các lớp học đã được chấp nhận (GiaSuID = current user)
        $lopHocList = LopHocYeuCau::with(['nguoiHoc.taiKhoan', 'monHoc', 'khoiLop'])
            ->where('GiaSuID', $giaSu->GiaSuID)
            ->whereIn('TrangThai', ['DangHoc', 'HoanThanh'])
            ->orderBy('NgayTao', 'desc')
            ->paginate(12);

        // Lấy các đề nghị đang chờ duyệt
        $pendingProposals = YeuCauNhanLop::with(['lopHocYeuCau.monHoc', 'lopHocYeuCau.khoiLop', 'lopHocYeuCau.nguoiHoc.taiKhoan'])
            ->where('GiaSuID', $giaSu->GiaSuID)
            ->where('VaiTroNguoiGui', 'GiaSu')
            ->where('TrangThai', 'Pending')
            ->orderBy('NgayTao', 'desc')
            ->get();

        return view('giasu.my-classes', compact('giaSu', 'lopHocList', 'pendingProposals'));
    }

    /**
     * Hiển thị chi tiết lớp học
     */
    public function showClass($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        $giaSu = $user->giaSu;
        if (!$giaSu) {
            return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        // Lấy lớp học với relation
        $lopHoc = LopHocYeuCau::with(['nguoiHoc.taiKhoan', 'monHoc', 'khoiLop', 'doiTuong'])
            ->where('LopYeuCauID', $id)
            ->where('GiaSuID', $giaSu->GiaSuID)
            ->firstOrFail();

        return view('giasu.class-detail', compact('lopHoc', 'giaSu'));
    }
}
