@extends('layouts.web')

@section('title', 'Trang chủ Gia sư')

@section('content')
    
    {{-- Thông báo chờ duyệt nếu TrangThai != 1 --}}
    @php
        $currentUser = Auth::user();
        $giaSu = $currentUser?->giaSu;
    @endphp
    
    @if($giaSu && $giaSu->TrangThai != 1)
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-5 rounded-lg shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="h-6 w-6 text-yellow-400"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-base font-semibold text-yellow-800">
                        Tài khoản đang chờ duyệt
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Tài khoản của bạn đang được xem xét. Bạn có thể xem danh sách lớp học và cập nhật thông tin cá nhân, nhưng <strong>chưa thể gửi đề nghị dạy</strong> cho đến khi tài khoản được phê duyệt.</p>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('giasu.profile.index') }}" class="inline-flex items-center px-4 py-2 border border-yellow-400 text-sm font-medium rounded-md text-yellow-800 bg-yellow-100 hover:bg-yellow-200 transition-colors">
                            <i data-lucide="user-check" class="w-4 h-4 mr-2"></i>
                            Hoàn thiện hồ sơ để được duyệt nhanh hơn
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Chào mừng trở lại!</h1>
        <p class="text-gray-500 mt-2 text-base font-medium">Tìm kiếm lớp học phù hợp nhất cho bạn.</p>
    </div>

    {{-- Search Form --}}
    <form method="GET" action="{{ route('giasu.dashboard') }}" class="mb-10">
        <div class="bg-white p-2 rounded-2xl shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)] border border-gray-100 flex items-center">
            
            <div class="pl-4 pr-3 text-gray-400">
                <i data-lucide="search" class="w-6 h-6"></i>
            </div>
            
            <input type="text" name="search" value="{{ $search ?? '' }}" 
                   placeholder="Nhập tên lớp học, môn học, hoặc khu vực..." 
                   class="w-full px-2 py-3 bg-transparent border-none focus:ring-0 outline-none text-gray-700 text-lg placeholder-gray-400">
            
            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 whitespace-nowrap ml-2">
                Tìm kiếm
            </button>
        </div>

        {{-- Active Filters Display --}}
        @if($search || $monId || $khoiLopId)
            <div class="mt-4 flex items-center text-sm flex-wrap gap-2">
                <span class="text-gray-500">Đang lọc:</span>
                
                @if($search)
                    <span class="inline-flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-medium">
                        "{{ $search }}"
                        <a href="{{ route('giasu.dashboard', array_diff_key(request()->query(), ['search' => ''])) }}" class="ml-2 text-blue-600 hover:text-blue-900">
                            <i data-lucide="x" class="w-3 h-3"></i>
                        </a>
                    </span>
                @endif

                @if($monId)
                    @php
                        $selectedMon = $monHocList->firstWhere('MonID', $monId);
                    @endphp
                    <span class="inline-flex items-center bg-green-100 text-green-800 px-3 py-1 rounded-full font-medium">
                        Môn: {{ $selectedMon->TenMon ?? 'N/A' }}
                        <a href="{{ route('giasu.dashboard', array_diff_key(request()->query(), ['mon_id' => ''])) }}" class="ml-2 text-green-600 hover:text-green-900">
                            <i data-lucide="x" class="w-3 h-3"></i>
                        </a>
                    </span>
                @endif

                @if($khoiLopId)
                    @php
                        $selectedKhoi = $khoiLopList->firstWhere('KhoiLopID', $khoiLopId);
                    @endphp
                    <span class="inline-flex items-center bg-purple-100 text-purple-800 px-3 py-1 rounded-full font-medium">
                        Lớp: {{ $selectedKhoi->BacHoc ?? 'N/A' }}
                        <a href="{{ route('giasu.dashboard', array_diff_key(request()->query(), ['khoi_lop_id' => ''])) }}" class="ml-2 text-purple-600 hover:text-purple-900">
                            <i data-lucide="x" class="w-3 h-3"></i>
                        </a>
                    </span>
                @endif

                <a href="{{ route('giasu.dashboard') }}" class="text-red-500 hover:text-red-700 font-medium hover:underline flex items-center">
                    <i data-lucide="x" class="w-3 h-3 mr-1"></i> Xóa tất cả
                </a>
            </div>
        @endif

        {{-- Advanced Filters --}}
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                {{-- Môn học --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Môn học</label>
                    <select name="mon_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả</option>
                        @foreach($monHocList as $mon)
                            <option value="{{ $mon->MonID }}" {{ $monId == $mon->MonID ? 'selected' : '' }}>{{ $mon->TenMon }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Khối lớp --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Khối lớp</label>
                    <select name="khoi_lop_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả</option>
                        @foreach($khoiLopList as $khoi)
                            <option value="{{ $khoi->KhoiLopID }}" {{ $khoiLopId == $khoi->KhoiLopID ? 'selected' : '' }}>{{ $khoi->BacHoc }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Sắp xếp --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sắp xếp</label>
                    <select name="sort_by" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="newest" {{ $sortBy == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="fee_high" {{ $sortBy == 'fee_high' ? 'selected' : '' }}>Học phí cao → thấp</option>
                        <option value="fee_low" {{ $sortBy == 'fee_low' ? 'selected' : '' }}>Học phí thấp → cao</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    Áp dụng bộ lọc
                </button>
            </div>
        </div>
    </form>

    {{-- Classes List --}}
    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
        Lớp học đề xuất
        @if($lopHocList->total() > 0)
            <span class="ml-3 bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $lopHocList->total() }}</span>
        @endif
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($lopHocList as $lop)
            <x-web.class-card :lopHoc="$lop" />
        @empty
            <div class="col-span-3 flex flex-col items-center justify-center py-16 text-gray-500">
                <div class="bg-gray-100 p-4 rounded-full mb-4">
                    <i data-lucide="search-x" class="w-8 h-8 text-gray-400"></i>
                </div>
                <p class="text-lg font-medium">Không tìm thấy lớp học nào phù hợp.</p>
                <a href="{{ route('giasu.dashboard') }}" class="text-blue-600 hover:underline mt-2 font-medium">Quay lại tất cả</a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-12 flex justify-center">
        {{ $lopHocList->links() }}
    </div>

    {{-- Modal Đề nghị dạy --}}
    <div id="deNghiModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                <form id="deNghiForm">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i data-lucide="send" class="h-6 w-6 text-green-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Gửi đề nghị dạy
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">
                                        Bạn muốn gửi đề nghị dạy lớp <span id="modalClassName" class="font-bold text-gray-900"></span>
                                    </p>

                                    <input type="hidden" name="lop_hoc_id" id="modalClassID">

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú (tùy chọn)</label>
                                        <textarea name="ghi_chu" rows="4" maxlength="500" 
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none" 
                                                  placeholder="Thêm ghi chú cho đề nghị của bạn (tối đa 500 ký tự)..."></textarea>
                                        <p class="text-xs text-gray-500 mt-1">Ví dụ: giới thiệu kinh nghiệm, phương pháp giảng dạy...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Gửi đề nghị
                        </button>
                        <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Hủy bỏ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        function openDeNghiModal(lopHocId, lopHocName) {
            document.getElementById('modalClassID').value = lopHocId;
            document.getElementById('modalClassName').innerText = lopHocName;
            document.getElementById('deNghiModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('deNghiModal').classList.add('hidden');
            document.getElementById('deNghiForm').reset();
        }

        // Handle form submission with AJAX
        document.getElementById('deNghiForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin mr-2"></i> Đang gửi...';
            
            fetch('{{ route("giasu.de_nghi_day") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeModal();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Gửi đề nghị';
                lucide.createIcons(); // Re-initialize Lucide icons
            });
        });

        function showNotification(message, type) {
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const notification = document.createElement('div');
            notification.className = `fixed bottom-5 right-5 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-bounce`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 4000);
        }
    </script>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="fixed bottom-5 right-5 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg animate-bounce z-50">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="fixed bottom-5 right-5 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif

@endsection