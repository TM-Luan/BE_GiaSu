{{-- Notification Bell Icon with Dropdown --}}
<div class="relative" x-data="notificationBell()" x-init="init()" @click.away="open = false">
    {{-- Bell Icon --}}
    <button @click="toggleDropdown()" type="button" class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors">
        <i data-lucide="bell" class="w-6 h-6"></i>
        
        {{-- Unread Badge --}}
        <span x-show="unreadCount > 0" 
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full min-w-[18px]">
        </span>
    </button>

    {{-- Dropdown --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-lg border border-gray-200 z-50 max-h-[500px] flex flex-col"
         style="display: none;">
        
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Thông báo</h3>
            <button @click="markAllAsRead()" 
                    x-show="unreadCount > 0"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Đánh dấu tất cả đã đọc
            </button>
        </div>

        {{-- Notifications List --}}
        <div class="overflow-y-auto flex-1">
            <template x-if="notifications.length === 0">
                <div class="px-4 py-8 text-center text-gray-500">
                    <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-gray-400"></i>
                    <p>Chưa có thông báo nào</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <div @click="handleNotificationClick(notification)"
                     class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors"
                     :class="{ 'bg-blue-50': !notification.is_read }">
                    
                    <div class="flex items-start gap-3">
                        {{-- Icon --}}
                        <div class="flex-shrink-0 mt-1">
                            <template x-if="notification.type === 'request_received' || notification.type === 'invitation_received'">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i data-lucide="user-plus" class="w-5 h-5 text-blue-600"></i>
                                </div>
                            </template>
                            <template x-if="notification.type === 'request_accepted'">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                                </div>
                            </template>
                            <template x-if="notification.type === 'request_rejected'">
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                    <i data-lucide="x-circle" class="w-5 h-5 text-red-600"></i>
                                </div>
                            </template>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm" x-text="notification.title"></p>
                            <p class="text-gray-600 text-sm mt-0.5 line-clamp-2" x-text="notification.message"></p>
                            <p class="text-gray-400 text-xs mt-1" x-text="formatTime(notification.created_at)"></p>
                        </div>

                        {{-- Unread Dot --}}
                        <div x-show="!notification.is_read" class="flex-shrink-0">
                            <span class="w-2 h-2 bg-blue-600 rounded-full block"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <a href="{{ route('thongbao.index') }}" 
           class="block px-4 py-3 text-center text-sm text-blue-600 hover:bg-gray-50 font-medium border-t border-gray-200">
            Xem tất cả thông báo
        </a>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('notificationBell', () => ({
        open: false,
        notifications: [],
        unreadCount: 0,
        
        init() {
            this.loadNotifications();
            // Auto refresh every 30 seconds
            setInterval(() => {
                if (!this.open) {
                    this.loadNotifications();
                }
            }, 30000);
        },
        
        toggleDropdown() {
            this.open = !this.open;
            if (this.open) {
                // Reload khi mở dropdown
                this.loadNotifications();
            }
        },
        
        loadNotifications() {
            fetch('{{ route("thongbao.api.list") }}?limit=10', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Notification API Response:', data);
                if (data.success) {
                    this.notifications = data.data.notifications;
                    this.unreadCount = data.data.unread_count;
                    // Re-init Lucide icons after DOM update
                    this.$nextTick(() => {
                        lucide.createIcons();
                    });
                }
            })
            .catch(error => console.error('Error loading notifications:', error));
        },
        
        markAllAsRead() {
            fetch('{{ route("thongbao.mark-all-read") }}', {
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
                    this.notifications = this.notifications.map(n => ({ ...n, is_read: true }));
                    this.unreadCount = 0;
                }
            })
            .catch(error => console.error('Error marking all as read:', error));
        },
        
        handleNotificationClick(notification) {
            // Mark as read
            if (!notification.is_read) {
                fetch(`{{ url('/thong-bao') }}/${notification.id}/mark-read`, {
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
                        notification.is_read = true;
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    }
                });
            }

            // Redirect to related page
            if (notification.related_id) {
                const user = @json(Auth::user());
                if (user.nguoi_hoc_id || user.nguoiHoc) {
                    // Người học -> redirect to lớp học của tôi
                    window.location.href = `{{ url('/nguoihoc/lop-hoc') }}/${notification.related_id}`;
                } else if (user.gia_su_id || user.giaSu) {
                    // Gia sư -> redirect to lớp học của tôi
                    window.location.href = `{{ url('/giasu/lop-hoc') }}/${notification.related_id}`;
                }
            }
        },
        
        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000); // seconds

            if (diff < 60) return 'Vừa xong';
            if (diff < 3600) return Math.floor(diff / 60) + ' phút trước';
            if (diff < 86400) return Math.floor(diff / 3600) + ' giờ trước';
            if (diff < 604800) return Math.floor(diff / 86400) + ' ngày trước';
            
            return date.toLocaleDateString('vi-VN');
        }
    }));
});
</script>
@endpush
