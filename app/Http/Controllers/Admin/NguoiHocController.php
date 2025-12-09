<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use App\Models\NguoiHoc;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class NguoiHocController extends Controller
{
    private const NGUOIHOC_ROLE_ID = 3;

    // =============================================
    // ===== HÀM INDEX() ĐANG BỊ THIẾU CỦA BẠN =====
    // =============================================
    public function index(Request $request)
    {
        $query = TaiKhoan::with('nguoihoc', 'phanquyen')
            ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::NGUOIHOC_ROLE_ID))
            ->orderByDesc('TaiKhoanID');

        // Logic lọc (đã có)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('Email', 'like', "%$search%")
                  ->orWhere('SoDienThoai', 'like', "%$search%")
                  ->orWhereHas('nguoihoc', fn($q) => $q->where('HoTen', 'like', "%$search%"));
            });
        }
        if ($trangthai = $request->input('trangthai')) {
            if ($trangthai === '0' || $trangthai === '1') {
                $query->where('TrangThai', (int)$trangthai);
            }
        }
        
        $nguoihocList = $query->paginate(10)->withQueryString(); 

        return view('admin.nguoihoc.index', [
            'nguoihocList' => $nguoihocList
        ]);
    }
    // =============================================
    // =============================================

    /**
     * Hiển thị form tạo mới người học.
     */
    public function create()
    {
        return view('admin.nguoihoc.create');
    }

    /**
     * Lưu người học mới vào CSDL.
     */
    public function store(Request $request)
    {
        $messages = $this->getValidationMessages();
        $rules = [
            'Email' => 'required|email|max:100|unique:TaiKhoan,Email',
            'SoDienThoai' => ['nullable', 'string', 'regex:/^0\d{9}$/', 'unique:TaiKhoan,SoDienThoai'],
            'TrangThai' => 'required|in:0,1,2',
            'MatKhau' => ['required', 'confirmed', Password::min(8)],
            'HoTen' => 'required|string|max:150',
            'GioiTinh' => 'nullable|string|in:Nam,Nữ,Khác',
            'NgaySinh' => 'nullable|date|before:today',
            'DiaChi' => 'nullable|string|max:255',
            'AnhDaiDien' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
        
        $validated = $request->validate($rules, $messages);
        
        $taiKhoanData = [
            'Email' => $validated['Email'],
            'SoDienThoai' => $validated['SoDienThoai'],
            'TrangThai' => $validated['TrangThai'],
            'MatKhauHash' => Hash::make($validated['MatKhau'])
        ];
        $nguoiHocData = [
            'HoTen' => $validated['HoTen'],
            'GioiTinh' => $validated['GioiTinh'] ?? null,
            'NgaySinh' => $validated['NgaySinh'] ?? null,
            'DiaChi' => $validated['DiaChi'] ?? null,
        ];

        try {
            DB::transaction(function () use ($request, $taiKhoanData, $nguoiHocData) {
                if ($request->hasFile('AnhDaiDien')) {
                    $apiKey = env('IMAGEBB_API_KEY');
                    $response = Http::attach( 'image', file_get_contents($request->file('AnhDaiDien')), $request->file('AnhDaiDien')->getClientOriginalName())
                                ->post('https://api.imgbb.com/1/upload', ['key' => $apiKey]);
                    if ($response->successful()) {
                        $nguoiHocData['AnhDaiDien'] = $response->json()['data']['url'];
                    }
                }
                $newTaiKhoan = TaiKhoan::create($taiKhoanData);
                PhanQuyen::create([
                    'TaiKhoanID' => $newTaiKhoan->TaiKhoanID,
                    'VaiTroID' => self::NGUOIHOC_ROLE_ID
                ]);
                $nguoiHocData['TaiKhoanID'] = $newTaiKhoan->TaiKhoanID;
                NguoiHoc::create($nguoiHocData);
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi tạo mới: ' . $e->getMessage());
        }
        return redirect()->route('admin.nguoihoc.index')->with('success', 'Thêm người học thành công!');
    }


    /**
     * Hiển thị form chỉnh sửa người học.
     */
    public function edit(string $id)
    {
        $taiKhoan = TaiKhoan::with('nguoihoc')
            ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::NGUOIHOC_ROLE_ID))
            ->findOrFail($id); 
        return view('admin.nguoihoc.edit', [
            'taiKhoan' => $taiKhoan
        ]);
    }

    /**
     * Cập nhật thông tin người học trong CSDL.
     */
    public function update(Request $request, string $id)
    {
        $taiKhoan = TaiKhoan::with('nguoihoc')->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::NGUOIHOC_ROLE_ID))
            ->findOrFail($id);

        $messages = $this->getValidationMessages();
        $rules = [
            'Email' => ['required', 'email', 'max:100', Rule::unique('TaiKhoan', 'Email')->ignore($taiKhoan->TaiKhoanID, 'TaiKhoanID')],
            'SoDienThoai' => ['nullable', 'string', 'regex:/^0\d{9}$/', Rule::unique('TaiKhoan', 'SoDienThoai')->ignore($taiKhoan->TaiKhoanID, 'TaiKhoanID')],
            'TrangThai' => 'required|in:0,1,2',
            'MatKhau' => ['nullable', 'confirmed', Password::min(8)],
            'HoTen' => 'required|string|max:150',
            'GioiTinh' => 'nullable|string|in:Nam,Nữ,Khác',
            'NgaySinh' => 'nullable|date|before:today',
            'DiaChi' => 'nullable|string|max:255',
            'AnhDaiDien' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        $validated = $request->validate($rules, $messages);
        
        $taiKhoanData = [
            'Email' => $validated['Email'],
            'SoDienThoai' => $validated['SoDienThoai'],
            'TrangThai' => $validated['TrangThai'],
        ];
        $nguoiHocData = [
            'HoTen' => $validated['HoTen'],
            'GioiTinh' => $validated['GioiTinh'] ?? null,
            'NgaySinh' => $validated['NgaySinh'] ?? null,
            'DiaChi' => $validated['DiaChi'] ?? null,
        ];

        try {
            DB::transaction(function () use ($request, $taiKhoan, $taiKhoanData, $nguoiHocData, $validated) {
                if (!empty($validated['MatKhau'])) {
                    $taiKhoanData['MatKhauHash'] = Hash::make($validated['MatKhau']);
                }
                $taiKhoan->update($taiKhoanData);

                if ($request->hasFile('AnhDaiDien')) {
                    if ($taiKhoan->nguoihoc?->AnhDaiDien && !str_starts_with($taiKhoan->nguoihoc->AnhDaiDien, 'http')) {
                        Storage::disk('public')->delete($taiKhoan->nguoihoc->AnhDaiDien);
                    }
                    $apiKey = env('IMAGEBB_API_KEY');
                    $response = Http::attach( 'image', file_get_contents($request->file('AnhDaiDien')), $request->file('AnhDaiDien')->getClientOriginalName())
                                ->post('https://api.imgbb.com/1/upload', ['key' => $apiKey]);
                    if ($response->successful()) {
                        $nguoiHocData['AnhDaiDien'] = $response->json()['data']['url'];
                    }
                }
                NguoiHoc::updateOrCreate(
                    ['TaiKhoanID' => $taiKhoan->TaiKhoanID], 
                    $nguoiHocData
                );
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi cập nhật: ' . $e->getMessage());
        }
        return redirect()->route('admin.nguoihoc.index')->with('success', 'Cập nhật người học thành công!');
    }

    // =============================================
    // ===== BỔ SUNG HÀM SHOW() VÀ DESTROY() =====
    // =============================================

    /**
     * Hiển thị thông tin chi tiết của người học.
     */
    public function show(string $id)
    {
        $taiKhoan = TaiKhoan::with('nguoihoc', 'phanquyen.vaitro')
            ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::NGUOIHOC_ROLE_ID))
            ->findOrFail($id); 
            
        return view('admin.nguoihoc.show', [ 'taiKhoan' => $taiKhoan ]);
    }

    /**
     * Xóa người học khỏi CSDL.
     */
    public function destroy(string $id)
    {
        try {
            $taiKhoan = TaiKhoan::whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::NGUOIHOC_ROLE_ID))
                                ->findOrFail($id);
            $taiKhoan->delete();
            return redirect()->route('admin.nguoihoc.index')->with('success', 'Đã xóa người học thành công.');
        } catch (\Exception $e) {
            return redirect()->route('admin.nguoihoc.index')->with('error', 'Lỗi khi xóa: ' . $e->getMessage());
        }
    }
    // =============================================
    // =============================================

    /**
     * Helper function: Trả về mảng thông báo lỗi Tiếng Việt
     */
    private function getValidationMessages()
    {
        return [
            'required' => 'Thông tin này là bắt buộc, không được để trống.',
            'string' => 'Thông tin này phải là một chuỗi ký tự.',
            'boolean' => 'Giá trị này không hợp lệ.',
            'date' => 'Không đúng định dạng ngày tháng.',
            'image' => 'File tải lên phải là hình ảnh.',
            'mimes' => 'Hình ảnh phải có định dạng: :values.',
            'max' => [
                'string' => 'Không được vượt quá :max ký tự.',
                'file' => 'Dung lượng file không được vượt quá :max KB (2MB).',
            ],
            'in' => 'Giá trị được chọn không hợp lệ.',
            'Email.email' => 'Email không đúng định dạng (ví dụ: ten@gmail.com).',
            'Email.unique' => 'Email này đã có người khác sử dụng.',
            'SoDienThoai.regex' => 'Số điện thoại phải là 10 chữ số, bắt đầu bằng số 0.',
            'SoDienThoai.unique' => 'Số điện thoại này đã có người khác sử dụng.',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'MatKhau.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'NgaySinh.before' => 'Ngày sinh không thể là một ngày trong tương lai.',
        ];
    }
}