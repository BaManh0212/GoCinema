@extends('admin.layouts.admin')

@section('title', 'Thông tin Rạp')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0 text-gradient">
            <i class="bi bi-building"></i> Thông tin rạp chiếu
        </h2>
    </div>

    {{-- ✅ Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success shadow-sm rounded-3">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger shadow-sm rounded-3">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- 📦 Thông tin rạp --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header text-white fw-bold rounded-top-4" style="background: linear-gradient(90deg, #007bff, #00c3ff);">
            Thông tin rạp: {{ $rap->ten }}
        </div>
        <div class="card-body">
            <div class="row mb-3 align-items-center">
                <div class="col-md-2 text-center">
                    @if($rap->logo)
                        <img src="{{ asset('uploads/rap/' . $rap->logo) }}" 
                             alt="Logo {{ $rap->ten }}" 
                             class="rounded-circle border border-secondary shadow-sm mb-2"
                             style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <span class="badge bg-light text-dark">Chưa có logo</span>
                    @endif
                </div>
                <div class="col-md-10">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="fw-semibold mb-1 text-secondary">Tên rạp:</p>
                            <p class="fs-5">{{ $rap->ten }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="fw-semibold mb-1 text-secondary">Slug:</p>
                            <p class="fs-5">{{ $rap->slug }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="fw-semibold mb-1 text-secondary">Số điện thoại:</p>
                            <p class="fs-5">{{ $rap->so_dien_thoai }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="fw-semibold mb-1 text-secondary">Email:</p>
                            <p class="fs-5">{{ $rap->email }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="fw-semibold mb-1 text-secondary">Địa chỉ:</p>
                            <p class="fs-6 text-muted">{{ $rap->dia_chi }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="fw-semibold mb-1 text-secondary">Ngày tạo:</p>
                            <p class="fs-6 text-muted">{{ $rap->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 📋 Danh sách phòng chiếu --}}
    @if(isset($rap->phongchieus) && $rap->phongchieus->count() > 0)
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header text-white fw-bold rounded-top-4" style="background: linear-gradient(90deg, #17a2b8, #00c3ff);">
                Danh sách phòng chiếu trong rạp
            </div>
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead class="table-header text-white text-center">
                        <tr>
                            <th style="width:50px;">STT</th>
                            <th class="text-start">Tên phòng</th>
                            <th>Định dạng</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rap->phongchieus as $key => $p)
                            <tr class="table-row">
                                <td class="text-center fw-bold text-muted">{{ $key + 1 }}</td>
                                <td class="text-start ps-4">{{ $p->ten }}</td>
                                <td>{{ $p->dinhDang?->ten ?? 'Không có' }}</td>
                                <td class="text-center">
                                    @if($p->trang_thai == 'hoat_dong')
                                        <span class="badge bg-success">Hoạt động</span>
                                    @elseif($p->trang_thai == 'bao_tri')
                                        <span class="badge bg-warning text-dark">Bảo trì</span>
                                    @else
                                        <span class="badge bg-danger">Ngừng sử dụng</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

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
</style>
@endsection
