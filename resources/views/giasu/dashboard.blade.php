<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Gia sư</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold text-gray-800">Chào mừng Gia sư, {{ Auth::user()->Email }}!</h1>
        <p class="mt-2 text-gray-600">Đây là trang quản lý của bạn.</p>

        <!-- Nút Đăng xuất -->
        <form action="{{ route('logout') }}" method="POST" class="mt-4">
            @csrf
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                Đăng xuất
            </button>
        </form>
    </div>
</body>
</html>