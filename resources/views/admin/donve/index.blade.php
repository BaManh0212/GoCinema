@extends('admin.layouts.admin')

@section('title', '📋 Quản lý Đơn Đặt Vé')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-ticket-detailed"></i> Quản lý Đơn Đặt Vé
            </h2>
            <small class="text-muted">Xem, lọc và quản lý danh sách đơn đặt vé của khách hàng</small>
        </div>
    </div>

    {{-- 🔍 Bộ lọc --}}
    <div class="card mb-4 border-0 shadow-sm rounded-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.donve.index') }}" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <select name="trang_thai" class="form-select rounded-pill">
                        <option value="">-- Lọc theo trạng thái --</option>
                        <option value="cho_thanh_toan" {{ request('trang_thai') == 'cho_thanh_toan' ? 'selected' : '' }}>Chờ thanh toán</option>
                        <option value="da_thanh_toan" {{ request('trang_thai') == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="da_checkin" {{ request('trang_thai') == 'da_checkin' ? 'selected' : '' }}>Đã check-in</option>
                        <option value="da_huy" {{ request('trang_thai') == 'da_huy' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="trang_thai_ve" class="form-select rounded-pill">
                        <option value="">-- Lọc theo trạng thái vé --</option>
                        <option value="chua_checkin" {{ request('trang_thai_ve') == 'chua_checkin' ? 'selected' : '' }}>Chưa check-in</option>
                        <option value="da_checkin" {{ request('trang_thai_ve') == 'da_checkin' ? 'selected' : '' }}>Đã check-in</option>
                    </select>
                </div>
                <a href="{{ route('admin.admin.scan.qr') }}" class="btn btn-primary">
                    <i class="bi bi-qr-code-scan"></i> Quét mã QR
                </a>
                <div class="ms-auto text-end">
                    <button type="submit" class="btn btn-primary rounded-pill shadow-sm px-4 me-2">
                        <i class="bi bi-funnel"></i> Lọc
                    </button>
                    <a href="{{ route('admin.donve.index') }}" class="btn btn-outline-secondary rounded-pill shadow-sm px-4">
                        <i class="bi bi-arrow-clockwise"></i> Đặt lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ✅ Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm rounded-3">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- 🔍 Check-in Form --}}
    <div class="card mb-4 border-0 shadow-sm rounded-4">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="bi bi-check-circle text-warning"></i> Check-in theo mã đơn
            </h5>
            <form id="checkinForm" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-8">
                    <label for="ma_don_checkin" class="form-label fw-semibold">Mã đơn</label>
                    <input id="ma_don_checkin" name="ma_don" type="text" required
                           class="form-control rounded-pill shadow-sm"
                           placeholder="Nhập mã đơn (ví dụ: mã in trên vé)">
                </div>
                <div class="col-md-4">
                    <button type="submit" id="checkinSubmitBtn" class="btn btn-warning rounded-pill shadow-sm px-4">
                        <i class="bi bi-check-circle"></i> Check-in
                    </button>
                </div>
            </form>
            <div id="checkinResult" class="alert mt-3" style="display: none;"></div>
            <small class="text-muted mt-2 d-block">
                <i class="bi bi-info-circle"></i> Chỉ có thể check-in đơn đã thanh toán trong khung giờ cho phép (45-10 phút trước khi phim bắt đầu).
            </small>
        </div>
    </div>

    {{-- 📋 Bảng dữ liệu --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-header text-white">
                    <tr class="text-center">
                        <th style="width: 60px;">STT</th>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Phim</th>
                        <th>Suất chiếu</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái thanh toán</th>
                        <th>Trạng thái vé</th>
                        <th style="width: 200px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donDatVes as $don)
                        @php
                            $payment_status = $don->trang_thai === 'da_checkin' ? 'da_thanh_toan' : $don->trang_thai;
                            $payment_color = match($payment_status) {
                                'cho_thanh_toan' => 'secondary',
                                'da_thanh_toan' => 'success',
                                'da_huy' => 'danger',
                                default => 'dark'
                            };
                            $ticket_status = $don->trang_thai === 'da_checkin' ? 'da_checkin' : 'chua_checkin';
                            $ticket_color = $ticket_status === 'da_checkin' ? 'info' : 'secondary';
                        @endphp

                        <tr class="table-row text-center">
                            <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                            <td class="fw-semibold text-primary">{{ $don->ma_don }}</td>
                            <td>{{ $don->nguoiDung->ho_ten ?? 'N/A' }}</td>
                            <td>{{ $don->suatChieu->phim->tieu_de ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($don->suatChieu->gio_bat_dau)->format('H:i d/m/Y') }}</td>
                            <td class="fw-semibold">{{ number_format($don->tong_tien, 0, ',', '.') }} đ</td>

                            <td>
                                <span class="badge-status bg-{{ $payment_color }}">
                                    {{ ucfirst(str_replace('_', ' ', $payment_status)) }}
                                </span>
                            </td>

                            <td>
                                <span class="badge-status bg-{{ $ticket_color }}">
                                    @if($ticket_status === 'chua_checkin')
                                        Chưa check-in
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $ticket_status)) }}
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    @if($don->trang_thai === 'da_huy')
                                        <button class="btn btn-sm btn-secondary rounded-pill px-3 shadow-sm" disabled title="Đơn đã hủy">
                                            <i class="bi bi-eye"></i> Xem
                                        </button>
                                        <button class="btn btn-sm btn-secondary rounded-pill px-3 shadow-sm" disabled title="Đơn đã hủy">
                                            <i class="bi bi-printer"></i> In vé
                                        </button>
                                    @else
                                        <a href="{{ route('admin.donve.show', $don->id) }}" class="btn btn-sm btn-info text-white rounded-pill px-3 shadow-sm">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                        @php $canPrint = in_array($don->trang_thai, ['da_thanh_toan','da_checkin']); @endphp
                                        @if($canPrint)
                                            <a href="{{ route('admin.donve.print', $don->id) }}" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                                <i class="bi bi-printer"></i> In vé
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm" disabled title="Chỉ in khi đã thanh toán hoặc đã check-in">
                                                <i class="bi bi-printer"></i> In vé
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox"></i> Không có đơn đặt vé nào phù hợp.
                            </td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

{{-- 📄 Phân trang --}}
    <div class="mt-3">
        {{ $donDatVes->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkinForm = document.getElementById('checkinForm');
    const checkinSubmitBtn = document.getElementById('checkinSubmitBtn');
    const checkinResult = document.getElementById('checkinResult');
    const maDonInput = document.getElementById('ma_don_checkin');

    checkinForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const maDon = maDonInput.value.trim();
        if (!maDon) {
            showCheckinResult('Vui lòng nhập mã đơn.', 'danger');
            return;
        }

        checkinSubmitBtn.disabled = true;
        checkinSubmitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Đang xử lý...';

        fetch('{{ route("admin.donve.checkinByCode") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                ma_don: maDon
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success === false) {
                showCheckinResult(data.message, 'danger');
            } else {
                showCheckinResult(data.message || 'Check-in thành công!', 'success');
                maDonInput.value = '';
                // Reload page after 2 seconds to show updated status
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showCheckinResult('Có lỗi xảy ra. Vui lòng thử lại.', 'danger');
        })
        .finally(() => {
            checkinSubmitBtn.disabled = false;
            checkinSubmitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Check-in';
        });
    });

    function showCheckinResult(message, type) {
        checkinResult.className = `alert alert-${type} mt-3`;
        checkinResult.textContent = message;
        checkinResult.style.display = 'block';
        // Auto-hide after 5 seconds for success messages
        if (type === 'success') {
            setTimeout(() => {
                checkinResult.style.display = 'none';
            }, 5000);
        }
    }
});
</script>

{{-- 🎨 CSS --}}
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
    border-bottom: none !important;
}
.table td {
    padding: 1rem 1.2rem;
    vertical-align: middle;
}

.card {
    border-radius: 1rem;
}

/* 🌈 Badge trạng thái */
.badge-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    text-transform: capitalize;
    min-width: 100px;
    display: inline-block;
    text-align: center;
    background: white !important;
    color: black !important;
}

.bg-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}
</style>
@endsection
