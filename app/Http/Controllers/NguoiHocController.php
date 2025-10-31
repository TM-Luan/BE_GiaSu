<?php

namespace App\Http\Controllers;
use App\Models\NguoiHoc;
use Illuminate\Http\Request;
use App\Http\Resources\NguoiHocResources;
use App\Http\Requests\NguoiHocRequest;
use App\Models\LopHocYeuCau;
use App\Http\Resources\LopHocYeuCauResource;
use Illuminate\Support\Facades\Log; // Thêm để debug nếu cần

class NguoiHocController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $nh = NguoiHoc::with('taiKhoan')->get();
        return NguoiHocResources::collection($nh);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $nh = NguoiHoc::create($request->validated());
        return response()->json([
            'message' => 'Tạo người học thành công!',
            'data' => new NguoiHocResources($nh)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $nh = NguoiHoc::with('taiKhoan')->findOrFail($id);
        return new NguoiHocResources($nh);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         $nh = NguoiHoc::findOrFail($id);
        $nh->update($request->validated());
        return response()->json([
            'message' => 'Cập nhật thành công!',
            'data' => new NguoiHocResources($nh)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
                $nh = NguoiHoc::findOrFail($id);
        $nh->delete();

        return response()->json(['message' => 'Đã xóa người học.']);
    }
    /**
     * Lấy danh sách lớp của người học đang đăng nhập.
     */
    /**
 * Lấy danh sách lớp học của người học đang đăng nhập.
 * API: GET /nguoihoc/lopcuatoi
 */
    public function getLopHocCuaNguoiHoc(Request $request)
    {
        try {
            // ✅ Nếu chưa có auth, cho phép lấy từ query
            $taiKhoanID = $request->user()->TaiKhoanID ?? $request->query('TaiKhoanID');

            if (!$taiKhoanID) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thiếu thông tin TaiKhoanID hoặc chưa đăng nhập.'
                ], 400);
            }

            $nguoiHoc = NguoiHoc::where('TaiKhoanID', $taiKhoanID)->first();

            if (!$nguoiHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin người học cho tài khoản này.'
                ], 404);
            }

            $lopHocList = LopHocYeuCau::where('NguoiHocID', $nguoiHoc->NguoiHocID)
                ->with(['nguoiHoc','monHoc','khoiLop','giaSu','doiTuong','thoiGianDay'])
                ->orderBy('NgayTao', 'desc')
                ->get();

            return LopHocYeuCauResource::collection($lopHocList);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi máy chủ: ' . $e->getMessage()
            ], 500);
        }
    }

}
