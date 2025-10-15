@extends('admin.layouts.admin')
@section('content')
    <div class="container-fluid">
        <h1 class="h3 text-gray-800 mb-3">Top khách hàng</h1>
        @include('admin.reports.partials.filter')

        <div class="card shadow">
            <div class="card-body table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Khách hàng</th>
                            <th>Email</th>
                            <th class="text-end">Số đơn</th>
                            <th class="text-end">Chi tiêu</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $r)
                            <tr>
                                <td>{{ $r->ho_ten }}</td>
                                <td>{{ $r->email }}</td>
                                <td class="text-end">{{ number_format($r->so_don) }}</td>
                                <td class="text-end">{{ number_format($r->chi_tieu, 0, ',', '.') }}đ</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary"
                                        href="{{ route('admin.reports.customers.show', $r->id) }}">Chi tiết</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
