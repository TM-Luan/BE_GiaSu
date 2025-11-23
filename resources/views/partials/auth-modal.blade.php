{{-- Authentication Modal với hiệu ứng sliding --}}
<div id="authModal" class="auth-modal" style="display: none;">
    <div class="auth-modal-overlay" onclick="closeAuthModal()"></div>
    
    <div class="auth-container" id="authContainer">
        
        {{-- FORM ĐĂNG NHẬP --}}
        <div class="auth-form-container sign-in-container">
            <form method="POST" action="{{ route('login.post') }}" class="auth-form">
                @csrf
                <h1 class="auth-title">Đăng nhập</h1>
                
                {{-- Hiển thị lỗi --}}
                @if ($errors->any() && session('auth_panel') == 'login')
                    <div class="auth-error-box">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                
                <input type="email" 
                       name="Email" 
                       placeholder="Email" 
                       class="auth-input @error('Email') is-invalid @enderror"
                       value="{{ old('Email') }}" 
                       required>
                
                <input type="password" 
                       name="MatKhau" 
                       placeholder="Mật khẩu" 
                       class="auth-input @error('MatKhau') is-invalid @enderror"
                       required>
                
                <a href="#" class="auth-link" id="toForgotPassword">Quên mật khẩu?</a>
                
                <button type="submit" class="auth-button">Đăng nhập</button>
            </form>
        </div>

        {{-- FORM ĐĂNG KÝ --}}
        <div class="auth-form-container sign-up-container">
            <form method="POST" action="{{ route('register.post') }}" class="auth-form">
                @csrf
                <h1 class="auth-title">Tạo tài khoản</h1>
                
                {{-- Hiển thị lỗi --}}
                @if ($errors->any() && session('auth_panel') == 'register')
                    <div class="auth-error-box">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                
                <input type="text" 
                       name="HoTen" 
                       placeholder="Họ và tên" 
                       class="auth-input @error('HoTen') is-invalid @enderror"
                       value="{{ old('HoTen') }}" 
                       required>
                
                <input type="email" 
                       name="Email" 
                       placeholder="Email" 
                       class="auth-input @error('Email') is-invalid @enderror"
                       value="{{ old('Email') }}" 
                       required>
                
                <input type="text" 
                       name="SoDienThoai" 
                       placeholder="Số điện thoại (10-11 số)" 
                       class="auth-input @error('SoDienThoai') is-invalid @enderror"
                       value="{{ old('SoDienThoai') }}" 
                       pattern="[0-9]{10,11}"
                       required>
                
                <input type="password" 
                       name="MatKhau" 
                       placeholder="Mật khẩu (tối thiểu 6 ký tự)" 
                       class="auth-input @error('MatKhau') is-invalid @enderror"
                       minlength="6"
                       required>
                
                <input type="password" 
                       name="MatKhau_confirmation" 
                       placeholder="Xác nhận mật khẩu" 
                       class="auth-input"
                       minlength="6"
                       required>
                
                {{-- Radio buttons cho vai trò --}}
                <div class="auth-role-group">
                    <label class="auth-role-label">
                        <input type="radio" name="VaiTro" value="3" {{ old('VaiTro') == '3' ? 'checked' : '' }} required>
                        <span class="auth-role-box">
                            <svg class="auth-role-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <div class="auth-role-text">
                                <strong>Người học</strong>
                                <small>Tìm gia sư phù hợp</small>
                            </div>
                        </span>
                    </label>
                    
                    <label class="auth-role-label">
                        <input type="radio" name="VaiTro" value="2" {{ old('VaiTro') == '2' ? 'checked' : '' }} required>
                        <span class="auth-role-box">
                            <svg class="auth-role-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                            <div class="auth-role-text">
                                <strong>Gia sư</strong>
                                <small>Dạy và kiếm thu nhập</small>
                            </div>
                        </span>
                    </label>
                </div>
                
                <label class="auth-checkbox-label">
                    <input type="checkbox" name="agree_terms" value="1" required>
                    <span>Tôi đồng ý với điều khoản sử dụng</span>
                </label>
                
                <button type="submit" class="auth-button">Đăng ký</button>
            </form>
        </div>

        {{-- FORM QUÊN MẬT KHẨU --}}
        <div class="auth-form-container forgot-password-container" style="display: none;">
            <form class="auth-form" id="forgotPasswordForm">
                @csrf
                <h1 class="auth-title">Quên mật khẩu</h1>
                <p class="auth-description">Nhập email để nhận mã xác nhận</p>
                
                <input type="email" 
                       name="email" 
                       placeholder="Email của bạn" 
                       class="auth-input"
                       required>
                
                <button type="submit" class="auth-button">Gửi mã</button>
                <a href="#" class="auth-link" id="backToLogin">Quay lại đăng nhập</a>
            </form>
        </div>

        {{-- OVERLAY PANEL --}}
        <div class="auth-overlay-container">
            <div class="auth-overlay">
                <div class="auth-overlay-panel auth-overlay-left">
                    <h1 class="auth-overlay-title">Chào mừng trở lại!</h1>
                    <p class="auth-overlay-text">
                        Để giữ kết nối với chúng tôi, vui lòng đăng nhập bằng thông tin cá nhân của bạn
                    </p>
                    <button class="auth-button-ghost" id="signIn">Đăng nhập</button>
                </div>
                <div class="auth-overlay-panel auth-overlay-right">
                    <h1 class="auth-overlay-title">Xin chào!</h1>
                    <p class="auth-overlay-text">
                        Nhập thông tin cá nhân và bắt đầu hành trình với chúng tôi
                    </p>
                    <button class="auth-button-ghost" id="signUp">Đăng ký</button>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Script tự động mở modal khi có lỗi validation --}}
@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            @if(session('auth_panel') == 'login')
                openAuthModal('login');
            @elseif(session('auth_panel') == 'register')
                openAuthModal('register');
            @endif
        }, 100);
    });
</script>
@endif
