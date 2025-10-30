@extends('admin.layouts.admin')

@section('content')
@php
    use Illuminate\Support\Arr;

    // --- Filter defaults ---
    $from = request('from') ?: now()->firstOfMonth()->toDateString();
    $to = request('to') ?: now()->toDateString();
    $rapId = request('rap_id'); // optional

    // --- Numbers from controller ---
    $grossRevenue = (float) ($grossRevenue ?? 0);
    $comboRevenue = (float) ($comboRevenue ?? 0);
    $productRevenue = (float) ($productRevenue ?? 0);
    $ticketRevenue = max($grossRevenue - $comboRevenue - $productRevenue, 0);
    $ticketsSold = (int) ($ticketsSold ?? 0);
    $paymentRate = (float) ($paymentRate ?? 0);
    $refundAmount = (float) ($refundAmount ?? 0);
    $canceledOrders = (int) ($canceledOrders ?? 0);

    // Chart & table data
    $hourLabels = $hourLabels ?? [];
    $hourRevenue = $hourRevenue ?? [];
    $topMovies = $topMovies ?? [];
    $topCustomers = $topCustomers ?? [];
    $latestOrders = $latestOrders ?? [];
    $revenueByCinema = $revenueByCinema ?? [];
@endphp

<div class="container-fluid">

    {{-- ========== HEADER + FILTERS ========== --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="d-none d-sm-inline-block">
            <a href="{{ route('admin.dashboard', ['from' => $from, 'to' => $to, 'rap_id' => $rapId]) }}"
               class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-sync-alt fa-sm text-white-50"></i> Làm mới
            </a>
            <a href="{{ route('admin.reports.revenue.total') }}?from={{ $from }}&to={{ $to }}@if($rapId)&rap_id={{ $rapId }}@endif"
               class="btn btn-sm btn-primary shadow-sm">
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
                            <option value="{{ $c->id }}" {{ (string)$rapId === (string)$c->id ? 'selected' : '' }}>
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

    {{-- ========== KPI ROWS ========== --}}
    <div class="row">
        @php
            $kpis = [
                ['color'=>'primary','icon'=>'ticket-alt','label'=>'Doanh thu vé','value'=>$ticketRevenue,'route'=>'admin.reports.revenue.tickets'],
                ['color'=>'success','icon'=>'hamburger','label'=>'Doanh thu combo','value'=>$comboRevenue,'route'=>'admin.reports.revenue.combos'],
                ['color'=>'warning','icon'=>'shopping-cart','label'=>'Doanh thu sản phẩm','value'=>$productRevenue,'route'=>'admin.reports.revenue.products'],
                ['color'=>'danger','icon'=>'film','label'=>'Vé đã bán','value'=>$ticketsSold,'route'=>'admin.reports.tickets'],
            ];
        @endphp
        @foreach($kpis as $k)
        <div class="col-xl-3 col-md-6 mb-4">
            <a class="text-decoration-none"
               href="{{ route($k['route']) }}?from={{ $from }}&to={{ $to }}@if($rapId)&rap_id={{ $rapId }}@endif">
                <div class="card border-left-{{ $k['color'] }} shadow h-100 py-2 hover-shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-{{ $k['color'] }} text-uppercase mb-1">{{ $k['label'] }}</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($k['value'], 0, ',', '.') }}{{ $k['label']=='Vé đã bán'?'':'đ' }}</div>
                        </div>
                        <i class="fas fa-{{ $k['icon'] }} fa-2x text-gray-300"></i>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <div class="row">
        @php
            $kpis2 = [
                ['color'=>'info','icon'=>'credit-card','label'=>'Tỉ lệ thanh toán','value'=>number_format($paymentRate,2,',','.') . '%','route'=>'admin.reports.payments'],
                ['color'=>'secondary','icon'=>'undo-alt','label'=>'Số tiền hoàn','value'=>number_format($refundAmount,0,',','.') . 'đ','route'=>'admin.reports.refunds'],
                ['color'=>'dark','icon'=>'times-circle','label'=>'Đơn đã hủy','value'=>number_format($canceledOrders,0,',','.'),'route'=>'admin.reports.orders.canceled'],
                ['color'=>'primary','icon'=>'chart-line','label'=>'Tổng doanh thu','value'=>number_format($grossRevenue,0,',','.') . 'đ','route'=>'admin.reports.revenue.total'],
            ];
        @endphp
        @foreach($kpis2 as $k)
        <div class="col-xl-3 col-md-6 mb-4">
            <a class="text-decoration-none"
               href="{{ route($k['route']) }}?from={{ $from }}&to={{ $to }}@if($rapId)&rap_id={{ $rapId }}@endif">
                <div class="card border-left-{{ $k['color'] }} shadow h-100 py-2 hover-shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-{{ $k['color'] }} text-uppercase mb-1">{{ $k['label'] }}</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $k['value'] }}</div>
                        </div>
                        <i class="fas fa-{{ $k['icon'] }} fa-2x text-gray-300"></i>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    {{-- ========== CHARTS ========== --}}
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Doanh thu theo giờ</h6>
                </div>
                <div class="card-body"><canvas id="myAreaChart"></canvas></div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 fw-bold text-primary">Tỷ lệ doanh thu</h6></div>
                <div class="card-body"><canvas id="myPieChart" class="pt-4 pb-2"></canvas></div>
            </div>
        </div>
    </div>

    {{-- ========== TABLES ========== --}}
    <div class="row">

        {{-- Top phim --}}
        <div class="col-xl-6">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">Top phim theo doanh thu</h6>
                    <a class="btn btn-sm btn-outline-primary"
                       href="{{ route('admin.reports.movies') }}?from={{ $from }}&to={{ $to }}@if($rapId)&rap_id={{ $rapId }}@endif">
                        Xem chi tiết
                    </a>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr><th>#</th><th>Phim</th><th class="text-end">Vé</th><th class="text-end">Doanh thu</th></tr>
                        </thead>
                        <tbody>
                            @forelse($topMovies as $i => $row)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>
                                        <a href="{{ route('admin.reports.movie.detail', ['id'=>$row->id]) }}?from={{ $from }}&to={{ $to }}">
                                            {{ $row->tieu_de }}
                                        </a>
                                    </td>
                                    <td class="text-end">{{ number_format($row->orders ?? 0,0,',','.') }}</td>
                                    <td class="text-end">{{ number_format($row->revenue ?? 0,0,',','.') }}đ</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Không có dữ liệu</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Top khách hàng (ĐÃ SỬA LỖI) --}}
        <div class="col-xl-6">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">Top khách hàng</h6>
                    <a class="btn btn-sm btn-outline-primary"
                       href="{{ route('admin.reports.customers') }}?from={{ $from }}&to={{ $to }}@if($rapId)&rap_id={{ $rapId }}@endif">
                        Xem chi tiết
                    </a>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr><th>#</th><th>Khách hàng</th><th>Email</th><th class="text-end">Đơn</th><th class="text-end">Tổng chi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($topCustomers as $i => $row)
                                @php $userId = Arr::get($row, 'nguoi_dung_id'); @endphp
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>
                                        @if($userId)
                                            <a href="{{ route('admin.reports.customer.detail', ['id'=>$userId]) }}?from={{ $from }}&to={{ $to }}">
                                                {{ Arr::get($row,'ho_ten','(Không rõ)') }}
                                            </a>
                                        @else
                                            {{ Arr::get($row,'ho_ten','(Không rõ)') }}
                                        @endif
                                    </td>
                                    <td>{{ Arr::get($row,'email','') }}</td>
                                    <td class="text-end">{{ number_format(Arr::get($row,'orders',0),0,',','.') }}</td>
                                    <td class="text-end">{{ number_format((float)Arr::get($row,'total',0),0,',','.') }}đ</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">Không có dữ liệu</td></tr>
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
                    <h6 class="m-0 fw-bold text-primary">Đơn mới nhất</h6>
                    <a class="btn btn-sm btn-outline-primary"
                       href="{{ route('admin.reports.orders') }}?from={{ $from }}&to={{ $to }}@if($rapId)&rap_id={{ $rapId }}@endif">
                        Xem chi tiết
                    </a>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr><th>Mã đơn</th><th>Thời gian</th><th>Khách</th><th class="text-end">Số tiền</th><th>TT đơn</th><th>TT thanh toán</th></tr>
                        </thead>
                        <tbody>
                            @forelse($latestOrders as $row)
                                <tr>
                                    <td><a href="{{ route('admin.reports.order.detail', ['id'=>Arr::get($row,'don_dat_ve_id')]) }}">{{ Arr::get($row,'ma_don') }}</a></td>
                                    <td>{{ Arr::get($row,'created_at') }}</td>
                                    <td>{{ Arr::get($row,'khach') }}</td>
                                    <td class="text-end">{{ number_format((float)Arr::get($row,'amount',0),0,',','.') }}đ</td>
                                    <td><span class="badge bg-light text-dark">{{ Arr::get($row,'status','') }}</span></td>
                                    <td>
                                        <span class="badge {{ Arr::get($row,'pay_status')==='thanh_cong'?'bg-success':'bg-secondary' }}">
                                            {{ Arr::get($row,'pay_status','') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">Không có dữ liệu</td></tr>
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
                    <h6 class="m-0 fw-bold text-primary">Doanh thu theo rạp</h6>
                    <a class="btn btn-sm btn-outline-primary"
                       href="{{ route('admin.reports.cinemas') }}?from={{ $from }}&to={{ $to }}">
                        Xem chi tiết
                    </a>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr><th>#</th><th>Rạp</th><th class="text-end">Vé</th><th class="text-end">Doanh thu</th></tr>
                        </thead>
                        <tbody>
                            @forelse($revenueByCinema as $i => $row)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ Arr::get($row,'rap','') }}</td>
                                    <td class="text-end">{{ number_format(Arr::get($row,'tickets',0),0,',','.') }}</td>
                                    <td class="text-end">{{ number_format((float)Arr::get($row,'revenue',0),0,',','.') }}đ</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Không có dữ liệu</td></tr>
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
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    transform: translateY(-1px);
    transition: .15s;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const hourLabels = @json($hourLabels);
    const hourRevenue = @json($hourRevenue);
    const ticketRevenue = Number(@json($ticketRevenue));
    const comboRevenue = Number(@json($comboRevenue));
    const productRevenue = Number(@json($productRevenue));

    if (typeof Chart !== 'undefined') {
        // Area Chart
        const areaEl = document.getElementById('myAreaChart');
        if (areaEl) new Chart(areaEl, {
            type: 'line',
            data: { labels: hourLabels, datasets: [{ label: "Doanh thu", data: hourRevenue,
                borderColor: "rgba(78,115,223,1)", backgroundColor: "rgba(78,115,223,0.05)",
                pointRadius: 3, pointBackgroundColor: "rgba(78,115,223,1)", tension: 0.3 }] },
            options: { scales:{ y:{beginAtZero:true,ticks:{callback:v=>v.toLocaleString()+'đ'}} } }
        });

        // Pie Chart
        const pieEl = document.getElementById('myPieChart');
        if (pieEl) new Chart(pieEl, {
            type: 'doughnut',
            data: { labels: ['Vé','Combo','Sản phẩm'],
                    datasets: [{ data: [ticketRevenue, comboRevenue, productRevenue],
                    backgroundColor: ['#4e73df','#1cc88a','#f6c23e'] }] },
            options: { cutout: '70%' }
        });
    }
});
</script>
@endpush
