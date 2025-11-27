@extends('client.layouts.app')

@section('title', 'Thanh toán')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">Thanh toán</h1>

            <!-- Thông tin đơn hàng -->
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Thông tin đơn hàng</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Mã đơn hàng:</p>
                            <p class="font-semibold">{{ $donDatVe->ma_don }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Trạng thái:</p>
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($donDatVe->trang_thai === 'cho_thanh_toan') bg-yellow-100 text-yellow-800
                                @elseif($donDatVe->trang_thai === 'da_thanh_toan') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $donDatVe->trang_thai }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin phim -->
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Thông tin phim</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex flex-col md:flex-row gap-4">
                        <img src="{{ asset('storage/' . $donDatVe->suatChieu->phim->anh_poster) }}"
                             alt="{{ $donDatVe->suatChieu->phim->tieu_de }}"
                             class="w-full md:w-32 h-48 object-cover rounded-lg">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-800">{{ $donDatVe->suatChieu->phim->tieu_de }}</h3>
                            <div class="mt-2 space-y-1 text-sm text-gray-600">
                                <p><strong>Rạp:</strong> {{ $donDatVe->suatChieu->phong->rap->ten }}</p>
                                <p><strong>Phòng:</strong> {{ $donDatVe->suatChieu->phong->ten }}</p>
                                <p><strong>Suất chiếu:</strong> {{ \Carbon\Carbon::parse($donDatVe->suatChieu->gio_bat_dau)->format('d/m/Y H:i') }}</p>
                                <p><strong>Thời lượng:</strong> {{ $donDatVe->suatChieu->phim->thoi_luong }} phút</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin ghế -->
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Ghế đã chọn</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($donDatVe->chiTietVes as $chiTietVe)
                        <div class="bg-white rounded-lg p-3 border">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-semibold">{{ $chiTietVe->ghe->so_ghe_ngoi }}</p>
                                    <p class="text-sm text-gray-600">{{ ucfirst($chiTietVe->loai_ghe) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-blue-600">{{ number_format($chiTietVe->gia) }}đ</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Combo đã chọn -->
            @if($combos->count() > 0)
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Combo đã chọn</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    @foreach($combos as $combo)
                    <div class="flex justify-between items-center py-2">
                        <div>
                            <p class="font-semibold">{{ $combo->ten }}</p>
                            <p class="text-sm text-gray-600">Số lượng: {{ $combo->so_luong }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-blue-600">{{ number_format($combo->gia * $combo->so_luong) }}đ</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Mã giảm giá -->
            @if($donDatVe->maGiamGia)
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Mã giảm giá</h2>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-green-800">{{ $donDatVe->maGiamGia->ma }}</p>
                            <p class="text-sm text-green-600">{{ $donDatVe->maGiamGia->mo_ta }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-green-800">-{{ number_format($donDatVe->tong_tien - ($donDatVe->chiTietVes->sum('gia') + $combos->sum(function($combo) { return $combo->gia * $combo->so_luong; }))) }}đ</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tổng tiền -->
            <div class="mb-8">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-blue-800">Tổng tiền</h3>
                        <h3 class="text-2xl font-bold text-blue-800">{{ number_format($donDatVe->tong_tien) }}đ</h3>
                    </div>
                </div>
            </div>

            <!-- Phương thức thanh toán -->
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Chọn phương thức thanh toán</h2>
                <form id="paymentForm" action="{{ route('booking.process-payment', $donDatVe->id) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <label class="flex items-center">
                                <input type="radio" name="payment_method" value="momo" class="mr-3" checked>
                                <div class="flex items-center">
                                    <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-MoMo-Square-1024x1024.png" alt="MoMo" class="w-8 h-8 mr-3">
                                    <div>
                                        <p class="font-semibold">Ví MoMo</p>
                                        <p class="text-sm text-gray-600">Thanh toán nhanh qua ví điện tử</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4"> 
                            <label class="flex items-center"> 
                                <input type="radio" name="payment_method" value="vnpay" class="mr-3"> 
                                <div class="flex items-center"> 
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/4/42/VNPay_logo.png" alt="VNPay" class="w-8 h-8 mr-3"> 
                                    <div> 
                                        <p class="font-semibold">VNPay</p> 
                                        <p class="text-sm text-gray-600">Thanh toán nhanh qua cổng VNPay</p> 
                                    </div> 
                                </div> 
                            </label> 
                        </div>
                    <!-- Nút thanh toán -->
                    <div class="text-center mt-8">
                        <button type="submit" id="payButton" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition duration-300">
                            Thanh toán {{ number_format($donDatVe->tong_tien) }}đ
                        </button>
                        <p class="text-sm text-gray-600 mt-2">Bằng cách nhấn thanh toán, bạn đồng ý với điều khoản sử dụng</p>
                    </div>
                </form>
            </div>
 

@endsection
