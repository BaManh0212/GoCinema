@extends('staff.layouts.staff')

@section('content')
<div class="container mt-4">
    <h2 class="fw-bold text-warning">🗑️ Thùng rác Combo</h2>
    <a href="{{ route('staff.combo.index') }}" class="btn btn-secondary mb-3">⬅ Quay lại danh sách</a>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th>ID</th>
                        <th>Tên Combo</th>
                        <th>Giá (VNĐ)</th>
                        <th>Mô tả</th>
                        <th>Ngày xóa</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($combos as $combo)
                        <tr>
                            <td class="text-center">{{ $combo->id }}</td>
                            <td>{{ $combo->ten }}</td>
                            <td class="text-end">{{ number_format($combo->gia, 0, ',', '.') }} </td>
                            <td>{{ $combo->mo_ta }}</td>
                            <td class="text-center">{{ $combo->deleted_at ? $combo->deleted_at->format('d/m/Y H:i') : '-' }}</td>
                            <td class="text-center">
                                <form action="{{ route('staff.combo.restore', $combo->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning">♻️ Khôi phục</button>
                                </form>
                                <form action="{{ route('staff.combo.forceDelete', $combo->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn Combo này không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">❌ Xóa vĩnh viễn</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Không có Combo nào trong thùng rác.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
