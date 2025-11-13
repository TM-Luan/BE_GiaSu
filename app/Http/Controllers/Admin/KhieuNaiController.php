<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KhieuNai;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KhieuNaiController extends Controller
{
    /**
     * Hiển thị danh sách khiếu nại (Admin)
     */
    public function index(Request $request)
    {
        $query = KhieuNai::with(['taiKhoan.giasu', 'taiKhoan.nguoihoc', 'lop.monHoc', 'giaoDich'])
                    ->orderBy('NgayTao', 'desc');

        // Lọc theo trạng thái
        if ($request->filled('trangthai')) {
            $query->where('TrangThai', $request->trangthai);
        }

        // Tìm kiếm theo nội dung hoặc email
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('NoiDung', 'like', "%$search%")
                  ->orWhereHas('taiKhoan', function($q) use ($search) {
                      $q->where('Email', 'like', "%$search%");
                  });
            });
        }

        $khieuNaiList = $query->paginate(15)->withQueryString();

        // Kiểm tra xem request từ web hay API
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $khieuNaiList,
            ]);
        }

        // Trả về view cho web admin
        return view('admin.khieunai.index', ['khieunai' => $khieuNaiList]);
    }

    /**
     * Xem chi tiết khiếu nại
     */
    public function show(Request $request, $id)
    {
        $khieuNai = KhieuNai::with([
            'taiKhoan.giasu',
            'taiKhoan.nguoihoc',
            'lop.giasu',
            'lop.nguoihoc',
            'lop.monHoc',
            'giaoDich'
        ])->findOrFail($id);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $khieuNai,
            ]);
        }

        return view('admin.khieunai.show', ['khieunai' => $khieuNai]);
    }

    /**
     * Hiển thị form chỉnh sửa khiếu nại
     */
    public function edit($id)
    {
        $khieuNai = KhieuNai::with(['taiKhoan', 'lop'])->findOrFail($id);
        return view('admin.khieunai.edit', ['khieunai' => $khieuNai]);
    }

    /**
     * Cập nhật khiếu nại
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'TrangThai' => 'required|in:TiepNhan,DangXuLy,DaGiaiQuyet,TuChoi',
            'GhiChu' => 'nullable|string|max:1000',
            'PhanHoi' => 'nullable|string|max:1000',
        ]);

        $khieuNai = KhieuNai::findOrFail($id);
        
        $khieuNai->update([
            'TrangThai' => $validated['TrangThai'],
            'GhiChu' => $validated['GhiChu'] ?? $khieuNai->GhiChu,
            'PhanHoi' => $validated['PhanHoi'] ?? $khieuNai->PhanHoi,
            'NgayXuLy' => now(),
        ]);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật khiếu nại thành công!',
                'data' => $khieuNai,
            ]);
        }

        return redirect()->route('admin.khieunai.show', $id)
            ->with('success', 'Cập nhật khiếu nại thành công!');
    }

    /**
     * Xóa khiếu nại (Admin)
     */
    public function destroy(Request $request, $id)
    {
        try {
            $khieuNai = KhieuNai::findOrFail($id);
            $khieuNai->delete();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Xóa khiếu nại thành công!',
                ]);
            }

            return redirect()->route('admin.khieunai.index')
                ->with('success', 'Xóa khiếu nại thành công!');
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
     * Thống kê khiếu nại theo trạng thái
     */
    public function statistics()
    {
        $stats = KhieuNai::select('TrangThai', DB::raw('count(*) as total'))
                    ->groupBy('TrangThai')
                    ->get()
                    ->pluck('total', 'TrangThai');

        $total = KhieuNai::count();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'by_status' => $stats,
            ],
        ]);
    }
}
