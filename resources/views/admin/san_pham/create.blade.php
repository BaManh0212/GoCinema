@extends('admin.layouts.admin')

@section('content')
    <h1>Thêm sản phẩm</h1>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.san_pham.store') }}" method="POST" novalidate>
        @csrf

        {{-- Tên sản phẩm --}}
        <div class="form-group mb-3">
            <label for="ten">Tên sản phẩm</label>
            <input type="text" 
                   name="ten" 
                   id="ten" 
                   class="form-control @error('ten') is-invalid @enderror" 
                   value="{{ old('ten') }}">
            @error('ten')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Giá --}}
        <div class="form-group mb-3">
            <label for="gia">Giá</label>
            <input type="number" 
                   name="gia" 
                   id="gia" 
                   class="form-control @error('gia') is-invalid @enderror" 
                   value="{{ old('gia') }}">
            @error('gia')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Số lượng --}}
        <div class="form-group mb-3">
            <label for="so_luong">Số lượng</label>
            <input type="number" 
                   name="so_luong" 
                   id="so_luong" 
                   class="form-control @error('so_luong') is-invalid @enderror" 
                   value="{{ old('so_luong') }}">
            @error('so_luong')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Thêm</button>
    </form>
@endsection