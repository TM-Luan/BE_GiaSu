<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\TaiKhoan;
use App\Models\PhanQuyen;
use App\Models\GiaSu;
use App\Models\NguoiHoc;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB; // <<< THÊM DÒNG NÀY

class AuthController extends Controller
{
    /**
     * SỬA 1: SỬ DỤNG DB::TRANSACTION ĐỂ ĐĂNG KÝ
     */
    public function register(Request $request)
    {
        $fields = $request->validate([
            'HoTen' => 'required|string|max:255',
            'Email' => 'required|email|unique:TaiKhoan,email',
            'MatKhau' => 'required|min:6|confirmed',
            'SoDienThoai' => ['required', 'regex:/^(03|05|07|08|09)[0-9]{8}$/', 'unique:TaiKhoan,SoDienThoai'],
            'VaiTro' => 'required|in:1,2,3' // 2=GiaSu, 3=NguoiHoc
        ], [
            // Họ tên
            'HoTen.required' => 'Vui lòng nhập họ tên.',
            'HoTen.string' => 'Họ tên phải là chuỗi ký tự.',
            'HoTen.max' => 'Họ tên không được vượt quá 255 ký tự.',

            // Email
            'Email.required' => 'Vui lòng nhập email.',
            'Email.email' => 'Địa chỉ email không đúng định dạng.',
            'Email.unique' => 'Email này đã được đăng ký trong hệ thống.',
            'Email.max' => 'Email không được vượt quá 255 ký tự.',

            // Mật khẩu
            'MatKhau.required' => 'Vui lòng nhập mật khẩu.',
            'MatKhau.string' => 'Mật khẩu phải là chuỗi ký tự.',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 8 ký tự để đảm bảo an toàn.',
            'MatKhau.confirmed' => 'Mật khẩu xác nhận không khớp.',

            // Số điện thoại
            'SoDienThoai.required' => 'Vui lòng nhập số điện thoại.',
            'SoDienThoai.unique' => 'Số điện thoại này đã được sử dụng.',
            'SoDienThoai.regex' => 'Số điện thoại không hợp lệ (phải gồm 10 số và bắt đầu bằng 03, 05, 07, 08, 09).',

            // Vai trò
            'VaiTro.required' => 'Vui lòng chọn vai trò.',
            'VaiTro.integer' => 'Dữ liệu vai trò không hợp lệ.',
            'VaiTro.in' => 'Vai trò được chọn không tồn tại trong hệ thống.'
        ]);

        try {
            // Bọc logic bằng transaction
            $createdData = DB::transaction(function () use ($request) {

                // 1. Tạo Tài Khoản
                $tk = TaiKhoan::create([
                    'Email' => $request->Email,
                    'MatKhauHash' => Hash::make($request->MatKhau),
                    'SoDienThoai' => $request->SoDienThoai,
                    'TrangThai' => 1
                ]);

                // 2. Tạo Phân Quyền
                PhanQuyen::create([
                    'TaiKhoanID' => $tk->TaiKhoanID,
                    'VaiTroID' => $request->VaiTro
                ]);

                // 3. Tạo Hồ Sơ (Profile)
                if ($request->VaiTro == 2) {
                    GiaSu::create([
                        'TaiKhoanID' => $tk->TaiKhoanID,
                        'HoTen' => $request->HoTen, // HoTen này là "nguồn tin cậy"
                        'TrangThai' => 0
                    ]);
                } else if ($request->VaiTro == 3) {
                    NguoiHoc::create([
                        'TaiKhoanID' => $tk->TaiKhoanID,
                        'HoTen' => $request->HoTen, // HoTen này là "nguồn tin cậy"
                        'TrangThai' => 1
                    ]);
                }

                // Trả về dữ liệu cần thiết
                return [
                    'TaiKhoanID' => $tk->TaiKhoanID,
                    'Email' => $tk->Email,
                ];
            });

            // Nếu mọi thứ thành công
            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công',
                'data' => [
                    'TaiKhoanID' => $createdData['TaiKhoanID'],
                    'Email' => $createdData['Email'],
                    'HoTen' => $request->HoTen,
                    'VaiTro' => $request->VaiTro
                ]
            ], 201);

        } catch (\Exception $e) {
            // Nếu có lỗi, transaction sẽ tự động rollback
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
        } elseif ($tk->MatKhauHash === $request->MatKhau) {
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

        // =================================================================
        // <<< SỬA LOGIC: ĐỒNG BỘ VỚI WEB ĐỂ CHẶN TÀI KHOẢN BỊ KHÓA >>>
        // =================================================================
        
        // Nạp quan hệ giasu để kiểm tra trạng thái hồ sơ (nếu có)
        $tk->loadMissing('giasu');

        // Logic: Chặn khi TaiKhoan.TrangThai == 2 HOẶC (GiaSu tồn tại và GiaSu.TrangThai == 2)
        if ((int)$tk->TrangThai === 2 || ($tk->giasu && (int)$tk->giasu->TrangThai === 2)) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên để được hỗ trợ.'
            ], 403);
        }
        
        // =================================================================

        $phanQuyen = PhanQuyen::where('TaiKhoanID', $tk->TaiKhoanID)->first();
        $vaiTro = $phanQuyen ? $phanQuyen->VaiTroID : null;

        // Logic lấy HoTen từ bảng GiaSu/NguoiHoc (Giữ nguyên)
        $hoTen = null;
        if ($vaiTro == 2) {
            // Có thể dùng luôn $tk->giasu đã load ở trên nếu muốn tối ưu, 
            // hoặc giữ nguyên query cũ để tránh sửa nhiều.
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
        $user = $request->user();
        
        // 1. Xóa FCM Token của user này để tránh gửi nhầm thiết bị sau này
        if ($user) {
            $user->fcm_token = null;
            $user->save();
        }

        // 2. Xóa token đăng nhập (code cũ)
        $user->currentAccessToken()->delete();

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

    /**
     * SỬA 2: THÊM LOGIC LẤY THÔNG TIN NGƯỜI HỌC (roleId == 3)
     */
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
                $giaSu = GiaSu::with('monHoc')
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
                        'TenMon' => $giaSu->monHoc ? $giaSu->monHoc->TenMon : null
                    ]);
                }
            } elseif ($roleId == 3) {
                // <<< BẮT ĐẦU SỬA: Thêm logic cho Người Học
                $nguoiHoc = NguoiHoc::where('TaiKhoanID', $user->TaiKhoanID)->first();

                if ($nguoiHoc) {
                    $profileData = array_merge($profileData, [
                        'NguoiHocID' => $nguoiHoc->NguoiHocID,
                        'HoTen' => $nguoiHoc->HoTen,
                        'TrangThaiNghiepVu' => $nguoiHoc->TrangThai,
                        'DiaChi' => $nguoiHoc->DiaChi,
                        'GioiTinh' => $nguoiHoc->GioiTinh,
                        'NgaySinh' => $nguoiHoc->NgaySinh,
                        'AnhDaiDien' => $nguoiHoc->AnhDaiDien,
                    ]);
                }
                // <<< KẾT THÚC SỬA
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

    /**
     * SỬA 3 & 4: SỬA TÊN BẢNG MonHoc VÀ BỎ CẬP NHẬT HoTen Ở TaiKhoan
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // VALIDATION
        $request->validate([
            'HoTen' => 'nullable|string|max:255',
            'SoDienThoai' => [
                    'nullable',
                    'string',
                    'max:20',
                    Rule::unique('TaiKhoan', 'SoDienThoai')->ignore($user->TaiKhoanID, 'TaiKhoanID')
                ],
            'DiaChi' => 'nullable|string|max:255',
            'GioiTinh' => 'nullable|in:Nam,Nữ,Khác',
            'NgaySinh' => 'nullable|date|before:today',

            // SỬA 3: Sửa 'monhoc' thành 'MonHoc' (chính xác tên bảng CSDL)
            'MonID' => 'nullable|integer|exists:MonHoc,MonID',

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
            // Cập nhật bảng TaiKhoan
            $updateData = [];
            if ($request->has('SoDienThoai'))
                $updateData['SoDienThoai'] = $request->SoDienThoai;

            // SỬA 4: Xóa dòng cập nhật HoTen ở đây.
            // HoTen sẽ được cập nhật vào bảng GiaSu/NguoiHoc
            // if ($request->has('HoTen')) $updateData['HoTen'] = $request->HoTen; // <<< XÓA DÒNG NÀY

            if (!empty($updateData))
                $user->update($updateData);

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
            $uploadToImgBB = function ($file) use ($apiKey) {
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
                    // Cập nhật text fields (Đã bao gồm HoTen, MonID - Chính xác)
                    $fields = [
                        'HoTen',
                        'DiaChi',
                        'GioiTinh',
                        'NgaySinh',
                        'BangCap',
                        'TruongDaoTao',
                        'ChuyenNganh',
                        'ThanhTich',
                        'KinhNghiem',
                        'MonID'
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
                    // Cập nhật text fields (Đã bao gồm HoTen - Chính xác)
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
