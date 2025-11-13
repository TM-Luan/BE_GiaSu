<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiaoDich;
use App\Models\LopHocYeuCau;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GiaoDichController extends Controller
{
    /**
     * Hiển thị danh sách giao dịch
     */
    public function index(Request $request)
    {
        $query = GiaoDich::with([
            'taiKhoan.giasu',
            'taiKhoan.nguoihoc',
            'lop.giasu',
            'lop.nguoihoc'
        ])->orderBy('ThoiGian', 'desc');

        // Lọc theo trạng thái
        if ($request->filled('trangthai')) {
            $query->where('TrangThai', $request->trangthai);
        }

        // Lọc theo khoảng thời gian
        if ($request->filled('tu_ngay')) {
            $query->where('ThoiGian', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $query->where('ThoiGian', '<=', $request->den_ngay);
        }

        // Lọc theo khoảng số tiền
        if ($request->filled('min_amount')) {
            $query->where('SoTien', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('SoTien', '<=', $request->max_amount);
        }

        // Tìm kiếm theo email hoặc ghi chú
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('GhiChu', 'like', "%$search%")
                  ->orWhere('MaGiaoDich', 'like', "%$search%")
                  ->orWhereHas('taiKhoan', function($q) use ($search) {
                      $q->where('Email', 'like', "%$search%");
                  });
            });
        }

        // Lọc theo loại giao dịch
        if ($request->filled('loai')) {
            $query->where('LoaiGiaoDich', $request->loai);
        }

        $giaoDichList = $query->paginate(15)->withQueryString();

        // Kiểm tra xem request từ web hay API
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $giaoDichList,
            ]);
        }

        // Trả về view cho web admin
        return view('admin.giaodich.index', ['giaodich' => $giaoDichList]);
    }

    /**
     * Xem chi tiết giao dịch
     */
    public function show(Request $request, $id)
    {
        $giaoDich = GiaoDich::with([
            'taiKhoan.giasu',
            'taiKhoan.nguoihoc',
            'lop.giasu',
            'lop.nguoihoc',
            'lop.monhoc',
            'lop.khoilop'
        ])->findOrFail($id);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $giaoDich,
            ]);
        }

        return view('admin.giaodich.show', ['giaodich' => $giaoDich]);
    }

    /**
     * Hiển thị form chỉnh sửa giao dịch
     */
    public function edit($id)
    {
        $giaoDich = GiaoDich::with(['taiKhoan', 'lop'])->findOrFail($id);
        return view('admin.giaodich.edit', ['giaodich' => $giaoDich]);
    }

    /**
     * Cập nhật trạng thái giao dịch
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'TrangThai' => 'required|in:ChoXuLy,ThanhCong,ThatBai,HoanTien',
            'GhiChu' => 'nullable|string|max:500',
        ]);

        $giaoDich = GiaoDich::findOrFail($id);
        
        $giaoDich->update([
            'TrangThai' => $validated['TrangThai'],
            'GhiChu' => $validated['GhiChu'] ?? $giaoDich->GhiChu,
        ]);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái giao dịch thành công!',
                'data' => $giaoDich,
            ]);
        }

        return redirect()->route('admin.giaodich.show', $id)
            ->with('success', 'Cập nhật giao dịch thành công!');
    }

    /**
     * Xóa giao dịch
     */
    public function destroy(Request $request, $id)
    {
        try {
            $giaoDich = GiaoDich::findOrFail($id);
            $giaoDich->delete();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Xóa giao dịch thành công!',
                ]);
            }

            return redirect()->route('admin.giaodich.index')
                ->with('success', 'Xóa giao dịch thành công!');
        } catch (\Exception $e) {
            $message = 'Lỗi khi xóa: ' . $e->getMessage();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 500);
            }

            return redirect()->back()->with('error', $message);
        }
    }

    /**
     * Thống kê giao dịch
     */
    public function statistics(Request $request)
    {
        // Thống kê tổng quan
        $total = GiaoDich::count();
        $totalAmount = GiaoDich::sum('SoTien');
        $successAmount = GiaoDich::where('TrangThai', 'ThanhCong')->sum('SoTien');
        
        // Thống kê theo trạng thái
        $byStatus = GiaoDich::select('TrangThai', DB::raw('count(*) as count'), DB::raw('sum(SoTien) as total'))
                    ->groupBy('TrangThai')
                    ->get();

        // Thống kê theo tháng (12 tháng gần nhất)
        $byMonth = GiaoDich::select(
                DB::raw('YEAR(ThoiGian) as year'),
                DB::raw('MONTH(ThoiGian) as month'),
                DB::raw('count(*) as count'),
                DB::raw('sum(SoTien) as total')
            )
            ->where('ThoiGian', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Thống kê theo tuần (6 tuần gần nhất)
        $byWeek = GiaoDich::select(
                DB::raw('YEAR(ThoiGian) as year'),
                DB::raw('WEEK(ThoiGian, 1) as week'),
                DB::raw('count(*) as count'),
                DB::raw('sum(SoTien) as total')
            )
            ->where('ThoiGian', '>=', Carbon::now()->subWeeks(6))
            ->groupBy('year', 'week')
            ->orderBy('year', 'asc')
            ->orderBy('week', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'overview' => [
                    'total_transactions' => $total,
                    'total_amount' => $totalAmount,
                    'success_amount' => $successAmount,
                ],
                'by_status' => $byStatus,
                'by_month' => $byMonth,
                'by_week' => $byWeek,
            ],
        ]);
    }

    /**
     * Xuất báo cáo giao dịch (CSV hoặc JSON)
     */
    public function export(Request $request)
    {
        $query = GiaoDich::with(['taiKhoan', 'lop'])
                    ->orderBy('ThoiGian', 'desc');

        // Áp dụng các bộ lọc giống như index()
        if ($request->filled('trangthai')) {
            $query->where('TrangThai', $request->trangthai);
        }
        if ($request->filled('tu_ngay')) {
            $query->where('ThoiGian', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $query->where('ThoiGian', '<=', $request->den_ngay);
        }

        $giaoDichs = $query->get();

        // Format dữ liệu để export
        $exportData = $giaoDichs->map(function($gd) {
            return [
                'GiaoDichID' => $gd->GiaoDichID,
                'Email' => $gd->taiKhoan->Email ?? 'N/A',
                'SoTien' => $gd->SoTien,
                'ThoiGian' => $gd->ThoiGian,
                'TrangThai' => $gd->TrangThai,
                'GhiChu' => $gd->GhiChu ?? '',
                'LopYeuCauID' => $gd->LopYeuCauID ?? 'N/A',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $exportData,
            'total' => $exportData->count(),
        ]);
    }
}
