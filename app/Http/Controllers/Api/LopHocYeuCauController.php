<?php

namespace App\Http\Controllers\Api;
use App\Models\LopHocYeuCau;
use App\Http\Requests\LopHocYeuCauRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\LopHocYeuCauResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class LopHocYeuCauController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LopHocYeuCau::query();

        // SỬA: Xóa 'thoiGianDay'
        $query->with(['nguoiHoc', 'monHoc', 'khoiLop', 'giaSu', 'doiTuong']);

        if ($request->has('trang_thai')) {
            $query->where('TrangThai', $request->query('trang_thai'));
        }
        
        $lopHocList = $query->get();
        return LopHocYeuCauResource::collection($lopHocList);
    }

    /**
     * Tìm kiếm và lọc danh sách lớp học (Dành cho Gia Sư)
     */
    public function search(SearchRequest $request)
    {
        try {
            // SỬA: Xóa 'thoiGianDay'
            $query = LopHocYeuCau::with([
                'nguoiHoc',
                'giaSu', 
                'monHoc',
                'khoiLop',
                'doiTuong'
            ]);

           $query->where('TrangThai', 'TimGiaSu');
            
            // ... (các logic lọc 'keyword', 'subject_id', 'grade_id', 'form', 'price' giữ nguyên) ...

            // Lọc theo đối tượng (nếu cần)
            if ($request->filled('target_id')) {
                $query->where('DoiTuongID', $request->target_id);
            }

            // SỬA: Xóa logic lọc theo 'time_id'
            // if ($request->filled('time_id')) { ... }

            // Lọc theo địa chỉ
            if ($request->filled('location')) {
                $location = $request->location;
                $query->whereHas('nguoiHoc', function($q) use ($location) {
                    $q->where('DiaChi', 'LIKE', "%{$location}%");
                });
            }

            // ... (Sắp xếp và Phân trang giữ nguyên) ...

            $perPage = $request->get('per_page', 20);
            $classes = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => LopHocYeuCauResource::collection($classes->items()),
                'pagination' => [
                    'current_page' => $classes->currentPage(),
                    'last_page' => $classes->lastPage(),
                    'per_page' => $classes->perPage(),
                    'total' => $classes->total(),
                    'from' => $classes->firstItem(),
                    'to' => $classes->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tìm kiếm: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(LopHocYeuCauRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['TrangThai'] = 'TimGiaSu';

        $lopHoc = LopHocYeuCau::create($validatedData);
        $lopHoc->refresh(); 

        // SỬA: Xóa 'thoiGianDay'
        $lopHoc->load(['nguoiHoc', 'monHoc', 'khoiLop', 'giaSu', 'doiTuong']); 

        return (new LopHocYeuCauResource($lopHoc))
                ->response()
                ->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LopHocYeuCauRequest $request, $id)
    {
        $lopHoc = LopHocYeuCau::findOrFail($id);
        $lopHoc->update($request->validated());

        // SỬA: Xóa 'thoiGianDay'
        $lopHoc->load(['nguoiHoc', 'monHoc', 'khoiLop', 'giaSu', 'doiTuong']); 

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật lớp học thành công!',
            'data' => new LopHocYeuCauResource($lopHoc)
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $lopHoc = LopHocYeuCau::findOrFail($id);
        $lopHoc->delete();
        return response()->noContent();
    }
    
    public function show($id)
    {
        // SỬA: Xóa 'thoiGianDay'
        $lopHoc = LopHocYeuCau::with(['nguoiHoc', 'monHoc', 'khoiLop', 'giaSu', 'doiTuong'])
                        ->findOrFail($id); 

        return new LopHocYeuCauResource($lopHoc);
    }
}