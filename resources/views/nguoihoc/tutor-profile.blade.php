@extends('layouts.web')

@section('title', 'Hồ sơ gia sư ' . $gs->HoTen)

@section('content')
<div class="max-w-6xl mx-auto" x-data="{ modalOpen: false }">
    
    {{-- Breadcrumb --}}
    <div class="mb-6">
        <a href="{{ route('nguoihoc.dashboard') }}" class="inline-flex items-center text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Quay lại danh sách
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        {{-- CỘT TRÁI: THÔNG TIN CÁ NHÂN --}}
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center">
                <div class="relative mb-3">
                    {{-- FIX: Sử dụng mb_substr để lấy ký tự đầu tiên của tên Tiếng Việt --}}
                    <img src="{{ $gs->AnhDaiDien ? asset($gs->AnhDaiDien) : 'https://placehold.co/100x100/E0E7FF/3B82F6?text=' . mb_substr($gs->HoTen, 0, 1, 'UTF-8') . '&font=roboto' }}" 
                         alt="{{ $gs->HoTen }}"
                         class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md">
                </div>
                
                <h1 class="text-xl font-bold text-gray-900 text-center">{{ $gs->HoTen }}</h1>
                <p class="text-gray-500 text-sm mb-3 text-center">{{ $gs->KinhNghiem ?? 'Chưa cập nhật kinh nghiệm' }}</p>
                
                {{-- KHỐI HIỂN THỊ ĐIỂM SAO TỔNG QUAN --}}
                <div class="flex items-center gap-1 mb-4">
                    @php
                        // Lấy điểm trung bình từ Controller truyền sang hoặc tính toán từ quan hệ
                        $avgRating = $gs->rating_average ?? 0;
                        $countRating = $gs->rating_count ?? $gs->danh_gia_count ?? 0;
                        $roundedRating = floor($avgRating);
                    @endphp
                    
                    <div class="flex items-center">
                        @for ($i = 1; $i <= 5; $i++)
                            <i data-lucide="star" class="w-4 h-4 {{ $i <= $roundedRating ? 'text-yellow-500 fill-yellow-400' : 'text-gray-300' }}"></i>
                        @endfor
                    </div>
                    
                    <span class="font-bold text-gray-800 ml-2">
                        {{ $avgRating > 0 ? number_format($avgRating, 1) : '---' }}
                    </span>
                    <span class="text-gray-400 text-sm">({{ $countRating }} đánh giá)</span>
                </div>
                
                <button type="button" @click="modalOpen = true" class="w-full px-4 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200">
                    Mời dạy ngay
                </button>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 border-b pb-2">Thông tin chi tiết</h3>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Khu vực</p>
                    <p class="font-medium text-gray-800">{{ $gs->DiaChi ?? 'Chưa cập nhật' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Học phí tham khảo</p>
                    <p class="font-bold text-lg text-green-600">{{ number_format($gs->HocPhi ?? 0) }}đ/buổi</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Trường đào tạo</p>
                    <p class="font-medium text-gray-800">{{ $gs->TruongDaoTao ?? 'Chưa cập nhật' }}</p>
                </div>
            </div>
        </div>
        
        {{-- CỘT PHẢI: CHI TIẾT & ĐÁNH GIÁ --}}
        <div class="md:col-span-2 space-y-6">
            {{-- Chuyên môn --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="flex items-center text-lg font-bold text-gray-900 mb-4">
                    <i data-lucide="book-check" class="w-5 h-5 mr-2 text-blue-500"></i>
                    Chuyên môn & Kỹ năng
                </h3>
                <span class="inline-block bg-blue-50 text-blue-700 text-sm font-semibold px-3 py-1 rounded-full mb-4">
                    {{ $gs->ChuyenNganh ?? 'Chưa cập nhật' }}
                </span>
                
                <h4 class="font-semibold text-gray-800 mb-2">Giới thiệu bản thân</h4>
                <div class="prose prose-sm text-gray-600">
                    {!! nl2br(e($gs->ThanhTich)) !!}
                </div>
            </div>
            
            {{-- Bằng cấp --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="flex items-center text-lg font-bold text-gray-900 mb-4">
                    <i data-lucide="award" class="w-5 h-5 mr-2 text-orange-500"></i>
                    Bằng cấp & Chứng chỉ
                </h3>
                <div>
                    @if($gs->AnhBangCap)
                        <img src="{{ asset($gs->AnhBangCap) }}" class="rounded-lg border border-gray-200 max-h-64 object-contain" alt="Bằng cấp">
                    @else
                        <p class="text-gray-400 italic">Gia sư chưa cập nhật ảnh bằng cấp.</p>
                    @endif
                </div>
            </div>
            
            {{-- Danh sách Đánh giá --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                 <h3 class="flex items-center text-lg font-bold text-gray-900 mb-4">
                    <i data-lucide="message-square" class="w-5 h-5 mr-2 text-green-500"></i>
                    Đánh giá từ học viên ({{ $countRating }})
                </h3>
                <div class="space-y-4 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($gs->danhGia as $dg)
                        <div class="flex gap-3 border-b border-gray-100 pb-4 last:border-b-0">
                            <img src="https://ui-avatars.com/api/?name={{ $dg->taiKhoan->nguoiHoc->HoTen ?? 'Học viên' }}&background=random" class="w-10 h-10 rounded-full">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <h5 class="font-semibold">{{ $dg->taiKhoan->nguoiHoc->HoTen ?? 'Học viên ẩn danh' }}</h5>
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i data-lucide="star" class="w-3 h-3 {{ $i <= $dg->DiemSo ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">{{ $dg->BinhLuan ?: 'Không có nhận xét chi tiết.' }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($dg->NgayDanhGia)->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                                <i data-lucide="message-square-off" class="w-6 h-6 text-gray-400"></i>
                            </div>
                            <p class="text-gray-500">Chưa có đánh giá nào cho gia sư này.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Form Đánh giá --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 scroll-mt-20" id="rating-section">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i data-lucide="star" class="w-5 h-5 mr-2 text-yellow-500"></i>
                    Đánh giá của bạn
                </h3>

                {{-- FORM NHẬP ĐÁNH GIÁ --}}
                <div id="rating-form">
                    <div class="flex justify-center gap-2 mb-4" id="star-rating">
                        @for($i=1; $i<=5; $i++)
                            <i data-lucide="star" class="star-icon w-8 h-8 text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors" data-rating="{{ $i }}"></i>
                        @endfor
                    </div>
                    
                    <p class="text-center text-sm font-semibold mb-4 text-gray-500" id="rating-text">Chọn số sao để đánh giá</p>
                    
                    {{-- CẢNH BÁO ĐÁNH GIÁ THẤP --}}
                    <div id="low-rating-warning" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm p-3 rounded-lg mb-4 flex items-start">
                        <i data-lucide="alert-triangle" class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span>Vui lòng nhập lý do cụ thể nếu bạn đánh giá 1 hoặc 2 sao để chúng tôi cải thiện chất lượng.</span>
                    </div>
                    
                    <textarea id="rating-comment" 
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none text-sm"
                              placeholder="Chia sẻ trải nghiệm học tập của bạn với gia sư này..."
                              rows="4"
                              maxlength="1000"></textarea>
                    
                    <div class="text-xs text-gray-400 text-right mb-4" id="char-count">0/1000</div>
                    
                    <button type="button" 
                            id="submit-rating-btn"
                            class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed"
                            disabled>
                        Gửi đánh giá
                    </button>
                </div>

                {{-- HIỂN THỊ ĐÁNH GIÁ CŨ (Ẩn mặc định) --}}
                <div id="existing-rating" class="hidden">
                    <div class="mb-4">
                        <div class="flex justify-center gap-1 mb-2" id="existing-stars"></div>
                        <p class="text-center text-sm font-semibold text-gray-700" id="existing-rating-text"></p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-xl p-4 mb-4 relative">
                        <div class="absolute top-0 left-4 -mt-2 w-4 h-4 bg-gray-50 transform rotate-45"></div>
                        <p class="text-sm text-gray-700" id="existing-comment"></p>
                        <p class="text-xs text-gray-400 mt-2 italic" id="existing-date"></p>
                    </div>
                    
                    <button type="button" 
                            id="edit-rating-btn"
                            class="w-full py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                        Chỉnh sửa đánh giá
                    </button>
                    
                    <p class="text-xs text-center text-gray-400 mt-2" id="edit-limit-text"></p>
                </div>
            </div>
        </div>
        
    </div>
    
    {{-- MODAL MỜI DẠY (Alpine.js) --}}
    <div x-show="modalOpen" 
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 backdrop-blur-sm" 
         style="display: none;">
        
        <div @click.away="modalOpen = false" 
             class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 relative"
             x-show="modalOpen" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
            
            <button @click="modalOpen = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            <h3 class="text-xl font-bold text-gray-900 mb-2">Mời gia sư {{ $gs->HoTen }}</h3>
            <p class="text-gray-500 mb-6 text-sm">Chọn lớp học bạn muốn gửi lời mời giảng dạy:</p>

            <form action="{{ route('nguoihoc.moi_day') }}" method="POST">
                @csrf
                <input type="hidden" name="gia_su_id" value="{{ $gs->GiaSuID }}">
                
                <div class="space-y-3 max-h-60 overflow-y-auto pr-2 custom-scrollbar mb-6">
                    @forelse($myClasses as $lop)
                        <label class="block p-3 border border-gray-200 rounded-xl hover:bg-blue-50 hover:border-blue-300 transition-colors cursor-pointer group">
                            <div class="flex items-start">
                                <input type="radio" name="lop_yeu_cau_id" value="{{ $lop->LopYeuCauID }}" class="mt-1 w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <div class="ml-3">
                                    <p class="font-semibold text-gray-800 group-hover:text-blue-700">
                                        {{ $lop->monHoc->TenMon ?? 'Môn học' }} - {{ $lop->khoiLop->BacHoc ?? 'Lớp' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $lop->HinhThuc }} • <span class="text-green-600 font-medium">{{ number_format($lop->HocPhi) }}đ/buổi</span>
                                    </p>
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="text-center p-6 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                            <i data-lucide="folder-open" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                            <p class="font-medium text-gray-700 text-sm">Bạn chưa có lớp học nào phù hợp.</p>
                            <a href="{{ route('nguoihoc.lophoc.create') }}" class="inline-block mt-3 px-4 py-2 bg-blue-100 text-blue-700 text-xs font-bold rounded-lg hover:bg-blue-200">
                                + Tạo lớp học mới
                            </a>
                        </div>
                    @endforelse
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="modalOpen = false" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors text-sm">
                        Đóng
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-md shadow-blue-200 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            @if($myClasses->isEmpty()) disabled @endif>
                        Gửi lời mời
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Khởi tạo Lucide Icons nếu chưa load
    if (typeof lucide !== 'undefined') { lucide.createIcons(); }

    const ratingSection = document.getElementById('rating-section');
    if (!ratingSection) return;

    // 2. Variables
    const giaSuId = {{ $gs->GiaSuID }};
    let currentRating = 0;
    let existingRating = null;
    let canEdit = true;
    
    // Elements
    const starIcons = document.querySelectorAll('.star-icon');
    const ratingText = document.getElementById('rating-text');
    const commentField = document.getElementById('rating-comment');
    const charCount = document.getElementById('char-count');
    const submitBtn = document.getElementById('submit-rating-btn');
    const ratingForm = document.getElementById('rating-form');
    const existingRatingDiv = document.getElementById('existing-rating');
    const editBtn = document.getElementById('edit-rating-btn');
    const lowRatingWarning = document.getElementById('low-rating-warning');
    
    const ratingTexts = {
        1: 'Không hài lòng', 
        2: 'Tạm được', 
        3: 'Hài lòng', 
        4: 'Rất hài lòng', 
        5: 'Tuyệt vời!'
    };

    // 3. Init: Check Rating
    checkExistingRating();

    // 4. Event Listeners
    starIcons.forEach(star => {
        star.addEventListener('click', function() {
            currentRating = parseInt(this.getAttribute('data-rating'));
            updateStars();
            updateRatingText();
            updateSubmitButton();
        });

        star.addEventListener('mouseenter', function() {
            const hoverRating = parseInt(this.getAttribute('data-rating'));
            starIcons.forEach((s, index) => {
                const hoverIdx = index + 1;
                s.classList.remove('text-gray-300', 'text-yellow-400', 'text-yellow-300');
                if (hoverIdx <= hoverRating) s.classList.add('text-yellow-300');
                else s.classList.add('text-gray-300');
            });
        });

        star.addEventListener('mouseleave', function() { updateStars(); });
    });

    commentField.addEventListener('input', function() {
        charCount.textContent = this.value.length + '/1000';
        updateSubmitButton();
    });

    // 5. Logic Button Submit & Validation
    function updateSubmitButton() {
        const comment = commentField.value.trim();
        let shouldDisable = currentRating === 0;

        // Nếu 1-2 sao mà chưa có comment thì hiện cảnh báo và disable nút
        if (currentRating > 0 && currentRating <= 2 && comment.length === 0) {
            lowRatingWarning.classList.remove('hidden');
            shouldDisable = true;
        } else {
            lowRatingWarning.classList.add('hidden');
        }
        submitBtn.disabled = shouldDisable;
    }

    // 6. Handle Submit
    submitBtn.addEventListener('click', async function() {
        if (currentRating === 0) return alert('Vui lòng chọn số sao');
        
        const comment = commentField.value.trim();
        if (currentRating <= 2 && comment.length === 0) {
            alert('Vui lòng nhập lý do đánh giá thấp.');
            return commentField.focus();
        }
        
        if (!confirm(existingRating ? 'Cập nhật đánh giá?' : 'Gửi đánh giá này?')) return;

        submitBtn.disabled = true;
        submitBtn.textContent = 'Đang gửi...';

        try {
            const formData = new FormData();
            formData.append('gia_su_id', giaSuId);
            formData.append('diem_so', currentRating);
            if (comment) formData.append('binh_luan', comment);
            formData.append('_token', '{{ csrf_token() }}');

            const response = await fetch('{{ route("nguoihoc.danhgia.store") }}', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData
            });

            const data = await response.json();
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'Lỗi');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Gửi đánh giá';
            }
        } catch (error) {
            console.error(error);
            alert('Lỗi hệ thống');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Gửi đánh giá';
        }
    });

    // 7. Handle Edit
    editBtn.addEventListener('click', function() {
        if (!canEdit) return alert('Hết lượt chỉnh sửa');
        currentRating = existingRating.DiemSo;
        commentField.value = existingRating.BinhLuan || '';
        charCount.textContent = (existingRating.BinhLuan || '').length + '/1000';
        updateStars();
        updateRatingText();
        updateSubmitButton();
        ratingForm.classList.remove('hidden');
        existingRatingDiv.classList.add('hidden');
        submitBtn.textContent = 'Cập nhật đánh giá';
    });

    // 8. Helper Functions
    function updateStars() {
        starIcons.forEach((star, index) => {
            const rating = index + 1;
            star.classList.remove('text-gray-300', 'text-yellow-400', 'text-yellow-300');
            star.classList.add(rating <= currentRating ? 'text-yellow-400' : 'text-gray-300');
        });
    }

    function updateRatingText() {
        ratingText.textContent = currentRating > 0 ? ratingTexts[currentRating] : 'Chọn số sao';
        ratingText.className = currentRating > 0 ? 'text-center text-sm font-semibold mb-4 text-yellow-600' : 'text-center text-sm font-semibold mb-4 text-gray-500';
    }

    async function checkExistingRating() {
        try {
            const res = await fetch(`/nguoihoc/danh-gia/${giaSuId}/kiem-tra`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
            const data = await res.json();
            if (data.success && data.data) {
                existingRating = data.data;
                displayExistingRating();
            } else {
                ratingForm.classList.remove('hidden');
                existingRatingDiv.classList.add('hidden');
            }
        } catch (e) { 
            ratingForm.classList.remove('hidden'); 
        }
    }

    function displayExistingRating() {
        ratingForm.classList.add('hidden');
        existingRatingDiv.classList.remove('hidden');
        
        let starsHtml = '';
        for(let i=1; i<=5; i++) {
            starsHtml += `<i data-lucide="star" class="w-6 h-6 ${i <= existingRating.DiemSo ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'}"></i>`;
        }
        document.getElementById('existing-stars').innerHTML = starsHtml;
        if(typeof lucide !== 'undefined') lucide.createIcons();

        document.getElementById('existing-rating-text').textContent = ratingTexts[existingRating.DiemSo];
        
        const commentEl = document.getElementById('existing-comment');
        commentEl.innerHTML = existingRating.BinhLuan ? existingRating.BinhLuan : '<em class="text-gray-400">Không có nhận xét</em>';
        
        // Format Date (Simple JS approach)
        const date = new Date(existingRating.NgayDanhGia);
        document.getElementById('existing-date').textContent = 'Đánh giá ngày ' + date.toLocaleDateString('vi-VN');

        // Check LanSua
        const lanSua = existingRating.LanSua || 0;
        canEdit = lanSua < 1;
        document.getElementById('edit-limit-text').textContent = canEdit ? 'Bạn còn 1 lần chỉnh sửa' : 'Bạn đã hết lượt chỉnh sửa (Tối đa 1 lần)';
        if(!canEdit) {
            editBtn.classList.add('opacity-50', 'cursor-not-allowed');
            editBtn.classList.remove('hover:bg-gray-50');
        }
    }
});
</script>
@endpush

@endsection