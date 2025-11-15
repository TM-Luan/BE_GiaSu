<!DOCTYPE html>
<html lang="vi" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Trung tâm Gia sư</title>
    <!-- Tải Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tải Font Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen py-8">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-center text-gray-900 mb-6">
            Đăng ký Tài khoản
        </h2>

        <!-- Hiển thị lỗi (nếu có) -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4" role="alert">
                <strong class="font-bold">Có lỗi xảy ra!</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="list-disc ml-5">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Hiển thị thông báo thành công (nếu có) -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Hiển thị thông báo lỗi (nếu có) -->
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}" id="registerForm">
            @csrf

            <!-- Chọn vai trò -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Đăng ký với tư cách <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Radio Gia sư -->
                    <label class="relative flex flex-col items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition-all
                                  {{ old('VaiTro') == '2' ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-blue-400' }}">
                        <input type="radio" name="VaiTro" value="2" class="sr-only peer" 
                               {{ old('VaiTro') == '2' ? 'checked' : '' }} required>
                        <svg class="w-8 h-8 mb-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                        </svg>
                        <span class="text-sm font-semibold text-gray-700">Gia sư</span>
                        <div class="absolute top-2 right-2 w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-blue-500 peer-checked:bg-blue-500"></div>
                    </label>

                    <!-- Radio Học viên -->
                    <label class="relative flex flex-col items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition-all
                                  {{ old('VaiTro') == '3' ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-blue-400' }}">
                        <input type="radio" name="VaiTro" value="3" class="sr-only peer" 
                               {{ old('VaiTro') == '3' ? 'checked' : '' }} required>
                        <svg class="w-8 h-8 mb-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-gray-700">Học viên</span>
                        <div class="absolute top-2 right-2 w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-blue-500 peer-checked:bg-blue-500"></div>
                    </label>
                </div>
                @error('VaiTro')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Họ tên -->
            <div class="mb-4">
                <label for="HoTen" class="block text-sm font-medium text-gray-700 mb-2">
                    Họ và tên <span class="text-red-500">*</span>
                </label>
                <input type="text" name="HoTen" id="HoTen"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('HoTen') border-red-500 @enderror"
                       value="{{ old('HoTen') }}" required autocomplete="name" placeholder="Nguyễn Văn A">
                @error('HoTen')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Số điện thoại -->
            <div class="mb-4">
                <label for="SoDienThoai" class="block text-sm font-medium text-gray-700 mb-2">
                    Số điện thoại <span class="text-red-500">*</span>
                </label>
                <input type="tel" name="SoDienThoai" id="SoDienThoai"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('SoDienThoai') border-red-500 @enderror"
                       value="{{ old('SoDienThoai') }}" required pattern="[0-9]{10,11}" placeholder="0901234567">
                @error('SoDienThoai')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="Email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="Email" id="Email"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('Email') border-red-500 @enderror"
                       value="{{ old('Email') }}" required autocomplete="email" placeholder="email@example.com">
                @error('Email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mật khẩu -->
            <div class="mb-4">
                <label for="MatKhau" class="block text-sm font-medium text-gray-700 mb-2">
                    Mật khẩu <span class="text-red-500">*</span>
                </label>
                <input type="password" name="MatKhau" id="MatKhau"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('MatKhau') border-red-500 @enderror"
                       required autocomplete="new-password" minlength="6" placeholder="Tối thiểu 6 ký tự">
                @error('MatKhau')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Xác nhận mật khẩu -->
            <div class="mb-4">
                <label for="MatKhau_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Xác nhận mật khẩu <span class="text-red-500">*</span>
                </label>
                <input type="password" name="MatKhau_confirmation" id="MatKhau_confirmation"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       required autocomplete="new-password" minlength="6" placeholder="Nhập lại mật khẩu">
                <p id="passwordMatchError" class="text-red-500 text-xs mt-1 hidden">Mật khẩu không khớp!</p>
            </div>

            <!-- Đồng ý điều khoản -->
            <div class="mb-6">
                <div class="flex items-start">
                    <input id="agree_terms" name="agree_terms" type="checkbox" required
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1">
                    <label for="agree_terms" class="ml-2 block text-sm text-gray-900">
                        Tôi đồng ý với <a href="#" class="text-blue-600 hover:text-blue-500 font-medium">Điều khoản sử dụng</a> 
                        và <a href="#" class="text-blue-600 hover:text-blue-500 font-medium">Chính sách bảo mật</a>
                        <span class="text-red-500">*</span>
                    </label>
                </div>
                @error('agree_terms')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nút Đăng ký -->
            <div>
                <button type="submit" id="submitBtn"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    Đăng ký
                </button>
            </div>
        </form>

        <!-- Link Đăng nhập -->
        <p class="text-sm text-center text-gray-600 mt-6">
            Đã có tài khoản?
            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                Đăng nhập ngay
            </a>
        </p>
    </div>

    <script>
        // Kiểm tra mật khẩu khớp
        const password = document.getElementById('MatKhau');
        const confirmPassword = document.getElementById('MatKhau_confirmation');
        const errorMsg = document.getElementById('passwordMatchError');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('registerForm');

        function checkPasswordMatch() {
            if (confirmPassword.value && password.value !== confirmPassword.value) {
                errorMsg.classList.remove('hidden');
                submitBtn.disabled = true;
            } else {
                errorMsg.classList.add('hidden');
                submitBtn.disabled = false;
            }
        }

        password.addEventListener('input', checkPasswordMatch);
        confirmPassword.addEventListener('input', checkPasswordMatch);

        // Kiểm tra form trước khi submit
        form.addEventListener('submit', function(e) {
            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                errorMsg.classList.remove('hidden');
                confirmPassword.focus();
            }
        });
    </script>
</body>
</html>
