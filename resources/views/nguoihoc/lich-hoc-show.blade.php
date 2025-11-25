@extends('layouts.web')

@section('title', 'Lịch học: ' . $lopHoc->monHoc->TenMon)

@section('content')
<div class="w-full px-4" x-data="{ modalOpen: false, selectedEvent: {} }">
    
    <div class="mb-4">
        <a href="{{ route('nguoihoc.lophoc.index') }}" class="inline-flex items-center text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Quay lại Lớp học của tôi
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <div class="border-b border-gray-200 p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1">
                        {{ $lopHoc->monHoc->TenMon }}
                    </h1>
                    <p class="text-sm text-gray-500 font-medium">
                        {{ $lopHoc->khoiLop->BacHoc }} • GV: {{ $lopHoc->giaSu->HoTen ?? 'Chưa cập nhật' }}
                    </p>
                </div>
                
                <div class="flex items-center gap-4 flex-wrap">
                    <input type="date" id="datePicker" value="{{ now()->format('Y-m-d') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    
                    <button onclick="goToToday()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm">
                        <i data-lucide="calendar-days" class="w-4 h-4 mr-2"></i>
                        Hiện tại
                    </button>
                    
                    <div class="flex items-center gap-2">
                        <button onclick="previousWeek()" class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i data-lucide="chevron-left" class="w-5 h-5"></i>
                        </button>
                        <button onclick="nextWeek()" class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i data-lucide="chevron-right" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

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
                    @foreach(['morning' => 'Sáng', 'afternoon' => 'Chiều', 'evening' => 'Tối'] as $key => $label)
                    <tr class="border-b border-gray-200 h-40">
                        <td class="px-4 py-3 bg-yellow-50 border-r border-gray-200 font-medium text-sm text-gray-700">{{ $label }}</td>
                        @for($i=2; $i<=7; $i++) <td class="px-1 py-1 border-r border-gray-200 align-top" id="cell-{{$key}}-{{$i}}"></td> @endfor
                        <td class="px-1 py-1 align-top" id="cell-{{$key}}-1"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 p-4 bg-gray-50 rounded-b-2xl">
            <div class="flex items-center gap-6 text-sm flex-wrap">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-green-100 border border-green-300 rounded"></div>
                    <span class="text-gray-600">Đã học</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-blue-100 border border-blue-300 rounded"></div>
                    <span class="text-gray-600">Sắp tới (Online)</span>
                </div>
            </div>
        </div>
    </div>

    <div x-show="modalOpen" style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-50 backdrop-blur-sm"
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        
        <div @click.away="modalOpen = false" 
             class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden"
             x-show="modalOpen"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Chi tiết buổi học</h3>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="flex items-start gap-3">
                    <div class="mt-1 p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <i data-lucide="book-open" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Môn học</p>
                        <p class="text-gray-900 font-medium" x-text="selectedEvent.monHoc"></p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="mt-1 p-2 bg-purple-50 text-purple-600 rounded-lg">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Thời gian</p>
                        <p class="text-gray-900 font-medium" x-text="selectedEvent.thoiGianBatDau"></p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="mt-1 p-2 bg-orange-50 text-orange-600 rounded-lg">
                        <i data-lucide="user" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Gia sư</p>
                        <p class="text-gray-900 font-medium" x-text="selectedEvent.giaSuTen"></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-2">
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <span class="text-xs text-gray-500 block mb-1 uppercase font-semibold">Hình thức</span>
                        <span class="font-medium text-sm text-gray-900" x-text="selectedEvent.hinhThuc"></span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <span class="text-xs text-gray-500 block mb-1 uppercase font-semibold">Trạng thái</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800" x-text="selectedEvent.trangThai"></span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" @click="modalOpen = false" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-white transition-colors">
                    Đóng
                </button>
                <a :href="selectedEvent.duongDan" target="_blank" 
                   x-show="selectedEvent.hinhThuc === 'Online' && selectedEvent.duongDan"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 shadow-sm transition-colors flex items-center">
                    <i data-lucide="video" class="w-4 h-4 mr-2"></i>
                    Vào học
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentWeekStart = null;
    let scheduleData = [];

    document.addEventListener('DOMContentLoaded', function() {
        // 1. LẤY & MAP DỮ LIỆU
        const rawEvents = {!! $calendarDataJson !!};

        scheduleData = rawEvents.map(event => {
            const startDate = new Date(event.start);
            const timeStr = startDate.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
            const dateStr = event.start.split('T')[0];

            return {
                id: event.id,
                date: dateStr,
                time: timeStr,
                subject: event.title,
                tutor: event.extendedProps.giaSuTen,
                // Ưu tiên dùng mã trạng thái (DaHoc, Huy...) nếu backend trả về, 
                // nếu không thì mặc định là 'SapToi'
                status: event.extendedProps.trangThaiRaw || 'SapToi', 
                statusLabel: event.extendedProps.trangThai,
                isOnline: event.extendedProps.hinhThuc === 'Online',
                link: event.extendedProps.duongDan,
                hinhThuc: event.extendedProps.hinhThuc,
                fullData: event.extendedProps
            };
        });

        initCalendar();

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

        if (typeof lucide !== 'undefined') lucide.createIcons();
    });

    function initCalendar() {
        const today = new Date();
        const day = today.getDay();
        let diff = (day === 0) ? 1 : ((day === 6) ? 2 : (1 - day));
        
        currentWeekStart = new Date(today);
        currentWeekStart.setDate(today.getDate() + diff);
        currentWeekStart.setHours(0, 0, 0, 0);
        renderWeek();
    }

    function renderWeek() {
        ['morning', 'afternoon', 'evening'].forEach(period => {
            for (let day = 1; day <= 7; day++) {
                const cell = document.getElementById(`cell-${period}-${day}`);
                if (cell) cell.innerHTML = '';
            }
        });

        for (let i = 0; i < 7; i++) {
            const date = new Date(currentWeekStart);
            date.setDate(currentWeekStart.getDate() + i);
            const columnId = i === 6 ? 1 : (i + 2);
            
            const dateEl = document.getElementById(`date-${columnId}`);
            if (dateEl) dateEl.textContent = date.toLocaleDateString('vi-VN');
            
            const header = document.getElementById(`day-header-${columnId}`);
            const today = new Date(); today.setHours(0,0,0,0);
            
            if (date.getTime() === today.getTime()) {
                header.querySelector('div').classList.add('text-blue-600', 'font-bold');
            } else {
                header.querySelector('div').classList.remove('text-blue-600', 'font-bold');
            }
        }

        scheduleData.forEach(schedule => {
            const scheduleDate = new Date(schedule.date + 'T00:00:00');
            const weekEnd = new Date(currentWeekStart);
            weekEnd.setDate(currentWeekStart.getDate() + 7);
            
            if (scheduleDate >= currentWeekStart && scheduleDate < weekEnd) {
                const daysDiff = Math.floor((scheduleDate - currentWeekStart) / (1000 * 60 * 60 * 24));
                const columnId = daysDiff === 6 ? 1 : (daysDiff + 2);
                const period = getPeriodFromTime(schedule.time);
                const cell = document.getElementById(`cell-${period}-${columnId}`);
                if (cell) cell.innerHTML += createScheduleCard(schedule);
            }
        });

        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function getPeriodFromTime(timeStr) {
        const hour = parseInt(timeStr.split(':')[0]);
        if (hour >= 6 && hour < 12) return 'morning';
        if (hour >= 12 && hour < 18) return 'afternoon';
        return 'evening';
    }

    // --- LOGIC HIỂN THỊ CHI TIẾT (Giống file calendar gốc) ---
    function createScheduleCard(schedule) {
        // 1. Xác định trạng thái
        const status = schedule.status; 
        const hasLink = schedule.link && schedule.link.trim() !== '';
        
        // 2. Định nghĩa màu sắc mặc định
        let bgColor = 'bg-gray-50';
        let borderColor = 'border-gray-200';
        let textColor = 'text-gray-700';
        let statusBadge = '';
        
        // 3. Switch case giống hệt file gốc
        switch(status) {
            case 'DaHoc':
                bgColor = 'bg-green-50';
                borderColor = 'border-green-200';
                textColor = 'text-green-700';
                statusBadge = '<span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-800"><span class="w-1 h-1 bg-green-600 rounded-full mr-1"></span>Đã dạy</span>';
                break;
            case 'Huy':
                bgColor = 'bg-red-50';
                borderColor = 'border-red-200';
                textColor = 'text-red-700';
                statusBadge = '<span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-red-100 text-red-800"><span class="w-1 h-1 bg-red-600 rounded-full mr-1"></span>Đã hủy</span>';
                break;
            case 'DangDay':
                bgColor = 'bg-orange-50';
                borderColor = 'border-orange-200';
                textColor = 'text-orange-700';
                statusBadge = '<span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-orange-100 text-orange-800"><span class="w-1 h-1 bg-orange-600 rounded-full mr-1"></span>Đang dạy</span>';
                break;
            default: // SapToi
                if (schedule.isOnline) {
                    bgColor = 'bg-blue-50';
                    borderColor = 'border-blue-200';
                    textColor = 'text-blue-700';
                }
                statusBadge = '<span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-800"><span class="w-1 h-1 bg-blue-600 rounded-full mr-1"></span>Sắp tới</span>';
        }
        
        // 4. Nút tham gia (chỉ hiện khi chưa học xong/hủy và có link)
        const canJoin = hasLink && status !== 'DaHoc' && status !== 'Huy';
        const joinButton = canJoin 
            ? `<a href="${schedule.link}" target="_blank" onclick="event.stopPropagation()" class="inline-flex items-center px-2 py-1 mt-1.5 bg-blue-600 text-white text-[10px] font-bold rounded hover:bg-blue-700 transition-all shadow-sm w-full justify-center"><i data-lucide="video" class="w-3 h-3 mr-1"></i>Vào học</a>` 
            : '';
        
        // Chuẩn bị data cho modal onclick
        const dataStr = encodeURIComponent(JSON.stringify(schedule));

        // 5. Trả về HTML thẻ card đầy đủ
        return `
            <div class="mb-2 p-2 ${bgColor} border ${borderColor} rounded-lg hover:shadow-md transition-all cursor-pointer group" 
                 onclick="openDetailModal('${dataStr}')">
                
                <div class="flex justify-between items-start mb-1 gap-1">
                    <div class="text-xs font-bold ${textColor} truncate flex-1" title="${schedule.subject}">
                        ${schedule.subject}
                    </div>
                    ${statusBadge}
                </div>
                
                <div class="text-[11px] text-gray-600 font-medium flex items-center gap-1">
                    <i data-lucide="clock" class="w-3 h-3 text-gray-400"></i> ${schedule.time}
                </div>
                
                ${schedule.tutor ? `<div class="text-[11px] text-gray-500 mt-0.5 truncate"><i data-lucide="user" class="w-3 h-3 text-gray-400 inline"></i> GV: ${schedule.tutor}</div>` : ''}
                
                ${schedule.isOnline ? '<div class="text-[10px] text-blue-600 mt-1 font-medium"><i data-lucide="monitor" class="w-3 h-3 inline"></i> Online</div>' : ''}
                
                ${joinButton}
            </div>
        `;
    }

    function openDetailModal(encodedData) {
        const data = JSON.parse(decodeURIComponent(encodedData));
        const alpine = document.querySelector('[x-data]').__x;
        
        alpine.$data.selectedEvent = {
            monHoc: data.subject,
            giaSuTen: data.tutor,
            thoiGianBatDau: `${data.time} - ${data.date}`,
            hinhThuc: data.hinhThuc,
            trangThai: data.statusLabel,
            duongDan: data.link
        };
        alpine.$data.modalOpen = true;
        setTimeout(() => lucide.createIcons(), 50);
    }

    function previousWeek() { currentWeekStart.setDate(currentWeekStart.getDate() - 7); renderWeek(); }
    function nextWeek() { currentWeekStart.setDate(currentWeekStart.getDate() + 7); renderWeek(); }
    function goToToday() { initCalendar(); }
</script>
@endpush