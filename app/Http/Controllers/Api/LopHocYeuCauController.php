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
    // Thêm 'nguoiHoc.taiKhoan'
    $query->with(['nguoiHoc.taiKhoan', 'monHoc', 'khoiLop', 'giaSu', 'doiTuong']);

    if ($request->has('trang_thai')) {
        $query->where('TrangThai', $request->query('trang_thai'));
    }
    
    $lopHocList = $query->get();
    return LopHocYeuCauResource::collection($lopHocList);
}

    /**
     * Tìm kiếm và lọc danh sách lớp học (Dành cho Gia Sư)
     */
    // public function search(SearchRequest $request)
    // {
    //     try {
    //         // SỬA: Xóa 'thoiGianDay'
    //         $query = LopHocYeuCau::with([
    //             'nguoiHoc',
    //             'giaSu', 
    //             'monHoc',
    //             'khoiLop',
    //             'doiTuong'
    //         ]);

    //        $query->where('TrangThai', 'TimGiaSu');
            
    //         // ... (các logic lọc 'keyword', 'subject_id', 'grade_id', 'form', 'price' giữ nguyên) ...

    //         // Lọc theo đối tượng (nếu cần)
    //         if ($request->filled('target_id')) {
    //             $query->where('DoiTuongID', $request->target_id);
    //         }

    //         // SỬA: Xóa logic lọc theo 'time_id'
    //         // if ($request->filled('time_id')) { ... }

    //         // Lọc theo địa chỉ
    //         if ($request->filled('location')) {
    //             $location = $request->location;
    //             $query->whereHas('nguoiHoc', function($q) use ($location) {
    //                 $q->where('DiaChi', 'LIKE', "%{$location}%");
    //             });
    //         }

    //         // ... (Sắp xếp và Phân trang giữ nguyên) ...

    //         $perPage = $request->get('per_page', 20);
    //         $classes = $query->paginate($perPage);

    //         return response()->json([
    //             'success' => true,
    //             'data' => LopHocYeuCauResource::collection($classes->items()),
    //             'pagination' => [
    //                 'current_page' => $classes->currentPage(),
    //                 'last_page' => $classes->lastPage(),
    //                 'per_page' => $classes->perPage(),
    //                 'total' => $classes->total(),
    //                 'from' => $classes->firstItem(),
    //                 'to' => $classes->lastItem(),
    //             ]
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Lỗi tìm kiếm: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
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
    // SỬA: Thêm 'nguoiHoc.taiKhoan' vào danh sách with
    $lopHoc = LopHocYeuCau::with([
        'nguoiHoc.taiKhoan', // <--- QUAN TRỌNG: Để lấy SĐT
        'monHoc', 
        'khoiLop', 
        'giaSu', 
        'doiTuong'
    ])->findOrFail($id); 

    return new LopHocYeuCauResource($lopHoc);
}
    public function search(SearchRequest $request)
{
    try {
        // Eager load các quan hệ cần thiết để trả về JSON đầy đủ
        $query = LopHocYeuCau::with([
            'nguoiHoc', // Để lấy địa chỉ, tên người học
            'giaSu', 
            'monHoc',   // Để lấy tên môn
            'khoiLop',  // Để lấy tên khối
            'doiTuong'
        ]);

        // LUÔN LUÔN chỉ lấy lớp đang tìm gia sư
        $query->where('TrangThai', 'TimGiaSu');

        // 1. Tìm kiếm từ khóa (Keyword)
        // Tìm trong: Mô tả lớp, Tên môn học, hoặc Địa chỉ người học
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('MoTa', 'LIKE', "%{$keyword}%") // Tìm trong mô tả
                  ->orWhereHas('monHoc', function($subQ) use ($keyword) {
                      $subQ->where('TenMon', 'LIKE', "%{$keyword}%"); // Tìm theo tên môn
                  })
                  ->orWhereHas('nguoiHoc', function($subQ) use ($keyword) {
                      $subQ->where('DiaChi', 'LIKE', "%{$keyword}%"); // Tìm theo địa chỉ
                  });
            });
        }

        // 2. Lọc theo Môn học (Subject ID)
        if ($request->filled('subject_id')) {
            $query->where('MonID', $request->subject_id);
        }

        // 3. Lọc theo Cấp học (Grade ID - Khối lớp)
        if ($request->filled('grade_id')) {
            $query->where('KhoiLopID', $request->grade_id);
        }

        // 4. Lọc theo Hình thức (Online/Offline)
        if ($request->filled('form')) {
            // Flutter gửi 'Online'/'Offline' -> DB lưu cột HinhThuc
            $query->where('HinhThuc', $request->form);
        }

        // 5. Lọc theo Học phí (Min - Max)
        if ($request->filled('min_price')) {
            $query->where('HocPhi', '>=', (double)$request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('HocPhi', '<=', (double)$request->max_price);
        }

        // 6. Lọc theo Khu vực (Location)
        // Tìm tương đối trong địa chỉ của người học (vì lớp học thường diễn ra tại nhà người học)
        if ($request->filled('location')) {
            $location = $request->location;
            $query->whereHas('nguoiHoc', function($q) use ($location) {
                $q->where('DiaChi', 'LIKE', "%{$location}%");
            });
        }

        // Sắp xếp: Mới nhất lên đầu
        $query->orderBy('NgayTao', 'desc');

        // Phân trang
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