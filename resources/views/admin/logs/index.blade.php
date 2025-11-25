@extends('admin.layouts.admin')

@section('title', 'Lịch sử Check-in và In vé')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Lịch sử Check-in và In vé</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách hành động</h6>
        </div>
        <div class="card-body">
            @if ($logs->count())
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Người dùng</th>
                            <th>Mã đơn</th>
                            <th>Hành động</th>
                            <th>Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>{{ $log->user->ho_ten ?? 'Không xác định' }}</td>
                            <td>{{ $log->donDatVe->ma_don ?? 'Không xác định' }}</td>
                            <td>{{ ucfirst($log->action_type) }}</td>
                            <td>{{ $log->created_at->format('d-m-Y H:i:s') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $logs->links() }}
            </div>
            @else
            <p>Không có dữ liệu lịch sử hành động.</p>
            @endif
        </div>
    </div>
</div>
@endsection
