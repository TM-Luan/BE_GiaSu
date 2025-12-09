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
use Illuminate\Support\Facades\Log; // Thêm Log để debug
use Illuminate\Validation\Rules\Password;

class GiaSuController extends Controller
{
    private const GIASU_ROLE_ID = 2;

    public function index(Request $request)
    {
        // Lấy TaiKhoan (gia sư) bằng JOIN trực tiếp
        $query = TaiKhoan::select('TaiKhoan.*')
            ->join('PhanQuyen', 'PhanQuyen.TaiKhoanID', '=', 'TaiKhoan.TaiKhoanID')
            ->join('GiaSu', 'GiaSu.TaiKhoanID', '=', 'TaiKhoan.TaiKhoanID')
            ->where('PhanQuyen.VaiTroID', self::GIASU_ROLE_ID)
            ->where('GiaSu.TrangThai', 1) // Chỉ lấy hồ sơ đã DUYỆT
            ->with(['giasu', 'phanquyen'])
            ->orderByDesc('TaiKhoan.TaiKhoanID');

        // Lọc theo trạng thái tài khoản
        $tt = $request->input('trangthai', null);
        if ($tt !== null && $tt !== '') {
            if (is_numeric($tt)) {
                $query->where('TaiKhoan.TrangThai', (int)$tt);
            }
        }

        // Tìm kiếm
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('TaiKhoan.Email', 'like', "%$search%")
                  ->orWhere('TaiKhoan.SoDienThoai', 'like', "%$search%")
                  ->orWhere('GiaSu.HoTen', 'like', "%$search%");
            });
        }

        $giasuList = $query->paginate(10)->withQueryString();

        return view('admin.giasu.index', [
            'giasuList' => $giasuList
        ]);
    }

    public function pending(Request $request)
    {
        // Lấy TaiKhoan (gia sư) có hồ sơ CHƯA DUYỆT
        $query = TaiKhoan::select('TaiKhoan.*')
            ->join('PhanQuyen', 'PhanQuyen.TaiKhoanID', '=', 'TaiKhoan.TaiKhoanID')
            ->join('GiaSu', 'GiaSu.TaiKhoanID', '=', 'TaiKhoan.TaiKhoanID')
            ->where('PhanQuyen.VaiTroID', self::GIASU_ROLE_ID)
            ->where('GiaSu.TrangThai', '!=', 1)
            ->with(['giasu', 'phanquyen'])
            ->orderByDesc('TaiKhoan.TaiKhoanID');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('TaiKhoan.Email', 'like', "%$search%")
                  ->orWhere('TaiKhoan.SoDienThoai', 'like', "%$search%")
                  ->orWhere('GiaSu.HoTen', 'like', "%$search%");
            });
        }

        $giasuList = $query->paginate(10)->withQueryString();

        return view('admin.giasu.index', [
            'giasuList' => $giasuList,
            'isPending' => true
        ]);
    }

    public function approve($id)
    {
        try {
            $taiKhoan = TaiKhoan::with('giasu')->findOrFail($id);

            DB::transaction(function() use ($taiKhoan) {
                if ($taiKhoan->giasu) {
                    $taiKhoan->giasu->update(['TrangThai' => 1]);
                }
                $taiKhoan->update(['TrangThai' => 1]);
            });

             return redirect()->route('admin.giasu.pending')
                 ->with('success', 'Đã duyệt hồ sơ gia sư thành công!');
         } catch (\Exception $e) {
             return back()->with('error', 'Lỗi: ' . $e->getMessage());
         }
    }

    public function create()
    {
        return view('admin.giasu.create');
    }

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
            'BangCap' => 'nullable|string|max:255',
            'TruongDaoTao' => 'nullable|string|max:255',
            'ChuyenNganh' => 'nullable|string|max:255',
            'KinhNghiem' => 'nullable|string|max:255',
            'ThanhTich' => 'nullable|string',
            'AnhDaiDien' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'AnhCCCD_MatTruoc' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'AnhCCCD_MatSau' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'AnhBangCap' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
        
        $validated = $request->validate($rules, $messages);
        
        $taiKhoanData = [
            'Email' => $validated['Email'],
            'SoDienThoai' => $validated['SoDienThoai'],
            'TrangThai' => $validated['TrangThai'],
            'MatKhauHash' => Hash::make($validated['MatKhau']) 
        ];

        $giaSuData = [
            'HoTen' => $validated['HoTen'],
            'GioiTinh' => $validated['GioiTinh'] ?? null,
            'NgaySinh' => $validated['NgaySinh'] ?? null,
            'DiaChi' => $validated['DiaChi'] ?? null,
            'BangCap' => $validated['BangCap'] ?? null,
            'TruongDaoTao' => $validated['TruongDaoTao'] ?? null,
            'ChuyenNganh' => $validated['ChuyenNganh'] ?? null,
            'KinhNghiem' => $validated['KinhNghiem'] ?? null,
            'ThanhTich' => $validated['ThanhTich'] ?? null,
        ];

        try {
            DB::transaction(function () use ($request, $taiKhoanData, $giaSuData) {
                
                // --- SỬA LOGIC UPLOAD ẢNH ---
                $apiKey = env('IMAGEBB_API_KEY');
                if (!$apiKey) {
                    throw new \Exception("Chưa cấu hình API Key ImgBB trong .env");
                }

                $uploadImage = function($fileKey) use ($request, $apiKey) {
                    if ($request->hasFile($fileKey)) {
                        $file = $request->file($fileKey);
                        try {
                            $response = Http::attach(
                                'image', 
                                file_get_contents($file), 
                                $file->getClientOriginalName()
                            )->post('https://api.imgbb.com/1/upload', ['key' => $apiKey]);

                            if ($response->successful()) {
                                return $response->json()['data']['url'];
                            } else {
                                Log::error("ImgBB Upload Error ($fileKey): " . $response->body());
                                throw new \Exception("Không thể upload ảnh $fileKey. Lỗi API.");
                            }
                        } catch (\Exception $e) {
                            throw new \Exception("Lỗi upload $fileKey: " . $e->getMessage());
                        }
                    }
                    return null;
                };
                // --- KẾT THÚC SỬA ---

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

    public function update(Request $request, string $id)
    {
        $taiKhoan = TaiKhoan::with('giasu')
            ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::GIASU_ROLE_ID))
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
            'BangCap' => 'nullable|string|max:255',
            'TruongDaoTao' => 'nullable|string|max:255',
            'ChuyenNganh' => 'nullable|string|max:255',
            'KinhNghiem' => 'nullable|string|max:255',
            'ThanhTich' => 'nullable|string',
            'AnhDaiDien' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'AnhCCCD_MatTruoc' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'AnhCCCD_MatSau' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'AnhBangCap' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        $validated = $request->validate($rules, $messages);
        
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

                // --- SỬA LOGIC UPLOAD ẢNH (BẮT LỖI) ---
                $apiKey = env('IMAGEBB_API_KEY');
                if (!$apiKey) {
                    throw new \Exception("Chưa cấu hình API Key ImgBB (Kiểm tra .env hoặc config cache)");
                }

                $uploadImage = function($fileKey) use ($request, $apiKey) {
                    if ($request->hasFile($fileKey)) {
                        $file = $request->file($fileKey);
                        try {
                            $response = Http::attach(
                                'image', 
                                file_get_contents($file), 
                                $file->getClientOriginalName()
                            )->post('https://api.imgbb.com/1/upload', ['key' => $apiKey]);

                            if ($response->successful()) {
                                return $response->json()['data']['url'];
                            } else {
                                // Ghi log lỗi chi tiết
                                Log::error("Upload Failed [$fileKey]: " . $response->body());
                                // Ném lỗi để Transaction Rollback
                                throw new \Exception("Lỗi từ ImgBB: " . ($response->json()['error']['message'] ?? 'Không xác định'));
                            }
                        } catch (\Exception $e) {
                            throw $e; // Ném tiếp lỗi ra ngoài
                        }
                    }
                    return null;
                };

                // Chỉ cập nhật nếu upload thành công (có URL mới)
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
            return back()->withInput()->with('error', 'Lỗi cập nhật: ' . $e->getMessage());
        }

        return redirect()->route('admin.giasu.index')->with('success', 'Cập nhật gia sư thành công!');
    }

    public function show(string $id)
    {
        $taiKhoan = TaiKhoan::with('giasu', 'phanquyen.vaitro')
            ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::GIASU_ROLE_ID))
            ->findOrFail($id); 
        return view('admin.giasu.show', [ 'taiKhoan' => $taiKhoan ]);
    }

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

    public function approveProfile(string $id)
    {
        try {
            $taiKhoan = TaiKhoan::with('giasu')
                ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::GIASU_ROLE_ID))
                ->findOrFail($id);
            $taiKhoan->update(['TrangThai' => 1]);

            return response()->json([
                'success' => true,
                'message' => 'Duyệt hồ sơ gia sư thành công!',
                'data' => $taiKhoan->load('giasu'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi duyệt hồ sơ: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function rejectProfile(Request $request, string $id)
    {
        $request->validate(['ly_do' => 'nullable|string|max:500']);

        try {
            $taiKhoan = TaiKhoan::with('giasu')
                ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::GIASU_ROLE_ID))
                ->findOrFail($id);
            $taiKhoan->update(['TrangThai' => 0]);

            return response()->json([
                'success' => true,
                'message' => 'Từ chối hồ sơ gia sư thành công!',
                'ly_do' => $request->ly_do,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi từ chối hồ sơ: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function pendingList()
    {
        $pendingList = TaiKhoan::with('giasu', 'phanquyen')
            ->whereHas('phanquyen', fn($q) => $q->where('VaiTroID', self::GIASU_ROLE_ID))
            ->where('TrangThai', 0)
            ->orderByDesc('TaiKhoanID')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $pendingList,
        ]);
    }

    private function getValidationMessages()
    {
        return [
            'required' => 'Thông tin này là bắt buộc, không được để trống.',
            'string' => 'Thông tin này phải là một chuỗi ký tự.',
            'boolean' => 'Giá trị này không hợp lệ.',
            'TrangThai.in' => 'Trạng thái không hợp lệ. Vui lòng chọn 0, 1 hoặc 2.',
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