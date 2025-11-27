@extends('admin.layouts.admin')

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Carbon;
    
    // Date range handling
    $from = request('from') ? Carbon::parse(request('from'))->startOfDay() : now()->subDays(30)->startOfDay();
    $to = request('to') ? Carbon::parse(request('to'))->endOfDay() : now()->endOfDay();
    $rapId = request('rap_id');
    $dateRange = $from->format('d/m/Y') . ' - ' . $to->format('d/m/Y');
    
    // Format numbers
    $formatNumber = function($number) {
        return number_format($number, 0, ',', '.');
    };
    
    $formatCurrency = function($amount) {
        return number_format($amount, 0, ',', '.') . 'đ';
    };
@endphp

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tổng quan</h1>
        <div class="d-flex">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="form-inline mr-3">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                    </div>
                    <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-control form-control-sm">
                    <div class="input-group-append">
                        <span class="input-group-text">đến</span>
                    </div>
                    <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-control form-control-sm">
                    @if(isset($cinemas) && $cinemas->count() > 0)
                    <select name="rap_id" class="form-control form-control-sm ml-2" style="max-width: 200px;">
                        <option value="">Tất cả rạp</option>
                        @foreach($cinemas as $cinema)
                            <option value="{{ $cinema->id }}" {{ $rapId == $cinema->id ? 'selected' : '' }}>
                                {{ $cinema->ten }}
                            </option>
                        @endforeach
                    </select>
                    @endif
                    <div class="input-group-append">
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                    </div>
                </div>
            </form>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary ml-2">
                <i class="fas fa-sync-alt"></i>
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <!-- Total Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng doanh thu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $formatCurrency($grossRevenue) }}</div>
                            <div class="mt-2 text-muted text-xs">
                                <span class="text-success">
                                    <i class="fas fa-ticket-alt"></i> {{ $formatNumber($ticketsSold) }} vé đã bán
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Order Value Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Giá trị đơn TB</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $paidOrders > 0 ? $formatCurrency($grossRevenue / $paidOrders) : $formatCurrency(0) }}
                            </div>
                            <div class="mt-2 text-muted text-xs">
                                <span class="text-info">
                                    <i class="fas fa-calculator"></i> Trung bình mỗi đơn
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversion Rate Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tỷ lệ chuyển đổi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalOrders > 0 ? round($paidOrders / $totalOrders * 100, 1) : 0 }}%</div>
                            <div class="mt-2">
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-info" style="width: {{ $totalOrders > 0 ? min(100, $paidOrders/$totalOrders*100) : 0 }}%"></div>
                                </div>
                                <small class="text-muted">{{ $paidOrders }}/{{ $totalOrders }} đơn thành công</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Customers Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Khách hàng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $formatNumber($totalCustomers ?? 0) }}</div>
                            <div class="mt-2 text-muted text-xs">
                                <span class="text-success">
                                    <i class="fas fa-user-plus"></i> {{ $formatNumber($newCustomers ?? 0) }} khách mới
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Summary Cards -->
    <div class="row">
        <!-- Tickets Sold Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Vé đã bán</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $formatNumber($ticketsSold) }}</div>
                            <div class="mt-2 text-muted text-xs">
                                <span class="text-primary">
                                    <i class="fas fa-ticket-alt"></i> Vé phim đã thanh toán
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Combo Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Doanh thu combo</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $formatCurrency($comboRevenue ?? 0) }}</div>
                            <div class="mt-2 text-muted text-xs">
                                <span class="text-info">
                                    <i class="fas fa-utensils"></i> Combo & đồ ăn
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-utensils fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-light shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">
                                Tổng đơn hàng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $formatNumber($totalOrders) }}</div>
                            <div class="mt-2 text-muted text-xs">
                                <span class="text-warning">
                                    <i class="fas fa-clock"></i> Đang chờ thanh toán: {{ $totalOrders - $paidOrders }}
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Occupancy Rate Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Tỷ lệ lấp đầy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $totalSeats = DB::table('ghe')->count();
                                    $occupiedSeats = $ticketsSold;
                                    $occupancyRate = $totalSeats > 0 ? round($occupiedSeats / $totalSeats * 100, 1) : 0;
                                @endphp
                                {{ $occupancyRate }}%
                            </div>
                            <div class="mt-2 text-muted text-xs">
                                <span class="text-danger">
                                    <i class="fas fa-chair"></i> Ghế đã đặt
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chair fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue & Tickets Chart -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tổng quan doanh thu theo giờ</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Tùy chọn:</div>
                            <a class="dropdown-item" href="#">Xuất báo cáo</a>
                            <a class="dropdown-item" href="#">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Phân bổ doanh thu</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="revenuePieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Vé phim
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Combo
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Khác
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Charts -->
    <div class="row">
        <!-- Daily Tickets Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Vé bán theo ngày</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="dailyTicketsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Revenue Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Doanh thu theo ngày</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="dailyRevenueChart"></canvas>
                    </div>
                </div>
            </div
        </div>
    </div>

    <!-- More Charts -->
    <div class="row">
        <!-- Customer Growth Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tăng trưởng khách hàng</h6>
                </div>
                <div class="card-body">
                    <div class="chart-line">
                        <canvas id="customerGrowthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status Distribution Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tỉ trọng số đơn theo trạng thái</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @if(isset($orderStatusCounts))
                        @foreach($orderStatusCounts as $status)
                            @php
                                $color = $status->trang_thai === 'cho_thanh_toan' ? 'warning' : ($status->trang_thai === 'da_thanh_toan' ? 'success' : ($status->trang_thai === 'da_huy' ? 'danger' : 'secondary'));
                                $text = $status->trang_thai === 'cho_thanh_toan' ? 'Chờ thanh toán' : ($status->trang_thai === 'da_thanh_toan' ? 'Đã thanh toán' : ($status->trang_thai === 'da_huy' ? 'Đã hủy' : 'Không xác định'));
                            @endphp
                            <span class="mr-2">
                                <i class="fas fa-circle text-{{ $color }}"></i> {{ $text }}
                            </span>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue and Tickets by Movie Charts -->
    <div class="row">
        <!-- Revenue by Movie Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Doanh thu theo phim (VNĐ)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="revenueByMovieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tickets by Movie Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Số vé bán theo phim</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="ticketsByMovieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Movies & Recent Orders -->
    <div class="row">
        <!-- Top Movies -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Phim bán chạy</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Phim</th>
                                    <th>Doanh thu</th>
                                    <th>Vé bán</th>
                                    <th>Đánh giá</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topMovies as $movie)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="font-weight-bold">{{ $movie->tieu_de }}</div>
                                                <small class="text-muted">{{ $movie->the_loai }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-primary font-weight-bold">{{ $formatCurrency($movie->revenue) }}</td>
                                    <td>{{ $formatNumber($movie->tickets_sold) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-2">{{ number_format($movie->rating, 1) }}</div>
                                            <div>
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= floor($movie->rating))
                                                        <i class="fas fa-star text-warning"></i>
                                                    @elseif($i == ceil($movie->rating) && $movie->rating - floor($movie->rating) >= 0.5)
                                                        <i class="fas fa-star-half-alt text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-warning"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Đơn hàng gần đây</h6>
                    <a href="{{ route('admin.donve.index') }}" class="btn btn-sm btn-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($recentOrders as $order)
                        <div class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <span class="badge badge-{{ $order->status_color }} mr-2">{{ $order->status_text }}</span>
                                    #{{ $order->id }}
                                </h6>
                                <small>{{ $order->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-1">{{ $order->customer_name }}</p>
                                    <small class="text-muted">{{ $order->movie_title }}</small>
                                </div>
                                <div class="text-right">
                                    <div class="font-weight-bold text-primary">{{ $formatCurrency($order->total_amount) }}</div>
                                    <small class="text-muted">{{ $order->ticket_count }} vé</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-2"></i>
                            <p>Chưa có đơn hàng nào gần đây</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Combos, Top Vouchers & Top Customers -->
    <div class="row">
        <!-- Top Combos -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Combo bán chạy</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Combo</th>
                                    <th>Số lượng</th>
                                    <th>Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCombos ?? [] as $combo)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $combo->ten }}</div>
                                    </td>
                                    <td>{{ $formatNumber($combo->quantity_sold) }}</td>
                                    <td class="text-primary font-weight-bold">{{ $formatCurrency($combo->revenue) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Vouchers -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top mã giảm giá sử dụng</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Mã giảm giá</th>
                                    <th>Lượt sử dụng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topVouchers ?? [] as $voucher)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $voucher->ten }}</div>
                                    </td>
                                    <td class="text-success font-weight-bold">{{ $formatNumber($voucher->usage_count) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top khách hàng (theo doanh thu)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Khách hàng</th>
                                    <th>Doanh thu</th>
                                    <th>Đơn hàng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCustomers ?? [] as $customer)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $customer->ho_ten }}</div>
                                        <small class="text-muted">{{ $customer->email }}</small>
                                    </td>
                                    <td class="text-primary font-weight-bold">{{ $formatCurrency($customer->revenue) }}</td>
                                    <td>{{ $formatNumber($customer->orders) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .chart-area {
        position: relative;
        height: 20rem;
        width: 100%;
    }
    .chart-pie {
        position: relative;
        height: 15rem;
        width: 100%;
    }
    .bg-gradient-primary {
        background: linear-gradient(87deg, #4e73df 0, #224abe 100%) !important;
    }
    .bg-gradient-success {
        background: linear-gradient(87deg, #1cc88a 0, #13855c 100%) !important;
    }
    .bg-gradient-info {
        background: linear-gradient(87deg, #36b9cc 0, #258391 100%) !important;
    }
    .bg-gradient-warning {
        background: linear-gradient(87deg, #f6c23e 0, #dda20a 100%) !important;
    }
</style>
@endpush

@push('scripts')
<!-- Page level plugins -->
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

<script>
// Revenue Chart
var ctx = document.getElementById('revenueChart');
var myLineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($hourLabels),
        datasets: [{
            label: "Doanh thu",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: @json($hourRevenue),
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 12
                }
            }],
            yAxes: [{
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value, index, values) {
                        return value.toLocaleString() + 'đ';
                    }
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
        },
        legend: {
            display: false
        },
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            intersect: false,
            mode: 'index',
            caretPadding: 10,
            callbacks: {
                label: function(tooltipItem, chart) {
                    var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                    return datasetLabel + ': ' + tooltipItem.yLabel.toLocaleString() + 'đ';
                }
            }
        }
    }
});

// Revenue Pie Chart
var ctx = document.getElementById("revenuePieChart");
var myPieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ["Vé phim", "Combo", "Khác"],
        datasets: [{
            data: [
                {{ $ticketRevenue }}, 
                {{ $comboRevenue }}, 
                {{ $productRevenue }}
            ],
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
            callbacks: {
                label: function(tooltipItem, data) {
                    var dataset = data.datasets[tooltipItem.datasetIndex];
                    var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
                        return previousValue + currentValue;
                    });
                    var currentValue = dataset.data[tooltipItem.index];
                    var percentage = Math.floor(((currentValue/total) * 100)+0.5);
                    return data.labels[tooltipItem.index] + ': ' + currentValue.toLocaleString() + 'đ (' + percentage + '%)';
                }
            }
        },
        legend: {
            display: false
        },
        cutoutPercentage: 80,
    },
});

// Daily Tickets Chart
var ctx = document.getElementById("dailyTicketsChart");
var myBarChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json(collect($dailyTickets)->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m'); })),
        datasets: [{
            label: "Vé bán",
            backgroundColor: "rgba(78, 115, 223, 0.8)",
            borderColor: "rgba(78, 115, 223, 1)",
            borderWidth: 1,
            data: @json(collect($dailyTickets)->pluck('tickets')),
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 7
                }
            }],
            yAxes: [{
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value, index, values) {
                        return value.toLocaleString();
                    }
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
        },
        legend: {
            display: false
        },
        tooltips: {
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
            callbacks: {
                label: function(tooltipItem, chart) {
                    var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                    return datasetLabel + ': ' + tooltipItem.yLabel.toLocaleString() + ' vé';
                }
            }
        },
    }
});

// Customer Growth Chart
var ctx = document.getElementById("customerGrowthChart");
var myLineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json(collect($customerGrowth)->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m'); })),
        datasets: [{
            label: "Khách hàng mới",
            lineTension: 0.3,
            backgroundColor: "rgba(28, 200, 138, 0.05)",
            borderColor: "rgba(28, 200, 138, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(28, 200, 138, 1)",
            pointBorderColor: "rgba(28, 200, 138, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(28, 200, 138, 1)",
            pointHoverBorderColor: "rgba(28, 200, 138, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: @json(collect($customerGrowth)->pluck('customers')),
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 7
                }
            }],
            yAxes: [{
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value, index, values) {
                        return value.toLocaleString();
                    }
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
        },
        legend: {
            display: false
        },
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            intersect: false,
            mode: 'index',
            caretPadding: 10,
            callbacks: {
                label: function(tooltipItem, chart) {
                    var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                    return datasetLabel + ': ' + tooltipItem.yLabel.toLocaleString() + ' khách';
                }
            }
        }
    }
});

// Daily Revenue Chart
var ctx = document.getElementById("dailyRevenueChart");
var myBarChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json(collect($dailyRevenue)->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m'); })),
        datasets: [{
            label: "Doanh thu",
            backgroundColor: "rgba(54, 185, 204, 0.8)",
            borderColor: "rgba(54, 185, 204, 1)",
            borderWidth: 1,
            data: @json(collect($dailyRevenue)->pluck('revenue')),
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 7
                }
            }],
            yAxes: [{
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value, index, values) {
                        return value.toLocaleString() + 'đ';
                    }
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
        },
        legend: {
            display: false
        },
        tooltips: {
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
            callbacks: {
                label: function(tooltipItem, chart) {
                    var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                    return datasetLabel + ': ' + tooltipItem.yLabel.toLocaleString() + 'đ';
                }
            }
        },
    }
});

// Order Status Chart
@php
$orderStatusLabels = collect($orderStatusCounts)->map(function($status) {
    return $status->trang_thai === 'cho_thanh_toan' ? 'Chờ thanh toán' : ($status->trang_thai === 'da_thanh_toan' ? 'Đã thanh toán' : ($status->trang_thai === 'da_huy' ? 'Đã hủy' : 'Không xác định'));
})->toArray();
@endphp
var ctx = document.getElementById("orderStatusChart");
var myPieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: @json($orderStatusLabels),
        datasets: [{
            data: @json(collect($orderStatusCounts)->pluck('count')),
            backgroundColor: ['#f6c23e', '#1cc88a', '#e74a3b'],
            hoverBackgroundColor: ['#dda20a', '#17a673', '#be2617'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
            callbacks: {
                label: function(tooltipItem, data) {
                    var dataset = data.datasets[tooltipItem.datasetIndex];
                    var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
                        return previousValue + currentValue;
                    });
                    var currentValue = dataset.data[tooltipItem.index];
                    var percentage = Math.floor(((currentValue/total) * 100)+0.5);
                    return data.labels[tooltipItem.index] + ': ' + currentValue.toLocaleString() + ' (' + percentage + '%)';
                }
            }
        },
        legend: {
            display: false
        },
        cutoutPercentage: 80,
    },
});

// Revenue by Movie Chart
var ctx = document.getElementById("revenueByMovieChart");
var myBarChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json(collect($topMovies)->pluck('tieu_de')),
        datasets: [{
            label: "Doanh thu",
            backgroundColor: "rgba(78, 115, 223, 0.8)",
            borderColor: "rgba(78, 115, 223, 1)",
            borderWidth: 1,
            data: @json(collect($topMovies)->pluck('revenue')),
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value, index, values) {
                        return value.toLocaleString() + 'đ';
                    }
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
            yAxes: [{
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 10
                }
            }],
        },
        legend: {
            display: false
        },
        tooltips: {
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
            callbacks: {
                title: function(tooltipItems, data) {
                    return data.labels[tooltipItems[0].index];
                },
                label: function(tooltipItem, chart) {
                    var value = tooltipItem.xLabel || tooltipItem.yLabel;
                    return 'Doanh thu: ' + value.toLocaleString() + ' VND';
                }
            }
        },
    }
});

// Tickets by Movie Chart
var ctx = document.getElementById("ticketsByMovieChart");
var myBarChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json(collect($topMovies)->pluck('tieu_de')),
        datasets: [{
            label: "Vé bán",
            backgroundColor: "rgba(28, 200, 138, 0.8)",
            borderColor: "rgba(28, 200, 138, 1)",
            borderWidth: 1,
            data: @json(collect($topMovies)->pluck('tickets_sold')),
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value, index, values) {
                        return value.toLocaleString();
                    }
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
            yAxes: [{
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 10
                }
            }],
        },
        legend: {
            display: false
        },
        tooltips: {
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
            callbacks: {
                title: function(tooltipItems, data) {
                    return data.labels[tooltipItems[0].index];
                },
                label: function(tooltipItem, chart) {
                    var value = tooltipItem.xLabel || tooltipItem.yLabel;
                    return 'Vé bán: ' + value.toLocaleString() + ' vé';
                }
            }
        },
    }
});
</script>
@endpush
