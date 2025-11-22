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

<!-- ĐỒNG BỘ MOBILE: Modal sửa lịch học -->
<div id="editScheduleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-8 border w-full max-w-md shadow-2xl rounded-2xl bg-white">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">Cập nhật buổi học</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form id="editScheduleForm" onsubmit="submitEditSchedule(event)" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="editLichHocId" name="lich_hoc_id">
            
            <!-- Trạng thái -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Trạng thái</label>
                <select id="editTrangThai" name="TrangThai" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="SapToi">Sắp tới</option>
                    <option value="DaHoc">Đã hoàn thành</option>
                    <option value="Huy">Hủy buổi học</option>
                </select>
            </div>
            
            <!-- Link học online (chỉ hiện với lớp online) -->
            <div id="editLinkGroup" class="mb-6 hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Link học Online</label>
                <input type="url" id="editDuongDan" name="DuongDan" placeholder="https://meet.google.com/..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Cập nhật link để học viên có thể tham gia lớp</p>
            </div>
            
            <!-- Buttons -->
            <div class="flex gap-3">
                <button type="button" onclick="closeEditModal()" class="flex-1 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                    Hủy
                </button>
                <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentWeekStart = null;
    let scheduleData = {!! $scheduleDataJson !!};

    function initCalendar() {
        const today = new Date();
        const day = today.getDay(); // 0=Sunday, 1=Monday, 2=Tuesday,...6=Saturday
        let diff;
        
        // Tính số ngày đến thứ 2 tuần hiện tại/sau
        if (day === 0) {
            diff = 1; // Chủ nhật → Thứ 2 tuần sau
        } else if (day === 6) {
            diff = 2; // Thứ 7 → Thứ 2 tuần sau
        } else {
            diff = 1 - day; // Thứ khác → Thứ 2 tuần này
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
                document.getElementById(`cell-${period}-${day}`).innerHTML = '';
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
            document.getElementById(`date-${columnId}`).textContent = dateStr;
            
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

        lucide.createIcons();
    }

    function getPeriodFromTime(timeStr) {
        const hour = parseInt(timeStr.split(':')[0]);
        if (hour >= 6 && hour < 12) return 'morning';
        if (hour >= 12 && hour < 18) return 'afternoon';
        return 'evening';
    }

    function createScheduleCard(schedule) {
        // Xác định màu sắc theo trạng thái - ĐỒNG BỘ MOBILE
        let bgColor, borderColor, textColor, statusBadge;
        
        switch(schedule.status) {
            case 'DaHoc':
                bgColor = 'bg-green-50';
                borderColor = 'border-green-200';
                textColor = 'text-green-700';
                statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"><span class="w-1.5 h-1.5 mr-1 bg-green-600 rounded-full"></span>Đã dạy</span>';
                break;
            case 'Huy':
                bgColor = 'bg-red-50';
                borderColor = 'border-red-200';
                textColor = 'text-red-700';
                statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800"><span class="w-1.5 h-1.5 mr-1 bg-red-600 rounded-full"></span>Đã hủy</span>';
                break;
            case 'DangDay':
                bgColor = 'bg-orange-50';
                borderColor = 'border-orange-200';
                textColor = 'text-orange-700';
                statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800"><span class="w-1.5 h-1.5 mr-1 bg-orange-600 rounded-full"></span>Đang dạy</span>';
                break;
            default: // SapToi, ChuaDienRa
                bgColor = schedule.isOnline ? 'bg-blue-50' : 'bg-gray-50';
                borderColor = schedule.isOnline ? 'border-blue-200' : 'border-gray-200';
                textColor = schedule.isOnline ? 'text-blue-700' : 'text-gray-700';
                statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"><span class="w-1.5 h-1.5 mr-1 bg-blue-600 rounded-full"></span>Sắp tới</span>';
        }
        
        // ĐỒNG BỘ MOBILE: Nút "Tham gia" chỉ hiện khi có link VÀ không phải Đã học/Hủy
        const hasLink = schedule.link && schedule.link.trim() !== '';
        const canJoin = hasLink && schedule.status !== 'DaHoc' && schedule.status !== 'Huy';
        
        // ĐỒNG BỘ MOBILE: Nút "Cập nhật" chỉ hiện khi không phải Đã học/Hủy
        const canUpdate = schedule.status !== 'DaHoc' && schedule.status !== 'Huy';
        
        let buttons = '';
        if (canUpdate) {
            buttons += `<button onclick="openEditModal(${schedule.id}, '${schedule.status}', '${schedule.link || ''}', ${schedule.isOnline})" class="mt-2 w-full px-2 py-1 text-xs font-medium text-orange-600 bg-orange-50 border border-orange-200 rounded hover:bg-orange-100 transition-colors"><i data-lucide="edit" class="w-3 h-3 inline mr-1"></i>Cập nhật</button>`;
        }
        
        if (canJoin) {
            buttons += `<a href="${schedule.link}" target="_blank" class="block mt-2 px-2 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition-colors text-center" onclick="event.stopPropagation();"><i data-lucide="video" class="w-3 h-3 inline mr-1"></i>Tham gia</a>`;
        }
        
        return `
            <div class="mb-2 p-2 ${bgColor} border ${borderColor} rounded-lg hover:shadow-md transition-shadow cursor-pointer" onclick="showScheduleDetail(${schedule.id})">
                <div class="flex justify-between items-start mb-1">
                    <div class="text-xs font-semibold ${textColor}">${schedule.subject}</div>
                    ${statusBadge}
                </div>
                <div class="text-xs text-gray-600">${schedule.time}</div>
                <div class="text-xs text-gray-600 mt-0.5">${schedule.student}</div>
                ${schedule.isOnline ? '<div class="text-xs text-blue-600 mt-1"><i data-lucide="monitor" class="w-3 h-3 inline"></i> Online</div>' : ''}
                ${buttons}
            </div>
        `;
    }

    function showScheduleDetail(id) {
        // Tìm schedule data
        const schedule = scheduleData.find(s => s.id === id);
        if (!schedule) return;
        
        alert(`Chi tiết lịch học:\n\nMôn: ${schedule.subject}\nHọc viên: ${schedule.student}\nThời gian: ${schedule.time}\nTrạng thái: ${schedule.status}`);
    }

    function openEditModal(lichHocId, currentStatus, currentLink, isOnline) {
        const modal = document.getElementById('editScheduleModal');
        const form = document.getElementById('editScheduleForm');
        
        // Set form action with Laravel route
        form.setAttribute('data-lich-hoc-id', lichHocId);
        document.getElementById('editLichHocId').value = lichHocId;
        document.getElementById('editTrangThai').value = currentStatus || 'SapToi';
        document.getElementById('editDuongDan').value = currentLink || '';
        
        // Hiển thị/ẩn trường link
        const linkGroup = document.getElementById('editLinkGroup');
        if (isOnline) {
            linkGroup.classList.remove('hidden');
        } else {
            linkGroup.classList.add('hidden');
        }
        
        modal.classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editScheduleModal').classList.add('hidden');
    }

    function submitEditSchedule(event) {
        event.preventDefault();
        const form = event.target;
        const lichHocId = form.getAttribute('data-lich-hoc-id');
        const formData = new FormData(form);
        
        // ĐỒNG BỘ MOBILE: Gọi route updateSchedule với PUT method
        fetch(`/giasu/lop-hoc/lich-hoc/${lichHocId}/sua`, {
            method: 'POST', // Laravel form method spoofing
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text(); // Laravel may redirect with HTML
        })
        .then(responseText => {
            // ĐỒNG BỘ MOBILE: Laravel trả về redirect, coi như thành công
            closeEditModal();
            location.reload(); // Reload để cập nhật lịch
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi kết nối server');
        });
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
