@extends('layouts.web')

@section('title', 'Lịch học của tôi')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Lịch học của tôi</h1>
            <p class="text-gray-500 mt-2 text-base font-medium">Theo dõi tất cả các buổi học đã và đang diễn ra.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div id="calendar"></div>
    </div>

</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>

<style>
    /* Tinh chỉnh giao diện FullCalendar */
    .fc { font-family: inherit; }

    /* [THAY ĐỔI QUAN TRỌNG] Bỏ hết màu nền/viền */
    .fc-event {
        cursor: pointer;
        font-weight: 700; /* Làm chữ đậm hơn */
        border: none !important;
        background-color: transparent !important; /* NỀN TRONG SUỐT */
        padding: 4px 6px;
    }
    .fc-event:hover {
        background-color: #f3f4f6 !important; /* Thêm nền xám nhạt khi hover */
    }
    
    /* Ghi đè màu chữ (lấy từ Controller) */
    .fc-event-main, .fc-event-time {
        color: var(--fc-event-text-color, #000) !important; 
    }
    
    /* (CSS cho toolbar, button, today giữ nguyên) */
    .fc-toolbar-title { font-size: 1.5em !important; font-weight: 700; }
    .fc-button-primary {
        background-color: #3B82F6 !important;
        border-color: #3B82F6 !important;
        font-weight: 600;
    }
    .fc-button-primary:hover { background-color: #2563EB !important; }
    .fc-daygrid-day.fc-day-today {
    background: linear-gradient(135deg, #e0f7ff, #f0faff) !important;
    border-radius: 10px;
    }


</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        
        // [CACHE BUSTING] Dùng tên biến mới
        var calendarEvents = {!! $calendarDataJson !!}; 

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'vi',
            buttonText: {
                today: 'Hôm nay', month: 'Tháng', week: 'Tuần', day: 'Ngày', list: 'Danh sách'
            },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            events: calendarEvents,
            editable: false, 
            selectable: false,

            // [THAY ĐỔI] Ghi đè hiển thị sự kiện để đảm bảo màu chữ
            eventContent: function(arg) {
                // Giúp hiển thị giờ và tiêu đề cùng một màu
                let timeText = arg.timeText ? `<b>${arg.timeText}</b> ` : '';
                return { 
                    html: `<div style="color: ${arg.event.textColor};">
                               ${timeText}
                               <span class="fc-event-title">${arg.event.title}</span>
                           </div>`
                };
            },

            // (Logic click giữ nguyên như cũ)
            eventClick: function(info) {
                info.jsEvent.preventDefault(); 
                var props = info.event.extendedProps;
                var duongDan = props.duongDan;
                var hinhThuc = props.hinhThuc; 
                var content = [
                    'Môn học: ' + props.monHoc,
                    'Gia sư: ' + props.giaSuTen,
                    'Thời gian: ' + props.thoiGianBatDau,
                    'Hình thức: ' + hinhThuc,
                    'Trạng thái: ' + props.trangThai
                ].join('\n'); 

                if (hinhThuc === 'Online' && duongDan) {
                    if (confirm(content + '\n\nBạn có muốn mở link buổi học này không?')) {
                        window.open(duongDan, '_blank');
                    }
                } else {
                    alert(content);
                }
            }
        });

        calendar.render();
    });
</script>
@endpush