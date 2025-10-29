<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonHoc;
use App\Models\KhoiLop;
use App\Models\DoiTuong;
use App\Models\ThoiGianDay;



class DropdownDataController extends Controller
{
    //
    // Lấy danh sách Môn học
    public function getMonHocList()
    {
        // $data = MonHoc::all();
        // Sửa lại để chỉ lấy ID và Tên cho gọn
        $data = MonHoc::query()->select('MonID', 'TenMon')->get();
        return response()->json(['data' => $data]);
    }

    // Lấy danh sách Khối lớp
    public function getKhoiLopList()
    {
        $data = KhoiLop::query()->select('KhoiLopID', 'BacHoc')->get();
        return response()->json(['data' => $data]);
    }

    // Lấy danh sách Đối tượng
    public function getDoiTuongList()
    {
        $data = DoiTuong::query()->select('DoiTuongID', 'TenDoiTuong')->get();
        return response()->json(['data' => $data]);
    }

    // Lấy danh sách Thời gian dạy
    public function getThoiGianDayList()
    {
        $data = ThoiGianDay::query()->select('ThoiGianDayID', 'SoBuoi', 'BuoiHoc')->get();
        return response()->json(['data' => $data]);
    }
}
