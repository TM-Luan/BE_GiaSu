<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\GiaSuRequest;
use App\Http\Resources\GiaSuResource;
use App\Models\GiaSu;
use Illuminate\Http\Request; // <-- Đảm bảo bạn đã import Request

// === THÊM CÁC IMPORT CẦN THIẾT ===
use App\Models\LopHocYeuCau;
use App\Http\Resources\LopHocYeuCauResource;
// === KẾT THÚC THÊM IMPORT ===

class GiaSuController extends Controller
{
    // ... (Các hàm index, show, store, update, destroy của bạn giữ nguyên) ...

    public function index()
    {
        $tutors = GiaSu::with('taiKhoan')->get();
        return GiaSuResource::collection($tutors);
    }

    public function show($id)
    {
        $tutor = GiaSu::with('taiKhoan')->findOrFail($id);
        return new GiaSuResource($tutor);
    }

    public function store(GiaSuRequest $request)
    {
        $tutor = GiaSu::create($request->validated());
        return response()->json([
            'message' => 'Tạo gia sư thành công!',
            'data' => new GiaSuResource($tutor)
        ], 201);
    }

    public function update(GiaSuRequest $request, $id)
    {
        $tutor = GiaSu::findOrFail($id);
        $tutor->update($request->validated());
        return response()->json([
            'message' => 'Cập nhật thành công!',
            'data' => new GiaSuResource($tutor)
        ]);
    }

    public function destroy($id)
    {
        $tutor = GiaSu::findOrFail($id);
        $tutor->delete();

        return response()->json(['message' => 'Đã xóa gia sư.']);
    }


    /**
     * Lấy danh sách lớp đang dạy của gia sư đang đăng nhập.
     * API: GET /giasu/lopdangday
     */
    // === SỬA HÀM NÀY ===
    public function getLopDangDay(Request $request) // <-- Thêm 'Request $request'
    {
        try {
            // 1. Lấy TaiKhoanID từ token (AN TOÀN HƠN)
            $taiKhoanID = $request->user()->TaiKhoanID; // <-- Sửa từ auth()->user()

            // 2. Tìm GiaSuID tương ứng
            $giaSu = GiaSu::where('TaiKhoanID', $taiKhoanID)->first();

            if (!$giaSu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin gia sư cho tài khoản này.'
                ], 404);
            }

            // 3. Truy vấn các lớp học
            $lopHocList = LopHocYeuCau::where('GiaSuID', $giaSu->GiaSuID)
                                    ->where('TrangThai', 'DangHoc')
                                    ->with([
                                        'nguoiHoc', 
                                        'monHoc', 
                                        'khoiLop', 
                                        'doiTuong', 
                                        'thoiGianDay'
                                    ])
                                    ->orderBy('NgayTao', 'desc')
                                    ->get();

            // 4. Trả về bằng Resource
            return LopHocYeuCauResource::collection($lopHocList);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi máy chủ: ' . $e->getMessage()
            ], 500);
        }
    }
}
