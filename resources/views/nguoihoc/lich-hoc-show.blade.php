@extends('layouts.web')

@section('title', 'Lịch học: ' . $lopHoc->monHoc->TenMon)

@section('content')
<div class="max-w-6xl mx-auto" x-data="{ modalOpen: false, selectedEvent: {} }">
    
    <div class="mb-8">
        <a href="{{ route('nguoihoc.lophoc.index') }}" class="inline-flex items-center text-gray-500 hover:text-blue-600 transition-colors mb-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Quay lại Lớp học của tôi
        </a>
        
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Lịch học: {{ $lopHoc->monHoc->TenMon }} ({{ $lopHoc->khoiLop->BacHoc }})
        </h1>
        <p class="text-gray-500 mt-2 text-base font-medium">
            Gia sư: {{ $lopHoc->giaSu->HoTen ?? 'Chưa có gia sư' }}
        </p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div id="calendar"></div>
    </div>

    <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 backdrop-blur-sm" style="display: none;">
        
        <div @click.away="modalOpen = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6"
             x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
            
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Chi tiết buổi học</h3>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <div class="space-y-4 mb-6">
                <p><strong>Môn học:</strong> <span x-text="selectedEvent.monHoc"></span></p>
                <p><strong>Gia sư:</strong> <span x-text="selectedEvent.giaSuTen"></span></p>
                <p><strong>Thời gian:</strong> <span x-text="selectedEvent.thoiGianBatDau"></span></p>
                <p><strong>Hình thức:</strong> <span x-text="selectedEvent.hinhThuc"></span></p>
                <p><strong>Trạng thái:</strong> <span x-text="selectedEvent.trangThai"></span></p>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" @click="modalOpen = false" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                    Đóng
                </button>
                <a :href="selectedEvent.duongDan" target="_blank" 
                   x-show="selectedEvent.hinhThuc === 'Online' && selectedEvent.duongDan"
                   class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-md shadow-blue-200">
                    Mở link học
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>

<style>
    .fc { font-family: inherit; }
    .fc-event {
        cursor: pointer;
        font-weight: 700;
        border: none !important;
        background-color: transparent !important;
        padding: 4px 6px;
    }
    .fc-event:hover { background-color: #f3f4f6 !important; }
    .fc-event-main, .fc-event-time { color: var(--fc-event-text-color, #000) !important; }
    .fc-toolbar-title { font-size: 1.5em !important; font-weight: 700; }
    .fc-button-primary {
        background-color: #3B82F6 !important;
        border-color: #3B82F6 !important;
        font-weight: 600;
    }
    .fc-button-primary:hover { background-color: #2563EB !important; }
    .fc-daygrid-day.fc-day-today { background-color: #EFF6FF; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendarEvents = {!! $calendarDataJson !!}; 
        var alpineContext = document.querySelector('[x-data]').__x;

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'vi',
            buttonText: { today: 'Hôm nay', month: 'Tháng', week: 'Tuần', day: 'Ngày', list: 'Danh sách' },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            events: calendarEvents,
            editable: false, 
            selectable: false,
            eventContent: function(arg) {
                let timeText = arg.timeText ? `<b>${arg.timeText}</b> ` : '';
                return { 
                    html: `<div style="color: ${arg.event.textColor};">
                               ${timeText}
                               <span class="fc-event-title">${arg.event.title}</span>
                           </div>`
                };
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault(); 
                alpineContext.selectedEvent = info.event.extendedProps;
                alpineContext.modalOpen = true;
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            }
        });
        calendar.render();
    });
</script>
@endpush