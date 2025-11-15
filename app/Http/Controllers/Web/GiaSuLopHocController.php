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
        $lopDangDay = LopHocYeuCau::with(['nguoihoc', 'monhoc'])
            ->where('GiaSuID', $giaSuId)
            ->where('TrangThai', 'DangHoc')
            ->orderBy('NgayTao', 'desc')
            ->paginate(10, ['*'], 'danghoc_page');

        // TAB 2: ĐỀ NGHỊ
        // Lấy các yêu cầu nhận lớp liên quan đến gia sư này
        // - Gia sư GỬI đề nghị (VaiTroNguoiGui = 'GiaSu', TrangThai = 'ChoDuyet' hoặc 'TuChoi')
        // - Học viên MỜI gia sư (VaiTroNguoiGui = 'NguoiHoc', TrangThai = 'ChoDuyet' hoặc 'TuChoi')
        $yeuCauDeNghi = YeuCauNhanLop::with(['lophoc.nguoihoc', 'lophoc.monhoc'])
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
     */
    public function show($id)
    {
        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        
        if (!$giaSu) {
            abort(403);
        }

        // Tìm lớp học
        $lopHoc = LopHocYeuCau::with(['nguoihoc', 'monhoc', 'giasu'])
            ->findOrFail($id);

        // Kiểm tra quyền xem (phải là gia sư của lớp hoặc có yêu cầu liên quan)
        $hasAccess = false;
        
        // Case 1: Đang dạy lớp này
        if ($lopHoc->GiaSuID == $giaSu->GiaSuID) {
            $hasAccess = true;
        }
        
        // Case 2: Có yêu cầu nhận lớp này
        $yeuCau = YeuCauNhanLop::where('LopYeuCauID', $id)
            ->where('GiaSuID', $giaSu->GiaSuID)
            ->first();
        
        if ($yeuCau) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403, 'Bạn không có quyền xem lớp học này.');
        }

        return view('giasu.lop-hoc-show', compact('lopHoc', 'yeuCau'));
    }
}
