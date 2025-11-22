@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <!-- Header Chào mừng -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-white mb-0">Chào mừng trở lại, Admin!</h2>
            <p>Đây là tổng quan về hoạt động của nền tảng.</p>
        </div>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fa-solid fa-calendar-days me-2"></i> 30 ngày qua
            </button>
            <ul class="dropdown-menu dropdown-menu-dark">
                <li><a class="dropdown-item" href="#">7 ngày qua</a></li>
                <li><a class="dropdown-item" href="#">30 ngày qua</a></li>
                <li><a class="dropdown-item" href="#">90 ngày qua</a></li>
            </ul>
        </div>
    </div>

    <!-- Hàng Thẻ Thống kê -->
    <div class="row">
        {{-- Kiểm tra xem biến $_stats có tồn tại không --}}
        @if(isset($_stats) && is_array($_stats))
            @foreach($_stats as $stat)
            <div class="col-lg col-md-6 mb-4">
                <div class="card stat-card p-3">
                    <p class=" mb-2">{{ $stat['title'] }}</p>
                    <h3 class="mb-1 text-white h2">
                        @if(str_contains($stat['title'], 'Doanh Thu'))
                            {{ number_format($stat['count'], 0, ',', '.') }} VNĐ
                        @else
                            {{ number_format($stat['count']) }}
                        @endif
                    </h3>
                    <p class="small {{ $stat['color'] }} mb-0">{{ $stat['percent'] }}</p> 
                </div>
            </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card p-3 text-center">
                    <p class="texr-white 50">Không có dữ liệu thống kê.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Hàng Biểu đồ -->
    <div class="row mt-4">
        <!-- Biểu đồ đường (Line Chart) -->
        <div class="col-lg-8 mb-4">
            <div class="card p-4">
                <h5 class="card-title text-white mb-3">Xu hướng doanh thu (6 tuần gần nhất)</h5>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Biểu đồ tròn (Doughnut Chart) -->
        <div class="col-lg-4 mb-4">
            <div class="card p-4 text-center">
                <h5 class="card-title text-white mb-3">Phân bố người dùng</h5>
                <canvas id="userDistributionChart" style="max-height: 250px; margin: auto;"></canvas>
                <div class="mt-3">
                    <h4 class="text-white h3">{{ number_format(($tongNguoiHoc ?? 0) + ($tongGiaSu ?? 0)) }}</h4>
                    <p>Tổng số người dùng</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Mã JavaScript để vẽ biểu đồ -->
<script>
    const chartTextColor = '#e0e0e0';

    // Dữ liệu cho Biểu đồ đường (Lấy từ PHP)
    const revenueData = {
        labels: {!! json_encode($revenueChartLabels ?? []) !!},
        datasets: [{
            label: 'Doanh thu',
            data: {!! json_encode($revenueChartData ?? []) !!},
            borderColor: '#3b82f6', 
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            fill: true,
            tension: 0.4 
        }]
    };

    // Vẽ biểu đồ đường
    if (document.getElementById('revenueChart')) {
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: revenueData,
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, ticks: { color: chartTextColor, font: { size: 13 } } },
                    x: { ticks: { color: chartTextColor, font: { size: 13 } } }
                },
                plugins: {
                    legend: { labels: { color: chartTextColor, font: { size: 14 } } } 
                }
            }
        });
    }

    // Dữ liệu cho Biểu đồ tròn (Lấy từ PHP)
    const distributionData = {
        labels: ['Người học', 'Gia sư'],
        datasets: [{
            label: 'Phân bố',
            data: [{{ $tongNguoiHoc ?? 0 }}, {{ $tongGiaSu ?? 0 }}], 
            backgroundColor: ['#3b82f6', '#10b981'], 
            hoverOffset: 4,
            borderColor: 'transparent'
        }]
    };

    // Vẽ biểu đồ tròn
    if (document.getElementById('userDistributionChart')) {
        new Chart(document.getElementById('userDistributionChart'), {
            type: 'doughnut',
            data: distributionData,
            options: { 
                responsive: true,
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: { 
                            color: chartTextColor,
                            font: {
                                size: 14 
                            }
                        } 
                    }
                }
            }
        });
    }
</script>
@endpush