@extends('admin.layouts.admin')

@section('title', 'Check-in theo mã đơn')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold mb-4">Check-in theo mã đơn</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        // Nếu đang ở prefix staff/* thì gửi tới staff route, ngược lại gửi tới admin route
        $actionRoute = request()->is('staff/*') ? route('staff.donve.checkinByCode') : route('admin.donve.checkinByCode');
    @endphp
    <form action="{{ $actionRoute }}" method="POST" class="max-w-lg">
        @csrf

        <label for="ma_don" class="block text-sm font-medium text-gray-700">Mã đơn</label>
        <div class="mt-1 flex">
            <input id="ma_don" name="ma_don" type="text" required
                   value="{{ old('ma_don', $maDon ?? '') }}"
                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            <button type="submit" class="ml-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                Check-in
            </button>
        </div>

        <p class="text-sm text-gray-500 mt-3">Nhập mã đơn (ví dụ: mã in trên vé) và nhấn Check-in để ghi nhận vé đã sử dụng.</p>
    </form>
</div>
@endsection
