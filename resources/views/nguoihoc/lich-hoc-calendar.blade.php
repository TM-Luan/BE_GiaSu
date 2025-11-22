@extends('layouts.web')

@section('title', 'Lịch học, lịch thi theo tuần')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="w-full px-4">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="border-b border-gray-200 p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Lịch học, lịch thi theo tuần</h1>
            
            <!-- Toolbar -->
            <div class="flex items-center gap-4 mt-4 flex-wrap">
                <!-- Radio filters -->
                <div class="flex items-center gap-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="view_filter" value="all" checked class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Tất cả</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="view_filter" value="lichhoc" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Lịch học</span>
                    </label>
                </div>

                <!-- Date picker -->
                <input type="date" id="datePicker" value="{{ now()->format('Y-m-d') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                
                <!-- Actions -->
                <button onclick="goToToday()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <i data-lucide="calendar-days" class="w-4 h-4 mr-2"></i>
                    Hiện tại
                </button>
                
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                    In lịch
                </button>

                <!-- Navigation -->
                <div class="flex items-center gap-2 ml-auto">
                    <button onclick="previousWeek()" class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i data-lucide="chevron-left" class="w-5 h-5"></i>
                    </button>
                    <button onclick="nextWeek()" class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                    </button>
                    <button onclick="toggleFullscreen()" class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i data-lucide="maximize-2" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Calendar Table -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="w-24 px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">Ca học</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold border-r border-gray-200" id="day-header-2">
                            <div class="text-blue-600">Thứ 2</div>
                            <div class="text-gray-500 font-normal text-xs" id="date-2"></div>
                        </th>
                        <th class="px-4 py-3 text-center text-sm font-semibold border-r border-gray-200" id="day-header-3">
                            <div>Thứ 3</div>
                            <div class="text-gray-500 font-normal text-xs" id="date-3"></div>
                        </th>
                        <th class="px-4 py-3 text-center text-sm font-semibold border-r border-gray-200" id="day-header-4">
                            <div>Thứ 4</div>
                            <div class="text-gray-500 font-normal text-xs" id="date-4"></div>
                        </th>
                        <th class="px-4 py-3 text-center text-sm font-semibold border-r border-gray-200" id="day-header-5">
                            <div>Thứ 5</div>
                            <div class="text-gray-500 font-normal text-xs" id="date-5"></div>
                        </th>
                        <th class="px-4 py-3 text-center text-sm font-semibold border-r border-gray-200" id="day-header-6">
                            <div>Thứ 6</div>
                            <div class="text-gray-500 font-normal text-xs" id="date-6"></div>
                        </th>
                        <th class="px-4 py-3 text-center text-sm font-semibold border-r border-gray-200" id="day-header-7">
                            <div>Thứ 7</div>
                            <div class="text-gray-500 font-normal text-xs" id="date-7"></div>
                        </th>
                        <th class="px-4 py-3 text-center text-sm font-semibold" id="day-header-1">
                            <div>Chủ nhật</div>
                            <div class="text-gray-500 font-normal text-xs" id="date-1"></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-200 h-48">
                        <td class="px-4 py-3 bg-yellow-50 border-r border-gray-200 font-medium text-sm text-gray-700">Sáng</td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-morning-2"></td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-morning-3"></td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-morning-4"></td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-morning-5"></td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-morning-6"></td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-morning-7"></td>
                        <td class="px-2 py-2 align-top" id="cell-morning-1"></td>
                    </tr>
                    <tr class="border-b border-gray-200 h-48">
                        <td class="px-4 py-3 bg-yellow-50 border-r border-gray-200 font-medium text-sm text-gray-700">Chiều</td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-afternoon-2"></td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-afternoon-3"></td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-afternoon-4"></td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-afternoon-5"></td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-afternoon-6"></td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-afternoon-7"></td>
                        <td class="px-2 py-2 align-top" id="cell-afternoon-1"></td>
                    </tr>
                    <tr class="border-b border-gray-200 h-48">
                        <td class="px-4 py-3 bg-yellow-50 border-r border-gray-200 font-medium text-sm text-gray-700">Tối</td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-evening-2"></td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-evening-3"></td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-evening-4"></td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-evening-5"></td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-evening-6"></td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top" id="cell-evening-7"></td>
                        <td class="px-2 py-2 align-top" id="cell-evening-1"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Legend -->
        <div class="border-t border-gray-200 p-4">
            <div class="flex items-center gap-6 text-sm flex-wrap">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-gray-100 border border-gray-300 rounded"></div>
                    <span class="text-gray-600">Lịch học offline</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-blue-100 border border-blue-300 rounded"></div>
                    <span class="text-gray-600">Lịch học online</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentWeekStart = null;
    let scheduleData = {!! $scheduleDataJson !!};

    function initCalendar() {
        const today = new Date();
        const day = today.getDay(); // 0=Sunday, 6=Saturday
        let diff;
        
        if (day === 0) {
            diff = 1; // Nếu hôm nay là Chủ nhật, lấy Thứ 2 tuần sau
        } else if (day === 6) {
            diff = 2; // Nếu hôm nay là Thứ 7, lấy Thứ 2 tuần sau
        } else {
            diff = 1 - day; // Các ngày khác, lấy Thứ 2 tuần này
        }
        
        currentWeekStart = new Date(today);
        currentWeekStart.setDate(today.getDate() + diff);
        currentWeekStart.setHours(0, 0, 0, 0); // Reset time to midnight
        renderWeek();
    }

    function renderWeek() {
        // Clear all cells
        for (let period of ['morning', 'afternoon', 'evening']) {
            for (let day = 1; day <= 7; day++) {
                const cell = document.getElementById(`cell-${period}-${day}`);
                if (cell) cell.innerHTML = '';
            }
        }

        // Update header dates
        // currentWeekStart là Thứ 2 (Monday)
        // Cần map: Monday→id=2, Tuesday→id=3,...Saturday→id=7, Sunday→id=1
        for (let i = 0; i < 7; i++) {
            const date = new Date(currentWeekStart);
            date.setDate(currentWeekStart.getDate() + i);
            // i=0 (Mon)→2, i=1 (Tue)→3, i=2 (Wed)→4, i=3 (Thu)→5, i=4 (Fri)→6, i=5 (Sat)→7, i=6 (Sun)→1
            const columnId = i === 6 ? 1 : (i + 2);
            const dateStr = date.toLocaleDateString('vi-VN');
            const dateEl = document.getElementById(`date-${columnId}`);
            if (dateEl) dateEl.textContent = dateStr;
            
            // Highlight current day
            const header = document.getElementById(`day-header-${columnId}`);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (date.getTime() === today.getTime()) {
                header.querySelector('div').classList.add('text-blue-600');
            } else {
                header.querySelector('div').classList.remove('text-blue-600');
            }
        }

        // Fill schedule data
        scheduleData.forEach(schedule => {
            const scheduleDate = new Date(schedule.date + 'T00:00:00'); // Force local timezone
            
            // Check if schedule is in current week
            const weekEnd = new Date(currentWeekStart);
            weekEnd.setDate(currentWeekStart.getDate() + 7);
            
            if (scheduleDate >= currentWeekStart && scheduleDate < weekEnd) {
                // Calculate column ID based on days difference from Monday
                const daysDiff = Math.floor((scheduleDate - currentWeekStart) / (1000 * 60 * 60 * 24));
                // daysDiff: 0(Mon)→2, 1(Tue)→3, 2(Wed)→4, 3(Thu)→5, 4(Fri)→6, 5(Sat)→7, 6(Sun)→1
                const columnId = daysDiff === 6 ? 1 : (daysDiff + 2);
                
                const period = getPeriodFromTime(schedule.time);
                const cell = document.getElementById(`cell-${period}-${columnId}`);
                if (cell) {
                    cell.innerHTML += createScheduleCard(schedule);
                }
            }
        });

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function getPeriodFromTime(timeStr) {
        const hour = parseInt(timeStr.split(':')[0]);
        if (hour >= 6 && hour < 12) return 'morning';
        if (hour >= 12 && hour < 18) return 'afternoon';
        return 'evening';
    }

    function createScheduleCard(schedule) {
        // ĐỒNG BỘ MOBILE: Hiển thị trạng thái và nút tham gia
        const status = schedule.status || 'SapToi';
        const hasLink = schedule.link && schedule.link.trim() !== '';
        
        // Màu sắc theo trạng thái
        let bgColor = 'bg-gray-50';
        let borderColor = 'border-gray-200';
        let textColor = 'text-gray-700';
        let statusBadge = '';
        
        switch(status) {
            case 'DaHoc':
                bgColor = 'bg-green-50';
                borderColor = 'border-green-200';
                textColor = 'text-green-700';
                statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><span class="w-1.5 h-1.5 bg-green-600 rounded-full mr-1"></span>Đã dạy</span>';
                break;
            case 'Huy':
                bgColor = 'bg-red-50';
                borderColor = 'border-red-200';
                textColor = 'text-red-700';
                statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"><span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-1"></span>Đã hủy</span>';
                break;
            case 'DangDay':
                bgColor = 'bg-orange-50';
                borderColor = 'border-orange-200';
                textColor = 'text-orange-700';
                statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800"><span class="w-1.5 h-1.5 bg-orange-600 rounded-full mr-1"></span>Đang dạy</span>';
                break;
            default: // SapToi
                if (schedule.isOnline) {
                    bgColor = 'bg-blue-50';
                    borderColor = 'border-blue-200';
                    textColor = 'text-blue-700';
                }
                statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><span class="w-1.5 h-1.5 bg-blue-600 rounded-full mr-1"></span>Sắp tới</span>';
        }
        
        // Nút tham gia (chỉ hiển khi có link VÀ chưa hoàn thành/hủy)
        const canJoin = hasLink && status !== 'DaHoc' && status !== 'Huy';
        const joinButton = canJoin ? `<a href="${schedule.link}" target="_blank" onclick="event.stopPropagation()" class="inline-flex items-center px-3 py-1.5 mt-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all shadow-sm"><i data-lucide="video" class="w-3 h-3 mr-1"></i>Tham gia</a>` : '';
        
        return `
            <div class="mb-2 p-2 ${bgColor} border ${borderColor} rounded-lg hover:shadow-md transition-shadow cursor-pointer" onclick="showScheduleDetail(${schedule.id})">
                <div class="flex justify-between items-start mb-1">
                    <div class="text-xs font-semibold ${textColor}">${schedule.subject}</div>
                    ${statusBadge}
                </div>
                <div class="text-xs text-gray-600">${schedule.time}</div>
                ${schedule.tutor ? `<div class="text-xs text-gray-600 mt-0.5">GV: ${schedule.tutor}</div>` : ''}
                ${schedule.isOnline ? '<div class="text-xs text-blue-600 mt-1"><i data-lucide="monitor" class="w-3 h-3 inline"></i> Online</div>' : ''}
                ${joinButton}
            </div>
        `;
    }

    function previousWeek() {
        currentWeekStart.setDate(currentWeekStart.getDate() - 7);
        renderWeek();
    }

    function nextWeek() {
        currentWeekStart.setDate(currentWeekStart.getDate() + 7);
        renderWeek();
    }

    function goToToday() {
        initCalendar();
    }

    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            document.exitFullscreen();
        }
    }

    function showScheduleDetail(id) {
        // Tìm schedule data
        const schedule = scheduleData.find(s => s.id === id);
        if (!schedule) return;
        
        const statusText = {
            'DaHoc': 'Đã hoàn thành',
            'Huy': 'Đã hủy',
            'DangDay': 'Đang dạy',
            'SapToi': 'Sắp tới'
        }[schedule.status || 'SapToi'];
        
        alert(`Chi tiết lịch học:\n\nMôn: ${schedule.subject}\nGiáo viên: ${schedule.tutor}\nThời gian: ${schedule.time}\nTrạng thái: ${statusText}`);
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initCalendar();
        
        // Date picker change
        const datePicker = document.getElementById('datePicker');
        if (datePicker) {
            datePicker.addEventListener('change', function(e) {
                const selected = new Date(e.target.value);
                const day = selected.getDay();
                const diff = day === 0 ? -6 : 1 - day;
                currentWeekStart = new Date(selected);
                currentWeekStart.setDate(selected.getDate() + diff);
                renderWeek();
            });
        }

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
