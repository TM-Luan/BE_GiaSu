@extends('layouts.web')

@section('title', 'Trang chủ Người học')

@section('content')
    
    {{-- Header với Notification Bell --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Chào mừng trở lại!</h1>
            <p class="text-gray-500 mt-2 text-base font-medium">Tìm kiếm gia sư phù hợp nhất cho bạn.</p>
        </div>
        
        {{-- Notification Bell --}}
        <x-notification-bell />
    </div>

    <form method="GET" action="{{ route('nguoihoc.dashboard') }}" class="mb-10">
        <div class="bg-white p-2 rounded-2xl shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)] border border-gray-100 flex items-center">
            
            <div class="pl-4 pr-3 text-gray-400">
                <i data-lucide="search" class="w-6 h-6"></i>
            </div>
            
            <input type="text" name="q" value="{{ request('q') }}" 
                   placeholder="Nhập tên gia sư, môn học (Toán, Lý...), hoặc khu vực..." 
                   class="w-full px-2 py-3 bg-transparent border-none focus:ring-0 outline-none text-gray-700 text-lg placeholder-gray-400">
            
            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 whitespace-nowrap ml-2">
                Tìm kiếm
            </button>
        </div>

        @if(request('q'))
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-500 mr-2">Kết quả cho:</span>
                <span class="font-bold text-gray-900 mr-4">"{{ request('q') }}"</span>
                <a href="{{ route('nguoihoc.dashboard') }}" class="text-red-500 hover:text-red-700 font-medium hover:underline flex items-center">
                    <i data-lucide="x" class="w-3 h-3 mr-1"></i> Xóa tìm kiếm
                </a>
            </div>
        @endif
    </form>

    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
        Gia sư đề xuất
        @if($giasuList->total() > 0)
            <span class="ml-3 bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $giasuList->total() }}</span>
        @endif
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($giasuList as $gs)
             <x-web.tutor-card :taikhoanGiaSu="$gs" />
        @empty
            <div class="col-span-3 flex flex-col items-center justify-center py-16 text-gray-500">
                <div class="bg-gray-100 p-4 rounded-full mb-4">
                    <i data-lucide="search-x" class="w-8 h-8 text-gray-400"></i>
                </div>
                <p class="text-lg font-medium">Không tìm thấy gia sư nào phù hợp.</p>
                <a href="{{ route('nguoihoc.dashboard') }}" class="text-blue-600 hover:underline mt-2 font-medium">Quay lại tất cả</a>
            </div>
        @endforelse
    </div>

    <div class="mt-12 flex justify-center">
        {{ $giasuList->links() }}
    </div>
<div id="inviteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                <form action="{{ route('nguoihoc.moi_day') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i data-lucide="send" class="h-6 w-6 text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Mời gia sư dạy
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">
                                        Chọn lớp học mà bạn muốn mời gia sư <span id="modalGiaSuName" class="font-bold text-gray-900"></span> tham gia.
                                    </p>

                                    <input type="hidden" name="gia_su_id" id="modalGiaSuID">

                                    @if(isset($myClasses) && count($myClasses) > 0)
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Chọn lớp học</label>
                                        <select name="lop_yeu_cau_id" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md border">
                                            @foreach($myClasses as $lop)
                                                <option value="{{ $lop->LopYeuCauID }}">
                                                    {{ $lop->monHoc->TenMon ?? 'Môn học' }} - Lớp {{ $lop->KhoiLopID }} ({{ number_format($lop->HocPhi) }}đ)
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                            <div class="flex">
                                                <div class="ml-3">
                                                    <p class="text-sm text-yellow-700">
                                                        Bạn chưa có lớp học nào đang tìm gia sư. 
                                                        <a href="#" class="font-bold underline hover:text-yellow-800">Tạo lớp mới ngay</a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        @if(isset($myClasses) && count($myClasses) > 0)
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Gửi lời mời
                            </button>
                        @endif
                        <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Hủy bỏ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openInviteModal(giaSuId, giaSuName) {
            document.getElementById('modalGiaSuID').value = giaSuId;
            document.getElementById('modalGiaSuName').innerText = giaSuName;
            document.getElementById('inviteModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('inviteModal').classList.add('hidden');
        }
    </script>

    @if(session('success'))
        <div class="fixed bottom-5 right-5 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg animate-bounce">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="fixed bottom-5 right-5 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('error') }}
        </div>
    @endif

@endsection