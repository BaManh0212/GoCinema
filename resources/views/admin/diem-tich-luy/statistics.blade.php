@extends('layouts.app')

@section('title', 'Thống kê điểm tích lũy')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="fas fa-chart-bar me-2"></i>Thống kê điểm tích lũy</h2>
        </div>
        <div class="col text-end">
            <a href="{{ route('admin.diem-tich-luy.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
        </div>
    </div>

    <!-- Top 10 người dùng có nhiều điểm nhất -->
    <div class="card mb-4">
        <div class="card-header bg-warning">
            <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top 10 người dùng có nhiều điểm nhất</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%">Hạng</th>
                            <th style="width: 30%">Họ tên</th>
                            <th style="width: 30%">Email</th>
                            <th style="width: 15%">Vai trò</th>
                            <th style="width: 15%">Điểm</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topNguoiDung as $index => $nd)
                            <tr>
                                <td>
                                    @if($index == 0)
                                        <span class="badge bg-warning text-dark fs-5">
                                            <i class="fas fa-trophy"></i> #1
                                        </span>
                                    @elseif($index == 1)
                                        <span class="badge bg-secondary fs-5">
                                            <i class="fas fa-medal"></i> #2
                                        </span>
                                    @elseif($index == 2)
                                        <span class="badge bg-danger fs-5">
                                            <i class="fas fa-medal"></i> #3
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark">#{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.nguoi-dung.show', $nd->id) }}" class="text-decoration-none">
                                        <strong>{{ $nd->ho_ten }}</strong>
                                    </a>
                                </td>
                                <td>{{ $nd->email }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $nd->vaiTro->ten ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <strong class="text-primary fs-5">
                                        {{ number_format($nd->diem_tich_luy) }}
                                        <i class="fas fa-star text-warning"></i>
                                    </strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Chưa có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Thống kê theo tháng -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Thống kê theo tháng ({{ date('Y') }})</h5>
        </div>
        <div class="card-body">
            @if($thongKeThang->isEmpty())
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Chưa có dữ liệu thống kê</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tháng</th>
                                <th>Tích lũy (+)</th>
                                <th>Sử dụng (-)</th>
                                <th>Chênh lệch</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $monthlyData = [];
                                foreach($thongKeThang as $tk) {
                                    if (!isset($monthlyData[$tk->thang])) {
                                        $monthlyData[$tk->thang] = ['tich_luy' => 0, 'su_dung' => 0];
                                    }
                                    if ($tk->hanh_dong == 'tich_luy') {
                                        $monthlyData[$tk->thang]['tich_luy'] = $tk->tong_diem;
                                    } else {
                                        $monthlyData[$tk->thang]['su_dung'] = $tk->tong_diem;
                                    }
                                }
                            @endphp

                            @for($i = 1; $i <= 12; $i++)
                                @php
                                    $tichLuy = $monthlyData[$i]['tich_luy'] ?? 0;
                                    $suDung = $monthlyData[$i]['su_dung'] ?? 0;
                                    $chenhLech = $tichLuy - $suDung;
                                @endphp
                                <tr>
                                    <td><strong>Tháng {{ $i }}</strong></td>
                                    <td>
                                        <span class="text-success">
                                            <i class="fas fa-arrow-up me-1"></i>
                                            {{ number_format($tichLuy) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-danger">
                                            <i class="fas fa-arrow-down me-1"></i>
                                            {{ number_format($suDung) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="{{ $chenhLech >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $chenhLech >= 0 ? '+' : '' }}{{ number_format($chenhLech) }}
                                        </strong>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th>Tổng cộng</th>
                                <th>
                                    <span class="text-success fs-5">
                                        +{{ number_format($thongKeThang->where('hanh_dong', 'tich_luy')->sum('tong_diem')) }}
                                    </span>
                                </th>
                                <th>
                                    <span class="text-danger fs-5">
                                        -{{ number_format($thongKeThang->where('hanh_dong', 'su_dung')->sum('tong_diem')) }}
                                    </span>
                                </th>
                                <th>
                                    @php
                                        $tongChenhLech = $thongKeThang->where('hanh_dong', 'tich_luy')->sum('tong_diem') - 
                                                        $thongKeThang->where('hanh_dong', 'su_dung')->sum('tong_diem');
                                    @endphp
                                    <strong class="{{ $tongChenhLech >= 0 ? 'text-success' : 'text-danger' }} fs-5">
                                        {{ $tongChenhLech >= 0 ? '+' : '' }}{{ number_format($tongChenhLech) }}
                                    </strong>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
