<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaiKhoan;
use App\Models\PhanQuyen;
use App\Models\GiaSu;
use App\Models\NguoiHoc;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'HoTen' => 'required|string|max:255',
            'Email' => 'required|email|unique:TaiKhoan,Email',
            'MatKhau' => 'required|min:6|confirmed',
            'SoDienThoai' => 'nullable|string|max:20|unique:TaiKhoan,SoDienThoai',
            'VaiTro' => 'required|in:1,2,3' // 2=GiaSu, 3=NguoiHoc
        ], [
            'HoTen.required' => 'Vui lòng nhập họ tên.',
            'Email.required' => 'Vui lòng nhập email.',
            'Email.email' => 'Email không hợp lệ.',
            'Email.unique' => 'Email này đã được sử dụng.',
            'MatKhau.required' => 'Vui lòng nhập mật khẩu.',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'MatKhau.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'SoDienThoai.unique' => 'Số điện thoại đã tồn tại.',
            'VaiTro.required' => 'Vui lòng chọn vai trò.',
            'VaiTro.in' => 'Vai trò không hợp lệ.'
        ]);

        try {
            // 1. Tạo Tài Khoản (TrangThai = 1 là "Có thể đăng nhập")
            $tk = TaiKhoan::create([
                'HoTen' => $request->HoTen,
                'Email' => $request->Email,
                'MatKhauHash' => Hash::make($request->MatKhau),
                'SoDienThoai' => $request->SoDienThoai,
                'TrangThai' => 1 // 1 = Tài khoản có thể đăng nhập
            ]);

            // 2. Tạo Phân Quyền
            PhanQuyen::create([
                'TaiKhoanID' => $tk->TaiKhoanID,
                'VaiTroID' => $request->VaiTro
            ]);

            // 3. Tạo Hồ Sơ (Profile) với trạng thái nghiệp vụ
            if ($request->VaiTro == 2) {
                // Gia sư mặc định là 2 (Chờ duyệt)
                GiaSu::create([
                    'TaiKhoanID' => $tk->TaiKhoanID,
                    'HoTen' => $request->HoTen,
                    'TrangThai' => 2 // 2 = Chờ duyệt
                ]);
            } else if ($request->VaiTro == 3) {
                 // Người học mặc định là 1 (Hoạt động)
                NguoiHoc::create([
                    'TaiKhoanID' => $tk->TaiKhoanID,
                    'HoTen' => $request->HoTen,
                    'TrangThai' => 1 // 1 = Hoạt động
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công',
                'data' => [
                    'TaiKhoanID' => $tk->TaiKhoanID,
                    'Email' => $tk->Email,
                    'HoTen' => $request->HoTen,
                    'VaiTro' => $request->VaiTro
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đăng ký thất bại',
                'error' => 'Có lỗi xảy ra trong quá trình đăng ký: ' . $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'Email' => 'required|email',
            'MatKhau' => 'required'
        ]);

        $tk = TaiKhoan::where('Email', $request->Email)->first();

        if (!$tk) {
            return response()->json([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        // (Giữ nguyên logic kiểm tra mật khẩu linh hoạt)
        $isPasswordValid = false;
        if (Hash::needsRehash($tk->MatKhauHash) === false && Hash::check($request->MatKhau, $tk->MatKhauHash)) {
            $isPasswordValid = true;
        } 
        elseif ($tk->MatKhauHash === $request->MatKhau) {
            $isPasswordValid = true;
            $tk->MatKhauHash = Hash::make($request->MatKhau);
            $tk->save();
        }

        if (!$isPasswordValid) {
            return response()->json([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        // <<< SỬA LOGIC (QUAN TRỌNG): Kiểm tra trạng thái "Khóa" là 3
        if ($tk->TrangThai === 3) { 
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn đã bị khóa'
            ], 403);
        }

        $phanQuyen = PhanQuyen::where('TaiKhoanID', $tk->TaiKhoanID)->first();
        $vaiTro = $phanQuyen ? $phanQuyen->VaiTroID : null;

        $hoTen = $tk->HoTen;
        if ($vaiTro == 2) { 
            $giaSu = GiaSu::where('TaiKhoanID', $tk->TaiKhoanID)->first();
            if ($giaSu && $giaSu->HoTen) {
                $hoTen = $giaSu->HoTen;
            }
        } elseif ($vaiTro == 3) {
            $nguoiHoc = NguoiHoc::where('TaiKhoanID', $tk->TaiKhoanID)->first();
            if ($nguoiHoc && $nguoiHoc->HoTen) {
                $hoTen = $nguoiHoc->HoTen;
            }
        }

        $token = $tk->createToken($request->Email);

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'data' => [
                'TaiKhoanID' => $tk->TaiKhoanID,
                'Email' => $tk->Email,
                'HoTen' => $hoTen,
                'SoDienThoai' => $tk->SoDienThoai,
                'VaiTro' => $vaiTro,
            ],
            'token' => $token->plainTextToken,
        ], 200);
    }
    
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã đăng xuất thành công'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đăng xuất thất bại'
            ], 500);
        }
    }

    public function getProfile(Request $request)
    {
        try {
            $user = $request->user();
            $phanQuyen = PhanQuyen::where('TaiKhoanID', $user->TaiKhoanID)->first();
            $roleId = $phanQuyen ? $phanQuyen->VaiTroID : null;

            $profileData = [
                'TaiKhoanID' => $user->TaiKhoanID,
                'Email' => $user->Email,
                'SoDienThoai' => $user->SoDienThoai,
                'TrangThaiTaiKhoan' => $user->TrangThai, 
                'VaiTro' => $roleId
            ];

            if ($roleId == 2) {
                // ⭐️ SỬA ĐỔI: Thêm ->with('monHoc')
                $giaSu = GiaSu::with('monHoc') // <<< THÊM DÒNG NÀY
                    ->where('TaiKhoanID', $user->TaiKhoanID)
                    ->first();
                    
                if ($giaSu) {
                    $profileData = array_merge($profileData, [
                        'GiaSuID' => $giaSu->GiaSuID,
                        'HoTen' => $giaSu->HoTen,
                        'TrangThaiNghiepVu' => $giaSu->TrangThai, 
                        'DiaChi' => $giaSu->DiaChi,
                        'GioiTinh' => $giaSu->GioiTinh,
                        'NgaySinh' => $giaSu->NgaySinh,
                        'AnhCCCD_MatTruoc' => $giaSu->AnhCCCD_MatTruoc,
                        'AnhCCCD_MatSau' => $giaSu->AnhCCCD_MatSau,
                        'BangCap' => $giaSu->BangCap,
                        'AnhBangCap' => $giaSu->AnhBangCap,
                        'TruongDaoTao' => $giaSu->TruongDaoTao,
                        'ChuyenNganh' => $giaSu->ChuyenNganh,
                        'ThanhTich' => $giaSu->ThanhTich,
                        'KinhNghiem' => $giaSu->KinhNghiem,
                        'AnhDaiDien' => $giaSu->AnhDaiDien,
                        'MonID' => $giaSu->MonID,
                        
                        // ⭐️ SỬA ĐỔI: Trả về cả tên môn học
                        'TenMon' => $giaSu->monHoc ? $giaSu->monHoc->TenMon : null
                    ]);
                }
            } elseif ($roleId == 3) {
                // ... (logic NguoiHoc giữ nguyên)
            }

            return response()->json([
                'success' => true,
                'data' => $profileData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thông tin người dùng'
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // VALIDATION (Đã xóa Email, thêm MonID)
        $request->validate([
            'HoTen' => 'nullable|string|max:255',
            'SoDienThoai' => [
                'nullable', 'string', 'max:20',
                Rule::unique('TaiKhoan', 'SoDienThoai')->ignore($user->TaiKhoanID, 'TaiKhoanID')
            ],
            'DiaChi' => 'nullable|string|max:255',
            'GioiTinh' => 'nullable|in:Nam,Nữ,Khác',
            'NgaySinh' => 'nullable|date|before:today',
            'MonID' => 'nullable|integer|exists:monhoc,MonID', // <<< ĐÃ THÊM

            'AnhCCCD_MatTruoc' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'AnhCCCD_MatSau' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'AnhBangCap' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'AnhDaiDien' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'BangCap' => 'nullable|string|max:255',
            'TruongDaoTao' => 'nullable|string|max:255',
            'ChuyenNganh' => 'nullable|string|max:255',
            'ThanhTich' => 'nullable|string',
            'KinhNghiem' => 'nullable|string',
        ]);

        try {
            // Cập nhật bảng TaiKhoan (Đã xóa logic Email)
            $updateData = [];
            if ($request->has('SoDienThoai')) $updateData['SoDienThoai'] = $request->SoDienThoai;
            if ($request->has('HoTen')) $updateData['HoTen'] = $request->HoTen;
            if (!empty($updateData)) $user->update($updateData);

            // Cập nhật bảng GiaSu / NguoiHoc
            $phanQuyen = PhanQuyen::where('TaiKhoanID', $user->TaiKhoanID)->first();
            $roleId = $phanQuyen ? $phanQuyen->VaiTroID : null;
            $profileData = [];

            $apiKey = config('services.imgbb.key');

            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi cấu hình server: Thiếu API key của ImgBB.'
                ], 500);
            }

            // Hàm trợ giúp upload lên ImgBB
            $uploadToImgBB = function($file) use ($apiKey) {
                $imageBase64 = base64_encode(file_get_contents($file->getRealPath()));
                $response = Http::asForm()->post('https://api.imgbb.com/1/upload', [
                    'key' => $apiKey,
                    'image' => $imageBase64
                ]);

                if ($response->successful() && isset($response->json()['data']['url'])) {
                    return $response->json()['data']['url'];
                }
                return null; 
            };


            if ($roleId == 2) { // VAI TRÒ: GIA SƯ
                $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
                if ($giaSu) {
                    $giaSuUpdateData = [];
                    // Cập nhật text fields
                    $fields = [
                        'HoTen', 'DiaChi', 'GioiTinh', 'NgaySinh',
                        'BangCap', 'TruongDaoTao', 'ChuyenNganh',
                        'ThanhTich', 'KinhNghiem', 'MonID' // <<< ĐÃ THÊM
                    ];
                    foreach ($fields as $field) {
                        if ($request->has($field)) {
                            $giaSuUpdateData[$field] = $request->$field;
                        }
                    }

                    // 2. LOGIC UPLOAD ẢNH LÊN IMGBB (Gia sư)
                    $fileFields = ['AnhDaiDien', 'AnhCCCD_MatTruoc', 'AnhCCCD_MatSau', 'AnhBangCap'];
                    foreach ($fileFields as $fieldKey) {
                        if ($request->hasFile($fieldKey)) {
                            $url = $uploadToImgBB($request->file($fieldKey));
                            if ($url) {
                                $giaSuUpdateData[$fieldKey] = $url;
                            }
                        }
                    }

                    if (!empty($giaSuUpdateData)) {
                        $giaSu->update($giaSuUpdateData);
                    }
                    $profileData = $giaSu->fresh()->toArray(); 
                }
            } elseif ($roleId == 3) { // VAI TRÒ: NGƯỜI HỌC
                $nguoiHoc = NguoiHoc::where('TaiKhoanID', $user->TaiKhoanID)->first();
                if ($nguoiHoc) {
                    $nguoiHocUpdateData = [];
                    // Cập nhật text fields
                    $fields = ['HoTen', 'DiaChi', 'GioiTinh', 'NgaySinh'];
                    foreach ($fields as $field) {
                        if ($request->has($field)) {
                            $nguoiHocUpdateData[$field] = $request->$field;
                        }
                    }

                    if ($request->hasFile('AnhDaiDien')) {
                        $url = $uploadToImgBB($request->file('AnhDaiDien'));
                        if ($url) {
                            $nguoiHocUpdateData['AnhDaiDien'] = $url;
                        }
                    }

                    if (!empty($nguoiHocUpdateData)) {
                        $nguoiHoc->update($nguoiHocUpdateData);
                    }
                    $profileData = $nguoiHoc->fresh()->toArray(); 
                }
            }

            // Gộp dữ liệu từ TaiKhoan và (GiaSu/NguoiHoc) để trả về
            $finalData = array_merge($profileData, [
                'TaiKhoanID' => $user->TaiKhoanID,
                'Email' => $user->Email,
                'SoDienThoai' => $user->SoDienThoai,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin thành công',
                'data' => $finalData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật thông tin thất bại: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'MatKhauHienTai' => 'required',
            'MatKhauMoi' => 'required|min:6|confirmed',
        ]);

        try {
            $user = $request->user();

            if (!Hash::check($request->MatKhauHienTai, $user->MatKhauHash)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu hiện tại không đúng'
                ], 422);
            }

            if (Hash::check($request->MatKhauMoi, $user->MatKhauHash)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu mới không được trùng với mật khẩu hiện tại'
                ], 422);
            }

            $user->update([
                'MatKhauHash' => Hash::make($request->MatKhauMoi)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đổi mật khẩu thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'Email' => 'required|email|exists:TaiKhoan,Email',
            'MatKhauMoi' => 'required|min:6|confirmed',
        ]);

        try {
            $tk = TaiKhoan::where('Email', $request->Email)->first();

            if (!$tk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email không tồn tại trong hệ thống'
                ], 404);
            }

            $tk->update([
                'MatKhauHash' => Hash::make($request->MatKhauMoi)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đặt lại mật khẩu thành công'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đặt lại mật khẩu thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}