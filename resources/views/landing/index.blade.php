<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trung Tâm Gia Sư - Kết nối gia sư và học viên</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <i data-lucide="graduation-cap" class="w-8 h-8 text-blue-600 mr-2"></i>
                    <span class="text-xl font-bold text-gray-900">Trung Tâm Gia Sư</span>
                </div>

                <!-- Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="#gioi-thieu" class="text-gray-600 hover:text-blue-600 transition-colors">Giới thiệu</a>
                    <a href="#tinh-nang" class="text-gray-600 hover:text-blue-600 transition-colors">Tính năng</a>
                    <a href="#lien-he" class="text-gray-600 hover:text-blue-600 transition-colors">Liên hệ</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 font-medium transition-colors">
                        Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-md">
                        Đăng ký
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="pt-24 pb-16 bg-gradient-to-br from-blue-50 via-white to-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                        Kết nối 
                        <span class="text-blue-600">Gia sư</span> và 
                        <span class="text-blue-600">Học viên</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        Nền tảng tìm kiếm và quản lý gia sư chuyên nghiệp, giúp kết nối gia sư uy tín với học viên một cách nhanh chóng và hiệu quả.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('register') }}?role=student" class="bg-blue-600 text-white px-8 py-4 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg hover:shadow-xl text-center">
                            Tìm gia sư ngay
                        </a>
                        <a href="{{ route('register') }}?role=tutor" class="bg-white text-blue-600 px-8 py-4 rounded-xl font-bold border-2 border-blue-600 hover:bg-blue-50 transition-all text-center">
                            Trở thành gia sư
                        </a>
                    </div>
                </div>

                <!-- Right Image/Illustration -->
                <div class="relative">
                    <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-3xl p-8 shadow-2xl transform hover:scale-105 transition-transform duration-300">
                        <div class="bg-white rounded-2xl p-6 space-y-4">
                            <div class="flex items-center space-x-4">
                                <div class="bg-blue-100 p-3 rounded-full">
                                    <i data-lucide="book-open" class="w-6 h-6 text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Đa dạng môn học</p>
                                    <p class="text-sm text-gray-600">Toán, Văn, Anh, Lý, Hóa...</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="bg-green-100 p-3 rounded-full">
                                    <i data-lucide="users" class="w-6 h-6 text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Gia sư uy tín</p>
                                    <p class="text-sm text-gray-600">Đã qua kiểm duyệt</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="bg-orange-100 p-3 rounded-full">
                                    <i data-lucide="calendar" class="w-6 h-6 text-orange-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Lịch học linh hoạt</p>
                                    <p class="text-sm text-gray-600">Online & Offline</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="tinh-nang" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Tính năng nổi bật</h2>
                <p class="text-lg text-gray-600">Những gì chúng tôi mang lại cho bạn</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gradient-to-br from-blue-50 to-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-all border border-blue-100">
                    <div class="bg-blue-600 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="search" class="w-6 h-6 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Tìm kiếm thông minh</h3>
                    <p class="text-gray-600">Bộ lọc mạnh mẽ giúp tìm kiếm gia sư phù hợp theo môn học, khu vực, học phí...</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gradient-to-br from-green-50 to-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-all border border-green-100">
                    <div class="bg-green-600 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="shield-check" class="w-6 h-6 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Xác minh uy tín</h3>
                    <p class="text-gray-600">Hệ thống đánh giá và xác minh gia sư chuyên nghiệp, đảm bảo chất lượng giảng dạy.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gradient-to-br from-orange-50 to-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-all border border-orange-100">
                    <div class="bg-orange-600 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="calendar-check" class="w-6 h-6 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Quản lý lịch học</h3>
                    <p class="text-gray-600">Sắp xếp và theo dõi lịch học dễ dàng với lịch học thông minh.</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-gradient-to-br from-purple-50 to-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-all border border-purple-100">
                    <div class="bg-purple-600 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="video" class="w-6 h-6 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Học online linh hoạt</h3>
                    <p class="text-gray-600">Hỗ trợ học online qua video call, tiện lợi và tiết kiệm thời gian.</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-gradient-to-br from-pink-50 to-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-all border border-pink-100">
                    <div class="bg-pink-600 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="message-circle" class="w-6 h-6 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Hỗ trợ 24/7</h3>
                    <p class="text-gray-600">Đội ngũ hỗ trợ luôn sẵn sàng giải đáp mọi thắc mắc của bạn.</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-gradient-to-br from-cyan-50 to-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-all border border-cyan-100">
                    <div class="bg-cyan-600 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="wallet" class="w-6 h-6 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Thanh toán an toàn</h3>
                    <p class="text-gray-600">Hệ thống thanh toán bảo mật, minh bạch và tiện lợi.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section id="gioi-thieu" class="py-20 bg-gradient-to-br from-gray-50 to-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Cách thức hoạt động</h2>
                <p class="text-lg text-gray-600">Chỉ với 3 bước đơn giản</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="bg-blue-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6 shadow-lg">
                        1
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Đăng ký tài khoản</h3>
                    <p class="text-gray-600">Tạo tài khoản miễn phí chỉ trong vài giây với vai trò phù hợp.</p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="bg-blue-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6 shadow-lg">
                        2
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Tìm kiếm hoặc đăng tuyển</h3>
                    <p class="text-gray-600">Học viên tìm gia sư, gia sư tìm lớp học phù hợp với nhu cầu.</p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="bg-blue-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6 shadow-lg">
                        3
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Bắt đầu học</h3>
                    <p class="text-gray-600">Kết nối thành công và bắt đầu hành trình học tập hiệu quả.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-blue-600 to-blue-700">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                Sẵn sàng bắt đầu?
            </h2>
            <p class="text-xl text-blue-100 mb-8">
                Tham gia cùng hàng nghìn học viên và gia sư đang sử dụng nền tảng của chúng tôi
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-4 rounded-xl font-bold hover:bg-blue-50 transition-all shadow-lg text-center">
                    Đăng ký miễn phí
                </a>
                <a href="{{ route('login') }}" class="bg-transparent text-white px-8 py-4 rounded-xl font-bold border-2 border-white hover:bg-white hover:text-blue-600 transition-all text-center">
                    Đăng nhập ngay
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="lien-he" class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About -->
                <div>
                    <div class="flex items-center mb-4">
                        <i data-lucide="graduation-cap" class="w-6 h-6 text-blue-400 mr-2"></i>
                        <span class="text-lg font-bold text-white">Trung Tâm Gia Sư</span>
                    </div>
                    <p class="text-sm">
                        Nền tảng kết nối gia sư và học viên uy tín, chuyên nghiệp hàng đầu Việt Nam.
                    </p>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="text-white font-bold mb-4">Liên kết</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Về chúng tôi</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Điều khoản</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Chính sách</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h4 class="text-white font-bold mb-4">Hỗ trợ</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Trung tâm trợ giúp</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Liên hệ</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">FAQ</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="text-white font-bold mb-4">Liên hệ</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center">
                            <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                            contact@giasu.vn
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                            1900 xxxx
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="map-pin" class="w-4 h-4 mr-2"></i>
                            TP. Hồ Chí Minh
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm">
                <p>&copy; 2025 Trung Tâm Gia Sư. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
