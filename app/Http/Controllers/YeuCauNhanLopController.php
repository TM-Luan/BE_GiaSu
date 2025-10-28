<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\YeuCauNhanLop;
use App\Models\LopHocYeuCau;
use App\Models\GiaSu;
use App\Models\NguoiHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class YeuCauNhanLopController extends Controller
{
    /**
     * Gia sư gửi yêu cầu nhận lớp
     */
    public function giaSuGuiYeuCau(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lop_yeu_cau_id' => 'required|exists:lophocyeucau,LopYeuCauID',
            'ghi_chu' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Lấy thông tin gia sư từ tài khoản đăng nhập
            $taiKhoanID = auth()->user()->TaiKhoanID;
            $giaSu = GiaSu::where('TaiKhoanID', $taiKhoanID)->first();

            if (!$giaSu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thực hiện chức năng này'
                ], 403);
            }

            $lopYeuCau = LopHocYeuCau::find($request->lop_yeu_cau_id);

            // Kiểm tra trạng thái lớp
            if ($lopYeuCau->TrangThai !== 'TimGiaSu' && $lopYeuCau->TrangThai !== 'DangChonGiaSu') {
                return response()->json([
                    'success' => false,
                    'message' => 'Lớp học không trong trạng thái tìm gia sư'
                ], 400);
            }

            // Kiểm tra xem đã gửi yêu cầu chưa
            $yeuCauTonTai = YeuCauNhanLop::where('LopYeuCauID', $request->lop_yeu_cau_id)
                ->where('GiaSuID', $giaSu->GiaSuID)
                ->where('VaiTroNguoiGui', 'GiaSu')
                ->whereIn('TrangThai', ['Pending', 'Accepted'])
                ->exists();

            if ($yeuCauTonTai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã gửi yêu cầu cho lớp này rồi'
                ], 400);
            }

            $yeuCau = YeuCauNhanLop::create([
                'LopYeuCauID' => $request->lop_yeu_cau_id,
                'GiaSuID' => $giaSu->GiaSuID,
                'NguoiGuiTaiKhoanID' => $taiKhoanID,
                'VaiTroNguoiGui' => 'GiaSu',
                'TrangThai' => 'Pending',
                'GhiChu' => $request->ghi_chu
            ]);

            // Cập nhật trạng thái lớp sang DangChonGiaSu
            $lopYeuCau->update(['TrangThai' => 'DangChonGiaSu']);

            return response()->json([
                'success' => true,
                'message' => 'Gửi yêu cầu nhận lớp thành công',
                'data' => $yeuCau
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Người học mời gia sư dạy lớp
     */
    public function nguoiHocMoiGiaSu(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lop_yeu_cau_id' => 'required|exists:lophocyeucau,LopYeuCauID',
            'gia_su_id' => 'required|exists:giasu,GiaSuID',
            'ghi_chu' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Lấy thông tin người học từ tài khoản đăng nhập
            $taiKhoanID = auth()->user()->TaiKhoanID;
            $nguoiHoc = NguoiHoc::where('TaiKhoanID', $taiKhoanID)->first();

            if (!$nguoiHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thực hiện chức năng này'
                ], 403);
            }

            $lopYeuCau = LopHocYeuCau::find($request->lop_yeu_cau_id);

            // Kiểm tra lớp có thuộc về người học này không
            if ($lopYeuCau->NguoiHocID !== $nguoiHoc->NguoiHocID) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đây không phải lớp học của bạn'
                ], 403);
            }

            // Kiểm tra trạng thái lớp
            if ($lopYeuCau->TrangThai !== 'TimGiaSu' && $lopYeuCau->TrangThai !== 'DangChonGiaSu') {
                return response()->json([
                    'success' => false,
                    'message' => 'Lớp học không trong trạng thái tìm gia sư'
                ], 400);
            }

            // Kiểm tra xem đã mời gia sư này chưa
            $yeuCauTonTai = YeuCauNhanLop::where('LopYeuCauID', $request->lop_yeu_cau_id)
                ->where('GiaSuID', $request->gia_su_id)
                ->where('VaiTroNguoiGui', 'NguoiHoc')
                ->whereIn('TrangThai', ['Pending', 'Accepted'])
                ->exists();

            if ($yeuCauTonTai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã mời gia sư này rồi'
                ], 400);
            }

            $yeuCau = YeuCauNhanLop::create([
                'LopYeuCauID' => $request->lop_yeu_cau_id,
                'GiaSuID' => $request->gia_su_id,
                'NguoiGuiTaiKhoanID' => $taiKhoanID,
                'VaiTroNguoiGui' => 'NguoiHoc',
                'TrangThai' => 'Pending',
                'GhiChu' => $request->ghi_chu
            ]);

            // Cập nhật trạng thái lớp sang DangChonGiaSu
            $lopYeuCau->update(['TrangThai' => 'DangChonGiaSu']);

            return response()->json([
                'success' => true,
                'message' => 'Mời gia sư thành công',
                'data' => $yeuCau
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xác nhận yêu cầu (cho cả gia sư và người học)
     */
    public function xacNhanYeuCau(Request $request, $yeuCauID)
    {
        try {
            $yeuCau = YeuCauNhanLop::with(['lopYeuCau', 'giaSu'])->findOrFail($yeuCauID);
            $taiKhoanID = auth()->user()->TaiKhoanID;

            // Kiểm tra quyền xác nhận
            if ($yeuCau->VaiTroNguoiGui === 'GiaSu') {
                // Người học xác nhận yêu cầu của gia sư
                $nguoiHoc = NguoiHoc::where('TaiKhoanID', $taiKhoanID)->first();
                if (!$nguoiHoc || $yeuCau->lopYeuCau->NguoiHocID !== $nguoiHoc->NguoiHocID) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền xác nhận yêu cầu này'
                    ], 403);
                }
            } else {
                // Gia sư xác nhận lời mời của người học
                $giaSu = GiaSu::where('TaiKhoanID', $taiKhoanID)->first();
                if (!$giaSu || $yeuCau->GiaSuID !== $giaSu->GiaSuID) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền xác nhận yêu cầu này'
                    ], 403);
                }
            }

            if ($yeuCau->TrangThai !== 'Pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Yêu cầu đã được xử lý'
                ], 400);
            }

            DB::beginTransaction();

            // Cập nhật yêu cầu thành Accepted
            $yeuCau->update(['TrangThai' => 'Accepted']);

            // Cập nhật lớp học: gán gia sư và chuyển trạng thái sang DangHoc
            $yeuCau->lopYeuCau->update([
                'GiaSuID' => $yeuCau->GiaSuID,
                'TrangThai' => 'DangHoc'
            ]);

            // Từ chối tất cả yêu cầu khác của lớp này
            YeuCauNhanLop::where('LopYeuCauID', $yeuCau->LopYeuCauID)
                ->where('YeuCauID', '!=', $yeuCauID)
                ->where('TrangThai', 'Pending')
                ->update(['TrangThai' => 'Rejected']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xác nhận yêu cầu thành công',
                'data' => $yeuCau->load('lopYeuCau')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Từ chối yêu cầu
     */
    public function tuChoiYeuCau(Request $request, $yeuCauID)
    {
        try {
            $yeuCau = YeuCauNhanLop::with('lopYeuCau')->findOrFail($yeuCauID);
            $taiKhoanID = auth()->user()->TaiKhoanID;

            // Kiểm tra quyền từ chối
            if ($yeuCau->VaiTroNguoiGui === 'GiaSu') {
                $nguoiHoc = NguoiHoc::where('TaiKhoanID', $taiKhoanID)->first();
                if (!$nguoiHoc || $yeuCau->lopYeuCau->NguoiHocID !== $nguoiHoc->NguoiHocID) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền từ chối yêu cầu này'
                    ], 403);
                }
            } else {
                $giaSu = GiaSu::where('TaiKhoanID', $taiKhoanID)->first();
                if (!$giaSu || $yeuCau->GiaSuID !== $giaSu->GiaSuID) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền từ chối yêu cầu này'
                    ], 403);
                }
            }

            if ($yeuCau->TrangThai !== 'Pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Yêu cầu đã được xử lý'
                ], 400);
            }

            $yeuCau->update(['TrangThai' => 'Rejected']);

            return response()->json([
                'success' => true,
                'message' => 'Từ chối yêu cầu thành công',
                'data' => $yeuCau
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hủy yêu cầu đã gửi
     */
    public function huyYeuCau($yeuCauID)
    {
        try {
            $yeuCau = YeuCauNhanLop::findOrFail($yeuCauID);
            $taiKhoanID = auth()->user()->TaiKhoanID;

            // Chỉ người gửi mới được hủy
            if ($yeuCau->NguoiGuiTaiKhoanID !== $taiKhoanID) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền hủy yêu cầu này'
                ], 403);
            }

            if ($yeuCau->TrangThai !== 'Pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể hủy yêu cầu đang chờ xử lý'
                ], 400);
            }

            $yeuCau->update(['TrangThai' => 'Cancelled']);

            return response()->json([
                'success' => true,
                'message' => 'Hủy yêu cầu thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách yêu cầu đã gửi
     */
    public function danhSachYeuCauDaGui()
    {
        try {
            $taiKhoanID = auth()->user()->TaiKhoanID;

            $yeuCauList = YeuCauNhanLop::with(['lopYeuCau.monHoc', 'lopYeuCau.khoiLop', 'giaSu.taiKhoan'])
                ->where('NguoiGuiTaiKhoanID', $taiKhoanID)
                ->orderBy('NgayTao', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $yeuCauList
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách yêu cầu nhận được
     */
    public function danhSachYeuCauNhanDuoc()
    {
        try {
            $taiKhoanID = auth()->user()->TaiKhoanID;
            
            // Kiểm tra vai trò
            $giaSu = GiaSu::where('TaiKhoanID', $taiKhoanID)->first();
            $nguoiHoc = NguoiHoc::where('TaiKhoanID', $taiKhoanID)->first();

            if ($giaSu) {
                // Gia sư nhận được lời mời từ người học
                $yeuCauList = YeuCauNhanLop::with(['lopYeuCau.monHoc', 'lopYeuCau.khoiLop', 'lopYeuCau.nguoiHoc'])
                    ->where('GiaSuID', $giaSu->GiaSuID)
                    ->where('VaiTroNguoiGui', 'NguoiHoc')
                    ->orderBy('NgayTao', 'desc')
                    ->get();
            } elseif ($nguoiHoc) {
                // Người học nhận được yêu cầu từ gia sư
                $yeuCauList = YeuCauNhanLop::with(['lopYeuCau.monHoc', 'lopYeuCau.khoiLop', 'giaSu.taiKhoan'])
                    ->whereHas('lopYeuCau', function($query) use ($nguoiHoc) {
                        $query->where('NguoiHocID', $nguoiHoc->NguoiHocID);
                    })
                    ->where('VaiTroNguoiGui', 'GiaSu')
                    ->orderBy('NgayTao', 'desc')
                    ->get();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin người dùng'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $yeuCauList
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}