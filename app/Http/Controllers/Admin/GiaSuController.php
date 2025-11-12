<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use App\Models\GiaSu;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
// Dùng cho regex validation
use Illuminate\Validation\Rules\Password;

class GiaSuController extends Controller
{
    private const GIASU_ROLE_ID = 2;

    // =============================================
    // ===== HÀM INDEX() ĐANG BỊ THIẾU CỦA BẠN =====
    // =============================================
    public function index(Request $request)
    {
        $query = TaiKhoan::with('giasu', 'phanquyen')
            ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::GIASU_ROLE_ID))
            ->orderByDesc('TaiKhoanID');

        // Logic lọc (đã có trong file cũ)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('Email', 'like', "%$search%")
                  ->orWhere('SoDienThoai', 'like', "%$search%")
                  ->orWhereHas('giasu', fn($q) => $q->where('HoTen', 'like', "%$search%"));
            });
        }
        if ($trangthai = $request->input('trangthai')) {
            if ($trangthai === '0' || $trangthai === '1') {
                $query->where('TrangThai', (int)$trangthai);
            }
        }
        
        $giasuList = $query->paginate(10)->withQueryString(); 
        
        return view('admin.giasu.index', [ 
            'giasuList' => $giasuList 
        ]);
    }
    // =============================================
    // =============================================


    /**
     * Hiển thị form tạo mới gia sư.
     */
    public function create()
    {
        return view('admin.giasu.create');
    }

    /**
     * Lưu gia sư mới vào CSDL.
     */
    public function store(Request $request)
    {
        // --- BẮT LỖI (VIỆT HÓA) ---
        $messages = $this->getValidationMessages();

        $rules = [
            // Bảng TaiKhoan
            'Email' => 'required|email|max:100|unique:TaiKhoan,Email',
            // Rule::dimensions (nếu cần)
            'SoDienThoai' => [
                'nullable', 
                'string', 
                'regex:/^0\d{9}$/', // Bắt lỗi SĐT VN (10 số, bắt đầu bằng 0)
                'unique:TaiKhoan,SoDienThoai'
            ],
            'TrangThai' => 'required|boolean',
            'MatKhau' => ['required', 'confirmed', Password::min(8)], // Bắt buộc khi tạo

            // Bảng GiaSu
            'HoTen' => 'required|string|max:150',
            'GioiTinh' => 'nullable|string|in:Nam,Nữ,Khác',
            'NgaySinh' => 'nullable|date|before:today', // Không được sinh ở tương lai
            'DiaChi' => 'nullable|string|max:255',
            'BangCap' => 'nullable|string|max:255',
            'TruongDaoTao' => 'nullable|string|max:255',
            'ChuyenNganh' => 'nullable|string|max:255',
            'KinhNghiem' => 'nullable|string|max:255',
            'ThanhTich' => 'nullable|string',
            
            // 4 Trường ảnh
            'AnhDaiDien' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'AnhCCCD_MatTruoc' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'AnhCCCD_MatSau' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'AnhBangCap' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
        
        $validated = $request->validate($rules, $messages);
        // --- KẾT THÚC BẮT LỖI ---
        
        // ... (Code tách dữ liệu và logic store của bạn) ...
        $taiKhoanData = [
            'Email' => $validated['Email'],
            'SoDienThoai' => $validated['SoDienThoai'],
            'TrangThai' => $validated['TrangThai'],
            'MatKhauHash' => Hash::make($validated['MatKhau']) 
        ];

        $giaSuData = [
            'HoTen' => $validated['HoTen'], // Bắt buộc, nên an toàn
            'GioiTinh' => $validated['GioiTinh'] ?? null,
            'NgaySinh' => $validated['NgaySinh'] ?? null,
            'DiaChi' => $validated['DiaChi'] ?? null,
            'BangCap' => $validated['BangCap'] ?? null, // <-- SỬA LỖI TẠI ĐÂY
            'TruongDaoTao' => $validated['TruongDaoTao'] ?? null,
            'ChuyenNganh' => $validated['ChuyenNganh'] ?? null,
            'KinhNghiem' => $validated['KinhNghiem'] ?? null,
            'ThanhTich' => $validated['ThanhTich'] ?? null,
        ];

        try {
            DB::transaction(function () use ($request, $taiKhoanData, $giaSuData) {
                
                $apiKey = env('IMAGEBB_API_KEY');
                $uploadImage = function($fileKey) use ($request, $apiKey) {
                    if ($request->hasFile($fileKey)) {
                        $response = Http::attach( 'image', file_get_contents($request->file($fileKey)), $request->file($fileKey)->getClientOriginalName())
                                    ->post('https://api.imgbb.com/1/upload', ['key' => $apiKey]);
                        if ($response->successful()) return $response->json()['data']['url'];
                    }
                    return null;
                };

                $giaSuData['AnhDaiDien'] = $uploadImage('AnhDaiDien');
                $giaSuData['AnhCCCD_MatTruoc'] = $uploadImage('AnhCCCD_MatTruoc');
                $giaSuData['AnhCCCD_MatSau'] = $uploadImage('AnhCCCD_MatSau');
                $giaSuData['AnhBangCap'] = $uploadImage('AnhBangCap');

                $newTaiKhoan = TaiKhoan::create($taiKhoanData);

                PhanQuyen::create([
                    'TaiKhoanID' => $newTaiKhoan->TaiKhoanID,
                    'VaiTroID' => self::GIASU_ROLE_ID 
                ]);

                $giaSuData['TaiKhoanID'] = $newTaiKhoan->TaiKhoanID;
                GiaSu::create($giaSuData);
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi tạo mới: ' . $e->getMessage());
        }

        return redirect()->route('admin.giasu.index')->with('success', 'Thêm gia sư thành công!');
    }


    public function edit(string $id)
    {
        $taiKhoan = TaiKhoan::with('giasu')
            ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::GIASU_ROLE_ID))
            ->findOrFail($id); 
        return view('admin.giasu.edit', [ 'taiKhoan' => $taiKhoan ]);
    }

    /**
     * Cập nhật thông tin gia sư trong CSDL.
     */
    public function update(Request $request, string $id)
    {
        $taiKhoan = TaiKhoan::with('giasu')->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::GIASU_ROLE_ID))
            ->findOrFail($id);

        // --- BẮT LỖI (VIỆT HÓA) ---
        $messages = $this->getValidationMessages();

        $rules = [
            // Bảng TaiKhoan
            'Email' => [
                'required', 'email', 'max:100',
                Rule::unique('TaiKhoan', 'Email')->ignore($taiKhoan->TaiKhoanID, 'TaiKhoanID')
            ],
            'SoDienThoai' => [
                'nullable', 'string', 
                'regex:/^0\d{9}$/', // Bắt lỗi SĐT VN
                Rule::unique('TaiKhoan', 'SoDienThoai')->ignore($taiKhoan->TaiKhoanID, 'TaiKhoanID')
            ],
            'TrangThai' => 'required|boolean',
            'MatKhau' => ['nullable', 'confirmed', Password::min(8)], // Không bắt buộc khi update

            // Bảng GiaSu (Giống store)
            'HoTen' => 'required|string|max:150',
            'GioiTinh' => 'nullable|string|in:Nam,Nữ,Khác',
            'NgaySinh' => 'nullable|date|before:today', // Không được sinh ở tương lai
            'DiaChi' => 'nullable|string|max:255',
            'BangCap' => 'nullable|string|max:255',
            'TruongDaoTao' => 'nullable|string|max:255',
            'ChuyenNganh' => 'nullable|string|max:255',
            'KinhNghiem' => 'nullable|string|max:255',
            'ThanhTich' => 'nullable|string',
            
            // 4 Trường ảnh
            'AnhDaiDien' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'AnhCCCD_MatTruoc' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'AnhCCCD_MatSau' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'AnhBangCap' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        $validated = $request->validate($rules, $messages);
        // --- KẾT THÚC BẮT LỖI ---
        
        // ... (Code tách dữ liệu và logic update của bạn) ...
        $taiKhoanData = [
            'Email' => $validated['Email'],
            'SoDienThoai' => $validated['SoDienThoai'],
            'TrangThai' => $validated['TrangThai'],
        ];

        $giaSuData = [
            'HoTen' => $validated['HoTen'],
            'GioiTinh' => $validated['GioiTinh'],
            'NgaySinh' => $validated['NgaySinh'],
            'DiaChi' => $validated['DiaChi'],
            'BangCap' => $validated['BangCap'],
            'TruongDaoTao' => $validated['TruongDaoTao'],
            'ChuyenNganh' => $validated['ChuyenNganh'],
            'KinhNghiem' => $validated['KinhNghiem'],
            'ThanhTich' => $validated['ThanhTich'],
        ];

        try {
            DB::transaction(function () use ($request, $taiKhoan, $taiKhoanData, $giaSuData, $validated) {
                
                if (!empty($validated['MatKhau'])) {
                    $taiKhoanData['MatKhauHash'] = Hash::make($validated['MatKhau']);
                }
                
                $taiKhoan->update($taiKhoanData);

                $apiKey = env('IMAGEBB_API_KEY');
                $uploadImage = function($fileKey) use ($request, $apiKey) {
                    if ($request->hasFile($fileKey)) {
                        $response = Http::attach( 'image', file_get_contents($request->file($fileKey)), $request->file($fileKey)->getClientOriginalName())
                                    ->post('https://api.imgbb.com/1/upload', ['key' => $apiKey]);
                        if ($response->successful()) return $response->json()['data']['url'];
                    }
                    return null;
                };

                // Kiểm tra và upload 4 ảnh
                // (Chỉ gán nếu $uploadImage trả về giá trị, nếu không sẽ giữ ảnh cũ)
                if ($url = $uploadImage('AnhDaiDien')) $giaSuData['AnhDaiDien'] = $url;
                if ($url = $uploadImage('AnhCCCD_MatTruoc')) $giaSuData['AnhCCCD_MatTruoc'] = $url;
                if ($url = $uploadImage('AnhCCCD_MatSau')) $giaSuData['AnhCCCD_MatSau'] = $url;
                if ($url = $uploadImage('AnhBangCap')) $giaSuData['AnhBangCap'] = $url;

                GiaSu::updateOrCreate(
                    ['TaiKhoanID' => $taiKhoan->TaiKhoanID], 
                    $giaSuData
                );
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi cập nhật: ' . $e->getMessage());
        }

        return redirect()->route('admin.giasu.index')->with('success', 'Cập nhật gia sư thành công!');
    }

    // =============================================
    // ===== BỔ SUNG HÀM SHOW() VÀ DESTROY() =====
    // =============================================
    
    /**
     * Hiển thị thông tin chi tiết của gia sư.
     */
    public function show(string $id)
    {
        $taiKhoan = TaiKhoan::with('giasu', 'phanquyen.vaitro')
            ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::GIASU_ROLE_ID))
            ->findOrFail($id); 
            
        return view('admin.giasu.show', [ 'taiKhoan' => $taiKhoan ]);
    }

    /**
     * Xóa gia sư khỏi CSDL.
     */
    public function destroy(string $id)
    {
        try {
            $taiKhoan = TaiKhoan::whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::GIASU_ROLE_ID))
                                ->findOrFail($id);
            $taiKhoan->delete();
            return redirect()->route('admin.giasu.index')->with('success', 'Đã xóa gia sư thành công.');
        } catch (\Exception $e) {
            return redirect()->route('admin.giasu.index')->with('error', 'Lỗi khi xóa: ' . $e->getMessage());
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
            // Áp dụng chung
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

            // Cho Email
            'Email.email' => 'Email không đúng định dạng (ví dụ: ten@gmail.com).',
            'Email.unique' => 'Email này đã có người khác sử dụng.',
            
            // Cho Số điện thoại
            'SoDienThoai.regex' => 'Số điện thoại phải là 10 chữ số, bắt đầu bằng số 0.',
            'SoDienThoai.unique' => 'Số điện thoại này đã có người khác sử dụng.',

            // Cho Mật khẩu
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'MatKhau.confirmed' => 'Xác nhận mật khẩu không khớp.',

            // Cho Ngày sinh
            'NgaySinh.before' => 'Ngày sinh không thể là một ngày trong tương lai.',
        ];
    }
}