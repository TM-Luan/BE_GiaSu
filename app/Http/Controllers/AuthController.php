<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\TaiKhoan;
// use App\Models\PhanQuyen;
// use App\Models\GiaSu;
// use App\Models\NguoiHoc;
// use Illuminate\Support\Facades\Hash;


// class AuthController extends Controller
// {
//     public function register(Request $request)
//     {
//         $fields =$request->validate([
//             'HoTen' => 'required|string|max:255',
//             'Email' => 'required|email|unique:TaiKhoan,Email',
//             'MatKhau' => 'required|min:6',
//             'SoDienThoai' => 'nullable|string|max:20',
//             'VaiTro'=> 'required|in:1,2,3'
//         ]);
//         $existingPhone = TaiKhoan::where('SoDienThoai', $request->SoDienThoai)->first();

//         if ($existingPhone) {
//             return response()->json(['error' => 'Số điện thoại đã được sử dụng!'], 400);
//         }

//         $tk = TaiKhoan::create([
//             'Email' => $request->Email,
//             'MatKhauHash' => bcrypt($request->MatKhau),
//             'SoDienThoai' => $request->SoDienThoai,
//             'TrangThai' => 1
//         ]);
//         // $token = $tk->createToken($request->Email);
//         // return [
//         //     'TaiKhoan'=>$tk,
//         //     'token'=>$token->plainTextToken
//         // ];
//          PhanQuyen::create([
//             'TaiKhoanID' => $tk->TaiKhoanID,
//             'VaiTroID' => $request ->VaiTro
//         ]);
//         if($request->VaiTro == 2)
//         {
//         GiaSu::create([
//                     'TaiKhoanID' => $tk->TaiKhoanID,
//                     'HoTen' => $request->HoTen,
//                 ]);
//         }
//         else if($request->VaiTro == 3)
//         {
//                    NguoiHoc::create([
//                     'TaiKhoanID' => $tk->TaiKhoanID,
//                     'HoTen' => $request->HoTen,
//                 ]); 
//         }
//          return response()->json(['message' => 'Đăng ký thành công'], 201);
//     }
// public function login(Request $request)
// {
//     $request->validate([
//         'Email' => 'required|email|exists:TaiKhoan,Email',
//         'MatKhau' => 'required'
//     ]);

//     $tk = TaiKhoan::where('Email', $request->Email)->first();
    
//     if (!$tk || !Hash::check($request->MatKhau, $tk->MatKhauHash)) {
//         return response()->json([
//             'message' => 'Email hoặc mật khẩu không đúng'
//         ], 401);
//     }

//     // Kiểm tra tài khoản có bị khóa không
//     if ($tk->TrangThai === 0) {
//         return response()->json([
//             'message' => 'Tài khoản của bạn đã bị khóa'
//         ], 403);
//     }

//     $phanQuyen = PhanQuyen::where('TaiKhoanID', $tk->TaiKhoanID)->first();
//     $phanQuyen = PhanQuyen::where('TaiKhoanID', $tk->TaiKhoanID)->first();
//     $vaiTro = $phanQuyen ? $phanQuyen->VaiTroID : null;

//     $token = $tk->createToken($request->Email);

//     return response()->json([
//         'message' => 'Đăng nhập thành công',
//         'data' => [
//             'TaiKhoanID' => $tk->TaiKhoanID,
//             'Email' => $tk->Email,
//             'HoTen' => $tk->HoTen,
//             'VaiTro' => $vaiTro,
//         ],
//         'token' => $token->plainTextToken,
//     ], 200);
// }


//     public function logout(Request $request)
//     {
//         $request-> user()->tokens()->delete();
//          return response()->json(['message' => 'Đã đăng xuất']);
//     }
//     public function getProfile(Request $request){

//     // Lấy thông tin người dùng đã đăng nhập
//     $user = $request->user();
    
//     // Lấy thông tin vai trò người dùng
//     $phanQuyen = PhanQuyen::where('TaiKhoanID', $user->TaiKhoanID)->first();
//     $roleId = $phanQuyen ? $phanQuyen->VaiTroID : null;
    
//     // Kiểm tra vai trò người dùng và lấy thông tin từ bảng GiaSu hoặc NguoiHoc
//     if ($roleId == 2) { // Gia sư
//         $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
//         return response()->json([
//             'user' => [
//                 'Email' => $user->Email,
//                 'SoDienThoai' => $user->SoDienThoai,
//                 'HoTen' => $giaSu->HoTen,
//                 'DiaChi' => $giaSu->DiaChi ?? 'Không có thông tin',
//                 'GioiTinh' => $giaSu->GioiTinh ?? 'Không có thông tin',
//                 'NgaySinh' => $giaSu->NgaySinh ?? 'Không có thông tin',
//                 'BangCap' => $giaSu->BangCap ?? 'Không có thông tin',
//                 'KinhNghiem' => $giaSu->KinhNghiem ?? 'Không có thông tin',
//                 'AnhDaiDien' => $giaSu->AnhDaiDien ?? 'Không có thông tin'
//             ]
//         ]);
//     } elseif ($roleId == 3) { // Người học
//         $nguoiHoc = NguoiHoc::where('TaiKhoanID', $user->TaiKhoanID)->first();
//         return response()->json([
//             'user' => [
//                 'Email' => $user->Email,
//                 'SoDienThoai' => $user->SoDienThoai,
//                 'HoTen' => $nguoiHoc->HoTen,
//                 'DiaChi' => $nguoiHoc->DiaChi ?? 'Không có thông tin',
//                 'GioiTinh' => $nguoiHoc->GioiTinh ?? 'Không có thông tin',
//                 'NgaySinh' => $nguoiHoc->NgaySinh ?? 'Không có thông tin',
//                 'AnhDaiDien' => $nguoiHoc->AnhDaiDien ?? 'Không có thông tin'
//             ]
//         ]);
//     }

//     // Nếu vai trò không phải Gia sư hoặc Người học, trả về thông báo lỗi
//     return response()->json(['message' => 'Không tìm thấy thông tin người dùng'], 404);
// }
// public function updateProfile(Request $request)
// {
//     $user = $request->user();

//     // Validation: tất cả trường thông tin cá nhân là nullable (không bắt buộc)
//     $request->validate([
//         'HoTen' => 'nullable|string|max:255',
//         'Email' => 'nullable|email|max:255',
//         'SoDienThoai' => 'nullable|string|max:20',
//         'DiaChi' => 'nullable|string|max:255',
//         'GioiTinh' => 'nullable|string|max:10',
//         'NgaySinh' => 'nullable|date',
//         'BangCap' => 'nullable|string|max:255',
//         'KinhNghiem' => 'nullable|string|max:255',
//         'AnhDaiDien' => 'nullable|string|max:255'
//     ]);

//     // Kiểm tra unique cho SoDienThoai (loại trừ bản thân)
//     if ($request->filled('SoDienThoai')) {
//         $existsPhone = TaiKhoan::where('SoDienThoai', $request->SoDienThoai)
//             ->where('TaiKhoanID', '!=', $user->TaiKhoanID)
//             ->exists();
//         if ($existsPhone) {
//             return response()->json(['message' => 'Số điện thoại đã được sử dụng.'], 400);
//         }
//         $user->SoDienThoai = $request->SoDienThoai;
//     }

//     // Nếu muốn check unique cho email (tùy bạn)
//     if ($request->filled('Email')) {
//         $existsEmail = TaiKhoan::where('Email', $request->Email)
//             ->where('TaiKhoanID', '!=', $user->TaiKhoanID)
//             ->exists();
//         if ($existsEmail) {
//             return response()->json(['message' => 'Email đã được sử dụng.'], 400);
//         }
//         $user->Email = $request->Email;
//     }

//     $user->save();

//     // Cập nhật bảng GiaSu / NguoiHoc — chỉ cập nhật các trường được gửi
//     $phanQuyen = PhanQuyen::where('TaiKhoanID', $user->TaiKhoanID)->first();
//     $roleId = $phanQuyen ? $phanQuyen->VaiTroID : null;

//     if ($roleId == 2) {
//         $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
//         if ($giaSu) {
//             if ($request->has('HoTen')) $giaSu->HoTen = $request->HoTen;
//             if ($request->has('DiaChi')) $giaSu->DiaChi = $request->DiaChi;
//             if ($request->has('GioiTinh')) $giaSu->GioiTinh = $request->GioiTinh;
//             if ($request->has('NgaySinh')) $giaSu->NgaySinh = $request->NgaySinh;
//             if ($request->has('BangCap')) $giaSu->BangCap = $request->BangCap;
//             if ($request->has('KinhNghiem')) $giaSu->KinhNghiem = $request->KinhNghiem;
//             if ($request->has('AnhDaiDien')) $giaSu->AnhDaiDien = $request->AnhDaiDien;
//             $giaSu->save();
//         }
//     } elseif ($roleId == 3) {
//         $nguoiHoc = NguoiHoc::where('TaiKhoanID', $user->TaiKhoanID)->first();
//         if ($nguoiHoc) {
//             if ($request->has('HoTen')) $nguoiHoc->HoTen = $request->HoTen;
//             if ($request->has('DiaChi')) $nguoiHoc->DiaChi = $request->DiaChi;
//             if ($request->has('GioiTinh')) $nguoiHoc->GioiTinh = $request->GioiTinh;
//             if ($request->has('NgaySinh')) $nguoiHoc->NgaySinh = $request->NgaySinh;
//             if ($request->has('AnhDaiDien')) $nguoiHoc->AnhDaiDien = $request->AnhDaiDien;
//             $nguoiHoc->save();
//         }
//     }

//     return response()->json([
//         'message' => 'Cập nhật thành công',
//         'user' => [
//             'Email' => $user->Email,
//             'SoDienThoai' => $user->SoDienThoai,
//             'HoTen' => $request->has('HoTen') ? $request->HoTen : null,
//             'DiaChi' => $request->has('DiaChi') ? $request->DiaChi : null,
//             'GioiTinh' => $request->has('GioiTinh') ? $request->GioiTinh : null,
//             'NgaySinh' => $request->has('NgaySinh') ? $request->NgaySinh : null,
//             'BangCap' => $request->has('BangCap') ? $request->BangCap : null,
//             'KinhNghiem' => $request->has('KinhNghiem') ? $request->KinhNghiem : null,
//             'AnhDaiDien' => $request->has('AnhDaiDien') ? $request->AnhDaiDien : null,
//         ]
//     ]);
// }


// }<?php

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
            'MatKhau' => 'required|min:6|confirmed', // Thêm xác nhận mật khẩu
            'SoDienThoai' => 'nullable|string|max:20|unique:TaiKhoan,SoDienThoai',
            'VaiTro' => 'required|in:1,2,3'
        ]);

        try {
            $tk = TaiKhoan::create([
                'HoTen' => $request->HoTen, // Thêm Họ tên vào bảng TaiKhoan
                'Email' => $request->Email,
                'MatKhauHash' => Hash::make($request->MatKhau), // Sử dụng Hash::make thay vì bcrypt
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
                    'HoTen' => $tk->HoTen,
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

    // Lấy Họ tên theo vai trò
    $hoTen = $tk->HoTen; // Mặc định lấy từ TaiKhoan

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
}