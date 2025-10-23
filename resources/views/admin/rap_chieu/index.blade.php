@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-primary mb-0">
            <i class="bi bi-building"></i> 🏢 Thông tin rạp chiếu
        </h2>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-primary text-white fw-bold rounded-top-4">
            Thông tin rạp: {{ $rap->ten }}
        </div>

        <div class="card-body">
            <div class="row mb-3 align-items-center">
                <div class="col-md-2 text-center">
                    {{-- Hiển thị logo nếu có --}}
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
                            <p class="fw-semibold mb-1 text-secondary">Số điện thoại:</p>
                            <p class="fs-5">{{ $rap->so_dien_thoai }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="fw-semibold mb-1 text-secondary">Email:</p>
                            <p class="fs-5">{{ $rap->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="fw-semibold mb-1 text-secondary">Địa chỉ:</p>
                            <p class="fs-5">{{ $rap->dia_chi }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="fw-semibold mb-1 text-secondary">Ngày tạo:</p>
                            <p class="fs-6 text-muted">{{ $rap->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="fw-semibold mb-1 text-secondary">Cập nhật gần nhất:</p>
                            <p class="fs-6 text-muted">{{ $rap->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Danh sách phòng chiếu --}}
    @if(isset($rap->phongchieus) && $rap->phongchieus->count() > 0)
        <div class="card shadow-sm border-0 rounded-4 mt-4">
            <div class="card-header bg-info text-white fw-bold rounded-top-4">
                Danh sách phòng chiếu trong rạp
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="table-light text-secondary text-uppercase">
                        <tr>
                            <th>STT</th>
                            <th>Tên phòng</th>
                            <th>Định dạng</th>
                            <th>Tổng ghế</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rap->phongchieus as $key => $p)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td class="text-start ps-4">{{ $p->ten }}</td>
                                <td>{{ $p->dinhDang?->ten ?? 'Không có' }}</td>
                                <td>{{ $p->tong_ghe }}</td>
                                <td>
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
@endsection
