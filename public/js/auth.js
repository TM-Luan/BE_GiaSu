/**
 * Auth Modal JavaScript
 * Xử lý chuyển đổi giữa Login/Register/Forgot Password
 */

// Hàm mở modal
function openAuthModal(panel = 'login') {
    console.log('openAuthModal called with panel:', panel);
    const authModal = document.getElementById('authModal');
    const authContainer = document.getElementById('authContainer');
    
    if (!authModal) {
        console.error('authModal not found!');
        return;
    }
    
    if (!authContainer) {
        console.error('authContainer not found!');
        return;
    }
    
    console.log('Opening modal...');
    authModal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    if (panel === 'register') {
        authContainer.classList.add('right-panel-active');
    } else {
        authContainer.classList.remove('right-panel-active');
    }
    
    console.log('Modal opened successfully');
}

// Hàm đóng modal
function closeAuthModal() {
    const authModal = document.getElementById('authModal');
    if (!authModal) return;
    
    authModal.style.display = 'none';
    document.body.style.overflow = 'auto';
    
    const authContainer = document.getElementById('authContainer');
    if (authContainer) {
        authContainer.classList.remove('right-panel-active');
    }
    
    if (typeof hideForgotPassword === 'function') {
        hideForgotPassword();
    }
}

// Khởi tạo tất cả sau khi DOM loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing auth modal...');
    
    // Auto open modal từ URL query
    const urlParams = new URLSearchParams(window.location.search);
    const openParam = urlParams.get('open');
    
    console.log('URL open param:', openParam);
    
    if (openParam === 'login' || openParam === 'register') {
        console.log('Auto opening modal for:', openParam);
        setTimeout(function() {
            openAuthModal(openParam);
            // Remove query param sau khi mở modal
            const url = new URL(window.location);
            url.searchParams.delete('open');
            window.history.replaceState({}, '', url);
        }, 100); // Delay 100ms để chắc chắn DOM đã render
    }
    
    // Event listeners
    const authContainer = document.getElementById('authContainer');
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const toForgotPassword = document.getElementById('toForgotPassword');
    const backToLogin = document.getElementById('backToLogin');

    // Chuyển sang Sign Up
    if (signUpButton) {
        signUpButton.addEventListener('click', () => {
            authContainer.classList.add('right-panel-active');
        });
    }

    // Chuyển sang Sign In
    if (signInButton) {
        signInButton.addEventListener('click', () => {
            authContainer.classList.remove('right-panel-active');
        });
    }

    // Hiện form Forgot Password
    if (toForgotPassword) {
        toForgotPassword.addEventListener('click', (e) => {
            e.preventDefault();
            showForgotPassword();
        });
    }

    // Quay lại Login từ Forgot Password
    if (backToLogin) {
        backToLogin.addEventListener('click', (e) => {
            e.preventDefault();
            hideForgotPassword();
        });
    }

    // Đóng modal khi click overlay
    const authModal = document.getElementById('authModal');
    if (authModal) {
        authModal.addEventListener('click', (e) => {
            if (e.target === authModal) {
                closeAuthModal();
            }
        });
    }

    // Đóng modal khi nhấn ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeAuthModal();
        }
    });
});

// Hiện form Forgot Password
function showForgotPassword() {
    const signInContainer = document.querySelector('.sign-in-container');
    const forgotPasswordContainer = document.querySelector('.forgot-password-container');
    
    signInContainer.style.opacity = '0';
    setTimeout(() => {
        signInContainer.style.display = 'none';
        forgotPasswordContainer.style.display = 'flex';
        setTimeout(() => {
            forgotPasswordContainer.style.opacity = '1';
        }, 50);
    }, 300);
}

// Ẩn form Forgot Password và hiện lại Login
function hideForgotPassword() {
    const signInContainer = document.querySelector('.sign-in-container');
    const forgotPasswordContainer = document.querySelector('.forgot-password-container');
    
    forgotPasswordContainer.style.opacity = '0';
    setTimeout(() => {
        forgotPasswordContainer.style.display = 'none';
        signInContainer.style.display = 'flex';
        setTimeout(() => {
            signInContainer.style.opacity = '1';
        }, 50);
    }, 300);
}

// Xử lý form Forgot Password khi DOM loaded
document.addEventListener('DOMContentLoaded', function() {
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(forgotPasswordForm);
            const email = formData.get('email');
            
            // TODO: Gửi request đến backend để xử lý forgot password
            console.log('Forgot password for:', email);
            
            // Ví dụ hiển thị thông báo
            alert('Chức năng quên mật khẩu đang được phát triển. Email: ' + email);
        });
    }
});
