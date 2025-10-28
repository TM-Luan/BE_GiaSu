<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaiKhoan;
use App\Models\PhanQuyen;
use App\Models\GiaSu;
use App\Models\NguoiHoc;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'HoTen' => 'required|string|max:255',
            'Email' => 'required|email|unique:TaiKhoan,Email',
            'MatKhau' => 'required|min:6|confirmed',
            'SoDienThoai' => 'nullable|string|max:20|unique:TaiKhoan,SoDienThoai',
            'VaiTro' => 'required|in:1,2,3'
        ]);

        try {
            $tk = TaiKhoan::create([
                'HoTen' => $request->HoTen,
                'Email' => $request->Email,
                'MatKhauHash' => Hash::make($request->MatKhau),
                'SoDienThoai' => $request->SoDienThoai,
                'TrangThai' => 1
            ]);

            PhanQuyen::create([
                'TaiKhoanID' => $tk->TaiKhoanID,
                'VaiTroID' => $request->VaiTro
            ]);

            if ($request->VaiTro == 2) {
                GiaSu::create([
                    'TaiKhoanID' => $tk->TaiKhoanID,
                    'HoTen' => $request->HoTen,
                ]);
            } else if ($request->VaiTro == 3) {
                NguoiHoc::create([
                    'TaiKhoanID' => $tk->TaiKhoanID,
                    'HoTen' => $request->HoTen,
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
                'error' => 'Có lỗi xảy ra trong quá trình đăng ký'
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

        if (!$tk || !Hash::check($request->MatKhau, $tk->MatKhauHash)) {
            return response()->json([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        if ($tk->TrangThai === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn đã bị khóa'
            ], 403);
        }

        $phanQuyen = PhanQuyen::where('TaiKhoanID', $tk->TaiKhoanID)->first();
        $vaiTro = $phanQuyen ? $phanQuyen->VaiTroID : null;

        $hoTen = $tk->HoTen;

        if ($vaiTro == 2) { // Gia sư
            $giaSu = GiaSu::where('TaiKhoanID', $tk->TaiKhoanID)->first();
            if ($giaSu && $giaSu->HoTen) {
                $hoTen = $giaSu->HoTen;
            }
        } elseif ($vaiTro == 3) { // Người học
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
                'HoTen' => $hoTen, // Sử dụng Họ tên đã được xác định theo vai trò
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
                'TrangThai' => $user->TrangThai,
                'VaiTro' => $roleId
            ];

            if ($roleId == 2) {
                $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
                if ($giaSu) {
                    $profileData = array_merge($profileData, [
                        'HoTen' => $giaSu->HoTen,
                        'DiaChi' => $giaSu->DiaChi,
                        'GioiTinh' => $giaSu->GioiTinh,
                        'NgaySinh' => $giaSu->NgaySinh,
                        'BangCap' => $giaSu->BangCap,
                        'KinhNghiem' => $giaSu->KinhNghiem,
                        'AnhDaiDien' => $giaSu->AnhDaiDien
                    ]);
                }
            } elseif ($roleId == 3) {
                $nguoiHoc = NguoiHoc::where('TaiKhoanID', $user->TaiKhoanID)->first();
                if ($nguoiHoc) {
                    $profileData = array_merge($profileData, [
                        'HoTen' => $nguoiHoc->HoTen,
                        'DiaChi' => $nguoiHoc->DiaChi,
                        'GioiTinh' => $nguoiHoc->GioiTinh,
                        'NgaySinh' => $nguoiHoc->NgaySinh,
                        'AnhDaiDien' => $nguoiHoc->AnhDaiDien
                    ]);
                }
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

        $request->validate([
            'HoTen' => 'nullable|string|max:255',
            'Email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('TaiKhoan', 'Email')->ignore($user->TaiKhoanID, 'TaiKhoanID')
            ],
            'SoDienThoai' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('TaiKhoan', 'SoDienThoai')->ignore($user->TaiKhoanID, 'TaiKhoanID')
            ],
            'DiaChi' => 'nullable|string|max:255',
            'GioiTinh' => 'nullable|in:Nam,Nữ,Khác',
            'NgaySinh' => 'nullable|date|before:today',
            'BangCap' => 'nullable|string|max:255',
            'KinhNghiem' => 'nullable|string',
            'AnhDaiDien' => 'nullable|string|max:500'
        ]);

        try {
            // Cập nhật bảng TaiKhoan
            $updateData = [];
            if ($request->has('Email')) {
                $updateData['Email'] = $request->Email;
            }
            if ($request->has('SoDienThoai')) {
                $updateData['SoDienThoai'] = $request->SoDienThoai;
            }
            if ($request->has('HoTen')) {
                $updateData['HoTen'] = $request->HoTen;
            }

            if (!empty($updateData)) {
                $user->update($updateData);
            }

            // Cập nhật bảng GiaSu / NguoiHoc
            $phanQuyen = PhanQuyen::where('TaiKhoanID', $user->TaiKhoanID)->first();
            $roleId = $phanQuyen ? $phanQuyen->VaiTroID : null;

            $profileData = [];

            if ($roleId == 2) {
                $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
                if ($giaSu) {
                    $giaSuUpdateData = [];
                    $fields = ['HoTen', 'DiaChi', 'GioiTinh', 'NgaySinh', 'BangCap', 'KinhNghiem', 'AnhDaiDien'];

                    foreach ($fields as $field) {
                        if ($request->has($field)) {
                            $giaSuUpdateData[$field] = $request->$field;
                        }
                    }

                    if (!empty($giaSuUpdateData)) {
                        $giaSu->update($giaSuUpdateData);
                        $profileData = $giaSu->fresh()->toArray();
                    }
                }
            } elseif ($roleId == 3) {
                $nguoiHoc = NguoiHoc::where('TaiKhoanID', $user->TaiKhoanID)->first();
                if ($nguoiHoc) {
                    $nguoiHocUpdateData = [];
                    $fields = ['HoTen', 'DiaChi', 'GioiTinh', 'NgaySinh', 'AnhDaiDien'];

                    foreach ($fields as $field) {
                        if ($request->has($field)) {
                            $nguoiHocUpdateData[$field] = $request->$field;
                        }
                    }

                    if (!empty($nguoiHocUpdateData)) {
                        $nguoiHoc->update($nguoiHocUpdateData);
                        $profileData = $nguoiHoc->fresh()->toArray();
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin thành công',
                'data' => array_merge([
                    'TaiKhoanID' => $user->TaiKhoanID,
                    'Email' => $user->Email,
                    'SoDienThoai' => $user->SoDienThoai,
                    'HoTen' => $user->HoTen
                ], $profileData)
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

            // Kiểm tra mật khẩu hiện tại
            if (!Hash::check($request->MatKhauHienTai, $user->MatKhauHash)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu hiện tại không đúng'
                ], 422);
            }

            // Kiểm tra mật khẩu mới không được trùng với mật khẩu cũ
            if (Hash::check($request->MatKhauMoi, $user->MatKhauHash)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu mới không được trùng với mật khẩu hiện tại'
                ], 422);
            }

            // Cập nhật mật khẩu mới
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

            // Cập nhật mật khẩu mới
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