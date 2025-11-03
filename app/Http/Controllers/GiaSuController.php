<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\GiaSuRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\GiaSuResource;
use App\Models\GiaSu;
use Illuminate\Http\Request; // <-- Đảm bảo bạn đã import Request

// === THÊM CÁC IMPORT CẦN THIẾT ===
use App\Models\LopHocYeuCau;
use App\Models\YeuCauNhanLop;
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

    /**
     * Tìm kiếm và lọc danh sách gia sư
     * API: GET /giasu/search
     * Parameters:
     * - keyword: tìm kiếm theo tên
     * - min_price: giá tối thiểu
     * - max_price: giá tối đa
     * - subject_id: ID môn học
     * - grade_id: ID khối lớp
     * - experience_level: cấp độ kinh nghiệm
     * - gender: giới tính
     * - education_level: trình độ học vấn
     */
    public function search(SearchRequest $request)
    {
        try {
            $query = GiaSu::with(['taiKhoan', 'DanhGia']);

            // Tìm kiếm theo tên
            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where('HoTen', 'LIKE', "%{$keyword}%");
            }

            // Lọc theo giới tính
            if ($request->filled('gender')) {
                $query->where('GioiTinh', $request->gender);
            }

            // Lọc theo trình độ học vấn
            if ($request->filled('education_level')) {
                $query->where('BangCap', 'LIKE', "%{$request->education_level}%");
            }

            // Lọc theo kinh nghiệm
            if ($request->filled('experience_level')) {
                $experienceLevel = $request->experience_level;
                
                // Kiểm tra nếu là số năm (1, 2, 3, 5+)
                if (in_array($experienceLevel, ['1', '2', '3', '5+'])) {
                    $query->where(function($q) use ($experienceLevel) {
                        if ($experienceLevel === '5+') {
                            $q->where('KinhNghiem', 'LIKE', '%5%')
                              ->orWhere('KinhNghiem', 'LIKE', '%Trên 5%')
                              ->orWhere('KinhNghiem', 'LIKE', '%5+%');
                        } else {
                            $q->where('KinhNghiem', 'LIKE', "%{$experienceLevel} năm%")
                              ->orWhere('KinhNghiem', 'LIKE', "%{$experienceLevel}năm%");
                        }
                    });
                } else {
                    // Logic cũ cho beginner, intermediate, experienced, expert
                    $experienceMap = [
                        'beginner' => ['0-1 năm', 'Dưới 1 năm', 'Mới bắt đầu'],
                        'intermediate' => ['1-3 năm', '2-3 năm'],
                        'experienced' => ['3-5 năm', 'Trên 3 năm'],
                        'expert' => ['Trên 5 năm', '5+ năm', 'Nhiều năm']
                    ];

                    if (isset($experienceMap[$experienceLevel])) {
                        $experiences = $experienceMap[$experienceLevel];
                        $query->where(function($q) use ($experiences) {
                            foreach ($experiences as $exp) {
                                $q->orWhere('KinhNghiem', 'LIKE', "%{$exp}%");
                            }
                        });
                    }
                }
            }

            // Lọc theo số năm kinh nghiệm (min/max)
            if ($request->filled('min_experience') || $request->filled('max_experience')) {
                $query->where(function($q) use ($request) {
                    // Tìm kiếm pattern cho số năm trong trường KinhNghiem
                    if ($request->filled('min_experience')) {
                        $minExp = $request->min_experience;
                        $q->where('KinhNghiem', 'LIKE', "%{$minExp}%")
                          ->orWhere('KinhNghiem', 'LIKE', "%Trên {$minExp}%");
                    }
                    
                    if ($request->filled('max_experience')) {
                        $maxExp = $request->max_experience;
                        for ($i = 0; $i <= $maxExp; $i++) {
                            $q->orWhere('KinhNghiem', 'LIKE', "%{$i} năm%");
                        }
                    }
                });
            }

            // Lọc theo chuyên môn - tìm trong bảng MonHoc hoặc thuộc tính liên quan
            if ($request->filled('subject_id')) {
                $subjectId = $request->subject_id;
                // Tạm thời filter theo ID trong thuộc tính khác hoặc bỏ qua nếu không có relationship
                // Có thể filter theo tên trong BangCap hoặc KinhNghiem chứa tên môn
                $query->where(function($q) use ($subjectId) {
                    $q->where('BangCap', 'LIKE', "%{$subjectId}%")
                      ->orWhere('KinhNghiem', 'LIKE', "%{$subjectId}%")
                      ->orWhere('GiaSuID', $subjectId); // Hoặc logic khác phù hợp với DB
                });
            }

            // Lọc theo môn học và giá thông qua các lớp đã dạy (tạm thời comment out vì có thể bảng YeuCauNhanLop chưa có data)
            // if ($request->filled('subject_id')) {
            //     $query->whereHas('yeuCauNhanLop', function($q) use ($request) {
            //         $q->where('MonID', $request->subject_id);
            //     });
            // }

            // if ($request->filled('grade_id')) {
            //     $query->whereHas('yeuCauNhanLop', function($q) use ($request) {
            //         $q->where('KhoiLopID', $request->grade_id);
            //     });
            // }

            // if ($request->filled('min_price')) {
            //     $query->whereHas('yeuCauNhanLop', function($q) use ($request) {
            //         $q->where('HocPhi', '>=', $request->min_price);
            //     });
            // }

            // if ($request->filled('max_price')) {
            //     $query->whereHas('yeuCauNhanLop', function($q) use ($request) {
            //         $q->where('HocPhi', '<=', $request->max_price);
            //     });
            // }

            // Sắp xếp
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            switch ($sortBy) {
                case 'name':
                    $query->orderBy('HoTen', $sortOrder);
                    break;
                case 'experience':
                    $query->orderBy('KinhNghiem', $sortOrder);
                    break;
                default:
                    $query->orderBy('GiaSuID', $sortOrder);
            }

            // Phân trang
            $perPage = $request->get('per_page', 20);
            $tutors = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => GiaSuResource::collection($tutors->items()),
                'pagination' => [
                    'current_page' => $tutors->currentPage(),
                    'last_page' => $tutors->lastPage(),
                    'per_page' => $tutors->perPage(),
                    'total' => $tutors->total(),
                    'from' => $tutors->firstItem(),
                    'to' => $tutors->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tìm kiếm: ' . $e->getMessage()
            ], 500);
        }
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
    // public function getLopDangDay(Request $request) // <-- Thêm 'Request $request'
    // {
    //     try {
    //         // 1. Lấy TaiKhoanID từ token (AN TOÀN HƠN)
    //         $taiKhoanID = $request->user()->TaiKhoanID; // <-- Sửa từ auth()->user()

    //         // 2. Tìm GiaSuID tương ứng
    //         $giaSu = GiaSu::where('TaiKhoanID', $taiKhoanID)->first();

    //         if (!$giaSu) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Không tìm thấy thông tin gia sư cho tài khoản này.'
    //             ], 404);
    //         }

    //         // 3. Truy vấn các lớp học
    //         $lopHocList = LopHocYeuCau::where('GiaSuID', $giaSu->GiaSuID)
    //                                 ->where('TrangThai', 'DangHoc')
    //                                 ->with([
    //                                     'nguoiHoc', 
    //                                     'monHoc', 
    //                                     'khoiLop', 
    //                                     'doiTuong', 
    //                                     'thoiGianDay'
    //                                 ])
    //                                 ->orderBy('NgayTao', 'desc')
    //                                 ->get();

    //         // 4. Trả về bằng Resource
    //         return LopHocYeuCauResource::collection($lopHocList);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Lỗi máy chủ: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
}
