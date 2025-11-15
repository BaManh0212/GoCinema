@php
    /** $scopes = [['label'=>'Theo ngày','scope'=>'by_day'], ...] */
@endphp
<div class="d-flex flex-wrap gap-3">
    @foreach ($scopes as $s)
        <div class="d-inline-flex align-items-center gap-2">
            <span class="fw-semibold">{{ $s['label'] }}:</span>
            <div class="btn-group btn-group-sm" role="group">
                <a class="btn btn-outline-secondary"
                    href="{{ request()->fullUrlWithQuery(['scope' => $s['scope'], 'download' => 'csv']) }}">CSV</a>
                <a class="btn btn-outline-secondary"
                    href="{{ request()->fullUrlWithQuery(['scope' => $s['scope'], 'download' => 'xlsx']) }}">XLSX</a>
                <a class="btn btn-primary"
                    href="{{ request()->fullUrlWithQuery(['scope' => $s['scope'], 'download' => 'pdf']) }}">PDF</a>
            </div>
        </div>
    @endforeach
</div>
