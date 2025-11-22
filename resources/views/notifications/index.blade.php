@extends('layouts.web')

@section('title', 'Thông báo')

@section('content')
<div class="max-w-4xl mx-auto" x-data="notificationPage()">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Thông báo</h1>
        
        @if($notifications->where('is_read', false)->count() > 0)
        <button @click="markAllAsRead()" 
                type="button" 
                class="text-sm text-blue-600 hover:text-blue-800 font-medium hover:underline">
            Đánh dấu tất cả đã đọc
        </button>
        @endif
    </div>

    @if($notifications->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 divide-y divide-gray-200">
            @foreach($notifications as $notification)
            <div class="p-4 hover:bg-gray-50 transition-colors {{ !$notification->is_read ? 'bg-blue-50' : '' }}"
                 x-data="{ isRead: {{ $notification->is_read ? 'true' : 'false' }} }"
                 :class="{ 'bg-blue-50': !isRead }">
                <div class="flex items-start gap-4">
                    {{-- Icon --}}
                    <div class="flex-shrink-0 mt-1">
                        @if($notification->type === 'request_received' || $notification->type === 'invitation_received')
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i data-lucide="user-plus" class="w-6 h-6 text-blue-600"></i>
                        </div>
                        @elseif($notification->type === 'request_accepted')
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                        </div>
                        @elseif($notification->type === 'request_rejected')
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                        </div>
                        @else
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                            <i data-lucide="bell" class="w-6 h-6 text-gray-600"></i>
                        </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $notification->title }}</h3>
                                <p class="text-gray-600 mt-1">{{ $notification->message }}</p>
                                <p class="text-gray-400 text-sm mt-2">
                                    {{ $notification->created_at->locale('vi')->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Unread Dot --}}
                            <span x-show="!isRead" class="w-2 h-2 bg-blue-600 rounded-full block mt-2"></span>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-3 mt-3">
                            @if($notification->related_id)
                            <a href="{{ Auth::user()->nguoiHoc ? route('nguoihoc.lophoc.show', $notification->related_id) : route('giasu.lophoc.show', $notification->related_id) }}" 
                               class="text-sm text-blue-600 hover:text-blue-800 font-medium hover:underline">
                                Xem chi tiết
                            </a>
                            @endif

                            <button x-show="!isRead"
                                    @click="markAsRead({{ $notification->id }}, $el.closest('[x-data]'))"
                                    type="button" 
                                    class="text-sm text-gray-600 hover:text-gray-900 hover:underline">
                                Đánh dấu đã đọc
                            </button>

                            <button @click="deleteNotification({{ $notification->id }}, $el.closest('.p-4'))"
                                    type="button" 
                                    class="text-sm text-red-600 hover:text-red-800 hover:underline">
                                Xóa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Chưa có thông báo nào</h3>
            <p class="text-gray-500">Bạn sẽ nhận được thông báo khi có hoạt động mới</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('notificationPage', () => ({
        markAsRead(notificationId, element) {
            fetch(`/thong-bao/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update Alpine.js state
                    element.__x.$data.isRead = true;
                }
            })
            .catch(error => console.error('Error:', error));
        },
        
        markAllAsRead() {
            fetch('/thong-bao/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to show updated state
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        },
        
        deleteNotification(notificationId, element) {
            if (!confirm('Bạn có chắc muốn xóa thông báo này?')) {
                return;
            }
            
            fetch(`/thong-bao/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Fade out and remove element
                    element.style.transition = 'opacity 0.3s';
                    element.style.opacity = '0';
                    setTimeout(() => {
                        element.remove();
                        
                        // Check if no notifications left
                        const container = document.querySelector('.divide-y');
                        if (container && container.children.length === 0) {
                            window.location.reload();
                        }
                    }, 300);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }));
});
</script>
@endpush
@endsection
