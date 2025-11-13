<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LichHoc;
use App\Models\LopHocYeuCau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LichHocController extends Controller
{
    /**
     * Hiển thị danh sách lịch học
     * GET /api/admin/lichhoc
     */
    public function index(Request $request)
    {
        $query = LichHoc::with([
            'lopHocYeuCau.nguoiHoc.taiKhoan',
            'lopHocYeuCau.giaSu.taiKhoan',
            'lopHocYeuCau.monHoc'
        ])->orderBy('NgayHoc', 'desc');

        // Lọc theo trạng thái
        if ($request->filled('trangthai')) {
            $query->where('TrangThai', $request->trangthai);
        }

        // Lọc theo lớp
        if ($request->filled('lop_id')) {
            $query->where('LopYeuCauID', $request->lop_id);
        }

        // Lọc theo ngày
        if ($request->filled('tu_ngay')) {
            $query->whereDate('NgayHoc', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $query->whereDate('NgayHoc', '<=', $request->den_ngay);
        }

        // Lọc theo tháng
        if ($request->filled('thang') && $request->filled('nam')) {
            $query->whereYear('NgayHoc', $request->nam)
                  ->whereMonth('NgayHoc', $request->thang);
        }

        $lichHocList = $query->paginate(20)->withQueryString();

        return response()->json([
            'success' => true,
            'data' => $lichHocList,
        ]);
    }

    /**
     * Xem chi tiết lịch học
     * GET /api/admin/lichhoc/{id}
     */
    public function show($id)
    {
        $lichHoc = LichHoc::with([
            'lopHocYeuCau.nguoiHoc.taiKhoan',
            'lopHocYeuCau.giaSu.taiKhoan',
            'lopHocYeuCau.monHoc',
            'lopHocYeuCau.khoiLop',
            'lichHocGoc',
            'lichHocCon'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $lichHoc,
        ]);
    }

    /**
     * Cập nhật trạng thái lịch học
     * PUT /api/admin/lichhoc/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'TrangThai' => 'required|in:ChuaBatDau,DangHoc,HoanThanh,DaHuy',
        ]);

        try {
            $lichHoc = LichHoc::findOrFail($id);
            
            $lichHoc->update([
                'TrangThai' => $request->TrangThai,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái lịch học thành công!',
                'data' => $lichHoc,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cập nhật thông tin lịch học
     * PUT /api/admin/lichhoc/{id}
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'NgayHoc' => 'nullable|date',
            'ThoiGianBatDau' => 'nullable|date_format:H:i:s',
            'ThoiGianKetThuc' => 'nullable|date_format:H:i:s',
            'DuongDan' => 'nullable|url|max:500',
            'TrangThai' => 'nullable|in:ChuaBatDau,DangHoc,HoanThanh,DaHuy',
        ]);

        try {
            $lichHoc = LichHoc::findOrFail($id);
            
            $lichHoc->update($request->only([
                'NgayHoc', 'ThoiGianBatDau', 'ThoiGianKetThuc', 'DuongDan', 'TrangThai'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật lịch học thành công!',
                'data' => $lichHoc->load('lopHocYeuCau'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xóa lịch học
     * DELETE /api/admin/lichhoc/{id}
     */
    public function destroy($id)
    {
        try {
            $lichHoc = LichHoc::findOrFail($id);
            
            // Nếu là lịch gốc và có lịch con, xóa cả lịch con
            if ($lichHoc->IsLapLai && $lichHoc->lichHocCon()->count() > 0) {
                if (!request()->has('confirm_delete_all')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Lịch này có các buổi học lặp lại. Bạn có chắc muốn xóa tất cả?',
                        'require_confirm' => true,
                    ], 400);
                }
                
                // Xóa tất cả lịch con
                $lichHoc->lichHocCon()->delete();
            }

            $lichHoc->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa lịch học thành công!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Thống kê lịch học
     * GET /api/admin/lichhoc/statistics
     */
    public function statistics(Request $request)
    {
        $query = LichHoc::query();

        // Lọc theo tháng/năm nếu có
        if ($request->filled('thang') && $request->filled('nam')) {
            $query->whereYear('NgayHoc', $request->nam)
                  ->whereMonth('NgayHoc', $request->thang);
        }

        $total = $query->count();
        
        // Thống kê theo trạng thái
        $byStatus = LichHoc::select('TrangThai', DB::raw('count(*) as count'))
                    ->groupBy('TrangThai')
                    ->get()
                    ->pluck('count', 'TrangThai');

        // Thống kê theo ngày trong tuần
        $byDayOfWeek = LichHoc::select(
                DB::raw('DAYOFWEEK(NgayHoc) as day_of_week'),
                DB::raw('count(*) as count')
            )
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get()
            ->map(function($item) {
                $days = ['', 'Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
                return [
                    'day' => $days[$item->day_of_week] ?? 'N/A',
                    'count' => $item->count
                ];
            });

        // Lịch sắp tới (7 ngày)
        $upcoming = LichHoc::with(['lopHocYeuCau.giaSu', 'lopHocYeuCau.nguoiHoc'])
                    ->whereDate('NgayHoc', '>=', Carbon::today())
                    ->whereDate('NgayHoc', '<=', Carbon::today()->addDays(7))
                    ->orderBy('NgayHoc')
                    ->limit(10)
                    ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'overview' => [
                    'total' => $total,
                ],
                'by_status' => $byStatus,
                'by_day_of_week' => $byDayOfWeek,
                'upcoming' => $upcoming,
            ],
        ]);
    }

    /**
     * Lấy lịch học theo lớp
     * GET /api/admin/lichhoc/lop/{lopId}
     */
    public function getByLop($lopId)
    {
        $lichHocList = LichHoc::where('LopYeuCauID', $lopId)
                    ->orderBy('NgayHoc', 'desc')
                    ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $lichHocList,
        ]);
    }

    /**
     * Lấy lịch học theo tháng (dạng calendar)
     * GET /api/admin/lichhoc/calendar
     */
    public function getCalendar(Request $request)
    {
        $request->validate([
            'thang' => 'required|integer|min:1|max:12',
            'nam' => 'required|integer|min:2020',
        ]);

        $lichHocList = LichHoc::with([
            'lopHocYeuCau.giaSu.taiKhoan',
            'lopHocYeuCau.nguoiHoc.taiKhoan',
            'lopHocYeuCau.monHoc'
        ])
        ->whereYear('NgayHoc', $request->nam)
        ->whereMonth('NgayHoc', $request->thang)
        ->orderBy('NgayHoc')
        ->orderBy('ThoiGianBatDau')
        ->get()
        ->groupBy(function($item) {
            return $item->NgayHoc->format('Y-m-d');
        });

        return response()->json([
            'success' => true,
            'data' => $lichHocList,
        ]);
    }
}
