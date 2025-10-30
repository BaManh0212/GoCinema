@extends('admin.layouts.admin')
@section('content')
    <div class="container-fluid">
        <h1 class="h3 text-gray-800 mb-3">Top phim</h1>
        @include('admin.reports.partials.filter')

        <div class="card shadow">
            <div class="card-body table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Phim</th>
                            <th class="text-end">Vé</th>
                            <th class="text-end">Doanh thu</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $r)
                            <tr>
                                <td>{{ $r->tieu_de }}</td>
                                <td class="text-end">{{ number_format($r->so_ve) }}</td>
                                <td class="text-end">{{ number_format($r->doanh_thu, 0, ',', '.') }}đ</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary"
                                        href="{{ route('admin.reports.movies.show', $r->id) }}">Chi tiết</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
