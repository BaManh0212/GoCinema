@extends('admin.layouts.admin')
@section('content')
    <div class="container-fluid">
        <h1 class="h3 text-gray-800 mb-3">Phim: {{ $summary['phim'] }}</h1>
        @include('admin.reports.partials.filter')

        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="text-xs text-primary text-uppercase mb-1">Tổng vé</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['tong_ve']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="text-xs text-success text-uppercase mb-1">Doanh thu</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($summary['doanh_thu'], 0, ',', '.') }}đ</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-3">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Theo ngày</h6>
            </div>
            <div class="card-body"><canvas id="chartMovieDay" height="120"></canvas></div>
        </div>

        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Theo suất chiếu / rạp</h6>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Rạp</th>
                            <th>Bắt đầu</th>
                            <th>Kết thúc</th>
                            <th class="text-end">Vé</th>
                            <th class="text-end">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($byShowtime as $r)
                            <tr>
                                <td>{{ $r->rap }}</td>
                                <td>{{ \Carbon\Carbon::parse($r->gio_bat_dau)->format('d/m/Y H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($r->gio_ket_thuc)->format('d/m/Y H:i') }}</td>
                                <td class="text-end">{{ number_format($r->so_ve) }}</td>
                                <td class="text-end">{{ number_format($r->doanh_thu, 0, ',', '.') }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            if (!window.Chart) return;
            const ctx = document.getElementById('chartMovieDay');
            if (!ctx) return;
            const rows = @json($byDay, JSON_UNESCAPED_UNICODE);
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: rows.map(x => x.ngay),
                    datasets: [{
                        label: 'Doanh thu',
                        data: rows.map(x => x.doanh_thu),
                        borderColor: 'rgba(78,115,223,1)',
                        backgroundColor: 'rgba(78,115,223,.08)'
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: c => Number(c.raw).toLocaleString('vi-VN') + 'đ'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: v => Number(v).toLocaleString('vi-VN') + 'đ'
                            }
                        }
                    }
                }
            });
        })();
    </script>
@endpush
