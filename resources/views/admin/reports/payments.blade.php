@extends('admin.layouts.admin')
@section('content')
<div class="container-fluid">
  <h1 class="h3 text-gray-800 mb-3">Thanh toán</h1>
  @include('admin.reports.partials.filter')

  <div class="row">
    <div class="col-md-4 mb-3"><div class="card border-left-primary shadow h-100">
      <div class="card-body"><div class="text-xs text-primary text-uppercase mb-1">Tổng giao dịch</div>
      <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['tong_giao_dich']) }}</div></div></div></div>
    <div class="col-md-4 mb-3"><div class="card border-left-success shadow h-100">
      <div class="card-body"><div class="text-xs text-success text-uppercase mb-1">Thành công</div>
      <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['thanh_cong']) }}</div></div></div></div>
    <div class="col-md-4 mb-3"><div class="card border-left-info shadow h-100">
      <div class="card-body"><div class="text-xs text-info text-uppercase mb-1">Tỉ lệ thành công</div>
      <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['ti_le_thanh_cong'] }}%</div></div></div></div>
  </div>

  <div class="row">
    <div class="col-lg-6 mb-3">
      <div class="card shadow">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Theo phương thức</h6></div>
        <div class="card-body table-responsive">
          <table class="table table-sm table-striped">
            <thead><tr><th>Phương thức</th><th class="text-end">Số GD</th><th class="text-end">Tổng tiền</th></tr></thead>
            <tbody>
              @foreach($byMethod as $r)
                <tr>
                  <td>{{ $r->ten }}</td>
                  <td class="text-end">{{ number_format($r->so_giao_dich) }}</td>
                  <td class="text-end">{{ number_format($r->tong_tien,0,',','.') }}đ</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-6 mb-3">
      <div class="card shadow">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Theo trạng thái</h6></div>
        <div class="card-body table-responsive">
          <table class="table table-sm table-striped">
            <thead><tr><th>Trạng thái</th><th class="text-end">Số lượng</th><th class="text-end">Tổng tiền</th></tr></thead>
            <tbody>
              @foreach($byStatus as $r)
                <tr>
                  <td>{{ $r->trang_thai }}</td>
                  <td class="text-end">{{ number_format($r->so_luong) }}</td>
                  <td class="text-end">{{ number_format($r->tong_tien,0,',','.') }}đ</td>
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
