<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaiKhoan;
use App\Models\PhanQuyen;
use App\Models\GiaSu;
use App\Models\NguoiHoc;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields =$request->validate([
            'HoTen' => 'required|string|max:255',
            'Email' => 'required|email|unique:TaiKhoan,Email',
            'MatKhau' => 'required|min:6',
            'SoDienThoai' => 'nullable|string|max:20',
            'VaiTro'=> 'required|in:1,2,3'
        ]);
        $existingPhone = TaiKhoan::where('SoDienThoai', $request->SoDienThoai)->first();

        if ($existingPhone) {
            return response()->json(['error' => 'Số điện thoại đã được sử dụng!'], 400);
        }

        $tk = TaiKhoan::create([
            'Email' => $request->Email,
            'MatKhauHash' => bcrypt($request->MatKhau),
            'SoDienThoai' => $request->SoDienThoai,
            'TrangThai' => 1
        ]);
        // $token = $tk->createToken($request->Email);
        // return [
        //     'TaiKhoan'=>$tk,
        //     'token'=>$token->plainTextToken
        // ];
         PhanQuyen::create([
            'TaiKhoanID' => $tk->TaiKhoanID,
            'VaiTroID' => $request ->VaiTro
        ]);
        if($request->VaiTro == 2)
        {
        GiaSu::create([
                    'TaiKhoanID' => $tk->TaiKhoanID,
                    'HoTen' => $request->HoTen,
                ]);
        }
        else if($request->VaiTro == 3)
        {
                   NguoiHoc::create([
                    'TaiKhoanID' => $tk->TaiKhoanID,
                    'HoTen' => $request->HoTen,
                ]); 
        }
         return response()->json(['message' => 'Đăng ký thành công'], 201);
    }
    public function login(Request $request)
{
    $request->validate([
        'Email' => 'required|email|exists:TaiKhoan,Email',
        'MatKhau' => 'required'
    ]);

    $tk = TaiKhoan::where('Email', $request->Email)->first();
    if (!$tk || !Hash::check($request->MatKhau, $tk->MatKhauHash)) {
        return response()->json(['message' => 'Email hoặc mật khẩu không đúng'], 401);
    }

        $token = $tk->createToken($request->Email);
        return [
            'TaiKhoan'=>$tk,
            'token'=>$token->plainTextToken
        ];
    }

    public function logout(Request $request)
    {
        $request-> user()->tokens()->delete();
         return response()->json(['message' => 'Đã đăng xuất']);
    }
    public function getProfile(Request $request){

    // Lấy thông tin người dùng đã đăng nhập
    $user = $request->user();
    
    // Lấy thông tin vai trò người dùng
    $phanQuyen = PhanQuyen::where('TaiKhoanID', $user->TaiKhoanID)->first();
    $roleId = $phanQuyen ? $phanQuyen->VaiTroID : null;
    
    // Kiểm tra vai trò người dùng và lấy thông tin từ bảng GiaSu hoặc NguoiHoc
    if ($roleId == 2) { // Gia sư
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        return response()->json([
            'user' => [
                'Email' => $user->Email,
                'SoDienThoai' => $user->SoDienThoai,
                'HoTen' => $giaSu->HoTen,
                'DiaChi' => $giaSu->DiaChi ?? 'Không có thông tin',
                'GioiTinh' => $giaSu->GioiTinh ?? 'Không có thông tin',
                'NgaySinh' => $giaSu->NgaySinh ?? 'Không có thông tin',
                'BangCap' => $giaSu->BangCap ?? 'Không có thông tin',
                'KinhNghiem' => $giaSu->KinhNghiem ?? 'Không có thông tin',
                'AnhDaiDien' => $giaSu->AnhDaiDien ?? 'Không có thông tin'
            ]
        ]);
    } elseif ($roleId == 3) { // Người học
        $nguoiHoc = NguoiHoc::where('TaiKhoanID', $user->TaiKhoanID)->first();
        return response()->json([
            'user' => [
                'Email' => $user->Email,
                'SoDienThoai' => $user->SoDienThoai,
                'HoTen' => $nguoiHoc->HoTen,
                'DiaChi' => $nguoiHoc->DiaChi ?? 'Không có thông tin',
                'GioiTinh' => $nguoiHoc->GioiTinh ?? 'Không có thông tin',
                'NgaySinh' => $nguoiHoc->NgaySinh ?? 'Không có thông tin',
                'AnhDaiDien' => $nguoiHoc->AnhDaiDien ?? 'Không có thông tin'
            ]
        ]);
    }

    // Nếu vai trò không phải Gia sư hoặc Người học, trả về thông báo lỗi
    return response()->json(['message' => 'Không tìm thấy thông tin người dùng'], 404);
}
public function updateProfile(Request $request)
{
    $user = $request->user();

    // Validation: tất cả trường thông tin cá nhân là nullable (không bắt buộc)
    $request->validate([
        'HoTen' => 'nullable|string|max:255',
        'Email' => 'nullable|email|max:255',
        'SoDienThoai' => 'nullable|string|max:20',
        'DiaChi' => 'nullable|string|max:255',
        'GioiTinh' => 'nullable|string|max:10',
        'NgaySinh' => 'nullable|date',
        'BangCap' => 'nullable|string|max:255',
        'KinhNghiem' => 'nullable|string|max:255',
        'AnhDaiDien' => 'nullable|string|max:255'
    ]);

    // Kiểm tra unique cho SoDienThoai (loại trừ bản thân)
    if ($request->filled('SoDienThoai')) {
        $existsPhone = TaiKhoan::where('SoDienThoai', $request->SoDienThoai)
            ->where('TaiKhoanID', '!=', $user->TaiKhoanID)
            ->exists();
        if ($existsPhone) {
            return response()->json(['message' => 'Số điện thoại đã được sử dụng.'], 400);
        }
        $user->SoDienThoai = $request->SoDienThoai;
    }

    // Nếu muốn check unique cho email (tùy bạn)
    if ($request->filled('Email')) {
        $existsEmail = TaiKhoan::where('Email', $request->Email)
            ->where('TaiKhoanID', '!=', $user->TaiKhoanID)
            ->exists();
        if ($existsEmail) {
            return response()->json(['message' => 'Email đã được sử dụng.'], 400);
        }
        $user->Email = $request->Email;
    }

    $user->save();

    // Cập nhật bảng GiaSu / NguoiHoc — chỉ cập nhật các trường được gửi
    $phanQuyen = PhanQuyen::where('TaiKhoanID', $user->TaiKhoanID)->first();
    $roleId = $phanQuyen ? $phanQuyen->VaiTroID : null;

    if ($roleId == 2) {
        $giaSu = GiaSu::where('TaiKhoanID', $user->TaiKhoanID)->first();
        if ($giaSu) {
            if ($request->has('HoTen')) $giaSu->HoTen = $request->HoTen;
            if ($request->has('DiaChi')) $giaSu->DiaChi = $request->DiaChi;
            if ($request->has('GioiTinh')) $giaSu->GioiTinh = $request->GioiTinh;
            if ($request->has('NgaySinh')) $giaSu->NgaySinh = $request->NgaySinh;
            if ($request->has('BangCap')) $giaSu->BangCap = $request->BangCap;
            if ($request->has('KinhNghiem')) $giaSu->KinhNghiem = $request->KinhNghiem;
            if ($request->has('AnhDaiDien')) $giaSu->AnhDaiDien = $request->AnhDaiDien;
            $giaSu->save();
        }
    } elseif ($roleId == 3) {
        $nguoiHoc = NguoiHoc::where('TaiKhoanID', $user->TaiKhoanID)->first();
        if ($nguoiHoc) {
            if ($request->has('HoTen')) $nguoiHoc->HoTen = $request->HoTen;
            if ($request->has('DiaChi')) $nguoiHoc->DiaChi = $request->DiaChi;
            if ($request->has('GioiTinh')) $nguoiHoc->GioiTinh = $request->GioiTinh;
            if ($request->has('NgaySinh')) $nguoiHoc->NgaySinh = $request->NgaySinh;
            if ($request->has('AnhDaiDien')) $nguoiHoc->AnhDaiDien = $request->AnhDaiDien;
            $nguoiHoc->save();
        }
    }

    return response()->json([
        'message' => 'Cập nhật thành công',
        'user' => [
            'Email' => $user->Email,
            'SoDienThoai' => $user->SoDienThoai,
            'HoTen' => $request->has('HoTen') ? $request->HoTen : null,
            'DiaChi' => $request->has('DiaChi') ? $request->DiaChi : null,
            'GioiTinh' => $request->has('GioiTinh') ? $request->GioiTinh : null,
            'NgaySinh' => $request->has('NgaySinh') ? $request->NgaySinh : null,
            'BangCap' => $request->has('BangCap') ? $request->BangCap : null,
            'KinhNghiem' => $request->has('KinhNghiem') ? $request->KinhNghiem : null,
            'AnhDaiDien' => $request->has('AnhDaiDien') ? $request->AnhDaiDien : null,
        ]
    ]);
}


}