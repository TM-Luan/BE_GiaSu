@extends('layouts.web')

@section('title', 'Hồ sơ gia sư')

@section('content')
<div class="max-w-6xl mx-auto" x-data="{ modalOpen: false }">
    
    <div class="mb-6">
        <a href="{{ route('nguoihoc.dashboard') }}" class="inline-flex items-center text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Quay lại danh sách
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center">
                <div class="relative mb-3">
                    <img src="{{ $gs->AnhDaiDien ? asset($gs->AnhDaiDien) : 'https://placehold.co/100x100/E0E7FF/3B82F6?text=' . substr($gs->HoTen, 0, 1) . '&font=roboto' }}" 
                         alt="{{ $gs->HoTen }}"
                         class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md">
                </div>
                
                <h1 class="text-xl font-bold text-gray-900">{{ $gs->HoTen }}</h1>
                <p class="text-gray-500 text-sm mb-3">{{ $gs->KinhNghiem ?? 'Chưa cập nhật kinh nghiệm' }}</p>
                
                <div class="flex items-center gap-1 mb-4">
                    <i data-lucide="star" class="w-4 h-4 text-yellow-500 fill-yellow-400"></i>
                    <span class="font-bold text-gray-800">{{ $rating }}</span>
                    <span class="text-gray-400 text-sm">({{ $gs->danh_gia_count }} đánh giá)</span>
                </div>
                
                <button type="button" @click="modalOpen = true" class="w-full px-4 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200">
                    Mời dạy ngay
                </button>

                </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 border-b pb-2">Thông tin</h3>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Khu vực</p>
                    <p class="font-medium text-gray-800">{{ $gs->DiaChi ?? 'Chưa cập nhật' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Học phí tham khảo</p>
                    <p class="font-bold text-lg text-green-600">{{ $hocPhi }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Trường đào tạo</p>
                    <p class="font-medium text-gray-800">{{ $gs->TruongDaoTao ?? 'Chưa cập nhật' }}</p>
                </div>
            </div>
        </div>
        
        <div class="md:col-span-2 space-y-6">
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
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="flex items-center text-lg font-bold text-gray-900 mb-4">
                    <i data-lucide="award" class="w-5 h-5 mr-2 text-orange-500"></i>
                    Bằng cấp & Chứng chỉ
                </h3>
                <div>
                    @if($gs->AnhBangCap)
                        <img src="{{ asset($gs->AnhBangCap) }}" class="rounded-lg border border-gray-200" alt="Bằng cấp">
                    @else
                        <p class="text-gray-400">Chưa cập nhật bằng cấp.</p>
                    @endif
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                 <h3 class="flex items-center text-lg font-bold text-gray-900 mb-4">
                    <i data-lucide="message-square" class="w-5 h-5 mr-2 text-green-500"></i>
                    Đánh giá từ học viên ({{ $gs->danh_gia_count }})
                </h3>
                <div class="space-y-4">
                    @forelse($gs->danhGia as $dg)
                        <div class="flex gap-3 border-b border-gray-100 pb-4 last:border-b-0">
                            <img src="https://ui-avatars.com/api/?name={{ $dg->taiKhoan->nguoiHoc->HoTen ?? 'Học viên' }}" class="w-10 h-10 rounded-full">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <h5 class="font-semibold">{{ $dg->taiKhoan->nguoiHoc->HoTen ?? 'Học viên' }}</h5>
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i data-lucide="star" class="w-4 h-4 {{ $i <= $dg->DiemSo ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">{{ $dg->BinhLuan ?: 'Không có nhận xét' }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($dg->NgayDanhGia)->locale('vi')->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400">Chưa có đánh giá nào.</p>
                    @endforelse
                </div>
            </div>

            <!-- Rating Section -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100" id="rating-section">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i data-lucide="star" class="w-5 h-5 mr-2 text-yellow-500"></i>
                    Đánh giá gia sư này
                </h3>

                <div id="rating-form">
                    <div class="flex justify-center gap-2 mb-4" id="star-rating">
                        <i data-lucide="star" class="star-icon w-8 h-8 text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors" data-rating="1"></i>
                        <i data-lucide="star" class="star-icon w-8 h-8 text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors" data-rating="2"></i>
                        <i data-lucide="star" class="star-icon w-8 h-8 text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors" data-rating="3"></i>
                        <i data-lucide="star" class="star-icon w-8 h-8 text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors" data-rating="4"></i>
                        <i data-lucide="star" class="star-icon w-8 h-8 text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors" data-rating="5"></i>
                    </div>
                    
                    <p class="text-center text-sm font-semibold mb-4" id="rating-text">Chọn số sao</p>
                    
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

                <div id="existing-rating" class="hidden">
                    <div class="mb-4">
                        <div class="flex justify-center gap-1 mb-2" id="existing-stars"></div>
                        <p class="text-center text-sm font-semibold text-gray-700" id="existing-rating-text"></p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-xl p-4 mb-4">
                        <p class="text-sm text-gray-700" id="existing-comment"></p>
                        <p class="text-xs text-gray-400 mt-2" id="existing-date"></p>
                    </div>
                    
                    <button type="button" 
                            id="edit-rating-btn"
                            class="w-full py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                        Chỉnh sửa đánh giá
                    </button>
                    
                    <p class="text-xs text-center text-gray-400 mt-2" id="edit-limit-text"></p>
                </div>
            </div>
        </div>
        
    </div>
    
    <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 backdrop-blur-sm" style="display: none;">
        
        <div @click.away="modalOpen = false" class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6"
             x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
            
            <h3 class="text-xl font-bold text-gray-900 mb-2">Mời gia sư {{ $gs->HoTen }}</h3>
            <p class="text-gray-500 mb-6">Chọn lớp học bạn muốn gửi lời mời:</p>

            <form action="{{ route('nguoihoc.moi_day') }}" method="POST">
                @csrf
                <input type="hidden" name="gia_su_id" value="{{ $gs->GiaSuID }}">
                
                <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                    @forelse($myClasses as $lop)
                        <label class="block p-4 border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-blue-300 transition-colors cursor-pointer">
                            <div class="flex items-center">
                                <input type="radio" name="lop_yeu_cau_id" value="{{ $lop->LopYeuCauID }}" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <div class="ml-3">
                                    <p class="font-semibold text-gray-800">
                                        {{ $lop->monHoc->TenMon ?? '' }} - {{ $lop->khoiLop->BacHoc ?? '' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $lop->HinhThuc }} • {{ number_format($lop->HocPhi) }}đ/buổi
                                    </p>
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="text-center p-6 bg-gray-50 rounded-lg">
                            <p class="font-medium text-gray-700">Bạn không có lớp nào đang tìm gia sư.</p>
                            <p class="text-sm text-gray-500 mb-3">Vui lòng tạo lớp học mới trước khi mời.</p>
                            <a href="{{ route('nguoihoc.lophoc.create') }}" class="px-3 py-1.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">
                                Tạo lớp học mới
                            </a>
                        </div>
                    @endforelse
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                    <button type="button" @click="modalOpen = false" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        Hủy
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-md shadow-blue-200"
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
    const ratingSection = document.getElementById('rating-section');
    if (!ratingSection) return;

    const giaSuId = {{ $gs->GiaSuID }};
    
    let currentRating = 0;
    let existingRating = null;
    let canEdit = true;
    
    const starIcons = document.querySelectorAll('.star-icon');
    const ratingText = document.getElementById('rating-text');
    const commentField = document.getElementById('rating-comment');
    const charCount = document.getElementById('char-count');
    const submitBtn = document.getElementById('submit-rating-btn');
    const ratingForm = document.getElementById('rating-form');
    const existingRatingDiv = document.getElementById('existing-rating');
    const editBtn = document.getElementById('edit-rating-btn');
    
    const ratingTexts = {
        1: 'Không hài lòng',
        2: 'Tạm được',
        3: 'Hài lòng',
        4: 'Rất hài lòng',
        5: 'Tuyệt vời!'
    };

    // Check if user already rated this tutor
    checkExistingRating();

    // Star click handlers - Allow multiple changes before submitting
    starIcons.forEach(star => {
        // Click to select rating
        star.addEventListener('click', function() {
            currentRating = parseInt(this.getAttribute('data-rating'));
            updateStars();
            updateRatingText();
            updateSubmitButton();
        });

        // Hover effect to show interactivity
        star.addEventListener('mouseenter', function() {
            const hoverRating = parseInt(this.getAttribute('data-rating'));
            starIcons.forEach((s, index) => {
                const hoverIdx = index + 1;
                s.classList.remove('text-gray-300', 'text-yellow-400', 'text-yellow-300');
                if (hoverIdx <= hoverRating) {
                    s.classList.add('text-yellow-300');
                } else {
                    s.classList.add('text-gray-300');
                }
            });
        });

        star.addEventListener('mouseleave', function() {
            updateStars(); // Reset to current rating
        });

        // Add cursor pointer
        star.style.cursor = 'pointer';
    });

    // Comment character counter
    commentField.addEventListener('input', function() {
        charCount.textContent = this.value.length + '/1000';
    });

    // Submit rating
    submitBtn.addEventListener('click', async function() {
        if (currentRating === 0) {
            alert('Vui lòng chọn số sao đánh giá');
            return;
        }

        const comment = commentField.value.trim();
        
        if (!confirm(existingRating ? 'Bạn có chắc muốn cập nhật đánh giá?' : 'Bạn có chắc muốn gửi đánh giá này?')) {
            return;
        }

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
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                alert(data.message);
                // Reload page to update rating everywhere (header, dashboard, etc.)
                window.location.reload();
            } else {
                alert(data.message || 'Có lỗi xảy ra');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Gửi đánh giá';
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi gửi đánh giá');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Gửi đánh giá';
        }
    });

    // Edit rating button
    editBtn.addEventListener('click', function() {
        if (!canEdit) {
            alert('Bạn đã hết lượt chỉnh sửa đánh giá này');
            return;
        }
        
        // Populate form with existing data
        currentRating = existingRating.DiemSo;
        commentField.value = existingRating.BinhLuan || '';
        charCount.textContent = (existingRating.BinhLuan || '').length + '/1000';
        
        updateStars();
        updateRatingText();
        updateSubmitButton();
        
        // Show form, hide existing
        ratingForm.classList.remove('hidden');
        existingRatingDiv.classList.add('hidden');
        
        submitBtn.textContent = 'Cập nhật đánh giá';
    });

    function updateStars() {
        starIcons.forEach((star, index) => {
            const rating = index + 1;
            star.classList.remove('text-gray-300', 'text-yellow-400', 'text-yellow-300');
            if (rating <= currentRating) {
                star.classList.add('text-yellow-400');
            } else {
                star.classList.add('text-gray-300');
            }
        });
    }

    function updateRatingText() {
        ratingText.textContent = currentRating > 0 ? ratingTexts[currentRating] : 'Chọn số sao';
        ratingText.classList.toggle('text-yellow-600', currentRating > 0);
        ratingText.classList.toggle('text-gray-400', currentRating === 0);
    }

    function updateSubmitButton() {
        submitBtn.disabled = currentRating === 0;
    }

    async function checkExistingRating() {
        try {
            const response = await fetch(`/nguoihoc/danh-gia/${giaSuId}/kiem-tra`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success && data.data) {
                // Already rated, show existing rating
                existingRating = data.data;
                displayExistingRating();
            } else {
                // No rating yet or can't rate, show form
                ratingForm.classList.remove('hidden');
                existingRatingDiv.classList.add('hidden');
            }
        } catch (error) {
            console.error('Error checking rating:', error);
            // Default to showing form
            ratingForm.classList.remove('hidden');
            existingRatingDiv.classList.add('hidden');
        }
    }

    function displayExistingRating() {
        ratingForm.classList.add('hidden');
        existingRatingDiv.classList.remove('hidden');

        // Display stars
        const starsHtml = Array.from({length: 5}, (_, i) => 
            `<i data-lucide="star" class="w-6 h-6 ${i < existingRating.DiemSo ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'}"></i>`
        ).join('');
        document.getElementById('existing-stars').innerHTML = starsHtml;

        // Display text
        document.getElementById('existing-rating-text').textContent = ratingTexts[existingRating.DiemSo];
        
        // Display comment
        const commentEl = document.getElementById('existing-comment');
        if (existingRating.BinhLuan) {
            commentEl.textContent = existingRating.BinhLuan;
        } else {
            commentEl.innerHTML = '<em class="text-gray-400">Không có nhận xét</em>';
        }

        // Display date
        const date = new Date(existingRating.NgayDanhGia);
        document.getElementById('existing-date').textContent = 'Đánh giá ngày ' + date.toLocaleDateString('vi-VN');

        // Check edit limit
        const lanSua = existingRating.LanSua || 0;
        canEdit = lanSua < 1;
        
        if (!canEdit) {
            editBtn.disabled = true;
            editBtn.classList.add('opacity-50', 'cursor-not-allowed');
            document.getElementById('edit-limit-text').textContent = 'Bạn đã hết lượt chỉnh sửa';
        } else {
            document.getElementById('edit-limit-text').textContent = 'Bạn còn 1 lần chỉnh sửa';
        }
    }
});
</script>
@endpush

@endsection