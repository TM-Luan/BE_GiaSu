<!DOCTYPE html>
<html lang="vi" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Trung tâm Gia sư</title>
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
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-center text-gray-900 mb-6">
            Đăng nhập Tài khoản
        </h2>

        <!-- Hiển thị lỗi chung (nếu có) -->
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

        <form method="POST" action="{{ route('login.post') }}">
            @csrf <!-- Bắt buộc -->

            <!-- Email -->
            <div class="mb-4">
                <label for="Email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>
                <input type="email" name="Email" id="Email"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('Email') border-red-500 @enderror"
                       value="{{ old('Email') }}" required autocomplete="email" autofocus>
            </div>

            <!-- Mật khẩu -->
            <div class="mb-4">
                <label for="MatKhau" class="block text-sm font-medium text-gray-700 mb-2">
                    Mật khẩu
                </label>
                <input type="password" name="MatKhau" id="MatKhau"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('MatKhau') border-red-500 @enderror"
                       required autocomplete="current-password">
            </div>

            <!-- Ghi nhớ & Quên mật khẩu -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Ghi nhớ tôi
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                        Quên mật khẩu?
                    </a>
                </div>
            </div>

            <!-- Nút Đăng nhập -->
            <div>
                <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Đăng nhập
                </button>
            </div>
        </form>

        <!-- Link Đăng ký -->
        <p class="text-sm text-center text-gray-600 mt-6">
            Chưa có tài khoản?
            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                Đăng ký ngay
            </a>
        </p>
    </div>
</body>
</html>