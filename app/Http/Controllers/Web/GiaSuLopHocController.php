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
        // Đồng bộ với API mobile getLopCuaGiaSu - lopDangDay
        $lopDangDay = LopHocYeuCau::with(['nguoiHoc', 'monHoc', 'khoiLop', 'giaSu', 'doiTuong', 'thoiGianDay'])
            ->where('GiaSuID', $giaSuId)
            ->whereIn('TrangThai', ['DangHoc', 'HoanThanh'])
            ->orderByDesc('NgayTao')
            ->paginate(12, ['*'], 'danghoc_page');

        // TAB 2: ĐỀ NGHỊ
        // Đồng bộ với API mobile getLopCuaGiaSu - lopDeNghi
        $yeuCauDeNghi = YeuCauNhanLop::with(['lop.monHoc', 'lop.khoiLop', 'lop.nguoiHoc', 'giaSu', 'nguoiGuiTaiKhoan'])
            ->where('GiaSuID', $giaSuId)
            ->where('TrangThai', 'Pending')
            ->orderByDesc('NgayTao')
            ->paginate(12, ['*'], 'denghi_page');

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

        $yeuCau = YeuCauNhanLop::with('lop')->findOrFail($yeuCauId);

        // Kiểm tra quyền (phải là gia sư được mời)
        if ($yeuCau->GiaSuID != $giaSu->GiaSuID) {
            return back()->with('error', 'Bạn không có quyền chấp nhận yêu cầu này.');
        }

        // Kiểm tra trạng thái
        if ($yeuCau->TrangThai != 'Pending') {
            return back()->with('error', 'Yêu cầu này đã được xử lý rồi.');
        }

        try {
            DB::beginTransaction();

            // Cập nhật yêu cầu thành Accepted
            $yeuCau->TrangThai = 'Accepted';
            $yeuCau->NgayCapNhat = now();
            $yeuCau->save();

            // Cập nhật lớp học: gán gia sư và chuyển trạng thái sang DangHoc
            $lopHoc = $yeuCau->lop;
            $lopHoc->GiaSuID = $giaSu->GiaSuID;
            $lopHoc->TrangThai = 'DangHoc';
            $lopHoc->save();

            // Từ chối tất cả các yêu cầu khác cho lớp này
            YeuCauNhanLop::where('LopYeuCauID', $lopHoc->LopYeuCauID)
                ->where('YeuCauID', '!=', $yeuCauId)
                ->where('TrangThai', 'Pending')
                ->update(['TrangThai' => 'Rejected', 'NgayCapNhat' => now()]);

            DB::commit();

            // Chuyển đến trang tạo lịch thay vì về danh sách
            return redirect()->route('giasu.lophoc.schedule.create', $lopHoc->LopYeuCauID)
                ->with('success', 'Đã chấp nhận lời mời dạy học! Vui lòng tạo lịch học.');

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
        if ($yeuCau->TrangThai != 'Pending') {
            return back()->with('error', 'Yêu cầu này đã được xử lý rồi.');
        }

        $yeuCau->TrangThai = 'Rejected';
        $yeuCau->NgayCapNhat = now();
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

        // Chỉ được hủy khi còn Pending
        if ($yeuCau->TrangThai != 'Pending') {
            return back()->with('error', 'Không thể hủy yêu cầu đã được xử lý.');
        }

        $yeuCau->TrangThai = 'Cancelled';
        $yeuCau->NgayCapNhat = now();
        $yeuCau->save();

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

    /**
     * Hiển thị trang thanh toán phí nhận lớp
     */
    public function showPayment($id)
    {
        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        
        if (!$giaSu) {
            abort(403);
        }

        // Tìm lớp học
        $lopHoc = LopHocYeuCau::with(['nguoiHoc', 'monHoc', 'khoiLop'])
            ->findOrFail($id);

        // Kiểm tra quyền
        if ($lopHoc->GiaSuID != $giaSu->GiaSuID) {
            abort(403, 'Bạn không có quyền thanh toán cho lớp này.');
        }

        // Kiểm tra đã thanh toán chưa - Đồng bộ với mobile
        if ($lopHoc->TrangThaiThanhToan === 'DaThanhToan') {
            return redirect()->route('giasu.lophoc.index')
                ->with('info', 'Lớp học này đã được thanh toán.');
        }

        // Tính phí nhận lớp (30% học phí * số buổi/tuần * 4 tuần) - Đồng bộ với mobile
        $phiNhanLop = $lopHoc->HocPhi * ($lopHoc->SoBuoiTuan ?? 2) * 4 * 0.3;

        return view('giasu.payment', compact('lopHoc', 'phiNhanLop'));
    }

    /**
     * Xử lý thanh toán phí nhận lớp
     */
    public function processPayment(Request $request, $id)
    {
        $validated = $request->validate([
            'loai_giao_dich' => 'required|in:VNPAY,MoMo,ZaloPay,ChuyenKhoan'
        ]);

        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        
        if (!$giaSu) {
            abort(403);
        }

        // Tìm lớp học
        $lopHoc = LopHocYeuCau::findOrFail($id);

        // Kiểm tra quyền
        if ($lopHoc->GiaSuID != $giaSu->GiaSuID) {
            abort(403);
        }

        // Kiểm tra đã thanh toán chưa - Đồng bộ với mobile
        if ($lopHoc->TrangThaiThanhToan === 'DaThanhToan') {
            return redirect()->route('giasu.lophoc.index')
                ->with('info', 'Lớp học này đã được thanh toán.');
        }

        try {
            DB::beginTransaction();

            // Tính phí - Đồng bộ với mobile: 30% × HocPhi × SoBuoiTuan × 4 tuần
            $soTien = $lopHoc->HocPhi * ($lopHoc->SoBuoiTuan ?? 2) * 4 * 0.3;

            // Tạo giao dịch - Đồng bộ với mobile API
            DB::table('GiaoDich')->insert([
                'LopYeuCauID' => $lopHoc->LopYeuCauID,
                'TaiKhoanID' => $user->TaiKhoanID,
                'SoTien' => $soTien,
                'LoaiGiaoDich' => $validated['loai_giao_dich'],
                'GhiChu' => 'Thanh toán phí nhận lớp ' . $lopHoc->LopYeuCauID,
                'ThoiGian' => now(),
                'TrangThai' => 'Thành công',
                'MaGiaoDich' => 'TXN_' . time() . '_' . $user->TaiKhoanID
            ]);

            // Cập nhật trạng thái thanh toán - Đồng bộ với mobile
            $lopHoc->update(['TrangThaiThanhToan' => 'DaThanhToan']);

            DB::commit();

            // Sau khi thanh toán thành công, chuyển đến trang tạo lịch (Đồng bộ với mobile)
            return redirect()->route('giasu.lophoc.schedule.create', $lopHoc->LopYeuCauID)
                ->with('success', 'Thanh toán thành công! Vui lòng tạo lịch học cho lớp.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị trang tạo lịch học tự động
     */
    public function showCreateSchedule($id)
    {
        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        
        if (!$giaSu) {
            abort(403);
        }

        // Tìm lớp học
        $lopHoc = LopHocYeuCau::with(['nguoiHoc', 'monHoc', 'khoiLop'])
            ->findOrFail($id);

        // Kiểm tra quyền
        if ($lopHoc->GiaSuID != $giaSu->GiaSuID) {
            abort(403, 'Bạn không có quyền tạo lịch cho lớp này.');
        }

        // Bắt buộc thanh toán trước khi tạo lịch (Đồng bộ với mobile)
        if ($lopHoc->TrangThaiThanhToan !== 'DaThanhToan') {
            return redirect()->route('giasu.lophoc.payment', $lopHoc->LopYeuCauID)
                ->with('error', 'Vui lòng thanh toán phí nhận lớp trước khi tạo lịch học.');
        }

        return view('giasu.create-schedule', compact('lopHoc'));
    }

    /**
     * Lưu lịch học tự động
     */
    public function storeSchedule(Request $request, $id)
    {
        $validated = $request->validate([
            'ngay_bat_dau' => 'required|date|after_or_equal:today',
            'so_tuan' => 'required|integer|min:1|max:52',
            'duong_dan' => 'nullable|url',
            'buoi_hoc' => 'required|array|min:1',
            'buoi_hoc.*.thu' => 'required|integer|between:1,7',
            'buoi_hoc.*.gio' => 'required|date_format:H:i'
        ]);

        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        
        if (!$giaSu) {
            abort(403);
        }

        $lopHoc = LopHocYeuCau::findOrFail($id);

        if ($lopHoc->GiaSuID != $giaSu->GiaSuID) {
            abort(403);
        }

        // Bắt buộc thanh toán trước khi tạo lịch (Đồng bộ với mobile)
        if ($lopHoc->TrangThaiThanhToan !== 'DaThanhToan') {
            return redirect()->route('giasu.lophoc.payment', $lopHoc->LopYeuCauID)
                ->with('error', 'Vui lòng thanh toán phí nhận lớp trước khi tạo lịch học.');
        }

        try {
            DB::beginTransaction();

            $ngayBatDau = \Carbon\Carbon::parse($validated['ngay_bat_dau']);
            $soTuan = $validated['so_tuan'];
            $duongDan = $validated['duong_dan'] ?? null;
            $buoiHoc = $validated['buoi_hoc'];

            // Tạo lịch học theo tuần
            for ($tuan = 0; $tuan < $soTuan; $tuan++) {
                foreach ($buoiHoc as $buoi) {
                    $thu = $buoi['thu']; // 1=CN, 2=T2, 3=T3,...7=T7
                    $gio = $buoi['gio'];

                    // Tính ngày học
                    $ngayHoc = $ngayBatDau->copy()->addWeeks($tuan);
                    
                    // Điều chỉnh sang đúng thứ
                    $currentDayOfWeek = $ngayHoc->dayOfWeek == 0 ? 7 : $ngayHoc->dayOfWeek; // Carbon: 0=CN
                    $targetDayOfWeek = $thu == 1 ? 7 : $thu - 1; // Chuyển về format Carbon
                    $diff = $targetDayOfWeek - $currentDayOfWeek;
                    $ngayHoc->addDays($diff);

                    // Tính thời gian kết thúc
                    $thoiGianBatDau = $gio . ':00';
                    $thoiGianKetThuc = \Carbon\Carbon::parse($gio)->addMinutes($lopHoc->ThoiLuong ?? 90)->format('H:i:s');

                    // Kiểm tra trùng lịch (Đồng bộ với mobile)
                    if ($this->kiemTraTrungLich($giaSu->GiaSuID, $ngayHoc->format('Y-m-d'), $thoiGianBatDau, $thoiGianKetThuc)) {
                        DB::rollBack();
                        
                        // Lấy thông tin lịch trùng để hiển thị chi tiết
                        $lichTrung = DB::table('LichHoc')
                            ->join('LopHocYeuCau', 'LichHoc.LopYeuCauID', '=', 'LopHocYeuCau.LopYeuCauID')
                            ->where('LopHocYeuCau.GiaSuID', $giaSu->GiaSuID)
                            ->where('LichHoc.NgayHoc', $ngayHoc->format('Y-m-d'))
                            ->where('LichHoc.TrangThai', '!=', 'Huy')
                            ->where(function($q) use ($thoiGianBatDau, $thoiGianKetThuc) {
                                $q->where('LichHoc.ThoiGianBatDau', '<', $thoiGianKetThuc)
                                  ->where('LichHoc.ThoiGianKetThuc', '>', $thoiGianBatDau);
                            })
                            ->select('LichHoc.ThoiGianBatDau', 'LichHoc.ThoiGianKetThuc')
                            ->first();
                        
                        $thoiGianTrung = '';
                        if ($lichTrung) {
                            $batDau = \Carbon\Carbon::parse($lichTrung->ThoiGianBatDau)->format('H:i');
                            $ketThuc = \Carbon\Carbon::parse($lichTrung->ThoiGianKetThuc)->format('H:i');
                            $thoiGianTrung = $batDau . '-' . $ketThuc;
                        }
                        
                        return back()->with('error', 
                            '⚠️ Trùng lịch học! Bạn đã có lịch dạy vào ngày ' . 
                            $ngayHoc->format('d/m/Y') . 
                            ($thoiGianTrung ? ' (khung giờ ' . $thoiGianTrung . ')' : '') . 
                            '. Vui lòng chọn thời gian khác hoặc xóa lịch cũ trước khi tạo lịch mới.'
                        )->withInput();
                    }

                    DB::table('LichHoc')->insert([
                        'LopYeuCauID' => $lopHoc->LopYeuCauID,
                        'NgayHoc' => $ngayHoc->format('Y-m-d'),
                        'ThoiGianBatDau' => $thoiGianBatDau,
                        'ThoiGianKetThuc' => $thoiGianKetThuc,
                        'DuongDan' => $duongDan,
                        'TrangThai' => 'ChuaDienRa',
                        'NgayTao' => now()
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('giasu.lophoc.index')
                ->with('success', 'Tạo lịch học thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Cập nhật ghi chú cho đề nghị
     */
    public function updateProposalNote(Request $request, $yeuCauId)
    {
        $validated = $request->validate([
            'ghi_chu' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        
        if (!$giaSu) {
            abort(403);
        }

        $yeuCau = YeuCauNhanLop::findOrFail($yeuCauId);

        if ($yeuCau->GiaSuID != $giaSu->GiaSuID) {
            abort(403);
        }

        try {
            $yeuCau->update([
                'GhiChu' => $validated['ghi_chu'],
                'NgayCapNhat' => now()
            ]);

            return back()->with('success', 'Cập nhật ghi chú thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hủy lớp học chưa thanh toán
     */
    public function cancelClass($id)
    {
        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        
        if (!$giaSu) {
            abort(403);
        }

        $lopHoc = LopHocYeuCau::findOrFail($id);

        if ($lopHoc->GiaSuID != $giaSu->GiaSuID) {
            abort(403);
        }

        // ĐỒNG BỘ VỚI MOBILE: Chỉ cho phép hủy nếu chưa thanh toán
        if ($lopHoc->TrangThaiThanhToan === 'DaThanhToan') {
            return back()->with('error', 'Không thể hủy lớp đã thanh toán. Vui lòng liên hệ quản trị viên.');
        }

        // Kiểm tra đã có lịch học chưa - không cho hủy nếu đã tạo lịch
        $hasSchedule = DB::table('LichHoc')->where('LopYeuCauID', $id)->exists();
        if ($hasSchedule) {
            return back()->with('error', 'Không thể hủy lớp đã có lịch học. Vui lòng liên hệ quản trị viên.');
        }

        try {
            DB::beginTransaction();

            // Cập nhật trạng thái lớp học về TimGiaSu
            $lopHoc->update([
                'TrangThai' => 'TimGiaSu',
                'GiaSuID' => null // Bỏ gia sư khỏi lớp
            ]);

            // Cập nhật hoặc xóa yêu cầu nhận lớp
            YeuCauNhanLop::where('LopYeuCauID', $id)
                ->where('GiaSuID', $giaSu->GiaSuID)
                ->delete();

            DB::commit();

            return redirect()->route('giasu.lophoc.index')
                ->with('success', 'Đã hủy lớp học thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa tất cả lịch học của lớp (Đồng bộ với mobile - để tạo lịch mới)
     */
    public function deleteAllSchedules($id)
    {
        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        
        if (!$giaSu) {
            abort(403);
        }

        $lopHoc = LopHocYeuCau::findOrFail($id);

        if ($lopHoc->GiaSuID != $giaSu->GiaSuID) {
            abort(403);
        }

        // Chỉ cho phép xóa lịch nếu đã thanh toán (Đồng bộ với mobile)
        if ($lopHoc->TrangThaiThanhToan !== 'DaThanhToan') {
            return back()->with('error', 'Chỉ có thể xóa lịch khi đã thanh toán.');
        }

        try {
            DB::beginTransaction();

            // Xóa tất cả lịch học của lớp
            DB::table('LichHoc')->where('LopYeuCauID', $id)->delete();

            DB::commit();

            return redirect()->route('giasu.lophoc.index')
                ->with('success', 'Đã xóa tất cả lịch học. Bạn có thể tạo lịch mới.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Kiểm tra trùng lịch (Đồng bộ với mobile API)
     */
    private function kiemTraTrungLich($giasuId, $ngayHoc, $thoiGianBatDau, $thoiGianKetThuc, $lichHocId = null)
    {
        $query = DB::table('LichHoc')
            ->join('LopHocYeuCau', 'LichHoc.LopYeuCauID', '=', 'LopHocYeuCau.LopYeuCauID')
            ->where('LopHocYeuCau.GiaSuID', $giasuId)
            ->where('LichHoc.NgayHoc', $ngayHoc)
            ->where('LichHoc.TrangThai', '!=', 'Huy')
            ->where(function($q) use ($thoiGianBatDau, $thoiGianKetThuc) {
                $q->where('LichHoc.ThoiGianBatDau', '<', $thoiGianKetThuc)
                  ->where('LichHoc.ThoiGianKetThuc', '>', $thoiGianBatDau);
            });

        if ($lichHocId) {
            $query->where('LichHoc.LichHocID', '!=', $lichHocId);
        }

        return $query->exists();
    }
}
