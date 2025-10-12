@extends('admin.layouts.admin')

@section('content')
<h2>Danh sách rạp chiếu</h2>
<a href="{{ route('admin.rap.create') }}" class="btn btn-primary">+ Thêm rạp</a>

@if($raps->isEmpty())
  <p>Chưa có rạp nào.</p>
@else
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>STT</th>
        <th>Tên rạp</th>
        <th>Địa chỉ</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      @foreach($raps as $key => $r)
        <tr>
          <td>{{ $key + 1 }}</td>
          <td>{{ $r->ten }}</td>
          <td>{{ $r->dia_chi }}</td>
          <td>
            <a href="{{ route('admin.rap.edit', $r->id) }}" class="btn btn-sm btn-warning">Sửa</a>
            <form action="{{ route('admin.rap.destroy', $r->id) }}" method="POST" style="display:inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger" disabled>Xóa</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endif
@endsection
