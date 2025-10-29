<?php

namespace App\Http\Controllers;
use App\Models\LopHocYeuCau;
use App\Http\Requests\LopHocYeuCauRequest;   // <-- IMPORT MỚI
use App\Http\Resources\LopHocYeuCauResource; // <-- IMPORT MỚI
use Illuminate\Http\Request;

class LopHocYeuCauController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LopHocYeuCau::query();

        // Tải các quan hệ mà Resource (bên phải) cần
        // SỬA DÒNG NÀY:
        // Phải thêm 'doiTuong' và 'thoiGianDay' vào đây
        $query->with(['nguoiHoc', 'monHoc', 'khoiLop', 'giaSu', 'doiTuong', 'thoiGianDay']);

        // Giữ nguyên logic lọc 'trang_thai' của bạn
        if ($request->has('trang_thai')) {
            $query->where('TrangThai', $request->query('trang_thai'));
        }
        
        $lopHocList = $query->get();

        // Dùng Resource để định dạng danh sách
        return LopHocYeuCauResource::collection($lopHocList);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(LopHocYeuCauRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['TrangThai'] = 'ChoDuyet';

        $lopHoc = LopHocYeuCau::create($validatedData);
        
        // === THÊM DÒNG NÀY VÀO ===
        // Dòng này sẽ tải lại model từ DB
        // để lấy giá trị NgayTao (vốn được set tự động bởi DB)
        $lopHoc->refresh(); 
        // === KẾT THÚC THÊM ===

        // Bây giờ mới load các quan hệ
        $lopHoc->load(['nguoiHoc', 'monHoc', 'khoiLop', 'giaSu', 'doiTuong', 'thoiGianDay']); 

        // Trả về Resource (giờ đây đã có $lopHoc->NgayTao hợp lệ)
        return (new LopHocYeuCauResource($lopHoc))
                ->response()
                ->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LopHocYeuCauRequest $request, $id) // <-- Dùng Request để validate
    {
        $lopHoc = LopHocYeuCau::findOrFail($id);
        
        $lopHoc->update($request->validated());
        // Tải lại dữ liệu quan hệ
        $lopHoc->load(['nguoiHoc', 'monHoc', 'khoiLop', 'giaSu']); 

        return new LopHocYeuCauResource($lopHoc);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $lopHoc = LopHocYeuCau::findOrFail($id);
        $lopHoc->delete();

        // Trả về response 204 (No Content) chuẩn
        return response()->noContent();
    }
    public function show($id) // Tham số $id được Laravel tự động truyền vào
    {
        // Tải tất cả các quan hệ cần thiết cho trang chi tiết
        $lopHoc = LopHocYeuCau::with(['nguoiHoc', 'monHoc', 'khoiLop', 'giaSu', 'doiTuong', 'thoiGianDay'])
                        ->findOrFail($id); // Tìm bằng ID, nếu không thấy sẽ báo lỗi 404

        // Dùng Resource để định dạng
        return new LopHocYeuCauResource($lopHoc);
    }
}

