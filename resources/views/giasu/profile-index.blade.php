@extends('layouts.web')

@section('title', 'Thông tin cá nhân')

@section('content')
<div class="max-w-6xl mx-auto" x-data="{ tab: '{{ session('success_password') || $errors->has('current_password') ? 'password' : 'profile' }}' }">

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Cài đặt tài khoản</h1>
            <p class="text-gray-500 mt-2 text-base font-medium">Quản lý hồ sơ, thông tin cá nhân và bảo mật.</p>
        </div>
        <div class="flex items-center gap-4">
            @if($danhGiaStats && $danhGiaStats->tong_so_danh_gia > 0)
                <div class="flex items-center gap-2 px-4 py-2 bg-yellow-50 border border-yellow-200 rounded-full">
                    <i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-yellow-400"></i>
                    <span class="font-bold text-yellow-800">{{ $danhGiaStats->diem_trung_binh }}</span>
                    <span class="text-yellow-600 text-sm">({{ $danhGiaStats->tong_so_danh_gia }} đánh giá)</span>
                </div>
            @endif
            
            @if($user->giaSu->TrangThai == 2)
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-yellow-100 text-yellow-800">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Chờ duyệt
                </span>
            @elseif($user->giaSu->TrangThai == 1)
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-green-100 text-green-800">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Đã duyệt
                </span>
            @endif
        </div>
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
            <div class="mb-4 bg-blue-100 border border-blue-300 text-blue-800 px-4 py-3 rounded-xl" role="alert">
                {{ session('success_profile') }}
            </div>
        @endif

        <form action="{{ route('giasu.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="md:col-span-1" x-data="{ photoName: null, photoPreview: null }">
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Ảnh đại diện</label>
                        <div class="mt-2">
                            <span class="inline-block h-32 w-32 rounded-full overflow-hidden bg-gray-100">
                                <template x-if="!photoPreview">
                                    @php
                                        // ĐỒNG BỘ MOBILE: Hỗ trợ cả URL ImgBB và storage path
                                        $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user->giaSu->HoTen) . '&background=random&size=128';
                                        if ($user->giaSu->AnhDaiDien) {
                                            if (filter_var($user->giaSu->AnhDaiDien, FILTER_VALIDATE_URL)) {
                                                $avatarUrl = $user->giaSu->AnhDaiDien;
                                            } else {
                                                $avatarUrl = asset('storage/' . $user->giaSu->AnhDaiDien);
                                            }
                                        }
                                    @endphp
                                    <img src="{{ $avatarUrl }}" 
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
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Họ tên</label>
                            <input type="text" name="HoTen" value="{{ old('HoTen', $user->giaSu->HoTen) }}" 
                                   class="w-full px-4 py-3 text-base rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            @error('HoTen') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email" value="{{ $user->Email }}" disabled 
                                   class="w-full px-4 py-3 text-base rounded-xl border-2 border-gray-300 bg-gray-100 text-gray-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Số điện thoại</label>
                            <input type="text" name="SoDienThoai" value="{{ old('SoDienThoai', $user->SoDienThoai) }}" 
                                   class="w-full px-4 py-3 text-base rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            @error('SoDienThoai') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Ngày sinh</label>
                            <input type="date" name="NgaySinh" value="{{ old('NgaySinh', $user->giaSu->NgaySinh ? \Carbon\Carbon::parse($user->giaSu->NgaySinh)->format('Y-m-d') : '') }}" 
                                   class="w-full px-4 py-3 text-base rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Giới tính</label>
                            <select name="GioiTinh" 
                                    class="w-full px-4 py-3 text-base rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                <option value="Nam" {{ old('GioiTinh', $user->giaSu->GioiTinh) == 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ old('GioiTinh', $user->giaSu->GioiTinh) == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                <option value="Khác" {{ old('GioiTinh', $user->giaSu->GioiTinh) == 'Khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Bằng cấp</label>
                            <input type="text" name="BangCap" value="{{ old('BangCap', $user->giaSu->BangCap) }}" 
                                   class="w-full px-4 py-3 text-base rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                                   placeholder="VD: Cử nhân, Thạc sĩ...">
                            @error('BangCap') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Trường đào tạo</label>
                            <input type="text" name="TruongDaoTao" value="{{ old('TruongDaoTao', $user->giaSu->TruongDaoTao) }}" 
                                   class="w-full px-4 py-3 text-base rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                                   placeholder="VD: Đại học Khoa học Tự nhiên TP.HCM">
                            @error('TruongDaoTao') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Chuyên ngành</label>
                            <input type="text" name="ChuyenNganh" value="{{ old('ChuyenNganh', $user->giaSu->ChuyenNganh) }}" 
                                   class="w-full px-4 py-3 text-base rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                                   placeholder="VD: Toán học, Vật lý...">
                            @error('ChuyenNganh') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Thành tích</label>
                            <textarea name="ThanhTich" rows="3" 
                                      class="w-full px-4 py-3 text-base rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                                      placeholder="Các giải thưởng, chứng chỉ hoặc thành tích đạt được...">{{ old('ThanhTich', $user->giaSu->ThanhTich) }}</textarea>
                            @error('ThanhTich') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kinh nghiệm (năm)</label>
                            <input type="number" name="KinhNghiem" value="{{ old('KinhNghiem', $user->giaSu->KinhNghiem) }}" min="0" 
                                   class="w-full px-4 py-3 text-base rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            @error('KinhNghiem') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Môn dạy</label>
                            <select name="MonID" 
                                    class="w-full px-4 py-3 text-base rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                <option value="">Chọn môn học</option>
                                @foreach($monHocs as $mon)
                                    <option value="{{ $mon->MonID }}" {{ old('MonID', $user->giaSu->MonID) == $mon->MonID ? 'selected' : '' }}>
                                        {{ $mon->TenMon }}
                                    </option>
                                @endforeach
                            </select>
                            @error('MonID') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Địa chỉ</label>
                            <input type="text" name="DiaChi" value="{{ old('DiaChi', $user->giaSu->DiaChi) }}" 
                                   class="w-full px-4 py-3 text-base rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                                   placeholder="Ví dụ: Q.1, TP.HCM">
                            @error('DiaChi') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Document Upload Section -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Hồ sơ và giấy tờ</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- CCCD Front -->
                        <div x-data="{ preview: null }">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">CCCD/CMND (Mặt trước)</label>
                            <div class="mt-2">
                                <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-4 hover:border-blue-400 transition-colors">
                                    <template x-if="!preview">
                                        @if($user->giaSu->AnhCCCD_MatTruoc)
                                            @php
                                                $cccdFrontUrl = filter_var($user->giaSu->AnhCCCD_MatTruoc, FILTER_VALIDATE_URL) 
                                                    ? $user->giaSu->AnhCCCD_MatTruoc 
                                                    : asset('storage/' . $user->giaSu->AnhCCCD_MatTruoc);
                                            @endphp
                                            <img src="{{ $cccdFrontUrl }}" 
                                                 alt="CCCD mặt trước" class="w-full h-32 object-cover rounded-lg">
                                        @else
                                            <div class="flex flex-col items-center justify-center h-32 text-gray-400">
                                                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span class="text-sm">Chưa tải lên</span>
                                            </div>
                                        @endif
                                    </template>
                                    <template x-if="preview">
                                        <img :src="preview" class="w-full h-32 object-cover rounded-lg">
                                    </template>
                                </div>
                            </div>
                            <input type="file" name="AnhCCCD_MatTruoc" class="hidden" x-ref="cccdFront" x-on:change="
                                const reader = new FileReader();
                                reader.onload = (e) => { preview = e.target.result; };
                                reader.readAsDataURL($refs.cccdFront.files[0]);
                            ">
                            <button type="button" x-on:click.prevent="$refs.cccdFront.click()" 
                                    class="mt-3 w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                Tải lên
                            </button>
                            @error('AnhCCCD_MatTruoc') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- CCCD Back -->
                        <div x-data="{ preview: null }">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">CCCD/CMND (Mặt sau)</label>
                            <div class="mt-2">
                                <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-4 hover:border-blue-400 transition-colors">
                                    <template x-if="!preview">
                                        @if($user->giaSu->AnhCCCD_MatSau)
                                            @php
                                                $cccdBackUrl = filter_var($user->giaSu->AnhCCCD_MatSau, FILTER_VALIDATE_URL) 
                                                    ? $user->giaSu->AnhCCCD_MatSau 
                                                    : asset('storage/' . $user->giaSu->AnhCCCD_MatSau);
                                            @endphp
                                            <img src="{{ $cccdBackUrl }}" 
                                                 alt="CCCD mặt sau" class="w-full h-32 object-cover rounded-lg">
                                        @else
                                            <div class="flex flex-col items-center justify-center h-32 text-gray-400">
                                                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span class="text-sm">Chưa tải lên</span>
                                            </div>
                                        @endif
                                    </template>
                                    <template x-if="preview">
                                        <img :src="preview" class="w-full h-32 object-cover rounded-lg">
                                    </template>
                                </div>
                            </div>
                            <input type="file" name="AnhCCCD_MatSau" class="hidden" x-ref="cccdBack" x-on:change="
                                const reader = new FileReader();
                                reader.onload = (e) => { preview = e.target.result; };
                                reader.readAsDataURL($refs.cccdBack.files[0]);
                            ">
                            <button type="button" x-on:click.prevent="$refs.cccdBack.click()" 
                                    class="mt-3 w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                Tải lên
                            </button>
                            @error('AnhCCCD_MatSau') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Degree Certificate -->
                        <div x-data="{ preview: null }">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Bằng cấp</label>
                            <div class="mt-2">
                                <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-4 hover:border-blue-400 transition-colors">
                                    <template x-if="!preview">
                                        @if($user->giaSu->AnhBangCap)
                                            @php
                                                $degreeUrl = filter_var($user->giaSu->AnhBangCap, FILTER_VALIDATE_URL) 
                                                    ? $user->giaSu->AnhBangCap 
                                                    : asset('storage/' . $user->giaSu->AnhBangCap);
                                            @endphp
                                            <img src="{{ $degreeUrl }}" 
                                                 alt="Bằng cấp" class="w-full h-32 object-cover rounded-lg">
                                        @else
                                            <div class="flex flex-col items-center justify-center h-32 text-gray-400">
                                                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span class="text-sm">Chưa tải lên</span>
                                            </div>
                                        @endif
                                    </template>
                                    <template x-if="preview">
                                        <img :src="preview" class="w-full h-32 object-cover rounded-lg">
                                    </template>
                                </div>
                            </div>
                            <input type="file" name="AnhBangCap" class="hidden" x-ref="degree" x-on:change="
                                const reader = new FileReader();
                                reader.onload = (e) => { preview = e.target.result; };
                                reader.readAsDataURL($refs.degree.files[0]);
                            ">
                            <button type="button" x-on:click.prevent="$refs.degree.click()" 
                                    class="mt-3 w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                Tải lên
                            </button>
                            @error('AnhBangCap') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
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
            <div class="mb-4 bg-blue-100 border border-blue-300 text-blue-800 px-4 py-3 rounded-xl" role="alert">
                {{ session('success_password') }}
            </div>
        @endif

        <form action="{{ route('giasu.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="update_type" value="password">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <div class="max-w-md mx-auto">
                    <h3 class="text-xl font-bold text-gray-900 mb-8">Thay đổi mật khẩu</h3>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" required
                               class="w-full px-4 py-3 text-base rounded-xl border-2 
                               @error('current_password') border-red-500 @else border-gray-300 @enderror
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        @error('current_password') 
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p> 
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mật khẩu mới</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-3 text-base rounded-xl border-2
                               @error('password') border-red-500 @else border-gray-300 @enderror
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        @error('password') 
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p> 
                        @enderror
                        <p class="text-xs text-gray-500 mt-2">Mật khẩu phải có ít nhất 8 ký tự</p>
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Xác nhận mật khẩu mới</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-4 py-3 text-base rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>

                    <button type="submit" class="w-full px-6 py-4 text-base rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">
                        Cập nhật mật khẩu
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
