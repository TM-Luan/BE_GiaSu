<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\MonHoc;
use App\Models\KhoiLop;
use App\Models\DoiTuong;
use App\Http\Controllers\Controller;

class DropdownDataController extends Controller
{
    // Existing methods
    public function getMonHocList()
    {
        $data = MonHoc::query()->select('MonID', 'TenMon')->get();
        return response()->json(['data' => $data]);
    }

    public function getKhoiLopList()
    {
        $data = KhoiLop::query()->select('KhoiLopID', 'BacHoc')->get();
        return response()->json(['data' => $data]);
    }

    public function getDoiTuongList()
    {
        $data = DoiTuong::query()->select('DoiTuongID', 'TenDoiTuong')->get();
        return response()->json(['data' => $data]);
    }

    // SỬA: Xóa hàm getThoiGianDayList()
    /*
    public function getThoiGianDayList()
    {
        $data = ThoiGianDay::query()->select('ThoiGianDayID', 'SoBuoi', 'BuoiHoc')->get();
        return response()->json(['data' => $data]);
    }
    */

    /**
     * Lấy tất cả dữ liệu dropdown cho form filter
     * API: GET /api/filter-options
     */
    public function getFilterOptions()
    {
        try {
            $data = [
                'subjects' => MonHoc::select('MonID', 'TenMon')->get(),
                'grades' => KhoiLop::select('KhoiLopID', 'BacHoc')->get(),
                'targets' => DoiTuong::select('DoiTuongID', 'TenDoiTuong')->get(),
                // SỬA: Xóa 'times'
                // 'times' => ThoiGianDay::select('ThoiGianDayID', 'SoBuoi', 'BuoiHoc')->get(),
                
                // ... (Các phần còn lại giữ nguyên)
                'education_levels' => [
                    ['value' => 'Sinh viên', 'label' => 'Sinh viên'],
                    ['value' => 'Cử nhân', 'label' => 'Cử nhân'],
                    ['value' => 'Thạc sĩ', 'label' => 'Thạc sĩ'],
                    ['value' => 'Tiến sĩ', 'label' => 'Tiến sĩ'],
                    ['value' => 'Giáo viên', 'label' => 'Giáo viên'],
                ],
                'experience_levels' => [
                    ['value' => 'beginner', 'label' => 'Mới bắt đầu (0-1 năm)'],
                    ['value' => 'intermediate', 'label' => 'Trung bình (1-3 năm)'],
                    ['value' => 'experienced', 'label' => 'Có kinh nghiệm (3-5 năm)'],
                    ['value' => 'expert', 'label' => 'Chuyên gia (5+ năm)'],
                ],
                'genders' => [
                    ['value' => 'Nam', 'label' => 'Nam'],
                    ['value' => 'Nữ', 'label' => 'Nữ'],
                ],
                'forms' => [
                    ['value' => 'Online', 'label' => 'Online'],
                    ['value' => 'Offline', 'label' => 'Tại nhà'],
                    ['value' => 'Cả hai', 'label' => 'Cả hai'],
                ],
              'class_statuses' => [
                    ['value' => 'TimGiaSu', 'label' => 'Đang tìm gia sư'],
                    ['value' => 'DangHoc', 'label' => 'Đang học'],
                    ['value' => 'HoanThanh', 'label' => 'Hoàn thành'],
                    ['value' => 'Huy', 'label' => 'Đã hủy'],
                ],
                'sort_options' => [
                    // For tutors
                    'tutor_sort' => [
                        ['value' => 'name', 'label' => 'Tên A-Z'],
                        ['value' => 'experience', 'label' => 'Kinh nghiệm'],
                        ['value' => 'created_at', 'label' => 'Mới nhất'],
                    ],
                    // For classes
                    'class_sort' => [
                        ['value' => 'price', 'label' => 'Học phí'],
                        ['value' => 'duration', 'label' => 'Thời lượng'],
                        ['value' => 'students', 'label' => 'Số học viên'],
                        ['value' => 'created_at', 'label' => 'Mới nhất'],
                    ]
                ],
                'price_ranges' => [
                    ['min' => 0, 'max' => 100000, 'label' => 'Dưới 100k'],
                    ['min' => 100000, 'max' => 200000, 'label' => '100k - 200k'],
                    ['min' => 200000, 'max' => 500000, 'label' => '200k - 500k'],
                    ['min' => 500000, 'max' => 1000000, 'label' => '500k - 1M'],
                    ['min' => 1000000, 'max' => null, 'label' => 'Trên 1M'],
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi lấy dữ liệu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thống kê tổng quan
     * API: GET /api/search-stats
     */
    public function getSearchStats()
    {
        try {
            // SỬA: Xóa 'ChoDuyet'
            $stats = [
                'total_tutors' => \App\Models\GiaSu::count(),
                'total_classes' => \App\Models\LopHocYeuCau::where('TrangThai', 'TimGiaSu')->count(),
                'total_subjects' => MonHoc::count(),
                'avg_price' => \App\Models\LopHocYeuCau::avg('HocPhi'),
                'price_range' => [
                    'min' => \App\Models\LopHocYeuCau::min('HocPhi'),
                    'max' => \App\Models\LopHocYeuCau::max('HocPhi'),
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi lấy thống kê: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tìm kiếm gợi ý (autocomplete)
     * API: GET /api/search-suggestions
     */
    public function getSearchSuggestions(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $type = $request->get('type', 'all'); // 'tutors', 'classes', 'all'

            $suggestions = [];

            if ($type === 'tutors' || $type === 'all') {
                $tutorNames = \App\Models\GiaSu::where('HoTen', 'LIKE', "%{$query}%")
                    ->where('TrangThai', 1) // Chỉ hiển thị gia sư đã duyệt
                    ->limit(5)
                    ->pluck('HoTen')
                    ->toArray();
                
                $suggestions['tutors'] = array_map(function($name) {
                    return ['type' => 'tutor', 'text' => $name];
                }, $tutorNames);
            }

            if ($type === 'classes' || $type === 'all') {
                $classDescriptions = \App\Models\LopHocYeuCau::where('MoTa', 'LIKE', "%{$query}%")
                    ->limit(5)
                    ->pluck('MoTa')
                    ->toArray();
                
                $suggestions['classes'] = array_map(function($desc) {
                    return ['type' => 'class', 'text' => substr($desc, 0, 50) . '...'];
                }, $classDescriptions);
            }

            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi lấy gợi ý: ' . $e->getMessage()
            ], 500);
        }
    }
}