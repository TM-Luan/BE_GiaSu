@extends('layouts.web')

@section('title', 'Thông tin cá nhân')

@section('content')
<div class"max-w-4xl mx-auto" x-data="{ tab: '{{ session('success_password') || $errors->has('current_password') ? 'password' : 'profile' }}' }">

    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Cài đặt tài khoản</h1>
        <p class="text-gray-500 mt-2 text-base font-medium">Quản lý hồ sơ, thông tin cá nhân và bảo mật.</p>
    </div>

    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-6">
                <a href="#" @click.prevent="tab = 'profile'"
                   :class="{
                       'border-blue-600 text-blue-600': tab === 'profile',
                       'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'profile'
                   }"
                   class="whitespace-nowrap pb-4 px-1 border-b-2 font-semibold text-base transition-colors">
                    Hồ sơ
                </a>
                <a href="#" @click.prevent="tab = 'password'"
                   :class="{
                       'border-blue-600 text-blue-600': tab === 'password',
                       'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'password'
                   }"
                   class="whitespace-nowrap pb-4 px-1 border-b-2 font-semibold text-base transition-colors">
                    Đổi mật khẩu
                </a>
            </nav>
        </div>
    </div>

    <div x-show="tab === 'profile'">
        @if (session('success_profile'))
            <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-xl" role="alert">
                {{ session('success_profile') }}
            </div>
        @endif

        <form action="{{ route('nguoihoc.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="md:col-span-1" x-data="{ photoName: null, photoPreview: null }">
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Ảnh đại diện</label>
                        <div class="mt-2">
                            <span class="inline-block h-32 w-32 rounded-full overflow-hidden bg-gray-100">
                                <template x-if="!photoPreview">
                                    <img src="{{ $user->nguoiHoc->AnhDaiDien ? asset('storage/' . $user->nguoiHoc->AnhDaiDien) : 'https://ui-avatars.com/api/?name=' . urlencode($user->nguoiHoc->HoTen) . '&background=random&size=128' }}" 
                                         alt="Ảnh đại diện" class="h-full w-full object-cover">
                                </template>
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" class="h-full w-full object-cover">
                                </template>
                            </span>
                        </div>
                        <input type="file" name="AnhDaiDien" class="hidden" x-ref="photo" x-on:change="
                            photoName = $refs.photo.files[0].name;
                            const reader = new FileReader();
                            reader.onload = (e) => { photoPreview = e.target.result; };
                            reader.readAsDataURL($refs.photo.files[0]);
                        ">
                        <button type="button" x-on:click.prevent="$refs.photo.click()" class="mt-4 px-4 py-2 bg-white border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Thay đổi ảnh
                        </button>
                        @error('AnhDaiDien') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Họ tên</label>
                            <input type="text" name="HoTen" value="{{ old('HoTen', $user->nguoiHoc->HoTen) }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                            @error('HoTen') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" value="{{ $user->Email }}" disabled class="w-full rounded-xl border-gray-200 bg-gray-100 text-gray-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                            <input type="text" name="SoDienThoai" value="{{ old('SoDienThoai', $user->SoDienThoai) }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                            @error('SoDienThoai') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ngày sinh</label>
                            <input type="date" name="NgaySinh" value="{{ old('NgaySinh', $user->nguoiHoc->NgaySinh ? \Carbon\Carbon::parse($user->nguoiHoc->NgaySinh)->format('Y-m-d') : '') }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Giới tính</label>
                            <select name="GioiTinh" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                <option value="Nam" {{ old('GioiTinh', $user->nguoiHoc->GioiTinh) == 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ old('GioiTinh', $user->nguoiHoc->GioiTinh) == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                <option value="Khác" {{ old('GioiTinh', $user->nguoiHoc->GioiTinh) == 'Khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Địa chỉ</label>
                            <input type="text" name="DiaChi" value="{{ old('DiaChi', $user->nguoiHoc->DiaChi) }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500" placeholder="Ví dụ: Q.1, TP.HCM">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-8 pt-6 border-t border-gray-100">
                    <button type="submit" class="px-8 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">
                        Lưu thay đổi hồ sơ
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div x-show="tab === 'password'" style="display: none;">
        @if (session('success_password'))
            <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-xl" role="alert">
                {{ session('success_password') }}
            </div>
        @endif

        <form action="{{ route('nguoihoc.profile.password.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <div class="max-w-md mx-auto">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Thay đổi mật khẩu</h3>
                    
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" required
                               class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 
                               @error('current_password') border-red-500 @enderror">
                        @error('current_password') 
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu mới</label>
                        <input type="password" name="password" required
                               class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500
                               @error('password') border-red-500 @enderror">
                        @error('password') 
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Xác nhận mật khẩu mới</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="flex justify-end mt-8 pt-6 border-t border-gray-100">
                        <button type="submit" class="px-8 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">
                            Lưu mật khẩu
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection