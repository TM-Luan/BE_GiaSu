@extends('layouts.web')

@section('title', 'Lịch dạy của tôi')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Lịch dạy của tôi</h1>
        <p class="text-gray-500 mt-2 text-base font-medium">Theo dõi tất cả các buổi dạy đã và đang diễn ra.</p>
    </div>

    <!-- Calendar -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
        <div id="calendar"></div>
    </div>

    <!-- Danh sách lịch học theo ngày -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">
            Lịch dạy ngày: <span class="text-blue-600" id="selected-date">{{ now()->format('d/m/Y') }}</span>
        </h2>
        <div id="schedule-list" class="space-y-4">
            <!-- Danh sách lịch học sẽ được load bằng JavaScript -->
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
    .fc-event:hover {
        background-color: #f3f4f6 !important;
    }
    .fc-event-main, .fc-event-time {
        color: var(--fc-event-text-color, #000) !important; 
    }
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
    .fc-daygrid-day.has-schedule {
        background-color: #EFF6FF !important;
    }
    .fc-daygrid-day.has-schedule .fc-daygrid-day-number {
        color: #3B82F6 !important;
        font-weight: 700 !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendarEvents = {!! $calendarDataJson !!}; 
        var allSchedules = {!! $allSchedulesJson !!};

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'vi',
            buttonText: {
                today: 'Hôm nay', month: 'Tháng'
            },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            events: calendarEvents,
            editable: false, 
            selectable: false,
            eventContent: function(arg) {
                let timeText = arg.timeText ? `<b>${arg.timeText}</b> ` : '';
                return { 
                    html: `<div style="color: ${arg.event.textColor};">${timeText}<span class="fc-event-title">${arg.event.title}</span></div>`
                };
            },
            dateClick: function(info) {
                loadSchedulesForDate(info.dateStr);
            },
            dayCellDidMount: function(info) {
                var dateStr = info.date.toISOString().split('T')[0];
                var hasSchedule = allSchedules.some(function(schedule) {
                    return schedule.date === dateStr;
                });
                if (hasSchedule) {
                    info.el.classList.add('has-schedule');
                }
            }
        });

        calendar.render();
        var today = new Date().toISOString().split('T')[0];
        loadSchedulesForDate(today);

        function loadSchedulesForDate(dateStr) {
            var date = new Date(dateStr);
            var formattedDate = ('0' + date.getDate()).slice(-2) + '/' + 
                                ('0' + (date.getMonth() + 1)).slice(-2) + '/' + 
                                date.getFullYear();
            
            document.getElementById('selected-date').textContent = formattedDate;
            var schedules = allSchedules.filter(function(s) { return s.date === dateStr; });
            var listHtml = '';
            
            if (schedules.length === 0) {
                listHtml = `<div class="text-center py-12 text-gray-500"><i data-lucide="calendar-x" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i><p class="text-lg font-medium">Không có lịch dạy trong ngày này</p></div>`;
            } else {
                schedules.forEach(function(schedule) {
                    var statusBadge = getStatusBadge(schedule.trangThai);
                    var hinhThucBadge = schedule.hinhThuc === 'Online' 
                        ? '<span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-medium">ONLINE</span>'
                        : '<span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-medium">OFFLINE</span>';
                    
                    var actionButtons = '';
                    if (schedule.hinhThuc === 'Online' && schedule.duongDan) {
                        actionButtons = `<a href="${schedule.duongDan}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm"><i data-lucide="video" class="w-4 h-4 mr-2"></i>Tham gia lớp học</a>`;
                    }

                    listHtml += `<div class="border border-gray-200 rounded-xl p-5 hover:border-blue-300 hover:shadow-md transition-all"><div class="flex items-start justify-between mb-3"><div class="flex-1"><div class="flex items-center gap-2 mb-2"><h3 class="font-bold text-lg text-gray-900">${schedule.monHoc}</h3>${hinhThucBadge}${statusBadge}</div><p class="text-gray-600 text-sm"><i data-lucide="user" class="w-4 h-4 inline mr-1"></i>Học sinh: ${schedule.nguoiHocTen}</p></div></div><div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-4 text-sm"><div class="flex items-center text-gray-600"><i data-lucide="clock" class="w-4 h-4 mr-2 text-blue-500"></i><span>${schedule.thoiGianBatDau}</span></div>${schedule.hinhThuc === 'Online' && schedule.duongDan ? `<div class="flex items-center text-gray-600 col-span-2"><i data-lucide="link" class="w-4 h-4 mr-2 text-blue-500"></i><span class="truncate">Link học</span></div>` : ''}</div>${actionButtons ? `<div class="pt-3 border-t border-gray-100">${actionButtons}</div>` : ''}</div>`;
                });
            }

            document.getElementById('schedule-list').innerHTML = listHtml;
            lucide.createIcons();
        }

        function getStatusBadge(trangThai) {
            var badges = {
                'DaHoanThanh': '<span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-medium">ĐÃ HỌC</span>',
                'SapToi': '<span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-medium">SẮP TỚI</span>',
                'DangDay': '<span class="bg-orange-100 text-orange-700 px-2 py-1 rounded text-xs font-medium">ĐANG DẠY</span>',
                'Huy': '<span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-medium">ĐÃ HỦY</span>'
            };
            return badges[trangThai] || '<span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-medium">' + trangThai + '</span>';
        }
    });
</script>
@endpush
