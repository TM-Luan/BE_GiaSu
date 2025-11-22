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
        // Thêm 'nguoiHoc.taiKhoan' để lấy SĐT nếu cần
        $query->with(['nguoiHoc.taiKhoan', 'monHoc', 'khoiLop', 'giaSu', 'doiTuong']);

        if ($request->has('trang_thai')) {
            $query->where('TrangThai', $request->query('trang_thai'));
        }

        $lopHocList = $query->get();
        return LopHocYeuCauResource::collection($lopHocList);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LopHocYeuCauRequest $request)
    {
        $validatedData = $request->validated();
        
        // 1. Gán trạng thái lớp là Tìm Gia Sư
        $validatedData['TrangThai'] = 'TimGiaSu';
        
        // 2. QUAN TRỌNG: Gán trạng thái thanh toán mặc định là Chưa Thanh Toán
        $validatedData['TrangThaiThanhToan'] = 'ChuaThanhToan';

        // Tạo lớp học
        $lopHoc = LopHocYeuCau::create($validatedData);
        $lopHoc->refresh();

        // Eager load các quan hệ để trả về JSON đầy đủ ngay lập tức
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
        
        // Chỉ cập nhật các thông tin cho phép, không cập nhật TrangThaiThanhToan ở đây
        // (TrangThaiThanhToan chỉ được update qua GiaoDichController)
        $lopHoc->update($request->validated());

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
        // Thêm 'nguoiHoc.taiKhoan' để lấy SĐT
        $lopHoc = LopHocYeuCau::with([
            'nguoiHoc.taiKhoan',
            'monHoc',
            'khoiLop',
            'giaSu',
            'doiTuong'
        ])->findOrFail($id);

        return new LopHocYeuCauResource($lopHoc);
    }

    /**
     * Tìm kiếm và lọc danh sách lớp học (Dành cho Gia Sư)
     */
    public function search(SearchRequest $request)
    {
        try {
            $query = LopHocYeuCau::with([
                'nguoiHoc',
                'giaSu',
                'monHoc',
                'khoiLop',
                'doiTuong'
            ]);

            // LUÔN LUÔN chỉ lấy lớp đang tìm gia sư
            $query->where('TrangThai', 'TimGiaSu');

            // 1. Tìm kiếm từ khóa (Keyword)
            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('MoTa', 'LIKE', "%{$keyword}%")
                        ->orWhereHas('monHoc', function ($subQ) use ($keyword) {
                            $subQ->where('TenMon', 'LIKE', "%{$keyword}%");
                        })
                        ->orWhereHas('nguoiHoc', function ($subQ) use ($keyword) {
                            $subQ->where('DiaChi', 'LIKE', "%{$keyword}%");
                        });
                });
            }

            // 2. Lọc theo Môn học
            if ($request->filled('subject_id')) {
                $query->where('MonID', $request->subject_id);
            }

            // 3. Lọc theo Khối lớp
            if ($request->filled('grade_id')) {
                $query->where('KhoiLopID', $request->grade_id);
            }

            // 4. Lọc theo Hình thức
            if ($request->filled('form')) {
                $query->where('HinhThuc', $request->form);
            }

            // 5. Lọc theo Học phí
            if ($request->filled('min_price')) {
                $query->where('HocPhi', '>=', (double)$request->min_price);
            }
            if ($request->filled('max_price')) {
                $query->where('HocPhi', '<=', (double)$request->max_price);
            }

            // 6. Lọc theo Khu vực
            if ($request->filled('location')) {
                $location = $request->location;
                $query->whereHas('nguoiHoc', function ($q) use ($location) {
                    $q->where('DiaChi', 'LIKE', "%{$location}%");
                });
            }

            $query->orderBy('NgayTao', 'desc');

            $perPage = $request->get('per_page', 10);
            $classes = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => LopHocYeuCauResource::collection($classes->items()),
                'pagination' => [
                    'current_page' => $classes->currentPage(),
                    'last_page' => $classes->lastPage(),
                    'per_page' => $classes->perPage(),
                    'total' => $classes->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tìm kiếm lớp học: ' . $e->getMessage()
            ], 500);
        }
    }
}