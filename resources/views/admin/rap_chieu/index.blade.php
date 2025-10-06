@extends('admin.layouts.admin')

@section('content')
<h2>Danh sách rạp chiếu</h2>
<a href="{{ route('rap.create') }}" class="btn btn-primary">+ Thêm rạp</a>

@if($raps->isEmpty())
  <p>Chưa có rạp nào.</p>
@else
  <table class="table table-bordered">
    <thead>...</thead>
    <tbody>
      @foreach($raps as $r)
        <tr>
          <td>{{ $r->id }}</td>
          <td>{{ $r->ten }}</td>
          <td>{{ $r->dia_chi }}</td>
          <td>{{ $r->sdt }}</td>
          <td>
            <a href="{{ route('rap.edit', $r->id) }}" class="btn btn-sm btn-warning">Sửa</a>
            <form action="{{ route('rap.delete', $r->id) }}" method="POST" style="display:inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')">Xóa</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endif
@endsection
