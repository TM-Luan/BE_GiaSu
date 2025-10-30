<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\YeuCauNhanLop;
use App\Models\LopHocYeuCau;
use Carbon\Carbon;

class YeuCauNhanLopController extends Controller
{
    /**
     * Gia sư gửi yêu cầu nhận lớp.
     */
    public function giaSuGuiYeuCau(Request $request)
    {
        $request->validate([
            'LopYeuCauID' => 'required|integer',
            'GiaSuID' => 'required|integer',
            'NguoiGuiTaiKhoanID' => 'required|integer',
        ]);

        $yeuCau = YeuCauNhanLop::create([
            'LopYeuCauID' => $request->LopYeuCauID,
            'GiaSuID' => $request->GiaSuID,
            'NguoiGuiTaiKhoanID' => $request->NguoiGuiTaiKhoanID,
            'VaiTroNguoiGui' => 'GiaSu',
            'TrangThai' => 'Pending',
            'GhiChu' => $request->GhiChu,
        ]);

        return response()->json(['message' => 'Gửi yêu cầu thành công', 'data' => $yeuCau]);
    }

    /**
     * Người học mời gia sư.
     */
    public function nguoiHocMoiGiaSu(Request $request)
    {
        $request->validate([
            'LopYeuCauID' => 'required|integer',
            'GiaSuID' => 'required|integer',
            'NguoiGuiTaiKhoanID' => 'required|integer',
        ]);

        $yeuCau = YeuCauNhanLop::create([
            'LopYeuCauID' => $request->LopYeuCauID,
            'GiaSuID' => $request->GiaSuID,
            'NguoiGuiTaiKhoanID' => $request->NguoiGuiTaiKhoanID,
            'VaiTroNguoiGui' => 'NguoiHoc',
            'TrangThai' => 'Pending',
            'GhiChu' => $request->GhiChu,
        ]);

        return response()->json(['message' => 'Mời gia sư thành công', 'data' => $yeuCau]);
    }

    /**
     * Xác nhận yêu cầu nhận lớp (chấp nhận lời mời hoặc yêu cầu).
     */
    public function xacNhanYeuCau($yeuCauID)
    {
        $yeuCau = YeuCauNhanLop::find($yeuCauID);
        if (!$yeuCau) {
            return response()->json(['message' => 'Không tìm thấy yêu cầu'], 404);
        }

        $yeuCau->TrangThai = 'Accepted';
        $yeuCau->save();

        // Cập nhật lớp học để gán gia sư
        LopHocYeuCau::where('LopYeuCauID', $yeuCau->LopYeuCauID)
            ->update([
                'GiaSuID' => $yeuCau->GiaSuID,
                'TrangThai' => 'DangHoc',
            ]);

        return response()->json(['message' => 'Đã xác nhận yêu cầu']);
    }

    /**
     * Từ chối yêu cầu (dành cho người học khi được mời).
     */
    public function tuChoiYeuCau($yeuCauID)
    {
        $yeuCau = YeuCauNhanLop::find($yeuCauID);
        if (!$yeuCau) {
            return response()->json(['message' => 'Không tìm thấy yêu cầu'], 404);
        }

        $yeuCau->TrangThai = 'Rejected';
        $yeuCau->save();

        return response()->json(['message' => 'Đã từ chối yêu cầu']);
    }

    /**
     * Hủy yêu cầu (dành cho gia sư khi họ gửi yêu cầu đi).
     */
    public function huyYeuCau($yeuCauID)
    {
        $yeuCau = YeuCauNhanLop::find($yeuCauID);
        if (!$yeuCau) {
            return response()->json(['message' => 'Không tìm thấy yêu cầu'], 404);
        }

        $yeuCau->TrangThai = 'Cancelled';
        $yeuCau->save();

        return response()->json(['message' => 'Đã hủy yêu cầu']);
    }

    /**
     * Danh sách yêu cầu gia sư đã gửi.
     */
    public function danhSachYeuCauDaGui(Request $request)
    {
        $taiKhoanID = $request->query('NguoiGuiTaiKhoanID');

        $yeuCau = DB::table('yeucaunhanlop as y')
            ->join('lophocyeucau as l', 'y.LopYeuCauID', '=', 'l.LopYeuCauID')
            ->join('monhoc as m', 'l.MonID', '=', 'm.MonID')
            ->select('y.*', 'm.TenMon', 'l.HinhThuc', 'l.HocPhi', 'l.TrangThai as TrangThaiLop')
            ->where('y.NguoiGuiTaiKhoanID', $taiKhoanID)
            ->get();

        return response()->json($yeuCau);
    }

    /**
     * Danh sách yêu cầu mà người dùng nhận được (ví dụ người học nhận được lời mời từ gia sư).
     */
    public function danhSachYeuCauNhanDuoc(Request $request)
    {
        $giaSuID = $request->query('GiaSuID');

        $yeuCau = DB::table('yeucaunhanlop as y')
            ->join('lophocyeucau as l', 'y.LopYeuCauID', '=', 'l.LopYeuCauID')
            ->join('monhoc as m', 'l.MonID', '=', 'm.MonID')
            ->select('y.*', 'm.TenMon', 'l.HinhThuc', 'l.HocPhi', 'l.TrangThai as TrangThaiLop')
            ->where('y.GiaSuID', $giaSuID)
            ->get();

        return response()->json($yeuCau);
    }

    /**
     * ✅ Lấy danh sách lớp của gia sư (đang dạy + đề nghị)
     */
    public function getLopCuaGiaSu($giaSuID)
    {
        try {
            // 1️⃣ Lớp đang dạy
            $lopDangDay = DB::table('lophocyeucau as l')
                ->join('monhoc as m', 'l.MonID', '=', 'm.MonID')
                ->join('khoilop as k', 'l.KhoiLopID', '=', 'k.KhoiLopID')
                ->select(
                    'l.LopYeuCauID',
                    'm.TenMon',
                    'k.BacHoc',
                    'l.HinhThuc',
                    'l.HocPhi',
                    'l.ThoiLuong',
                    'l.TrangThai'
                )
                ->where('l.GiaSuID', $giaSuID)
                ->whereIn('l.TrangThai', ['DangHoc', 'HoanThanh'])
                ->get();

            // 2️⃣ Lớp đề nghị
            $lopDeNghi = DB::table('yeucaunhanlop as y')
                ->join('lophocyeucau as l', 'y.LopYeuCauID', '=', 'l.LopYeuCauID')
                ->join('monhoc as m', 'l.MonID', '=', 'm.MonID')
                ->select(
                    'y.YeuCauID',
                    'y.VaiTroNguoiGui',
                    'y.TrangThai',
                    'y.GhiChu',
                    'l.LopYeuCauID',
                    'm.TenMon',
                    'l.HinhThuc',
                    'l.HocPhi',
                    'l.TrangThai as TrangThaiLop'
                )
                ->where('y.GiaSuID', $giaSuID)
                ->where('y.TrangThai', 'Pending')
                ->get();

            return response()->json([
                'dang_day' => $lopDangDay,
                'de_nghi' => $lopDeNghi,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
