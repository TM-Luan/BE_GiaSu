<?php

namespace App\Http\Controllers;

use App\Http\Resources\LopHocYeuCauResource;
use App\Http\Resources\YeuCauNhanLopResource;
use App\Models\LopHocYeuCau;
use App\Models\YeuCauNhanLop;
use App\Models\GiaSu;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class YeuCauNhanLopController extends Controller
{
    private const STATUS_PENDING = 'Pending';
    private const STATUS_ACCEPTED = 'Accepted';
    private const STATUS_REJECTED = 'Rejected';
    private const STATUS_CANCELLED = 'Cancelled';

    // ... (Các hàm respondSuccess, respondError, resolveInput, getTaiKhoanId, toResource, loadCollection giữ nguyên) ...
    private function respondSuccess(string $message, $data = null, int $status = 200): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $status);
    }

    private function respondError(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }

    private function resolveInput(Request $request, array $keys)
    {
        foreach ($keys as $key) {
            if ($request->has($key) && $request->input($key) !== null && $request->input($key) !== '') {
                return $request->input($key);
            }
        }

        return null;
    }

    private function getTaiKhoanId(Request $request): ?int
    {
        $user = $request->user();
        if ($user && isset($user->TaiKhoanID)) {
            return (int) $user->TaiKhoanID;
        }

        $fallback = $this->resolveInput($request, [
            'NguoiGuiTaiKhoanID',
            'nguoi_gui_tai_khoan_id',
            'tai_khoan_id',
        ]);

        return $fallback !== null ? (int) $fallback : null;
    }

    private function toResource(YeuCauNhanLop $yeuCau): array
    {
        $yeuCau->loadMissing([
            'lop.monHoc',
            'lop.khoiLop',
            'lop.nguoiHoc',
            'giaSu',
            'nguoiGuiTaiKhoan',
        ]);

        return (new YeuCauNhanLopResource($yeuCau))->resolve();
    }

    private function loadCollection($query)
    {
        return $query->orderByDesc('NgayTao')->get();
    }


    public function giaSuGuiYeuCau(Request $request)
    {
        // 1. Lấy người dùng (Tài Khoản) đã xác thực qua API
        $user = Auth::user();
        if (!$user) {
            return $this->respondError('Chưa xác thực.', 401);
        }

        // 2. Tìm hồ sơ GiaSu tương ứng
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        if (!$giaSu) {
            return $this->respondError('Bạn không có quyền thực hiện thao tác này.', 403);
        }

        // 3. Áp dụng quy tắc nghiệp vụ:
        // Chỉ gia sư "Hoạt động" (1) mới được gửi đề nghị
        if ($giaSu->TrangThai != 1) { // 1 = Hoạt động
            return $this->respondError('Tài khoản của bạn chưa được duyệt. Vui lòng cập nhật thông tin tài khoản.', 403);
        }

        // 4. <<< SỬA VALIDATION: Đổi 'LopHocID' thành 'LopYeuCauID' và sửa tên bảng
        $request->validate([
            'LopYeuCauID' => 'required|exists:lophocyeucau,LopYeuCauID',
            'GhiChu' => 'nullable|string|max:500', // Thêm GhiChu
        ]);

        // 5. <<< SỬA LOGIC: Dùng 'LopYeuCauID'
        $lopHoc = LopHocYeuCau::find($request->LopYeuCauID);

        // Kiểm tra xem lớp học có ở trạng thái "TimGiaSu" không
        if ($lopHoc->TrangThai != 'TimGiaSu') { // <<< SỬA LOGIC: (Tên trạng thái trong DB)
            return $this->respondError('Lớp học này không còn ở trạng thái tìm gia sư.', 400);
        }

        // 6. <<< SỬA LOGIC: Dùng 'LopYeuCauID'
        $existingYeuCau = YeuCauNhanLop::where('LopYeuCauID', $request->LopYeuCauID)
            ->where('GiaSuID', $giaSu->GiaSuID) 
            ->where('TrangThai', self::STATUS_PENDING) // Chỉ kiểm tra nếu đang chờ
            ->first();

        if ($existingYeuCau) {
            return $this->respondError('Bạn đã gửi yêu cầu cho lớp học này rồi.', 400);
        }

        // 7. <<< SỬA LOGIC: Sửa 'LopHocID' và thêm các trường còn thiếu
        $yeuCau = YeuCauNhanLop::create([
            'LopYeuCauID' => $request->LopYeuCauID,
            'GiaSuID' => $giaSu->GiaSuID, 
            'NguoiGuiTaiKhoanID' => $user->TaiKhoanID, // <<< THÊM
            'VaiTroNguoiGui' => 'GiaSu', // <<< THÊM
            'TrangThai' => self::STATUS_PENDING, // <<< SỬA (Gán trạng thái chuẩn)
            'GhiChu' => $request->input('GhiChu'), // <<< THÊM
            'NgayTao' => Carbon::now(),
            'NgayCapNhat' => Carbon::now(),
        ]);

        return $this->respondSuccess(
            'Gửi đề nghị dạy thành công.',
            $this->toResource($yeuCau),
            201
        );
    }
    
    // ... (Các hàm còn lại của YeuCauNhanLopController giữ nguyên) ...

    public function nguoiHocMoiGiaSu(Request $request)
    {
        $lopId = $this->resolveInput($request, ['LopYeuCauID', 'lop_yeu_cau_id']);
        $giaSuId = $this->resolveInput($request, ['GiaSuID', 'gia_su_id']);
        $taiKhoanId = $this->getTaiKhoanId($request);

        if (!$lopId || !$giaSuId || !$taiKhoanId) {
            return $this->respondError('Thiếu dữ liệu bắt buộc.', 422);
        }

        $request->validate([
            'GhiChu' => 'nullable|string|max:500',
        ]);

        $lop = LopHocYeuCau::find($lopId);
        if (!$lop) {
            return $this->respondError('Không tìm thấy lớp học yêu cầu.', 404);
        }

        $existing = YeuCauNhanLop::where('LopYeuCauID', $lopId)
            ->where('GiaSuID', $giaSuId)
            ->whereIn('TrangThai', [self::STATUS_PENDING, self::STATUS_ACCEPTED])
            ->first();

        if ($existing) {
            return $this->respondError('Đề nghị dành cho gia sư này đã tồn tại.', 409);
        }

        $yeuCau = YeuCauNhanLop::create([
            'LopYeuCauID' => $lopId,
            'GiaSuID' => $giaSuId,
            'NguoiGuiTaiKhoanID' => $taiKhoanId,
            'VaiTroNguoiGui' => 'NguoiHoc',
            'TrangThai' => self::STATUS_PENDING,
            'GhiChu' => $request->input('GhiChu'),
            'NgayTao' => Carbon::now(),
            'NgayCapNhat' => Carbon::now(),
        ]);

        return $this->respondSuccess(
            'Đã gửi lời mời tới gia sư.',
            $this->toResource($yeuCau),
            201
        );
    }

    public function capNhatYeuCau(Request $request, int $yeuCauId)
    {
        $yeuCau = YeuCauNhanLop::find($yeuCauId);
        if (!$yeuCau) {
            return $this->respondError('Không tìm thấy đề nghị.', 404);
        }

        if ($yeuCau->TrangThai !== self::STATUS_PENDING) {
            return $this->respondError('Chỉ có thể chỉnh sửa đề nghị đang chờ xử lý.', 400);
        }

        $taiKhoanId = $this->getTaiKhoanId($request);
        if ($taiKhoanId && (int) $yeuCau->NguoiGuiTaiKhoanID !== (int) $taiKhoanId) {
            return $this->respondError('Bạn không có quyền chỉnh sửa đề nghị này.', 403);
        }

        $request->validate([
            'GhiChu' => 'nullable|string|max:500',
        ]);

        $yeuCau->GhiChu = $request->input('GhiChu');
        $yeuCau->NgayCapNhat = Carbon::now();
        $yeuCau->save();

        return $this->respondSuccess('Đã cập nhật đề nghị.', $this->toResource($yeuCau));
    }

    public function xacNhanYeuCau(Request $request, int $yeuCauId)
    {
        $yeuCau = YeuCauNhanLop::find($yeuCauId);
        if (!$yeuCau) {
            return $this->respondError('Không tìm thấy đề nghị.', 404);
        }

        if ($yeuCau->TrangThai !== self::STATUS_PENDING) {
            return $this->respondError('Đề nghị đã được xử lý.', 400);
        }

        $yeuCau->TrangThai = self::STATUS_ACCEPTED;
        $yeuCau->NgayCapNhat = Carbon::now();
        $yeuCau->save();

        LopHocYeuCau::where('LopYeuCauID', $yeuCau->LopYeuCauID)->update([
            'GiaSuID' => $yeuCau->GiaSuID,
            'TrangThai' => 'DangHoc',
        ]);

        YeuCauNhanLop::where('LopYeuCauID', $yeuCau->LopYeuCauID)
            ->where('YeuCauID', '!=', $yeuCauId)
            ->where('TrangThai', self::STATUS_PENDING)
            ->update([
                'TrangThai' => self::STATUS_REJECTED,
                'NgayCapNhat' => Carbon::now(),
            ]);

        return $this->respondSuccess('Đã xác nhận đề nghị.', $this->toResource($yeuCau));
    }

    public function tuChoiYeuCau(Request $request, int $yeuCauId)
    {
        $yeuCau = YeuCauNhanLop::find($yeuCauId);
        if (!$yeuCau) {
            return $this->respondError('Không tìm thấy đề nghị.', 404);
        }

        if ($yeuCau->TrangThai !== self::STATUS_PENDING) {
            return $this->respondError('Đề nghị đã được xử lý.', 400);
        }

        $yeuCau->TrangThai = self::STATUS_REJECTED;
        $yeuCau->NgayCapNhat = Carbon::now();
        $yeuCau->save();

        return $this->respondSuccess('Đã từ chối đề nghị.', $this->toResource($yeuCau));
    }

    public function huyYeuCau(Request $request, int $yeuCauId)
    {
        $yeuCau = YeuCauNhanLop::find($yeuCauId);
        if (!$yeuCau) {
            return $this->respondError('Không tìm thấy đề nghị.', 404);
        }

        if ($yeuCau->TrangThai !== self::STATUS_PENDING) {
            return $this->respondError('Không thể hủy đề nghị đã được xử lý.', 400);
        }

        $taiKhoanId = $this->getTaiKhoanId($request);
        if ($taiKhoanId && (int) $yeuCau->NguoiGuiTaiKhoanID !== (int) $taiKhoanId) {
            return $this->respondError('Bạn không có quyền hủy đề nghị này.', 403);
        }

        $yeuCau->TrangThai = self::STATUS_CANCELLED;
        $yeuCau->NgayCapNhat = Carbon::now();
        $yeuCau->save();

        return $this->respondSuccess('Đã hủy đề nghị.', $this->toResource($yeuCau));
    }

    public function danhSachYeuCauDaGui(Request $request)
    {
        $taiKhoanId = $this->getTaiKhoanId($request);
        if (!$taiKhoanId) {
            return $this->respondError('Thiếu thông tin tài khoản gửi đề nghị.', 422);
        }

        $yeuCau = $this->loadCollection(
            YeuCauNhanLop::with(['lop.monHoc', 'lop.khoiLop', 'lop.nguoiHoc', 'giaSu', 'nguoiGuiTaiKhoan'])
                ->where('NguoiGuiTaiKhoanID', $taiKhoanId)
        );

        return $this->respondSuccess(
            'Lấy danh sách đề nghị đã gửi thành công.',
            YeuCauNhanLopResource::collection($yeuCau)->resolve()
        );
    }

    public function danhSachYeuCauNhanDuoc(Request $request)
    {
        $giaSuId = $this->resolveInput($request, ['GiaSuID', 'gia_su_id']);
        if (!$giaSuId) {
            return $this->respondError('Thiếu thông tin gia sư.', 422);
        }

        $yeuCau = $this->loadCollection(
            YeuCauNhanLop::with(['lop.monHoc', 'lop.khoiLop', 'lop.nguoiHoc', 'giaSu', 'nguoiGuiTaiKhoan'])
                ->where('GiaSuID', $giaSuId)
        );

        return $this->respondSuccess(
            'Lấy danh sách đề nghị thành công.',
            YeuCauNhanLopResource::collection($yeuCau)->resolve()
        );
    }

    public function danhSachDeNghiTheoLop(int $lopYeuCauId)
    {
        $lop = LopHocYeuCau::find($lopYeuCauId);
        if (!$lop) {
            return $this->respondError('Không tìm thấy lớp học yêu cầu.', 404);
        }

        $yeuCau = $this->loadCollection(
            YeuCauNhanLop::with(['lop.monHoc', 'lop.khoiLop', 'lop.nguoiHoc', 'giaSu', 'nguoiGuiTaiKhoan'])
                ->where('LopYeuCauID', $lopYeuCauId)
        );

        return $this->respondSuccess(
            'Lấy danh sách đề nghị theo lớp thành công.',
            YeuCauNhanLopResource::collection($yeuCau)->resolve()
        );
    }

    public function getLopCuaGiaSu(int $giaSuId)
    {
        $lopDangDay = LopHocYeuCau::with(['nguoiHoc', 'monHoc', 'khoiLop', 'giaSu', 'doiTuong', 'thoiGianDay'])
            ->where('GiaSuID', $giaSuId)
            ->whereIn('TrangThai', ['DangHoc', 'HoanThanh'])
            ->orderByDesc('NgayTao')
            ->get();

        $lopDeNghi = YeuCauNhanLop::with(['lop.monHoc', 'lop.khoiLop', 'lop.nguoiHoc', 'giaSu', 'nguoiGuiTaiKhoan'])
            ->where('GiaSuID', $giaSuId)
            ->where('TrangThai', self::STATUS_PENDING)
            ->orderByDesc('NgayTao')
            ->get();

        return $this->respondSuccess('Lấy danh sách lớp của gia sư thành công.', [
            'lopDangDay' => LopHocYeuCauResource::collection($lopDangDay)->resolve(),
            'lopDeNghi' => YeuCauNhanLopResource::collection($lopDeNghi)->resolve(),
        ]);
    }
}