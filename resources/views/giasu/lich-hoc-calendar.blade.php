@extends('layouts.web')

@section('title', 'Lịch học, lịch thi theo tuần')

@section('content')
<div class="w-full px-4">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="border-b border-gray-200 p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Lịch học, lịch thi theo tuần</h1>
            
            <!-- Toolbar -->
            <div class="flex items-center gap-4 mt-4">
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
                
                <button onclick="printSchedule()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">
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
                    <button onclick="toggleNextWeek()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        Tiếp <i data-lucide="chevron-down" class="w-4 h-4 inline ml-1"></i>
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
            <div class="flex items-center gap-6 text-sm">
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
        const day = today.getDay();
        const diff = day === 0 ? -6 : 1 - day; // Monday of current week
        currentWeekStart = new Date(today);
        currentWeekStart.setDate(today.getDate() + diff);
        renderWeek();
    }

    function renderWeek() {
        // Clear all cells
        for (let period of ['morning', 'afternoon', 'evening']) {
            for (let day = 1; day <= 7; day++) {
                document.getElementById(`cell-${period}-${day}`).innerHTML = '';
            }
        }

        // Update header dates
        for (let i = 0; i < 7; i++) {
            const date = new Date(currentWeekStart);
            date.setDate(currentWeekStart.getDate() + i);
            const dayOfWeek = date.getDay() === 0 ? 7 : date.getDay();
            const dateStr = date.toLocaleDateString('vi-VN');
            document.getElementById(`date-${dayOfWeek}`).textContent = dateStr;
            
            // Highlight current day
            const header = document.getElementById(`day-header-${dayOfWeek}`);
            const today = new Date();
            if (date.toDateString() === today.toDateString()) {
                header.querySelector('div').classList.add('text-blue-600');
            } else {
                header.querySelector('div').classList.remove('text-blue-600');
            }
        }

        // Fill schedule data
        scheduleData.forEach(schedule => {
            const scheduleDate = new Date(schedule.date);
            const dayOfWeek = scheduleDate.getDay() === 0 ? 7 : scheduleDate.getDay();
            
            // Check if schedule is in current week
            if (scheduleDate >= currentWeekStart) {
                const weekEnd = new Date(currentWeekStart);
                weekEnd.setDate(currentWeekStart.getDate() + 7);
                if (scheduleDate < weekEnd) {
                    const period = getPeriodFromTime(schedule.time);
                    const cell = document.getElementById(`cell-${period}-${dayOfWeek}`);
                    if (cell) {
                        cell.innerHTML += createScheduleCard(schedule);
                    }
                }
            }
        });

        lucide.createIcons();
    }

    function getPeriodFromTime(timeStr) {
        const hour = parseInt(timeStr.split(':')[0]);
        if (hour >= 6 && hour < 12) return 'morning';
        if (hour >= 12 && hour < 18) return 'afternoon';
        return 'evening';
    }

    function createScheduleCard(schedule) {
        const bgColor = schedule.isOnline ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200';
        const textColor = schedule.isOnline ? 'text-blue-700' : 'text-gray-700';
        
        // Check if class is happening now or within next 30 minutes
        const now = new Date();
        const scheduleDateTime = new Date(schedule.date + ' ' + schedule.time);
        const timeDiff = scheduleDateTime - now;
        const isClassTime = timeDiff >= 0 && timeDiff <= 30 * 60 * 1000; // Within 30 minutes
        
        let joinButton = '';
        if (schedule.isOnline && schedule.link && isClassTime) {
            joinButton = `<a href="${schedule.link}" target="_blank" class="block mt-2 px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors text-center" onclick="event.stopPropagation();"><i data-lucide="video" class="w-3 h-3 inline mr-1"></i>Tham gia ngay</a>`;
        }
        
        return `
            <div class="mb-2 p-2 ${bgColor} border rounded-lg hover:shadow-md transition-shadow">
                <div class="text-xs font-semibold ${textColor}">${schedule.subject}</div>
                <div class="text-xs text-gray-600 mt-1">${schedule.time}</div>
                ${schedule.isOnline ? '<div class="text-xs text-blue-600 mt-1"><i data-lucide="video" class="w-3 h-3 inline"></i> Online</div>' : ''}
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

    function printSchedule() {
        window.print();
    }

    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            document.exitFullscreen();
        }
    }

    function toggleNextWeek() {
        // Toggle show/hide next week - implement if needed
    }

    function showScheduleDetail(id) {
        // Show schedule detail modal - implement if needed
        console.log('Show detail for schedule:', id);
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initCalendar();
        
        // Date picker change
        document.getElementById('datePicker').addEventListener('change', function(e) {
            const selected = new Date(e.target.value);
            const day = selected.getDay();
            const diff = day === 0 ? -6 : 1 - day;
            currentWeekStart = new Date(selected);
            currentWeekStart.setDate(selected.getDate() + diff);
            renderWeek();
        });

        lucide.createIcons();
    });
</script>
@endpush
