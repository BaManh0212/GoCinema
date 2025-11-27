@extends('admin.layouts.admin')
@section('content')
<div class="container-fluid">
  <h1 class="h3 text-gray-800 mb-3">Doanh thu vé</h1>

  @include('admin.reports.partials.filter')

  <div class="row">
    <div class="col-md-4 mb-3">
      <div class="card border-left-primary shadow h-100">
        <div class="card-body">
          <div class="text-xs text-primary text-uppercase mb-1">Tổng vé</div>
          <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['tong_ve']) }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card border-left-success shadow h-100">
        <div class="card-body">
          <div class="text-xs text-success text-uppercase mb-1">Doanh thu</div>
          <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['doanh_thu'],0,',','.') }}đ</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8 mb-3">
      <div class="card shadow">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Theo ngày</h6></div>
        <div class="card-body"><canvas id="chartTicketDay" height="140"></canvas></div>
      </div>
    </div>
    <div class="col-lg-4 mb-3">
      <div class="card shadow">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Top phim</h6></div>
        <div class="card-body table-responsive" style="max-height:420px; overflow:auto;">
          <table class="table table-sm table-striped">
            <thead><tr><th>Phim</th><th class="text-end">Vé</th><th class="text-end">Doanh thu</th></tr></thead>
            <tbody>
            @foreach($byMovie as $r)
              <tr>
                <td>
                  <a href="{{ route('admin.reports.movie.detail', $r->id) }}">{{ $r->tieu_de }}</a>
                </td>
                <td class="text-end">{{ number_format($r->so_ve) }}</td>
                <td class="text-end">{{ number_format($r->doanh_thu,0,',','.') }}đ</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="card shadow mt-3">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Theo rạp</h6></div>
        <div class="card-body table-responsive" style="max-height:340px; overflow:auto;">
          <table class="table table-sm table-striped">
            <thead><tr><th>Rạp</th><th class="text-end">Vé</th><th class="text-end">Doanh thu</th></tr></thead>
            <tbody>
            @foreach($byCinema as $r)
              <tr>
                <td>{{ $r->ten }}</td>
                <td class="text-end">{{ number_format($r->so_ve) }}</td>
                <td class="text-end">{{ number_format($r->doanh_thu,0,',','.') }}đ</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  if(!window.Chart) return;
  const ctx = document.getElementById('chartTicketDay');
  if(!ctx) return;

  const rows = @json($byDay, JSON_UNESCAPED_UNICODE);
  const labels = rows.map(x => x.ngay);
  new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Doanh thu',
        data: rows.map(x => x.doanh_thu),
        borderColor: 'rgba(78,115,223,1)',
        backgroundColor: 'rgba(78,115,223,0.08)',
        tension: .3
      }]
    },
    options: {
      maintainAspectRatio:false,
      plugins:{legend:{display:false},tooltip:{callbacks:{
        label: ctx => Number(ctx.raw).toLocaleString('vi-VN')+'đ'
      }}},
      scales:{y:{beginAtZero:true, ticks:{callback:v=>Number(v).toLocaleString('vi-VN')+'đ'}}}
    }
  });
})();
</script>
@endpush
