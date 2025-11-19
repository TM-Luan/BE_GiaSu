<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\LopHocYeuCau;
use App\Models\YeuCauNhanLop;
use App\Models\GiaSu;

class GiaSuLopHocController extends Controller
{
    /**
     * Hiển thị danh sách lớp học của gia sư
     * - Tab 1: Đang dạy (lớp đã nhận)
     * - Tab 2: Đề nghị (yêu cầu nhận lớp: đã gửi hoặc đã nhận)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Kiểm tra có phải gia sư không
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        if (!$giaSu) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        $giaSuId = $giaSu->GiaSuID;
        $tab = $request->get('tab', 'danghoc'); // 'danghoc' hoặc 'denghi'

        // TAB 1: LỚP ĐANG DẠY
        // Lấy các lớp mà gia sư đang dạy (TrangThai = 'DangHoc')
        $lopDangDay = LopHocYeuCau::with(['nguoiHoc', 'monHoc'])
            ->where('GiaSuID', $giaSuId)
            ->where('TrangThai', 'DangHoc')
            ->orderBy('NgayTao', 'desc')
            ->paginate(10, ['*'], 'danghoc_page');

        // TAB 2: ĐỀ NGHỊ
        // Lấy các yêu cầu nhận lớp liên quan đến gia sư này
        // - Gia sư GỬI đề nghị (VaiTroNguoiGui = 'GiaSu', TrangThai = 'ChoDuyet' hoặc 'TuChoi')
        // - Học viên MỜI gia sư (VaiTroNguoiGui = 'NguoiHoc', TrangThai = 'ChoDuyet' hoặc 'TuChoi')
        $yeuCauDeNghi = YeuCauNhanLop::with(['lophoc.nguoiHoc', 'lophoc.monHoc'])
            ->where('GiaSuID', $giaSuId)
            ->whereIn('TrangThai', ['ChoDuyet', 'TuChoi'])
            ->orderBy('NgayTao', 'desc')
            ->paginate(10, ['*'], 'denghi_page');

        return view('giasu.lop-hoc-index', compact(
            'lopDangDay',
            'yeuCauDeNghi',
            'tab'
        ));
    }

    /**
     * Chấp nhận lời mời dạy từ học viên
     */
    public function acceptInvitation(Request $request, $yeuCauId)
    {
        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        
        if (!$giaSu) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $yeuCau = YeuCauNhanLop::with('lophoc')->findOrFail($yeuCauId);

        // Kiểm tra quyền (phải là gia sư được mời)
        if ($yeuCau->GiaSuID != $giaSu->GiaSuID) {
            return back()->with('error', 'Bạn không có quyền chấp nhận yêu cầu này.');
        }

        // Kiểm tra trạng thái
        if ($yeuCau->TrangThai != 'ChoDuyet') {
            return back()->with('error', 'Yêu cầu này đã được xử lý rồi.');
        }

        try {
            DB::beginTransaction();

            // Cập nhật yêu cầu thành ChapNhan
            $yeuCau->TrangThai = 'ChapNhan';
            $yeuCau->save();

            // Cập nhật lớp học: gán gia sư và chuyển trạng thái sang DangHoc
            $lopHoc = $yeuCau->lophoc;
            $lopHoc->GiaSuID = $giaSu->GiaSuID;
            $lopHoc->TrangThai = 'DangHoc';
            $lopHoc->save();

            // Từ chối tất cả các yêu cầu khác cho lớp này
            YeuCauNhanLop::where('LopYeuCauID', $lopHoc->LopYeuCauID)
                ->where('YeuCauID', '!=', $yeuCauId)
                ->where('TrangThai', 'ChoDuyet')
                ->update(['TrangThai' => 'TuChoi']);

            DB::commit();

            return redirect()->route('giasu.lophoc.index', ['tab' => 'danghoc'])
                ->with('success', 'Đã chấp nhận lời mời dạy học!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Từ chối lời mời dạy từ học viên
     */
    public function rejectInvitation(Request $request, $yeuCauId)
    {
        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        
        if (!$giaSu) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $yeuCau = YeuCauNhanLop::findOrFail($yeuCauId);

        // Kiểm tra quyền
        if ($yeuCau->GiaSuID != $giaSu->GiaSuID) {
            return back()->with('error', 'Bạn không có quyền từ chối yêu cầu này.');
        }

        // Kiểm tra trạng thái
        if ($yeuCau->TrangThai != 'ChoDuyet') {
            return back()->with('error', 'Yêu cầu này đã được xử lý rồi.');
        }

        $yeuCau->TrangThai = 'TuChoi';
        $yeuCau->save();

        return back()->with('success', 'Đã từ chối lời mời.');
    }

    /**
     * Hủy đề nghị dạy đã gửi (chỉ khi còn ChoDuyet)
     */
    public function cancelProposal(Request $request, $yeuCauId)
    {
        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        
        if (!$giaSu) {
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $yeuCau = YeuCauNhanLop::findOrFail($yeuCauId);

        // Kiểm tra quyền (phải là gia sư gửi)
        if ($yeuCau->GiaSuID != $giaSu->GiaSuID || $yeuCau->VaiTroNguoiGui != 'GiaSu') {
            return back()->with('error', 'Bạn không có quyền hủy yêu cầu này.');
        }

        // Chỉ được hủy khi còn ChoDuyet
        if ($yeuCau->TrangThai != 'ChoDuyet') {
            return back()->with('error', 'Không thể hủy yêu cầu đã được xử lý.');
        }

        $yeuCau->delete();

        return back()->with('success', 'Đã hủy đề nghị dạy.');
    }

    /**
     * Xem chi tiết lớp học (cho cả lớp đang dạy và đề nghị)
     * Gia sư có thể xem chi tiết bất kỳ lớp học nào để quyết định gửi đề nghị
     */
    public function show($id)
    {
        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        
        if (!$giaSu) {
            abort(403);
        }

        // Tìm lớp học
        $lopHoc = LopHocYeuCau::with(['nguoiHoc.taiKhoan', 'monHoc', 'khoiLop', 'giaSu'])
            ->findOrFail($id);

        // Kiểm tra xem gia sư có yêu cầu liên quan đến lớp này không
        $yeuCau = YeuCauNhanLop::where('LopYeuCauID', $id)
            ->where('GiaSuID', $giaSu->GiaSuID)
            ->first();

        return view('giasu.lop-hoc-show', compact('lopHoc', 'yeuCau'));
    }

    /**
     * Xem lịch học của một lớp cụ thể
     */
    public function schedule($id)
    {
        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        
        if (!$giaSu) {
            abort(403);
        }

        // Tìm lớp học
        $lopHoc = LopHocYeuCau::with(['nguoiHoc', 'monHoc'])
            ->findOrFail($id);

        // Kiểm tra quyền (phải là gia sư của lớp)
        if ($lopHoc->GiaSuID != $giaSu->GiaSuID) {
            abort(403, 'Bạn không có quyền xem lịch học của lớp này.');
        }

        // Chuyển hướng đến trang lịch học chung với filter
        return redirect()->route('giasu.lichhoc.index', ['lop' => $id]);
    }

    /**
     * Trang thêm lịch học mới
     */
    public function addSchedule($id)
    {
        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        
        if (!$giaSu) {
            abort(403);
        }

        // Tìm lớp học
        $lopHoc = LopHocYeuCau::with(['nguoiHoc', 'monHoc'])
            ->findOrFail($id);

        // Kiểm tra quyền (phải là gia sư của lớp)
        if ($lopHoc->GiaSuID != $giaSu->GiaSuID) {
            abort(403, 'Bạn không có quyền thêm lịch học cho lớp này.');
        }

        // Trả về view thêm lịch học (tạm thời chuyển về lịch học)
        return redirect()->route('giasu.lichhoc.index')
            ->with('info', 'Chức năng thêm lịch học đang được phát triển.');
    }
}
