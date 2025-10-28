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
    public function index(Request $request)
    {
        $query = LopHocYeuCau::query();

        // Eager load relationships for the Resource
        // Tải các quan hệ mà Resource (bên phải) cần
        $query->with(['nguoiHoc', 'monHoc', 'khoiLop', 'giaSu']);

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
    public function store(LopHocYeuCauRequest $request) // <-- Dùng Request để validate
    {
        // Dữ liệu đã được validate tự động bởi LopHocYeuCauRequest
        $validatedData = $request->validated();

        // Set trạng thái mặc định (dựa trên file sql.sql)
        $validatedData['TrangThai'] = 'ChoDuyet';

        $lopHoc = LopHocYeuCau::create($validatedData);
        // Tải các quan hệ để Resource có thể dùng
        $lopHoc->load(['nguoiHoc', 'monHoc', 'khoiLop']); 

        // Trả về Resource đã định dạng với status 201 (Created)
        return (new LopHocYeuCauResource($lopHoc))
                ->response()
                ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Tải tất cả các quan hệ cần thiết cho trang chi tiết
        $lopHoc = LopHocYeuCau::with(['nguoiHoc', 'monHoc', 'khoiLop', 'giaSu', 'doiTuong', 'thoiGianDay'])
                        ->findOrFail($id);

        // Dùng Resource để định dạng
        return new LopHocYeuCauResource($lopHoc);
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
}

