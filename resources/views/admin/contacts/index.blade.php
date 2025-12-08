@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-envelope-paper"></i> Quản lý Liên hệ
            </h2>
            <small class="text-muted">Xem, lọc và quản lý các tin nhắn từ khách hàng</small>
        </div>
    </div>

    {{-- 🔍 Bộ lọc --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-auto">
                    <input name="q" value="{{ request('q') }}" class="form-control rounded-pill"
                           placeholder="Tìm tên/email hoặc nội dung...">
                </div>
                <div class="col-auto">
                    <select name="status" class="form-select rounded-pill">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status')=='pending' ? 'selected':'' }}>Chưa đọc</option>
                        <option value="read" {{ request('status')=='read' ? 'selected':'' }}>Đã đọc</option>
                        <option value="replied" {{ request('status')=='replied' ? 'selected':'' }}>Đã trả lời</option>
                    </select>
                </div>

                <div class="ms-auto text-end">
                    <button class="btn btn-primary shadow-sm rounded-pill px-4 me-2">Lọc</button>
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>

    {{-- 📋 Bảng danh sách liên hệ --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-header text-white">
                    <tr class="text-center">
                        <th>STT</th>
                        <th class="text-start">Người gửi</th>
                        <th class="text-start">Email</th>
                        <th class="text-start">Nội dung</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $c)
                        <tr class="table-row">
                            <td class="text-center fw-bold text-muted">{{ $c->id }}</td>
                            <td>{{ $c->name }}</td>
                            <td class="text-muted">{{ $c->email }}</td>
                            <td class="text-truncate" style="max-width: 280px;">{{ Str::limit($c->message, 80) }}</td>
                            <td class="text-center">
                                @if($c->status == 'pending')
                                    <span class="badge bg-danger px-3 py-2">Chưa đọc</span>
                                @elseif($c->status == 'read')
                                    <span class="badge bg-warning text-dark px-3 py-2">Đã đọc</span>
                                @else
                                    <span class="badge bg-success px-3 py-2">Đã trả lời</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $c->created_at->format('d-m-Y H:i') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.contacts.show', $c->id) }}"
                                   class="btn btn-sm btn-info text-white rounded-pill px-3 shadow-sm">
                                    <i class="bi bi-eye"></i> Xem
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox"></i> Không có liên hệ nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $contacts->links('pagination::bootstrap-5') }}
    </div>

</div>

{{-- 🎨 CSS đồng bộ --}}
<style>
.text-gradient {
    background: linear-gradient(90deg, #007bff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.table-header {
    background: linear-gradient(90deg, #007bff, #00c3ff);
}
.table-row {
    background-color: #fff;
    transition: all 0.25s ease-in-out;
}
.table-row:nth-child(even) {
    background-color: #f8f9fa;
}
.table-row:hover {
    background-color: #e9f5ff;
    transform: scale(1.01);
}
.table th {
    font-weight: 600;
    letter-spacing: 0.3px;
    border-bottom: none !important;
}
.table td {
    padding: 1rem 1.2rem;
    vertical-align: middle;
}
.card {
    border-radius: 1rem;
}

/*nút lọc và đặt lại nằm bên phải */
.ms-auto {
    margin-left: auto !important;
}
.text-end {
    text-align: right !important;
}
</style>

@endsection
