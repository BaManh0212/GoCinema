@extends('admin.layouts.admin')
@section('content')
<div class="container-fluid">
  <h1 class="h3 text-gray-800 mb-3">Doanh thu sản phẩm</h1>

  @include('admin.reports.partials.filter')

  <div class="row">
    <div class="col-md-6 mb-3">
      <div class="card border-left-warning shadow h-100">
        <div class="card-body">
          <div class="text-xs text-warning text-uppercase mb-1">Tổng doanh thu</div>
          <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['doanh_thu'],0,',','.') }}đ</div>
        </div>
      </div>
    </div>
    <div class="col-md-6 mb-3">
      <div class="card border-left-info shadow h-100">
        <div class="card-body">
          <div class="text-xs text-info text-uppercase mb-1">Tổng SL</div>
          <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['tong_so_luong']) }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow mb-3">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Theo sản phẩm</h6></div>
    <div class="card-body table-responsive">
      <table class="table table-sm table-striped">
        <thead><tr><th>Sản phẩm</th><th class="text-end">SL</th><th class="text-end">Doanh thu</th></tr></thead>
        <tbody>
          @foreach($rows as $r)
            <tr>
              <td>{{ $r->ten }}</td>
              <td class="text-end">{{ number_format($r->so_luong) }}</td>
              <td class="text-end">{{ number_format($r->doanh_thu,0,',','.') }}đ</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="card shadow">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Theo ngày</h6></div>
    <div class="card-body"><canvas id="chartProductDay" height="120"></canvas></div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  if(!window.Chart) return;
  const ctx = document.getElementById('chartProductDay');
  if(!ctx) return;
  const rows = @json($byDay, JSON_UNESCAPED_UNICODE);
  new Chart(ctx, {
    type: 'bar',
    data: { labels: rows.map(x=>x.ngay), datasets: [{label:'Doanh thu', data: rows.map(x=>x.doanh_thu)}] },
    options: {
      maintainAspectRatio:false,
      plugins:{legend:{display:false}, tooltip:{callbacks:{label: c=>Number(c.raw).toLocaleString('vi-VN')+'đ'}}},
      scales:{y:{beginAtZero:true, ticks:{callback:v=>Number(v).toLocaleString('vi-VN')+'đ'}}}
    }
  });
})();
</script>
@endpush
