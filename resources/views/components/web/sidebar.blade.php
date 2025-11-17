<nav class="w-64 bg-white h-screen border-r border-gray-100 flex flex-col justify-between sticky top-0">
    <div class="p-6">
        <!-- Logo & Brand -->
        <div class="flex items-center mb-10 gap-3">
            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white shadow-sm">
                <i data-lucide="graduation-cap" class="w-6 h-6"></i>
            </div>
            <span class="text-xl font-bold text-gray-800 tracking-tight">TutorApp</span>
        </div>

        @php
            $user = Auth::user();
            $isGiaSu = $user->phanquyen && $user->phanquyen->VaiTroID == 2;
            $isNguoiHoc = $user->phanquyen && $user->phanquyen->VaiTroID == 3;
        @endphp

        <!-- Menu -->
        <ul class="space-y-2">
            @if($isNguoiHoc)
                <!-- Menu cho Người học -->
                <li>
                    <a href="{{ route('nguoihoc.dashboard') }}" 
                       class="flex items-center px-4 py-3 rounded-xl font-medium transition-colors
                       {{ Request::routeIs('nguoihoc.dashboard') || Request::routeIs('nguoihoc.giasu.show') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="home" class="w-5 h-5 mr-3"></i>
                        Trang chủ
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('nguoihoc.lophoc.index') }}" 
                       class="flex items-center px-4 py-3 rounded-xl font-medium transition-colors
                       {{ Request::routeIs('nguoihoc.lophoc.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="book-open" class="w-5 h-5 mr-3"></i>
                        Lớp học của tôi
                    </a>
                </li>

                <li>
                    <a href="{{ route('nguoihoc.lichhoc.index') }}" 
                       class="flex items-center px-4 py-3 rounded-xl font-medium transition-colors
                       {{ Request::routeIs('nguoihoc.lichhoc.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="calendar-days" class="w-5 h-5 mr-3"></i>
                        Lịch học
                    </a>
                </li>

                <li>
                    <a href="{{ route('nguoihoc.profile.index') }}" 
                       class="flex items-center px-4 py-3 rounded-xl font-medium transition-colors
                       {{ Request::routeIs('nguoihoc.profile.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="user" class="w-5 h-5 mr-3"></i>
                        Thông tin cá nhân
                    </a>
                </li>
            @elseif($isGiaSu)
                <!-- Menu cho Gia sư -->
                <li>
                    <a href="{{ route('giasu.dashboard') }}" 
                       class="flex items-center px-4 py-3 rounded-xl font-medium transition-colors
                       {{ Request::routeIs('giasu.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="home" class="w-5 h-5 mr-3"></i>
                        Trang chủ
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('giasu.lophoc.index') }}" 
                       class="flex items-center px-4 py-3 rounded-xl font-medium transition-colors
                       {{ Request::routeIs('giasu.lophoc.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="book-open" class="w-5 h-5 mr-3"></i>
                        Lớp học của tôi
                    </a>
                </li>

                <li>
                    <a href="{{ route('giasu.lichhoc.index') }}" 
                       class="flex items-center px-4 py-3 rounded-xl font-medium transition-colors
                       {{ Request::routeIs('giasu.lichhoc.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="calendar-days" class="w-5 h-5 mr-3"></i>
                        Lịch học
                    </a>
                </li>

                <li>
                    <a href="{{ route('giasu.profile.index') }}" 
                       class="flex items-center px-4 py-3 rounded-xl font-medium transition-colors
                       {{ Request::routeIs('giasu.profile.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="user" class="w-5 h-5 mr-3"></i>
                        Thông tin cá nhân
                    </a>
                </li>
            @endif
        </ul>
    </div>

    
    <!-- Footer Sidebar -->
    <div class="p-6 border-t border-gray-50">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center justify-center w-full px-4 py-3 rounded-xl text-white font-semibold bg-blue-600 hover:bg-blue-700 transition-colors shadow-md shadow-blue-200">
                <i data-lucide="log-out" class="w-5 h-5 mr-3"></i>
                Đăng xuất
            </button>
        </form>
    </div>
</nav>