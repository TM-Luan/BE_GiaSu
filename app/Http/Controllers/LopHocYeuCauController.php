<?php

namespace App\Http\Controllers;
use App\Models\LopHocYeuCau;
use App\Http\Requests\LopHocYeuCauRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\LopHocYeuCauResource;
use Illuminate\Http\Request;

class LopHocYeuCauController extends Controller
{
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
     * Tìm kiếm và lọc danh sách lớp học
     * API: GET /lophoc/search
     * Parameters:
     * - keyword: tìm kiếm theo mô tả
     * - min_price: học phí tối thiểu
     * - max_price: học phí tối đa
     * - subject_id: ID môn học
     * - grade_id: ID khối lớp
     * - target_id: ID đối tượng
     * - time_id: ID thời gian dạy
     * - form: hình thức (online/offline)
     * - status: trạng thái lớp
     * - location: địa chỉ
     */
    public function search(SearchRequest $request)
    {
        try {
            $query = LopHocYeuCau::with(['nguoiHoc', 'monHoc', 'khoiLop', 'giaSu', 'doiTuong', 'thoiGianDay']);

            // Tìm kiếm theo từ khóa trong mô tả
            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where(function($q) use ($keyword) {
                    $q->where('MoTa', 'LIKE', "%{$keyword}%")
                      ->orWhereHas('nguoiHoc', function($subQ) use ($keyword) {
                          $subQ->where('HoTen', 'LIKE', "%{$keyword}%");
                      });
                });
            }

            // Lọc theo môn học
            if ($request->filled('subject_id')) {
                $query->where('MonID', $request->subject_id);
            }

            // Lọc theo khối lớp
            if ($request->filled('grade_id')) {
                $query->where('KhoiLopID', $request->grade_id);
            }

            // Lọc theo đối tượng
            if ($request->filled('target_id')) {
                $query->where('DoiTuongID', $request->target_id);
            }

            // Lọc theo thời gian dạy
            if ($request->filled('time_id')) {
                $query->where('ThoiGianDayID', $request->time_id);
            }

            // Lọc theo hình thức
            if ($request->filled('form')) {
                $query->where('HinhThuc', $request->form);
            }

            // Lọc theo trạng thái
            if ($request->filled('status')) {
                $query->where('TrangThai', $request->status);
            } else {
                // Mặc định chỉ lấy lớp đang tìm gia sư
                $query->whereIn('TrangThai', ['TimGiaSu', 'ChoDuyet']);
            }

            // Lọc theo học phí
            if ($request->filled('min_price')) {
                $query->where('HocPhi', '>=', $request->min_price);
            }

            if ($request->filled('max_price')) {
                $query->where('HocPhi', '<=', $request->max_price);
            }

            // Lọc theo địa chỉ (thông qua người học)
            if ($request->filled('location')) {
                $location = $request->location;
                $query->whereHas('nguoiHoc', function($q) use ($location) {
                    $q->where('DiaChi', 'LIKE', "%{$location}%");
                });
            }

            // Sắp xếp
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            switch ($sortBy) {
                case 'price':
                    $query->orderBy('HocPhi', $sortOrder);
                    break;
                case 'duration':
                    $query->orderBy('ThoiLuong', $sortOrder);
                    break;
                case 'students':
                    $query->orderBy('SoLuong', $sortOrder);
                    break;
                default:
                    $query->orderBy('NgayTao', $sortOrder);
            }

            // Phân trang
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

