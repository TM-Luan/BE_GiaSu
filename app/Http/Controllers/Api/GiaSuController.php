<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GiaSuRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\GiaSuResource;
use App\Models\GiaSu;

class GiaSuController extends Controller
{
    public function index()
    {
        $tutors = GiaSu::with('taiKhoan')
            ->where('TrangThai', 1)
            ->get();

        return GiaSuResource::collection($tutors);
    }


    // public function search(SearchRequest $request)
    // {
    //     try {
    //         $query = GiaSu::with(['taiKhoan']);

    //         // Tìm kiếm theo tên
    //         if ($request->filled('keyword')) {
    //             $keyword = $request->keyword;
    //             $query->where('HoTen', 'LIKE', "%{$keyword}%");
    //         }

    //         // 1. Lọc theo chuyên môn (môn học)
    //         // Giả định: Gia sư có quan hệ với LopHocYeuCau qua GiaSuID
    //         if ($request->filled('subject_id')) {
    //             $subjectId = $request->subject_id;
    //             $query->whereHas('lopHocYeuCau', function($q) use ($subjectId) {
    //                 $q->where('MonID', $subjectId)
    //                   ->whereIn('TrangThai', ['DangHoc', 'HoanThanh']);
    //             });
    //         }

    //         // 2. Lọc theo đánh giá trung bình
    //         if ($request->filled('min_rating')) {
    //             $minRating = (float) $request->min_rating;

    //             // Sử dụng join với subquery để tính AVG rating
    //             $query->whereExists(function($subQuery) use ($minRating) {
    //                 $subQuery->selectRaw('AVG(danhgia.DiemSo) as avg_rating')
    //                         ->from('LopHocYeuCau')
    //                         ->leftJoin('danhgia', 'LopHocYeuCau.LopYeuCauID', '=', 'danhgia.LopYeuCauID')
    //                         ->whereColumn('LopHocYeuCau.GiaSuID', 'giasu.GiaSuID')
    //                         ->groupBy('LopHocYeuCau.GiaSuID')
    //                         ->havingRaw('AVG(danhgia.DiemSo) >= ?', [$minRating]);
    //             });
    //         }

    //         if ($request->filled('max_rating')) {
    //             $maxRating = (float) $request->max_rating;

    //             $query->whereExists(function($subQuery) use ($maxRating) {
    //                 $subQuery->selectRaw('AVG(danhgia.DiemSo) as avg_rating')
    //                         ->from('LopHocYeuCau')
    //                         ->leftJoin('danhgia', 'LopHocYeuCau.LopYeuCauID', '=', 'danhgia.LopYeuCauID')
    //                         ->whereColumn('LopHocYeuCau.GiaSuID', 'giasu.GiaSuID')
    //                         ->groupBy('LopHocYeuCau.GiaSuID')
    //                         ->havingRaw('AVG(danhgia.DiemSo) <= ?', [$maxRating]);
    //             });
    //         }

    //         // 3. Lọc theo kinh nghiệm
    //         if ($request->filled('experience_level')) {
    //             $experienceLevel = $request->experience_level;

    //             // Kiểm tra nếu là số năm (1, 2, 3, 5+)
    //             if (in_array($experienceLevel, ['1', '2', '3', '5+'])) {
    //                 $query->where(function($q) use ($experienceLevel) {
    //                     if ($experienceLevel === '5+') {
    //                         $q->where('KinhNghiem', 'LIKE', '%5%')
    //                           ->orWhere('KinhNghiem', 'LIKE', '%Trên 5%')
    //                           ->orWhere('KinhNghiem', 'LIKE', '%5+%')
    //                           ->orWhere('KinhNghiem', 'LIKE', '%6%')
    //                           ->orWhere('KinhNghiem', 'LIKE', '%7%')
    //                           ->orWhere('KinhNghiem', 'LIKE', '%8%')
    //                           ->orWhere('KinhNghiem', 'LIKE', '%9%')
    //                           ->orWhere('KinhNghiem', 'LIKE', '%10%');
    //                     } else {
    //                         $q->where('KinhNghiem', 'LIKE', "%{$experienceLevel} năm%")
    //                           ->orWhere('KinhNghiem', 'LIKE', "%{$experienceLevel}năm%");
    //                     }
    //                 });
    //             }
    //         }

    //         // Lọc theo số năm kinh nghiệm (min/max)
    //         if ($request->filled('min_experience')) {
    //             $minExp = $request->min_experience;
    //             $query->where(function($q) use ($minExp) {
    //                 for ($i = $minExp; $i <= 10; $i++) {
    //                     $q->orWhere('KinhNghiem', 'LIKE', "%{$i} năm%")
    //                       ->orWhere('KinhNghiem', 'LIKE', "%{$i}năm%");
    //                 }
    //                 $q->orWhere('KinhNghiem', 'LIKE', '%Trên%')
    //                   ->orWhere('KinhNghiem', 'LIKE', '%+%');
    //             });
    //         }

    //         if ($request->filled('max_experience')) {
    //             $maxExp = $request->max_experience;
    //             $query->where(function($q) use ($maxExp) {
    //                 for ($i = 0; $i <= $maxExp; $i++) {
    //                     $q->orWhere('KinhNghiem', 'LIKE', "%{$i} năm%")
    //                       ->orWhere('KinhNghiem', 'LIKE', "%{$i}năm%");
    //                 }
    //             });
    //         }

    //         // 4. Lọc theo giới tính
    //         if ($request->filled('gender')) {
    //             $query->where('GioiTinh', $request->gender);
    //         }

    //         // Lọc theo trình độ học vấn và chuyên ngành
    //         if ($request->filled('education_level')) {
    //             $query->where(function($q) use ($request) {
    //                 $q->where('BangCap', 'LIKE', "%{$request->education_level}%")
    //                   ->orWhere('TruongDaoTao', 'LIKE', "%{$request->education_level}%");
    //             });
    //         }

    //         if ($request->filled('chuyen_nganh')) {
    //             $query->where('ChuyenNganh', 'LIKE', "%{$request->chuyen_nganh}%");
    //         }

    //         // Lọc theo thành tích
    //         if ($request->filled('thanh_tich')) {
    //             $query->where('ThanhTich', 'LIKE', "%{$request->thanh_tich}%");
    //         }

    //         // Sắp xếp
    //         $sortBy = $request->get('sort_by', 'created_at');
    //         $sortOrder = $request->get('sort_order', 'desc');

    //         switch ($sortBy) {
    //             case 'name':
    //                 $query->orderBy('HoTen', $sortOrder);
    //                 break;
    //             case 'experience':
    //                 $query->orderBy('KinhNghiem', $sortOrder);
    //                 break;
    //             case 'education':
    //                 $query->orderBy('BangCap', $sortOrder)
    //                       ->orderBy('TruongDaoTao', $sortOrder);
    //                 break;
    //             case 'achievement':
    //                 $query->orderBy('ThanhTich', $sortOrder);
    //                 break;
    //             default:
    //                 $query->orderBy('GiaSuID', $sortOrder);
    //         }

    //         // Phân trang
    //         $perPage = $request->get('per_page', 20);
    //         $tutors = $query->paginate($perPage);

    //         return response()->json([
    //             'success' => true,
    //             'data' => GiaSuResource::collection($tutors->items()),
    //             'pagination' => [
    //                 'current_page' => $tutors->currentPage(),
    //                 'last_page' => $tutors->lastPage(),
    //                 'per_page' => $tutors->perPage(),
    //                 'total' => $tutors->total(),
    //                 'from' => $tutors->firstItem(),
    //                 'to' => $tutors->lastItem(),
    //             ]
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Lỗi tìm kiếm: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function show($id)
    {
        $tutor = GiaSu::with(['taiKhoan', 'monHoc'])
            ->where('TrangThai', 1) // Chỉ hiển thị gia sư đã duyệt
            ->findOrFail($id);
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

    public function search(SearchRequest $request)
    {
        try {
            $query = GiaSu::with(['taiKhoan', 'monHoc']); // Eager load monHoc

            // 1. Tìm kiếm từ khóa chung (Tên, Trường, Chuyên ngành)
            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('HoTen', 'LIKE', "%{$keyword}%")
                        ->orWhere('TruongDaoTao', 'LIKE', "%{$keyword}%")
                        ->orWhere('ChuyenNganh', 'LIKE', "%{$keyword}%");
                });
            }

            // 2. Lọc theo Khu vực (QUAN TRỌNG: Đã thêm phần này vì Flutter gửi lên 'location')
            if ($request->filled('location')) {
                $query->where('DiaChi', 'LIKE', "%{$request->location}%");
            }

            // 3. Lọc theo Môn học (SỬA LỖI LOGIC)
            // Tìm theo chuyên môn gia sư đăng ký (MonID) thay vì lịch sử dạy
            if ($request->filled('subject_id')) {
                $query->where('MonID', $request->subject_id);
            }

            // 4. Lọc theo Giới tính
            if ($request->filled('gender')) {
                // Đảm bảo mapping đúng: Flutter gửi 'Nam'/'Nu' -> Database lưu 'Nam'/'Nu' (hoặc 1/0 tùy DB của bạn)
                $query->where('GioiTinh', $request->gender);
            }

            // 5. Lọc theo Trình độ (Bằng cấp)
            if ($request->filled('education_level')) {
                // Tìm tương đối
                $query->where('BangCap', 'LIKE', "%{$request->education_level}%");
            }

            // 6. Lọc theo Kinh nghiệm
            if ($request->filled('experience_level')) {
                $exp = $request->experience_level;
                // Nếu Flutter gửi số (1, 2, 3) hoặc chuỗi "Trên 5 năm"
                // Tìm kiếm linh hoạt hơn
                $query->where('KinhNghiem', 'LIKE', "%{$exp}%");
            }

            // 7. Lọc theo Đánh giá (Rating)
            // Logic này giữ nguyên nhưng tối ưu query
            if ($request->filled('min_rating') || $request->filled('max_rating')) {
                $minRating = $request->get('min_rating', 0);
                $maxRating = $request->get('max_rating', 5);

                $query->whereHas('danhGia', function ($q) use ($minRating, $maxRating) {
                    // Lưu ý: Cần đảm bảo relationship 'danhGia' trong Model GiaSu hoạt động đúng
                    $q->selectRaw('AVG(DiemSo) as aggregate')
                        ->groupBy('GiaSuID') // Group theo gia sư trong bảng đánh giá (nếu có cột này) hoặc bảng trung gian
                        ->havingRaw('AVG(DiemSo) >= ? AND AVG(DiemSo) <= ?', [$minRating, $maxRating]);
                });
            }

            // Sắp xếp
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            switch ($sortBy) {
                case 'name':
                    $query->orderBy('HoTen', $sortOrder);
                    break;
                case 'experience':
                    // Lưu ý: KinhNghiem là chuỗi nên sort có thể không chính xác tuyệt đối
                    $query->orderBy('KinhNghiem', $sortOrder);
                    break;
                default:
                    $query->orderBy('GiaSuID', 'desc');
            }

            // Chỉ lấy gia sư đã được admin duyệt
            $query->where('TrangThai', 1);

            $tutors = $query->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => GiaSuResource::collection($tutors->items()),
                'pagination' => [
                    'current_page' => $tutors->currentPage(),
                    'last_page' => $tutors->lastPage(),
                    'total' => $tutors->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tìm kiếm: ' . $e->getMessage()
            ], 500);
        }
    }
}