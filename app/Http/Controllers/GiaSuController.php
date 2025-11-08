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
     * Tìm kiếm và lọc danh sách gia sư (Dành cho Học Viên)
     * API: GET /giasu/search
     * Filters:
     * - subject_id: Lọc theo chuyên môn (môn học)
     * - min_rating, max_rating: Lọc theo đánh giá
     * - experience_level: Lọc theo kinh nghiệm
     * - gender: Lọc theo giới tính
     * - keyword: Tìm kiếm theo tên
     */
    public function search(SearchRequest $request)
    {
        try {
            $query = GiaSu::with(['taiKhoan']);

            // Tìm kiếm theo tên
            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where('HoTen', 'LIKE', "%{$keyword}%");
            }

            // 1. Lọc theo chuyên môn (môn học)
            // Giả định: Gia sư có quan hệ với LopHocYeuCau qua GiaSuID
            if ($request->filled('subject_id')) {
                $subjectId = $request->subject_id;
                $query->whereHas('lopHocYeuCau', function($q) use ($subjectId) {
                    $q->where('MonID', $subjectId)
                      ->whereIn('TrangThai', ['DangHoc', 'HoanThanh']);
                });
            }

            // 2. Lọc theo đánh giá trung bình
            if ($request->filled('min_rating')) {
                $minRating = (float) $request->min_rating;
                
                // Sử dụng join với subquery để tính AVG rating
                $query->whereExists(function($subQuery) use ($minRating) {
                    $subQuery->selectRaw('AVG(danhgia.DiemSo) as avg_rating')
                            ->from('lophocyeucau')
                            ->leftJoin('danhgia', 'lophocyeucau.LopYeuCauID', '=', 'danhgia.LopYeuCauID')
                            ->whereColumn('lophocyeucau.GiaSuID', 'giasu.GiaSuID')
                            ->groupBy('lophocyeucau.GiaSuID')
                            ->havingRaw('AVG(danhgia.DiemSo) >= ?', [$minRating]);
                });
            }

            if ($request->filled('max_rating')) {
                $maxRating = (float) $request->max_rating;
                
                $query->whereExists(function($subQuery) use ($maxRating) {
                    $subQuery->selectRaw('AVG(danhgia.DiemSo) as avg_rating')
                            ->from('lophocyeucau')
                            ->leftJoin('danhgia', 'lophocyeucau.LopYeuCauID', '=', 'danhgia.LopYeuCauID')
                            ->whereColumn('lophocyeucau.GiaSuID', 'giasu.GiaSuID')
                            ->groupBy('lophocyeucau.GiaSuID')
                            ->havingRaw('AVG(danhgia.DiemSo) <= ?', [$maxRating]);
                });
            }

            // 3. Lọc theo kinh nghiệm
            if ($request->filled('experience_level')) {
                $experienceLevel = $request->experience_level;
                
                // Kiểm tra nếu là số năm (1, 2, 3, 5+)
                if (in_array($experienceLevel, ['1', '2', '3', '5+'])) {
                    $query->where(function($q) use ($experienceLevel) {
                        if ($experienceLevel === '5+') {
                            $q->where('KinhNghiem', 'LIKE', '%5%')
                              ->orWhere('KinhNghiem', 'LIKE', '%Trên 5%')
                              ->orWhere('KinhNghiem', 'LIKE', '%5+%')
                              ->orWhere('KinhNghiem', 'LIKE', '%6%')
                              ->orWhere('KinhNghiem', 'LIKE', '%7%')
                              ->orWhere('KinhNghiem', 'LIKE', '%8%')
                              ->orWhere('KinhNghiem', 'LIKE', '%9%')
                              ->orWhere('KinhNghiem', 'LIKE', '%10%');
                        } else {
                            $q->where('KinhNghiem', 'LIKE', "%{$experienceLevel} năm%")
                              ->orWhere('KinhNghiem', 'LIKE', "%{$experienceLevel}năm%");
                        }
                    });
                }
            }

            // Lọc theo số năm kinh nghiệm (min/max)
            if ($request->filled('min_experience')) {
                $minExp = $request->min_experience;
                $query->where(function($q) use ($minExp) {
                    for ($i = $minExp; $i <= 10; $i++) {
                        $q->orWhere('KinhNghiem', 'LIKE', "%{$i} năm%")
                          ->orWhere('KinhNghiem', 'LIKE', "%{$i}năm%");
                    }
                    $q->orWhere('KinhNghiem', 'LIKE', '%Trên%')
                      ->orWhere('KinhNghiem', 'LIKE', '%+%');
                });
            }

            if ($request->filled('max_experience')) {
                $maxExp = $request->max_experience;
                $query->where(function($q) use ($maxExp) {
                    for ($i = 0; $i <= $maxExp; $i++) {
                        $q->orWhere('KinhNghiem', 'LIKE', "%{$i} năm%")
                          ->orWhere('KinhNghiem', 'LIKE', "%{$i}năm%");
                    }
                });
            }

            // 4. Lọc theo giới tính
            if ($request->filled('gender')) {
                $query->where('GioiTinh', $request->gender);
            }

            // Lọc theo trình độ học vấn
            if ($request->filled('education_level')) {
                $query->where('BangCap', 'LIKE', "%{$request->education_level}%");
            }

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
