<div class="btn-group" role="group" aria-label="Export">
    <a class="btn btn-sm btn-outline-secondary" href="{{ request()->fullUrlWithQuery(['download' => 'csv']) }}">CSV</a>
    <a class="btn btn-sm btn-outline-secondary" href="{{ request()->fullUrlWithQuery(['download' => 'xlsx']) }}">XLSX</a>
    <a class="btn btn-sm btn-primary" href="{{ request()->fullUrlWithQuery(['download' => 'pdf']) }}">PDF</a>
</div>
