@extends('admin.layouts.admin')

@section('content')
    @php
        use Illuminate\Support\Arr;

        // --- Filter defaults ---
        $from = request('from') ?: now()->firstOfMonth()->toDateString();
        $to = request('to') ?: now()->toDateString();
        $rapId = request('rap_id'); // optional

        // --- Numbers from controller (có fallback 0/[] để tránh lỗi) ---
        $grossRevenue = (float) ($grossRevenue ?? 0); // Tổng doanh thu (vé+combo+SP)
        $comboRevenue = (float) ($comboRevenue ?? 0);
        $productRevenue = (float) ($productRevenue ?? 0);
        $ticketRevenue = max($grossRevenue - $comboRevenue - $productRevenue, 0);
        $ticketsSold = (int) ($ticketsSold ?? 0);
        $paymentRate = (float) ($paymentRate ?? 0); // tỉ lệ thanh toán thành công (%)
        $refundAmount = (float) ($refundAmount ?? 0);
        $canceledOrders = (int) ($canceledOrders ?? 0);

        // Biểu đồ
        $hourLabels = $hourLabels ?? []; // ["08:00", "09:00", ...]
        $hourRevenue = $hourRevenue ?? []; // [100000, 200000, ...]

        // Bảng
        $topMovies = $topMovies ?? []; // [['ten' => '...', 'revenue'=>..., 'tickets'=>...], ...]
        $topCustomers = $topCustomers ?? []; // [['ho_ten'=>'','email'=>'','total'=>...,'orders'=>...], ...]
        $latestOrders = $latestOrders ?? []; // [['ma_don'=>'','created_at'=>'','khach'=>'','amount'=>...,'status'=>'','pay_status'=>''], ...]
        $revenueByCinema = $revenueByCinema ?? []; // [['rap'=>'','revenue'=>...,'tickets'=>...], ...]
    @endphp

    <div class="container-fluid">

        {{-- PAGE HEADER + FILTERS --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-3">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            <div class="d-none d-sm-inline-block">
                <a href="{{ route('admin.dashboard', ['from' => $from, 'to' => $to, 'rap_id' => $rapId]) }}"
                    class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-sync-alt fa-sm text-white-50"></i> Làm mới
                </a>
                <a href="{{ route('admin.reports.revenue.total') }}?from={{ $from }}&to={{ $to }}@if ($rapId) &rap_id={{ $rapId }} @endif"
                    class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-chart-line fa-sm text-white-50"></i> Xem báo cáo
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.dashboard') }}" class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="small text-muted mb-1">Từ ngày</label>
                        <input type="date" name="from" value="{{ $from }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted mb-1">Đến ngày</label>
                        <input type="date" name="to" value="{{ $to }}" class="form-control">
                    </div>

                    @if (!empty($cinemas))
                        <div class="col-md-3">
                            <label class="small text-muted mb-1">Rạp</label>
                            <select name="rap_id" class="form-control">
                                <option value="">Tất cả rạp</option>
                                @foreach ($cinemas as $c)
                                    <option value="{{ $c->id }}"
                                        {{ (string) $rapId === (string) $c->id ? 'selected' : '' }}>
                                        {{ $c->ten }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-md-3">
                        <button class="btn btn-primary"><i class="fas fa-filter"></i> Lọc</button>
                    </div>
                </div>
            </div>
        </form>

        {{-- KPI ROW 1 --}}
        <div class="row">
            {{-- Doanh thu vé --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <a class="text-decoration-none"
                    href="{{ route('admin.reports.revenue.tickets') }}?from={{ $from }}&to={{ $to }}@if ($rapId) &rap_id={{ $rapId }} @endif">
                    <div class="card border-left-primary shadow h-100 py-2 hover-shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col me-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Doanh thu vé
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($ticketRevenue, 0, ',', '.') }}đ
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Doanh thu combo --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <a class="text-decoration-none"
                    href="{{ route('admin.reports.revenue.combos') }}?from={{ $from }}&to={{ $to }}@if ($rapId) &rap_id={{ $rapId }} @endif">
                    <div class="card border-left-success shadow h-100 py-2 hover-shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col me-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Doanh thu combo
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($comboRevenue, 0, ',', '.') }}đ</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-hamburger fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Doanh thu sản phẩm --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <a class="text-decoration-none"
                    href="{{ route('admin.reports.revenue.products') }}?from={{ $from }}&to={{ $to }}@if ($rapId) &rap_id={{ $rapId }} @endif">
                    <div class="card border-left-warning shadow h-100 py-2 hover-shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col me-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Doanh thu sản
                                        phẩm</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($productRevenue, 0, ',', '.') }}đ</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Vé đã bán --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <a class="text-decoration-none"
                    href="{{ route('admin.reports.tickets') }}?from={{ $from }}&to={{ $to }}@if ($rapId) &rap_id={{ $rapId }} @endif">
                    <div class="card border-left-danger shadow h-100 py-2 hover-shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col me-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Vé đã bán</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($ticketsSold, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-film fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- KPI ROW 2 --}}
        <div class="row">
            {{-- Tỉ lệ thanh toán --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <a class="text-decoration-none"
                    href="{{ route('admin.reports.payments') }}?from={{ $from }}&to={{ $to }}@if ($rapId) &rap_id={{ $rapId }} @endif">
                    <div class="card border-left-info shadow h-100 py-2 hover-shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col me-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tỉ lệ thanh toán
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($paymentRate, 2, ',', '.') }}%</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Hoàn tiền --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <a class="text-decoration-none"
                    href="{{ route('admin.reports.refunds') }}?from={{ $from }}&to={{ $to }}@if ($rapId) &rap_id={{ $rapId }} @endif">
                    <div class="card border-left-secondary shadow h-100 py-2 hover-shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col me-2">
                                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Số tiền hoàn
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($refundAmount, 0, ',', '.') }}đ</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-undo-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Đơn hủy --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <a class="text-decoration-none"
                    href="{{ route('admin.reports.orders.canceled') }}?from={{ $from }}&to={{ $to }}@if ($rapId) &rap_id={{ $rapId }} @endif">
                    <div class="card border-left-dark shadow h-100 py-2 hover-shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col me-2">
                                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Đơn đã hủy</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($canceledOrders, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Tổng doanh thu --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <a class="text-decoration-none"
                    href="{{ route('admin.reports.revenue.total') }}?from={{ $from }}&to={{ $to }}@if ($rapId) &rap_id={{ $rapId }} @endif">
                    <div class="card border-left-primary shadow h-100 py-2 hover-shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col me-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng doanh thu
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($grossRevenue, 0, ',', '.') }}đ</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- CHARTS --}}
        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Doanh thu theo giờ</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="myAreaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tỷ lệ doanh thu</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="myPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLES --}}
        <div class="row">
            {{-- Top phim --}}
            <div class="col-xl-6">
                <div class="card shadow mb-4 h-100">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Top phim theo doanh thu</h6>
                        <a class="btn btn-sm btn-outline-primary"
                            href="{{ route('admin.reports.movies') }}?from={{ $from }}&to={{ $to }}@if ($rapId) &rap_id={{ $rapId }} @endif">
                            Xem chi tiết
                        </a>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Phim</th>
                                    <th class="text-end">Vé</th>
                                    <th class="text-end">Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topMovies as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <a
                                                href="{{ route('admin.reports.movie.detail', $row->id) }}?from={{ $from }}&to={{ $to }}">
                                                {{ $row->tieu_de }}
                                            </a>
                                        </td>
                                        <td class="text-end">{{ number_format($row->orders ?? 0, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            {{ number_format((float) ($row->revenue ?? 0), 0, ',', '.') }}đ</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Top khách hàng --}}
            <div class="col-xl-6">
                <div class="card shadow mb-4 h-100">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Top khách hàng</h6>
                        <a class="btn btn-sm btn-outline-primary"
                            href="{{ route('admin.reports.customers') }}?from={{ $from }}&to={{ $to }}@if ($rapId) &rap_id={{ $rapId }} @endif">
                            Xem chi tiết
                        </a>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Khách hàng</th>
                                    <th>Email</th>
                                    <th class="text-end">Đơn</th>
                                    <th class="text-end">Tổng chi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCustomers as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <a
                                                href="{{ route('admin.reports.customer.detail', Arr::get($row, 'nguoi_dung_id')) }}?from={{ $from }}&to={{ $to }}">
                                                {{ Arr::get($row, 'ho_ten', '(Không rõ)') }}
                                            </a>
                                        </td>
                                        <td>{{ Arr::get($row, 'email', '') }}</td>
                                        <td class="text-end">{{ number_format(Arr::get($row, 'orders', 0), 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format((float) Arr::get($row, 'total', 0), 0, ',', '.') }}đ</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Đơn mới nhất --}}
            <div class="col-xl-6">
                <div class="card shadow mb-4 h-100">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Đơn mới nhất</h6>
                        <a class="btn btn-sm btn-outline-primary"
                            href="{{ route('admin.reports.orders') }}?from={{ $from }}&to={{ $to }}@if ($rapId) &rap_id={{ $rapId }} @endif">
                            Xem chi tiết
                        </a>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Thời gian</th>
                                    <th>Khách</th>
                                    <th class="text-end">Số tiền</th>
                                    <th>TT đơn</th>
                                    <th>TT thanh toán</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestOrders as $row)
                                    <tr>
                                        <td>
                                            <a
                                                href="{{ route('admin.reports.order.detail', Arr::get($row, 'don_dat_ve_id')) }}">
                                                {{ Arr::get($row, 'ma_don') }}
                                            </a>
                                        </td>
                                        <td>{{ Arr::get($row, 'created_at') }}</td>
                                        <td>{{ Arr::get($row, 'khach') }}</td>
                                        <td class="text-end">
                                            {{ number_format((float) Arr::get($row, 'amount', 0), 0, ',', '.') }}đ</td>
                                        <td>
                                            <span
                                                class="badge bg-light text-dark">{{ Arr::get($row, 'status', '') }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ Arr::get($row, 'pay_status') === 'thanh_cong' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ Arr::get($row, 'pay_status', '') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Doanh thu theo rạp --}}
            <div class="col-xl-6">
                <div class="card shadow mb-4 h-100">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Doanh thu theo rạp</h6>
                        <a class="btn btn-sm btn-outline-primary"
                            href="{{ route('admin.reports.cinemas') }}?from={{ $from }}&to={{ $to }}">
                            Xem chi tiết
                        </a>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Rạp</th>
                                    <th class="text-end">Vé</th>
                                    <th class="text-end">Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($revenueByCinema as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ Arr::get($row, 'rap', '') }}</td>
                                        <td class="text-end">
                                            {{ number_format(Arr::get($row, 'tickets', 0), 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format((float) Arr::get($row, 'revenue', 0), 0, ',', '.') }}đ</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        .hover-shadow:hover {
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
            transform: translateY(-1px);
            transition: .15s;
        }
    </style>
@endpush

@push('scripts')
    {{-- Nếu layout chưa nạp Chart.js, mở dòng dưới --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hourLabels = @json($hourLabels);
            const hourRevenue = @json($hourRevenue);
            const ticketRevenue = Number(@json($ticketRevenue));
            const comboRevenue = Number(@json($comboRevenue));
            const productRevenue = Number(@json($productRevenue));

            // Area chart
            const areaEl = document.getElementById('myAreaChart');
            if (areaEl && typeof Chart !== 'undefined') {
                new Chart(areaEl, {
                    type: 'line',
                    data: {
                        labels: hourLabels,
                        datasets: [{
                            label: "Doanh thu",
                            data: hourRevenue,
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
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => Number(value).toLocaleString('vi-VN') + 'đ'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => Number(ctx.raw).toLocaleString('vi-VN') + 'đ'
                                }
                            }
                        }
                    }
                });
            }

            // Pie chart
            const pieEl = document.getElementById('myPieChart');
            if (pieEl && typeof Chart !== 'undefined') {
                new Chart(pieEl, {
                    type: 'doughnut',
                    data: {
                        labels: ["Vé", "Combo", "Sản phẩm"],
                        datasets: [{
                            data: [ticketRevenue, comboRevenue, productRevenue],
                            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx =>
                                        `${ctx.label}: ${Number(ctx.raw||0).toLocaleString('vi-VN')}đ`
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
