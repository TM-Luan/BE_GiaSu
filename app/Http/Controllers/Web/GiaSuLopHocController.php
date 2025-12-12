<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\LopHocYeuCau;
use App\Models\YeuCauNhanLop;
use App\Models\GiaSu;
use App\Models\GiaoDich;
use App\Models\LichHoc;
use App\Helpers\FCMHelper;
use App\Models\TaiKhoan;
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
        
        // Lấy tab hiện tại từ URL, mặc định là 'dang_day'
        $currentTab = $request->get('tab', 'dang_day'); 

        // 1. DATA TAB: ĐANG DẠY (Chỉ lấy trạng thái DangHoc)
        $lopDangDay = LopHocYeuCau::with(['nguoiHoc', 'monHoc', 'khoiLop', 'giaSu', 'doiTuong', 'thoiGianDay'])
            ->where('GiaSuID', $giaSuId)
            ->where('TrangThai', 'DangHoc') 
            ->orderByDesc('NgayTao')
            ->paginate(9, ['*'], 'dang_day_page');

        // 2. DATA TAB: ĐÃ DẠY (Lịch sử: Hoàn thành, Đã kết thúc)
        $lopDaDay = LopHocYeuCau::with(['nguoiHoc', 'monHoc', 'khoiLop', 'giaSu', 'doiTuong', 'thoiGianDay'])
            ->where('GiaSuID', $giaSuId)
            ->whereIn('TrangThai', ['HoanThanh', 'DaKetThuc']) 
            ->orderByDesc('NgayTao')
            ->paginate(9, ['*'], 'da_day_page');

        // 3. DATA TAB: LỜI MỜI / ĐỀ NGHỊ (Chỉ lấy Pending)
        $yeuCauDeNghi = YeuCauNhanLop::with(['lop.monHoc', 'lop.khoiLop', 'lop.nguoiHoc', 'giaSu', 'nguoiGuiTaiKhoan'])
            ->where('GiaSuID', $giaSuId)
            ->where('TrangThai', 'Pending')
            ->orderByDesc('NgayTao')
            ->paginate(9, ['*'], 'de_nghi_page');

        return view('giasu.my-classes', compact(
            'lopDangDay',
            'lopDaDay',
            'yeuCauDeNghi',
            'currentTab',
            'giaSu' // Truyền biến giaSu để dùng trong debug hoặc hiển thị
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

            // --- Tạo thông báo cho người học ---
            $lopHocInfo = LopHocYeuCau::with(['nguoiHoc', 'monHoc', 'khoiLop'])->find($lopHoc->LopYeuCauID);

            if ($lopHocInfo && $lopHocInfo->nguoiHoc) {
                $tenLop = ($lopHocInfo->monHoc->TenMon ?? 'Lớp học') . ' - ' . ($lopHocInfo->khoiLop->TenKhoiLop ?? '');
                
                $title = 'Lời mời được chấp nhận';
                $message = "Gia sư đã chấp nhận dạy lớp $tenLop";

                // A. Lưu DB
                \App\Models\Notification::create([
                    'user_id' => $lopHocInfo->nguoiHoc->TaiKhoanID,
                    'title' => $title,
                    'message' => $message,
                    'type' => 'request_accepted',
                    'related_id' => $lopHocInfo->LopYeuCauID,
                    'is_read' => false,
                ]);

                // B. [THÊM MỚI] Gửi FCM cho Học viên
                $taiKhoanHocVien = TaiKhoan::find($lopHocInfo->nguoiHoc->TaiKhoanID);
                if ($taiKhoanHocVien && $taiKhoanHocVien->fcm_token) {
                    FCMHelper::send(
                        $taiKhoanHocVien->fcm_token,
                        $title,
                        $message,
                        [
                            'type' => 'request_accepted',
                            'id' => (string)$lopHocInfo->LopYeuCauID
                        ]
                    );
                }
            }

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

       // --- Tạo thông báo cho người học ---
        $lopHocInfo = YeuCauNhanLop::with(['lop.nguoiHoc', 'lop.monHoc', 'lop.khoiLop'])->find($yeuCauId);

        if ($lopHocInfo && $lopHocInfo->lop && $lopHocInfo->lop->nguoiHoc) {
            $tenLop = ($lopHocInfo->lop->monHoc->TenMon ?? 'Lớp học') . ' - ' . ($lopHocInfo->lop->khoiLop->TenKhoiLop ?? '');
            
            $title = 'Lời mời bị từ chối';
            $message = "Gia sư đã từ chối dạy lớp $tenLop";

            // A. Lưu DB
            \App\Models\Notification::create([
                'user_id' => $lopHocInfo->lop->nguoiHoc->TaiKhoanID,
                'title' => $title,
                'message' => $message,
                'type' => 'request_rejected',
                'related_id' => $lopHocInfo->LopYeuCauID,
                'is_read' => false,
            ]);

            // B. [THÊM MỚI] Gửi FCM cho Học viên
            $taiKhoanHocVien = TaiKhoan::find($lopHocInfo->lop->nguoiHoc->TaiKhoanID);
            if ($taiKhoanHocVien && $taiKhoanHocVien->fcm_token) {
                FCMHelper::send(
                    $taiKhoanHocVien->fcm_token,
                    $title,
                    $message,
                    [
                        'type' => 'request_rejected',
                        'id' => (string)$lopHocInfo->LopYeuCauID
                    ]
                );
            }
        }

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

        $lopHoc = LopHocYeuCau::findOrFail($id);

        if ($lopHoc->GiaSuID != $giaSu->GiaSuID) {
            abort(403);
        }

        if ($lopHoc->TrangThaiThanhToan === 'DaThanhToan') {
            return redirect()->route('giasu.lophoc.index')
                ->with('info', 'Lớp học này đã được thanh toán.');
        }

        // Tính phí
        $soTien = $lopHoc->HocPhi * ($lopHoc->SoBuoiTuan ?? 2) * 4 * 0.3;

        // Xử lý riêng cho VNPAY
        if ($validated['loai_giao_dich'] === 'VNPAY') {
            return $this->createVnPayPayment($request, $user, $lopHoc, $soTien);
        }

        // Xử lý cho các phương thức khác (Giữ nguyên logic cũ hoặc phát triển thêm sau)
        try {
            DB::beginTransaction();

            GiaoDich::create([
                'LopYeuCauID' => $lopHoc->LopYeuCauID,
                'TaiKhoanID' => $user->TaiKhoanID,
                'SoTien' => $soTien,
                'LoaiGiaoDich' => $validated['loai_giao_dich'],
                'GhiChu' => 'Thanh toán phí nhận lớp ' . $lopHoc->LopYeuCauID,
                'ThoiGian' => now(),
                'TrangThai' => 'ThanhCong', // Giả lập thành công cho các phương thức khác
                'MaGiaoDich' => 'TXN_' . time() . '_' . $user->TaiKhoanID
            ]);

            $lopHoc->update(['TrangThaiThanhToan' => 'DaThanhToan']);

            DB::commit();

            return redirect()->route('giasu.lophoc.schedule.create', $lopHoc->LopYeuCauID)
                ->with('success', 'Thanh toán thành công! Vui lòng tạo lịch học cho lớp.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * [MỚI] Hàm tạo URL thanh toán VNPAY
     */
    private function createVnPayPayment($request, $user, $lopHoc, $soTien)
    {
        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_Url = env('VNP_URL');
        // URL trả về xử lý trên Web
        $vnp_Returnurl = route('giasu.lophoc.payment.vnpay_return');

        $vnp_TxnRef = time() . "_" . $user->TaiKhoanID; // Mã giao dịch
        $vnp_OrderInfo = "Thanh toan phi lop " . $lopHoc->LopYeuCauID;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $soTien * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();

        // Lưu giao dịch Pending vào DB
        GiaoDich::create([
            'LopYeuCauID' => $lopHoc->LopYeuCauID,
            'TaiKhoanID' => $user->TaiKhoanID,
            'SoTien' => $soTien,
            'LoaiGiaoDich' => 'VNPAY',
            'GhiChu' => 'Thanh toán phí nhận lớp (VNPAY)',
            'ThoiGian' => now(),
            'TrangThai' => 'ChoXuLy', // Quan trọng: Đang chờ xử lý
            'MaGiaoDich' => $vnp_TxnRef
        ]);

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_BankCode" => "NCB",
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // Chuyển hướng người dùng sang trang thanh toán VNPAY
        return redirect($vnp_Url);
    }

    /**
     * [MỚI] Xử lý kết quả trả về từ VNPAY (Web)
     */
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_SecureHash = $request->vnp_SecureHash;

        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

        $vnp_TxnRef = $request->vnp_TxnRef;
        $vnp_ResponseCode = $request->vnp_ResponseCode;

        if ($secureHash == $vnp_SecureHash) {
            if ($vnp_ResponseCode == '00') {
                // --- THANH TOÁN THÀNH CÔNG ---
                $giaoDich = GiaoDich::where('MaGiaoDich', $vnp_TxnRef)->first();

                if ($giaoDich) {
                    if ($giaoDich->TrangThai != 'ThanhCong') {
                        DB::beginTransaction();
                        try {
                            $giaoDich->update(['TrangThai' => 'ThanhCong']);

                            $lopHoc = LopHocYeuCau::find($giaoDich->LopYeuCauID);
                            if ($lopHoc) {
                                $lopHoc->update(['TrangThaiThanhToan' => 'DaThanhToan']);
                            }
                            DB::commit();

                            // Chuyển hướng về trang tạo lịch
                            return redirect()->route('giasu.lophoc.schedule.create', $giaoDich->LopYeuCauID)
                                ->with('success', 'Thanh toán VNPAY thành công! Vui lòng tạo lịch học.');
                        } catch (\Exception $e) {
                            DB::rollBack();
                            return redirect()->route('giasu.lophoc.index')
                                ->with('error', 'Lỗi cập nhật dữ liệu: ' . $e->getMessage());
                        }
                    } else {
                        // Đã xử lý trước đó rồi
                        return redirect()->route('giasu.lophoc.schedule.create', $giaoDich->LopYeuCauID)
                            ->with('info', 'Giao dịch đã được ghi nhận thành công.');
                    }
                } else {
                    return redirect()->route('giasu.lophoc.index')->with('error', 'Không tìm thấy giao dịch.');
                }
            } else {
                // --- THANH TOÁN THẤT BẠI ---
                // Cập nhật trạng thái thất bại nếu tìm thấy giao dịch
                $giaoDich = GiaoDich::where('MaGiaoDich', $vnp_TxnRef)->first();
                if ($giaoDich) {
                    $giaoDich->update(['TrangThai' => 'ThatBai']);
                    return redirect()->route('giasu.lophoc.payment', $giaoDich->LopYeuCauID)
                        ->with('error', 'Thanh toán VNPAY thất bại hoặc bị hủy. Vui lòng thử lại.');
                }
                return redirect()->route('giasu.lophoc.index')->with('error', 'Thanh toán thất bại.');
            }
        } else {
            return redirect()->route('giasu.lophoc.index')->with('error', 'Chữ ký VNPAY không hợp lệ!');
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
        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();

        if (!$giaSu) {
            abort(403);
        }

        $lopHoc = LopHocYeuCau::findOrFail($id);

        if ($lopHoc->GiaSuID != $giaSu->GiaSuID) {
            abort(403);
        }

        // ĐỒNG BỘ MOBILE: Lấy số buổi/tuần trước khi validate
        $soBuoiTuan = $lopHoc->SoBuoiTuan ?? 2;

        $validated = $request->validate([
            'ngay_bat_dau' => 'required|date|after_or_equal:today',
            'so_tuan' => 'required|integer|min:1|max:52',
            'duong_dan' => 'nullable|url',
            'buoi_hoc' => "required|array|min:1|max:{$soBuoiTuan}",
            'buoi_hoc.*.thu' => 'required|integer|between:1,7',
            'buoi_hoc.*.gio' => 'required|date_format:H:i'
        ], [
            'buoi_hoc.max' => "Lớp này chỉ học tối đa {$soBuoiTuan} buổi/tuần. Bạn không thể thêm quá {$soBuoiTuan} buổi.",
        ]);

        // Bắt buộc thanh toán trước khi tạo lịch
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
            $thoiLuong = $lopHoc->ThoiLuong ?? 90;

            // ĐỒNG BỘ 100% MOBILE API
            // Web form: thu (1=CN, 2=T2, 3=T3, 4=T4, 5=T5, 6=T6, 7=T7)
            // Mobile API: ngay_thu (0=CN, 1=T2, 2=T3, 3=T4, 4=T5, 5=T6, 6=T7)
            // Carbon startOfWeek(SUNDAY): Chủ nhật = ngày 0 của tuần
            foreach ($buoiHoc as $buoi) {
                $thu = $buoi['thu']; // Web form: 1=CN, 2=T2, 3=T3,...7=T7
                $gio = $buoi['gio'];

                // Chuyển đổi web form (1-7) sang mobile API (0-6)
                // QUAN TRỌNG: 1→0 (CN), 2→1 (T2), 3→2 (T3), 4→3 (T4), 5→4 (T5), 6→5 (T6), 7→6 (T7)
                $ngayThu = $thu - 1;

                // Logic CHÍNH XÁC giống mobile API (LichHocController.php line 250)
                // startOfWeek(SUNDAY) trả về Chủ nhật đầu tuần
                // addDays(0) = Chủ nhật, addDays(1) = Thứ 2, addDays(2) = Thứ 3...
                $ngayDauTien = $ngayBatDau->copy()->startOfWeek(\Carbon\Carbon::SUNDAY)->addDays($ngayThu);

                // Nếu ngày tìm được < ngày bắt đầu → Lấy tuần sau
                if ($ngayDauTien->isBefore($ngayBatDau, 'day')) {
                    $ngayDauTien->addWeek();
                }

                // Tính thời gian kết thúc
                $thoiGianBatDau = $gio . ':00';
                $thoiGianKetThuc = \Carbon\Carbon::parse($gio)->addMinutes($thoiLuong)->format('H:i:s');

                // Tạo lịch cho số tuần
                for ($tuan = 0; $tuan < $soTuan; $tuan++) {
                    $ngayHoc = $ngayDauTien->copy()->addWeeks($tuan);

                    // Kiểm tra trùng lịch
                    if ($this->kiemTraTrungLich($giaSu->GiaSuID, $ngayHoc->format('Y-m-d'), $thoiGianBatDau, $thoiGianKetThuc)) {
                        DB::rollBack();

                        // Lấy thông tin lịch trùng để hiển thị chi tiết
                        $lichTrung = DB::table('LichHoc')
                            ->join('LopHocYeuCau', 'LichHoc.LopYeuCauID', '=', 'LopHocYeuCau.LopYeuCauID')
                            ->where('LopHocYeuCau.GiaSuID', $giaSu->GiaSuID)
                            ->where('LichHoc.NgayHoc', $ngayHoc->format('Y-m-d'))
                            ->where('LichHoc.TrangThai', '!=', 'Huy')
                            ->where(function ($q) use ($thoiGianBatDau, $thoiGianKetThuc) {
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

                        return back()->with(
                            'error',
                            '⚠️ Trùng lịch học! Bạn đã có lịch dạy vào ngày ' .
                            $ngayHoc->format('d/m/Y') .
                            ($thoiGianTrung ? ' (khung giờ ' . $thoiGianTrung . ')' : '') .
                            '. Vui lòng chọn thời gian khác hoặc xóa lịch cũ trước khi tạo lịch mới.'
                        )->withInput();
                    }

                    LichHoc::create([
                        'LopYeuCauID' => $lopHoc->LopYeuCauID,
                        'NgayHoc' => $ngayHoc->format('Y-m-d'),
                        'ThoiGianBatDau' => $thoiGianBatDau,
                        'ThoiGianKetThuc' => $thoiGianKetThuc,
                        'DuongDan' => $duongDan,
                        'TrangThai' => 'SapToi', // <--- SỬA: Đồng bộ với Mobile API
                        'NgayTao' => now(),
                        'IsLapLai' => false,     // <--- THÊM: Để Mobile không bị lỗi null
                        'LichHocGocID' => null   // <--- THÊM
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
            ->where(function ($q) use ($thoiGianBatDau, $thoiGianKetThuc) {
                $q->where('LichHoc.ThoiGianBatDau', '<', $thoiGianKetThuc)
                    ->where('LichHoc.ThoiGianKetThuc', '>', $thoiGianBatDau);
            });

        if ($lichHocId) {
            $query->where('LichHoc.LichHocID', '!=', $lichHocId);
        }

        return $query->exists();
    }

    /**
     * Sửa lịch học - ĐỒNG BỘ VỚI MOBILE (capNhatLichHocGiaSu)
     * DuongDan: Link (Online) hoặc Địa chỉ (Offline)
     */
    public function updateSchedule(Request $request, $lichHocId)
    {
        $validated = $request->validate([
            'ThoiGianBatDau' => 'sometimes|required|date_format:H:i',
            'ThoiGianKetThuc' => 'sometimes|required|date_format:H:i',
            'NgayHoc' => 'sometimes|required|date|after_or_equal:today',
            'DuongDan' => 'nullable|string|max:500', // Link hoặc địa chỉ
            'TrangThai' => 'nullable|in:DangDay,SapToi,DaHoc,Huy'
        ]);

        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();

        if (!$giaSu) {
            abort(403);
        }

        $lichHoc = LichHoc::with('lopHocYeuCau')->findOrFail($lichHocId);

        if ($lichHoc->lopHocYeuCau->GiaSuID != $giaSu->GiaSuID) {
            abort(403, 'Bạn không có quyền sửa lịch học này');
        }

        try {
            // Kiểm tra trùng lịch khi cập nhật thời gian/ngày
            if ($request->has('NgayHoc') || $request->has('ThoiGianBatDau') || $request->has('ThoiGianKetThuc')) {
                $ngayHoc = $validated['NgayHoc'] ?? $lichHoc->NgayHoc;
                $thoiGianBatDau = ($validated['ThoiGianBatDau'] ?? substr($lichHoc->ThoiGianBatDau, 0, 5)) . ':00';
                $thoiGianKetThuc = ($validated['ThoiGianKetThuc'] ?? substr($lichHoc->ThoiGianKetThuc, 0, 5)) . ':00';

                if ($this->kiemTraTrungLich($giaSu->GiaSuID, $ngayHoc, $thoiGianBatDau, $thoiGianKetThuc, $lichHocId)) {
                    return back()->with('error', '⚠️ Trùng lịch học. Vui lòng chọn thời gian khác.')->withInput();
                }
            }

            // Chuyển đổi format giờ nếu có
            if (isset($validated['ThoiGianBatDau'])) {
                $validated['ThoiGianBatDau'] = $validated['ThoiGianBatDau'] . ':00';
            }
            if (isset($validated['ThoiGianKetThuc'])) {
                $validated['ThoiGianKetThuc'] = $validated['ThoiGianKetThuc'] . ':00';
            }

            $lichHoc->update($validated);

            $message = 'Cập nhật lịch học thành công!';
            if ($request->has('DuongDan')) {
                $hinhThuc = $lichHoc->lopHocYeuCau->HinhThuc ?? 'Offline';
                $message = $hinhThuc === 'Online'
                    ? '✅ Đã cập nhật link học online!'
                    : '✅ Đã cập nhật địa chỉ học!';
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Cập nhật trạng thái buổi học - ĐỒNG BỘ VỚI MOBILE
     */
    public function updateScheduleStatus(Request $request, $lichHocId)
    {
        $validated = $request->validate([
            'TrangThai' => 'required|in:DangDay,SapToi,DaHoc,Huy'
        ]);

        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();

        if (!$giaSu) {
            abort(403);
        }

        $lichHoc = LichHoc::with('lopHocYeuCau')->findOrFail($lichHocId);

        if ($lichHoc->lopHocYeuCau->GiaSuID != $giaSu->GiaSuID) {
            abort(403, 'Bạn không có quyền cập nhật trạng thái lịch học này');
        }

        try {
            $lichHoc->update(['TrangThai' => $validated['TrangThai']]);

            return back()->with('success', 'Cập nhật trạng thái thành công!');

        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    /**
     * Hoàn thành lớp học (Kết thúc khóa dạy)
     */
    public function complete($id)
    {
        $user = Auth::user();
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();

        if (!$giaSu) {
            abort(403);
        }

        // Tìm lớp học chính chủ
        $lopHoc = LopHocYeuCau::where('LopYeuCauID', $id)
            ->where('GiaSuID', $giaSu->GiaSuID)
            ->firstOrFail();

        // Chỉ cho phép hoàn thành nếu đang học
        if ($lopHoc->TrangThai !== 'DangHoc') {
            return back()->with('error', 'Lớp học này không ở trạng thái đang dạy.');
        }

        try {
            DB::beginTransaction();

            // 1. Cập nhật trạng thái lớp
            $lopHoc->update(['TrangThai' => 'HoanThanh']);

            // 2. Xóa các lịch học chưa diễn ra (để sạch dữ liệu) - Giống logic mobile
            LichHoc::where('LopYeuCauID', $id)->delete();

            DB::commit();

            return redirect()->route('giasu.lophoc.index', ['tab' => 'da_day'])
                ->with('success', 'Chúc mừng! Bạn đã hoàn thành lớp học này.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
