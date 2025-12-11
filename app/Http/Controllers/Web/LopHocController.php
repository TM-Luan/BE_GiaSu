<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LopHocYeuCau;
use App\Models\MonHoc;
use App\Models\KhoiLop;
use App\Models\DoiTuong;
use Illuminate\Support\Facades\DB; // <--- THÊM DÒNG NÀY
use App\Models\KhieuNai;

class LopHocController extends Controller
{
    /**
     * Hiển thị trang "Lớp học của tôi" (Giống hình ảnh)
     */
    public function index(Request $request)
    {
        // Lấy NguoiHocID của user đang đăng nhập
        $nguoiHocId = Auth::user()->nguoiHoc->NguoiHocID;

        // Khởi tạo query
        $query = LopHocYeuCau::where('NguoiHocID', $nguoiHocId)
            ->with('monHoc', 'khoiLop', 'giaSu', 'yeuCauNhanLops'); // Lấy kèm thông tin

        // Lọc theo Trạng thái (Tất cả, Đang tìm, Đã có, Hoàn thành)
        if ($request->has('trangthai') && !empty($request->trangthai)) {
            $query->where('TrangThai', $request->trangthai);
        }

        // Lọc theo Từ khóa (Tìm kiếm tên lớp, môn học)
        if ($request->has('q') && !empty($request->q)) {
            $keyword = $request->q;
            $query->whereHas('monHoc', function ($q) use ($keyword) {
                $q->where('TenMon', 'LIKE', "%{$keyword}%");
            });
        }

        // Sắp xếp mới nhất lên đầu
        $lopHocList = $query->orderBy('NgayTao', 'desc')->paginate(9);

        // Giữ lại tham số lọc trên URL khi phân trang
        $lopHocList->appends($request->all());

        return view('nguoihoc.lop-hoc-index', compact('lopHocList'));
    }

    /**
     * Hiển thị form "Tạo lớp học mới"
     */
    public function create()
    {
        // Lấy dữ liệu cho các dropdown
        $monHocList = MonHoc::all();
        $khoiLopList = KhoiLop::all();
        $doiTuongList = DoiTuong::all();

        return view('nguoihoc.lop-hoc-create', compact('monHocList', 'khoiLopList', 'doiTuongList'));
    }

    /**
     * Lưu lớp học mới vào CSDL
     */
    public function store(Request $request)
    {
        // 1. Validate dữ liệu
        $validated = $request->validate([
            'MonID' => 'required|exists:MonHoc,MonID',
            'KhoiLopID' => 'required|exists:KhoiLop,KhoiLopID',
            'DoiTuongID' => 'required|exists:DoiTuong,DoiTuongID',
            'HinhThuc' => 'required|in:Online,Offline',
            'HocPhi' => 'required|numeric|min:0',
            'ThoiLuong' => 'required|integer|in:60,90,120', // Thời lượng (phút)
            'SoBuoiTuan' => 'required|integer|min:1|max:5',
            'LichHocMongMuon' => 'required|string|max:255',
            'MoTa' => 'nullable|string|max:1000',
        ]);

        // 2. Lấy NguoiHocID
        $nguoiHocId = Auth::user()->nguoiHoc->NguoiHocID;

        // 3. Tạo lớp học mới
        LopHocYeuCau::create(array_merge($validated, [
            'NguoiHocID' => $nguoiHocId,
            'TrangThai' => 'TimGiaSu', // Trạng thái mặc định khi mới tạo
            'NgayTao' => now(),
            'SoLuong' => 1 // Mặc định là 1 học viên
        ]));

        // 4. Chuyển hướng về trang danh sách
        return redirect()->route('nguoihoc.lophoc.index')->with('success', 'Tạo lớp học mới thành công!');
    }
    // ... (Các hàm index, create, store cũ giữ nguyên)

    /**
     * Hiển thị danh sách Gia sư ứng tuyển (Đề nghị) cho một lớp
     */
    public function showProposals($id)
    {
        $nguoiHocId = Auth::user()->nguoiHoc->NguoiHocID;

        // 1. Lấy thông tin lớp học & kiểm tra quyền sở hữu
        $lopHoc = LopHocYeuCau::where('LopYeuCauID', $id)
            ->where('NguoiHocID', $nguoiHocId)
            ->with(['monHoc', 'khoiLop'])
            ->firstOrFail();

        // 2. Lấy danh sách đề nghị (Chỉ lấy những yêu cầu do Gia sư gửi đến)
        // Quan hệ: LopHocYeuCau -> hasMany YeuCauNhanLop
        $proposals = \App\Models\YeuCauNhanLop::where('LopYeuCauID', $id)
            ->where('VaiTroNguoiGui', 'GiaSu') // Quan trọng: Chỉ lấy gia sư ứng tuyển
            ->whereIn('TrangThai', ['Pending', 'Accepted', 'Rejected']) // Lấy tất cả trạng thái để xem lịch sử
            ->with(['giaSu.taiKhoan', 'giaSu.danhGia']) // Eager load thông tin gia sư
            ->orderBy('NgayTao', 'desc')
            ->get();

        return view('nguoihoc.lop-hoc-proposals', compact('lopHoc', 'proposals'));
    }

    /**
     * Chấp nhận một gia sư
     */
    /**
     * Chấp nhận một gia sư
     */
    public function acceptProposal($yeuCauId)
    {
        $nguoiHocId = Auth::user()->nguoiHoc->NguoiHocID;

        // 1. Tìm yêu cầu
        $yeuCau = \App\Models\YeuCauNhanLop::findOrFail($yeuCauId);

        // 2. Validate: Lớp này có phải của người đang đăng nhập không?
        if ($yeuCau->lop->NguoiHocID != $nguoiHocId) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        // 3. Validate: Lớp đã có người dạy chưa?
        if ($yeuCau->lop->TrangThai != 'TimGiaSu') {
            return back()->with('error', 'Lớp học này đã kết thúc hoặc đang học, không thể nhận thêm.');
        }

        // 4. Xử lý giao dịch
        // SỬA Ở ĐÂY: Bỏ dấu "\" đi, chỉ dùng "DB::" vì đã use ở trên đầu file
        DB::transaction(function () use ($yeuCau) {
            // A. Cập nhật trạng thái Yêu cầu này thành Accepted
            $yeuCau->update([
                'TrangThai' => 'Accepted',
                'NgayCapNhat' => now()
            ]);

            // B. Cập nhật trạng thái Lớp học -> DangHoc & Gán GiaSuID
            $yeuCau->lop->update([
                'TrangThai' => 'DangHoc',
                'GiaSuID' => $yeuCau->GiaSuID
            ]);

            // C. Từ chối tất cả các yêu cầu Pending khác của lớp này
            \App\Models\YeuCauNhanLop::where('LopYeuCauID', $yeuCau->LopYeuCauID)
                ->where('YeuCauID', '!=', $yeuCau->YeuCauID)
                ->where('TrangThai', 'Pending')
                ->update(['TrangThai' => 'Rejected']);
        });

        // --- Tạo thông báo cho gia sư (giống mobile) ---
        $lopHocInfo = \App\Models\LopHocYeuCau::with(['monHoc', 'khoiLop'])->find($yeuCau->LopYeuCauID);
        $giaSuInfo = \App\Models\GiaSu::find($yeuCau->GiaSuID);

        if ($lopHocInfo && $giaSuInfo) {
            $tenLop = ($lopHocInfo->monHoc->TenMon ?? 'Lớp học') . ' - ' . ($lopHocInfo->khoiLop->TenKhoiLop ?? '');

            \App\Models\Notification::create([
                'user_id' => $giaSuInfo->TaiKhoanID,
                'title' => 'Yêu cầu được chấp nhận',
                'message' => "Yêu cầu dạy lớp $tenLop đã được chấp nhận",
                'type' => 'request_accepted',
                'related_id' => $lopHocInfo->LopYeuCauID,
                'is_read' => false,
            ]);
            $taiKhoanGiaSu = \App\Models\TaiKhoan::find($giaSuInfo->TaiKhoanID);
            if ($taiKhoanGiaSu && $taiKhoanGiaSu->fcm_token) {
                \App\Helpers\FCMHelper::send(
                    $taiKhoanGiaSu->fcm_token,
                    'Yêu cầu được chấp nhận',
                    "Yêu cầu dạy lớp $tenLop đã được chấp nhận",
                    [
                        'type' => 'request_accepted',
                        'id' => (string) $lopHocInfo->LopYeuCauID
                    ]
                );
            }
        }

        return back()->with('success', 'Đã chấp nhận gia sư thành công! Lớp học đã chuyển sang trạng thái Đang học.');
    }

    /**
     * Từ chối một gia sư
     */
    public function rejectProposal($yeuCauId)
    {
        $nguoiHocId = Auth::user()->nguoiHoc->NguoiHocID;
        $yeuCau = \App\Models\YeuCauNhanLop::findOrFail($yeuCauId);

        if ($yeuCau->lop->NguoiHocID != $nguoiHocId) {
            abort(403);
        }

        $yeuCau->update([
            'TrangThai' => 'Rejected',
            'NgayCapNhat' => now()
        ]);

        // --- Tạo thông báo cho gia sư (giống mobile) ---
        $lopHocInfo = \App\Models\LopHocYeuCau::with(['monHoc', 'khoiLop'])->find($yeuCau->LopYeuCauID);
        $giaSuInfo = \App\Models\GiaSu::find($yeuCau->GiaSuID);

        if ($lopHocInfo && $giaSuInfo) {
            $tenLop = ($lopHocInfo->monHoc->TenMon ?? 'Lớp học') . ' - ' . ($lopHocInfo->khoiLop->TenKhoiLop ?? '');

            \App\Models\Notification::create([
                'user_id' => $giaSuInfo->TaiKhoanID,
                'title' => 'Yêu cầu bị từ chối',
                'message' => "Yêu cầu dạy lớp $tenLop đã bị từ chối",
                'type' => 'request_rejected',
                'related_id' => $lopHocInfo->LopYeuCauID,
                'is_read' => false,
            ]);
            $taiKhoanGiaSu = \App\Models\TaiKhoan::find($giaSuInfo->TaiKhoanID);
            if ($taiKhoanGiaSu && $taiKhoanGiaSu->fcm_token) {
                \App\Helpers\FCMHelper::send(
                    $taiKhoanGiaSu->fcm_token,
                    'Yêu cầu bị từ chối',
                    "Yêu cầu dạy lớp $tenLop đã bị từ chối",
                    [
                        'type' => 'request_rejected',
                        'id' => (string) $lopHocInfo->LopYeuCauID
                    ]
                );
            }
        }


        return back()->with('success', 'Đã từ chối yêu cầu.');
    }
    /**
     * Hiển thị form Sửa lớp học
     */
    public function edit($id)
    {
        $nguoiHocId = Auth::user()->nguoiHoc->NguoiHocID;

        // 1. Tìm lớp học và kiểm tra quyền sở hữu
        $lopHoc = LopHocYeuCau::where('LopYeuCauID', $id)
            ->where('NguoiHocID', $nguoiHocId)
            ->firstOrFail();

        // 2. Kiểm tra trạng thái (Chỉ cho sửa khi đang tìm gia sư)
        if ($lopHoc->TrangThai !== 'TimGiaSu') {
            return redirect()->route('nguoihoc.lophoc.index')
                ->with('error', 'Không thể sửa lớp học đã có gia sư hoặc đã kết thúc.');
        }

        // 3. Lấy dữ liệu dropdown (như hàm create)
        $monHocList = MonHoc::all();
        $khoiLopList = KhoiLop::all();
        $doiTuongList = DoiTuong::all();

        return view('nguoihoc.lop-hoc-edit', compact('lopHoc', 'monHocList', 'khoiLopList', 'doiTuongList'));
    }

    /**
     * Cập nhật thông tin lớp học vào CSDL
     */
    public function update(Request $request, $id)
    {
        $nguoiHocId = Auth::user()->nguoiHoc->NguoiHocID;

        // 1. Tìm và kiểm tra (Validation)
        $lopHoc = LopHocYeuCau::where('LopYeuCauID', $id)
            ->where('NguoiHocID', $nguoiHocId)
            ->firstOrFail();

        if ($lopHoc->TrangThai !== 'TimGiaSu') {
            return back()->with('error', 'Không thể sửa lớp học này.');
        }

        // 2. Validate dữ liệu đầu vào
        $validated = $request->validate([
            'MonID' => 'required|exists:MonHoc,MonID',
            'KhoiLopID' => 'required|exists:KhoiLop,KhoiLopID',
            'DoiTuongID' => 'required|exists:DoiTuong,DoiTuongID',
            'HinhThuc' => 'required|in:Online,Offline',
            'HocPhi' => 'required|numeric|min:0',
            'ThoiLuong' => 'required|integer|min:30',
            'SoBuoiTuan' => 'required|integer|min:1',
            'LichHocMongMuon' => 'required|string|max:255',
            'MoTa' => 'nullable|string|max:1000',
        ]);

        // 3. Thực hiện Update
        $lopHoc->update($validated);

        // 4. Redirect về trang danh sách
        return redirect()->route('nguoihoc.lophoc.index')->with('success', 'Cập nhật thông tin lớp học thành công!');
    }
    /**
     * Đóng (Hủy) lớp học đang tìm gia sư
     */
    public function cancel($id)
    {
        $nguoiHocId = Auth::user()->nguoiHoc->NguoiHocID;

        // 1. Tìm lớp và kiểm tra quyền sở hữu
        $lopHoc = LopHocYeuCau::where('LopYeuCauID', $id)
            ->where('NguoiHocID', $nguoiHocId)
            ->firstOrFail();

        // 2. Chỉ cho phép hủy khi đang tìm gia sư
        if ($lopHoc->TrangThai !== 'TimGiaSu') {
            return back()->with('error', 'Chỉ có thể hủy các lớp đang tìm gia sư.');
        }

        // 3. Thực hiện hủy lớp và hủy các yêu cầu liên quan
        DB::transaction(function () use ($lopHoc) {
            // A. Cập nhật trạng thái lớp thành "Hủy"
            $lopHoc->update([
                'TrangThai' => 'Huy'
            ]);

            // B. Cập nhật tất cả các yêu cầu 'Pending' của lớp này thành 'Cancelled'
            // Để các gia sư biết lớp này đã bị chủ đóng
            $lopHoc->yeuCauNhanLops()
                ->where('TrangThai', 'Pending')
                ->update(['TrangThai' => 'Cancelled']);
        });

        return back()->with('success', 'Đã đóng lớp học thành công.');
    }
    /**
     * Xóa vĩnh viễn lớp học (Chỉ áp dụng cho lớp Đã Hủy)
     */
    public function destroy($id)
    {
        $nguoiHocId = Auth::user()->nguoiHoc->NguoiHocID;

        // 1. Tìm lớp và kiểm tra chính chủ
        $lopHoc = LopHocYeuCau::where('LopYeuCauID', $id)
            ->where('NguoiHocID', $nguoiHocId)
            ->firstOrFail();

        // 2. Kiểm tra điều kiện xóa: Chỉ xóa được lớp "Đã hủy" hoặc "Đang tìm"
        // (Không cho xóa lớp Đang học hoặc Đã hoàn thành để giữ lịch sử giao dịch)
        if (!in_array($lopHoc->TrangThai, ['Huy', 'TimGiaSu'])) {
            return back()->with('error', 'Không thể xóa lớp học đang diễn ra hoặc đã hoàn thành.');
        }

        // 3. Xóa vĩnh viễn (CSDL đã set ON DELETE CASCADE nên sẽ xóa sạch dữ liệu kèm theo)
        $lopHoc->delete();

        return back()->with('success', 'Đã xóa lớp học vĩnh viễn.');
    }
    /**
     * Xem chi tiết lớp học (Mọi trạng thái)
     */
    public function show($id)
    {
        $nguoiHocId = Auth::user()->nguoiHoc->NguoiHocID;

        // 1. Lấy thông tin chi tiết kèm các quan hệ
        $lopHoc = LopHocYeuCau::where('LopYeuCauID', $id)
            ->where('NguoiHocID', $nguoiHocId)
            ->with([
                'monHoc',
                'khoiLop',
                'giaSu.taiKhoan', // Lấy thông tin gia sư nếu đã có
                'yeuCauNhanLops' => function ($q) {
                    $q->orderBy('NgayTao', 'desc'); // Lấy lịch sử đề nghị
                }
            ])
            ->firstOrFail();

        return view('nguoihoc.lop-hoc-show', compact('lopHoc'));
    }
    /**
     * Hiển thị form để tạo khiếu nại (Kèm danh sách lịch sử)
     */
    public function createComplaint($id)
    {
        $nguoiHocId = Auth::user()->nguoiHoc->NguoiHocID;
        $taiKhoanId = Auth::id();

        // 1. Tìm lớp học
        $lopHoc = LopHocYeuCau::where('LopYeuCauID', $id)
            ->where('NguoiHocID', $nguoiHocId)
            ->with('giaSu', 'monHoc')
            ->firstOrFail();

        // 2. Validate trạng thái lớp (Giữ nguyên logic cũ)
        if (!in_array($lopHoc->TrangThai, ['DangHoc', 'HoanThanh'])) {
            return redirect()->route('nguoihoc.lophoc.index')
                ->with('error', 'Bạn không thể khiếu nại một lớp chưa bắt đầu.');
        }

        // 3. [MỚI] Lấy danh sách khiếu nại CỦA TÔI về LỚP NÀY
        $lichSuKhieuNai = KhieuNai::where('TaiKhoanID', $taiKhoanId)
            ->where('LopYeuCauID', $id)
            ->orderBy('NgayTao', 'desc')
            ->get();

        // 4. Truyền biến $lichSuKhieuNai sang View
        return view('nguoihoc.lop-hoc-complaint', compact('lopHoc', 'lichSuKhieuNai'));
    }

    /**
     * Lưu khiếu nại mới vào CSDL
     */
    // ... (Các hàm cũ giữ nguyên) ...

    /**
     * Lưu khiếu nại mới vào CSDL (CÓ CHỐNG SPAM)
     */
    public function storeComplaint(Request $request, $id)
    {
        $nguoiHocId = Auth::user()->nguoiHoc->NguoiHocID;
        $taiKhoanId = Auth::id();

        // 1. Tìm lớp học
        $lopHoc = LopHocYeuCau::where('LopYeuCauID', $id)
            ->where('NguoiHocID', $nguoiHocId)
            ->firstOrFail();

        // === 🛑 THÊM LOGIC CHỐNG SPAM TẠI ĐÂY ===
        // Kiểm tra xem tài khoản này đã khiếu nại lớp này chưa
        $daGui = KhieuNai::where('TaiKhoanID', $taiKhoanId)
            ->where('LopYeuCauID', $lopHoc->LopYeuCauID)
            ->exists();

        if ($daGui) {
            // Nếu đã gửi rồi -> Trả về thông báo lỗi
            return redirect()->route('nguoihoc.lophoc.show', $id)
                ->with('error', 'Bạn đã gửi khiếu nại về lớp này rồi. Vui lòng chờ Admin phản hồi.');
        }
        // ==========================================

        // 2. Validate dữ liệu
        $request->validate([
            'NoiDung' => 'required|string|min:20|max:1000',
        ], [
            'NoiDung.required' => 'Vui lòng nhập nội dung khiếu nại.',
            'NoiDung.min' => 'Nội dung khiếu nại cần ít nhất 20 ký tự.'
        ]);

        // 3. Tạo khiếu nại
        KhieuNai::create([
            'TaiKhoanID' => $taiKhoanId,
            'LopYeuCauID' => $lopHoc->LopYeuCauID,
            'NoiDung' => $request->NoiDung,
            'TrangThai' => 'TiepNhan',
            'NgayTao' => now()
        ]);

        return redirect()->route('nguoihoc.lophoc.show', $id)
            ->with('success', 'Gửi khiếu nại thành công! Bạn có thể chỉnh sửa trong vòng 5 phút.');
    }

    /**
     * [MỚI] Cập nhật khiếu nại (Chỉ cho phép trong 5 phút + Trạng thái Tiếp nhận)
     */
    public function updateComplaint(Request $request, $khieuNaiId)
    {
        $taiKhoanId = Auth::id();

        // 1. Tìm khiếu nại chính chủ
        $khieuNai = KhieuNai::where('KhieuNaiID', $khieuNaiId)
            ->where('TaiKhoanID', $taiKhoanId)
            ->firstOrFail();

        // 2. Kiểm tra thời gian (5 phút)
        $thoiGianTao = \Carbon\Carbon::parse($khieuNai->NgayTao);
        if (now()->diffInMinutes($thoiGianTao) > 5) {
            return back()->with('error', 'Đã quá 5 phút, bạn không thể chỉnh sửa được nữa!');
        }

        // 3. Kiểm tra trạng thái (Admin đã xử lý chưa?)
        if ($khieuNai->TrangThai !== 'TiepNhan') {
            return back()->with('error', 'Admin đang xử lý hồ sơ này, không thể chỉnh sửa!');
        }

        // 4. Cập nhật
        $request->validate(['NoiDung' => 'required|string|min:20|max:1000']);

        $khieuNai->update([
            'NoiDung' => $request->NoiDung
        ]);

        return back()->with('success', 'Cập nhật nội dung khiếu nại thành công!');
    }

    /**
     * [MỚI] Xóa khiếu nại (Chỉ cho phép trong 5 phút + Trạng thái Tiếp nhận)
     */
    public function destroyComplaint($khieuNaiId)
    {
        $taiKhoanId = Auth::id();

        // 1. Tìm khiếu nại chính chủ
        $khieuNai = KhieuNai::where('KhieuNaiID', $khieuNaiId)
            ->where('TaiKhoanID', $taiKhoanId)
            ->firstOrFail();

        // 2. Kiểm tra thời gian (5 phút)
        $thoiGianTao = \Carbon\Carbon::parse($khieuNai->NgayTao);
        if (now()->diffInMinutes($thoiGianTao) > 5) {
            return back()->with('error', 'Đã quá 5 phút, bạn không thể xóa được nữa!');
        }

        // 3. Kiểm tra trạng thái
        if ($khieuNai->TrangThai !== 'TiepNhan') {
            return back()->with('error', 'Admin đang xử lý, không thể thu hồi!');
        }

        // 4. Xóa
        $khieuNai->delete();

        return back()->with('success', 'Đã thu hồi khiếu nại thành công.');
    }
}