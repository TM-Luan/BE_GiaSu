<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LopHocYeuCau;
use App\Models\NguoiHoc;
use App\Models\GiaSu;
use App\Models\MonHoc;
use App\Models\KhoiLop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LopHocController extends Controller
{
    /**
     * Hiển thị danh sách lớp học
     * GET /api/admin/lophoc
     */
    public function index(Request $request)
    {
        $query = LopHocYeuCau::with([
            'nguoiHoc.taiKhoan',
            'giaSu.taiKhoan',
            'monHoc',
            'khoiLop',
            'doiTuong',
            'thoiGianDay'
        ])->orderBy('NgayTao', 'desc');

        // Lọc theo trạng thái
        if ($request->filled('trangthai')) {
            $query->where('TrangThai', $request->trangthai);
        }

        // Lọc theo hình thức
        if ($request->filled('hinhthuc')) {
            $query->where('HinhThuc', $request->hinhthuc);
        }

        // Lọc theo môn học
        if ($request->filled('mon_id')) {
            $query->where('MonID', $request->mon_id);
        }

        // Lọc theo khối lớp
        if ($request->filled('khoi_id')) {
            $query->where('KhoiLopID', $request->khoi_id);
        }

        // Tìm kiếm
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('MoTa', 'like', "%$search%")
                  ->orWhereHas('nguoiHoc', function($q) use ($search) {
                      $q->where('HoTen', 'like', "%$search%");
                  })
                  ->orWhereHas('giaSu', function($q) use ($search) {
                      $q->where('HoTen', 'like', "%$search%");
                  })
                  ->orWhereHas('monHoc', function($q) use ($search) {
                      $q->where('TenMon', 'like', "%$search%");
                  });
            });
        }

        $lopHocList = $query->paginate(15)->withQueryString();

        // Debug log
        Log::info('LopHoc Filter', [
            'search' => $request->input('search'),
            'trangthai' => $request->input('trangthai'),
            'hinhthuc' => $request->input('hinhthuc'),
            'total_results' => $lopHocList->total()
        ]);

        // Kiểm tra xem request từ web hay API
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $lopHocList,
            ]);
        }

        // Trả về view cho web admin
        return view('admin.lophoc.index', ['lophoc' => $lopHocList]);
    }    /**
     * Xem chi tiết lớp học
     * GET /api/admin/lophoc/{id}
     */
    public function show(Request $request, $id)
    {
        $lopHoc = LopHocYeuCau::with([
            'nguoiHoc.taiKhoan',
            'giaSu.taiKhoan',
            'monHoc',
            'khoiLop',
            'doiTuong',
            'thoiGianDay',
            'lichHocs',
            'yeuCauNhanLops',
            'danhGias'
        ])->findOrFail($id);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $lopHoc,
            ]);
        }

        return view('admin.lophoc.show', ['lophoc' => $lopHoc]);
    }

    /**
     * Hiển thị form chỉnh sửa lớp học
     */
    public function edit($id)
    {
        $lopHoc = LopHocYeuCau::with([
            'nguoiHoc.taiKhoan',
            'giaSu.taiKhoan',
            'monHoc',
            'khoiLop'
        ])->findOrFail($id);

        return view('admin.lophoc.edit', ['lophoc' => $lopHoc]);
    }

    /**
     * Cập nhật trạng thái lớp học
     * PUT /api/admin/lophoc/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'TrangThai' => 'required|in:DangMo,DangDay,HoanThanh,DaHuy',
            'GhiChu' => 'nullable|string|max:500',
        ]);

        try {
            $lopHoc = LopHocYeuCau::findOrFail($id);
            
            $lopHoc->update([
                'TrangThai' => $request->TrangThai,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái lớp học thành công!',
                'data' => $lopHoc->load(['nguoiHoc', 'giaSu', 'monHoc']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cập nhật thông tin lớp học
     * PUT /api/admin/lophoc/{id}
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'HocPhi' => 'nullable|numeric|min:0',
            'ThoiLuong' => 'nullable|integer|min:1',
            'SoLuong' => 'nullable|integer|min:1',
            'MoTa' => 'nullable|string|max:1000',
            'HinhThuc' => 'nullable|in:Online,Offline,TrucTiep',
            'TrangThai' => 'nullable|in:DangMo,DangDay,HoanThanh,DaHuy',
        ]);

        try {
            $lopHoc = LopHocYeuCau::findOrFail($id);
            
            $lopHoc->update($request->only([
                'HocPhi', 'ThoiLuong', 'SoLuong', 'MoTa', 'HinhThuc', 'TrangThai'
            ]));

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật lớp học thành công!',
                    'data' => $lopHoc->load(['nguoiHoc', 'giaSu', 'monHoc', 'khoiLop']),
                ]);
            }

            return redirect()->route('admin.lophoc.show', $id)
                ->with('success', 'Cập nhật lớp học thành công!');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi khi cập nhật: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Lỗi khi cập nhật: ' . $e->getMessage());
        }
    }

    /**
     * Xóa lớp học
     * DELETE /api/admin/lophoc/{id}
     */
  public function destroy(Request $request, $id)
{
    try {
        $lopHoc = LopHocYeuCau::findOrFail($id);

        // Không cần kiểm tra lichHoc nữa vì DB sẽ tự động cascade
        $lopHoc->delete();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa lớp học thành công (bao gồm các dữ liệu liên quan)!',
            ]);
        }

        return redirect()->route('admin.lophoc.index')
            ->with('success', 'Xóa lớp học và toàn bộ dữ liệu liên quan thành công!');
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
     * Thống kê lớp học
     * GET /api/admin/lophoc/statistics
     */
    public function statistics()
    {
        $total = LopHocYeuCau::count();
        
        // Thống kê theo trạng thái
        $byStatus = LopHocYeuCau::select('TrangThai', DB::raw('count(*) as count'))
                    ->groupBy('TrangThai')
                    ->get()
                    ->pluck('count', 'TrangThai');

        // Thống kê theo hình thức
        $byForm = LopHocYeuCau::select('HinhThuc', DB::raw('count(*) as count'))
                    ->groupBy('HinhThuc')
                    ->get()
                    ->pluck('count', 'HinhThuc');

        // Thống kê theo môn học (top 10)
        $bySubject = LopHocYeuCau::with('monHoc')
                    ->select('MonID', DB::raw('count(*) as count'))
                    ->groupBy('MonID')
                    ->orderByDesc('count')
                    ->limit(10)
                    ->get()
                    ->map(function($item) {
                        return [
                            'MonHoc' => $item->monHoc->TenMon ?? 'N/A',
                            'SoLop' => $item->count
                        ];
                    });

        // Thống kê học phí trung bình
        $avgFee = LopHocYeuCau::avg('HocPhi');
        $totalFee = LopHocYeuCau::where('TrangThai', 'HoanThanh')->sum('HocPhi');

        return response()->json([
            'success' => true,
            'data' => [
                'overview' => [
                    'total' => $total,
                    'avg_fee' => round($avgFee, 2),
                    'total_completed_fee' => $totalFee,
                ],
                'by_status' => $byStatus,
                'by_form' => $byForm,
                'by_subject' => $bySubject,
            ],
        ]);
    }

    /**
     * Lấy danh sách lớp của một gia sư
     * GET /api/admin/lophoc/giasu/{giaSuId}
     */
    public function getByGiaSu($giaSuId)
    {
        $lopHocList = LopHocYeuCau::with([
            'nguoiHoc.taiKhoan',
            'monHoc',
            'khoiLop'
        ])
        ->where('GiaSuID', $giaSuId)
        ->orderBy('NgayTao', 'desc')
        ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $lopHocList,
        ]);
    }

    /**
     * Lấy danh sách lớp của một người học
     * GET /api/admin/lophoc/nguoihoc/{nguoiHocId}
     */
    public function getByNguoiHoc($nguoiHocId)
    {
        $lopHocList = LopHocYeuCau::with([
            'giaSu.taiKhoan',
            'monHoc',
            'khoiLop'
        ])
        ->where('NguoiHocID', $nguoiHocId)
        ->orderBy('NgayTao', 'desc')
        ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $lopHocList,
        ]);
    }
}
