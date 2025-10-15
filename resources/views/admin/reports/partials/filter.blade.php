<form method="GET" action="{{ url()->current() }}" class="row g-2 mb-3">
  <div class="col-md-3">
    <label class="small text-muted d-block mb-1">Từ ngày</label>
    <input type="date" name="from" class="form-control"
           value="{{ request('from') ?? now()->subDays(29)->toDateString() }}">
  </div>
  <div class="col-md-3">
    <label class="small text-muted d-block mb-1">Đến ngày</label>
    <input type="date" name="to" class="form-control"
           value="{{ request('to') ?? now()->toDateString() }}">
  </div>
  <div class="col-md-6 d-flex align-items-end gap-2">
    <button class="btn btn-primary me-2">Lọc</button>
    <a href="{{ url()->current() }}" class="btn btn-light">Xóa lọc</a>

    <div class="ms-auto btn-group" role="group" aria-label="Quick ranges">
      @php
        $today = now()->toDateString();
        $last7 = now()->subDays(6)->toDateString();
        $last30 = now()->subDays(29)->toDateString();
        $startMonth = now()->startOfMonth()->toDateString();
        $startYear = now()->startOfYear()->toDateString();
      @endphp
      <a class="btn btn-outline-secondary"
         href="{{ url()->current() }}?from={{ $last7 }}&to={{ $today }}">7 ngày</a>
      <a class="btn btn-outline-secondary"
         href="{{ url()->current() }}?from={{ $last30 }}&to={{ $today }}">30 ngày</a>
      <a class="btn btn-outline-secondary"
         href="{{ url()->current() }}?from={{ $startMonth }}&to={{ $today }}">Tháng này</a>
      <a class="btn btn-outline-secondary"
         href="{{ url()->current() }}?from={{ $startYear }}&to={{ $today }}">YTD</a>
      <a class="btn btn-outline-secondary"
         href="{{ url()->current() }}?from={{ request('from') }}&to={{ request('to') }}&format=json">Tải JSON</a>
    </div>
  </div>
</form>
