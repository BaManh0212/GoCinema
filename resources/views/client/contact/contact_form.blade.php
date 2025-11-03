@extends('client.layouts.app')

@section('title', 'Liên hệ')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/contact.css') }}">
@endpush

@section('content')

{{-- ===== HERO + FORM SECTION ===== --}}
<section class="contact-hero">
    <div class="overlay"></div>
    <div class="container py-5">
        <div class="row align-items-center gx-5">

            {{-- LEFT TEXT --}}
            <div class="col-lg-6 text-white">
                <h1 class="fw-bold mb-4">Liên hệ với chúng tôi!</h1>
                <p class="mb-4">
                    Bạn có ý tưởng phim hay, kịch bản hấp dẫn hay đơn giản là mong muốn hợp tác sản xuất phim chất lượng cao?
                </p>
                <p class="mb-4">
                    Đội ngũ GoCinema luôn tìm kiếm những cơ hội hợp tác mới, những ý tưởng sáng tạo và đối tác trong lĩnh vực điện ảnh.
                </p>
                <p>Hãy điền vào mẫu bên cạnh, chúng tôi sẽ phản hồi sớm nhất.</p>
            </div>

            {{-- RIGHT FORM --}}
            <div class="col-lg-6">
                <div class="contact-form-box p-4">
                    @if(session('success'))
                        <div class="alert">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">

                            <div class="col-md-12">
                                <select name="prefix" class="form-select">
                                    <option value="Ông">Ông</option>
                                    <option value="Bà">Bà</option>
                                    <option value="Anh">Anh</option>
                                    <option value="Chị">Chị</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <input type="text" name="name" class="form-control"
                                       placeholder="Họ và tên *"
                                       value="{{ old('name', auth()->user()->name ?? '') }}" required>
                            </div>

                            <div class="col-md-6">
                                <input type="text" name="phone" class="form-control"
                                       placeholder="Số điện thoại *" required>
                            </div>

                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control"
                                       placeholder="Email *"
                                       value="{{ old('email', auth()->user()->email ?? '') }}" required>
                            </div>

                            <div class="col-md-12">
                                <textarea name="message" class="form-control" rows="5"
                                          placeholder="Nội dung chi tiết về dự án/yêu cầu của bạn *" required>{{ old('message') }}</textarea>
                            </div>

                        </div>

                        <div class="mt-4">
                            <button class="btn btn-contact w-100">Gửi liên hệ →</button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</section>

{{-- ===== MAP + INFO SECTION ===== --}}
<section class="contact-info">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="map-container">
                    <iframe class="rounded-3 w-100" height="400"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3836.0000000000005!2d105.749445!3d21.036749!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3134520000000001%3A0x0000000000000000!2zQ2FvIMSQ4bqhaSBCw6FpLCBYdcOibiBQxZyw7MgdGFsYyBIw6AgTuG7mWksIEjDoCBO4buZaQ!5e0!3m2!1svi!2s!4v1709700000000!5m2!1svi!2s"
                        allowfullscreen loading="lazy">
                    </iframe>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="contact-info-text">
                    <h3>Thông tin liên hệ</h3>
                    <p><strong>Trụ sở chính:</strong> Số 13, Phố Trịnh Văn Bô, Nam Từ Liêm, Hà Nội</p>
                    <p><strong>Điện thoại:</strong> 0359445669</p>
                    <p><strong>Email:</strong> gocinema@gmail.com</p>
                    <p><strong>Giờ làm việc:</strong> Thứ 2 - Thứ 6: 8:00 - 18:00</p>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/contact.css') }}">
<style>
    body {
        background: url('{{ asset('uploads/backgrounds/backGroundContact.png') }}') no-repeat center center fixed;
        background-size: cover;
        background-attachment: fixed;
        z-index: 0;
    }
</style>
@endpush