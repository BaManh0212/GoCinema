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
                <div class="hero-content">
                    <h1 class="fw-bold mb-4 display-4">Liên hệ với chúng tôi!</h1>
                    <p class="mb-4 lead">
                        Bạn có ý tưởng phim hay, kịch bản hấp dẫn hay đơn giản là mong muốn hợp tác sản xuất phim chất lượng cao?
                    </p>
                    <p class="mb-4">
                        Đội ngũ GoCinema luôn tìm kiếm những cơ hội hợp tác mới, những ý tưởng sáng tạo và đối tác trong lĩnh vực điện ảnh.
                    </p>
                    <p class="mb-4">Hãy điền vào mẫu bên cạnh, chúng tôi sẽ phản hồi sớm nhất.</p>

                    {{-- Contact Stats --}}
                    <div class="contact-stats mt-5">
                        <div class="row g-4">
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="stat-number">24/7</div>
                                    <div class="stat-label">Hỗ trợ</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="stat-number"><1h</div>
                                    <div class="stat-label">Phản hồi</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT FORM --}}
            <div class="col-lg-6">
                <div class="contact-form-box p-4">
                    <div class="form-header mb-4">
                        <h3 class="text-white mb-3">Gửi tin nhắn</h3>
                        <p class="text-light opacity-75 mb-0">Chúng tôi luôn sẵn sàng lắng nghe ý tưởng của bạn</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" class="contact-form">
                        @csrf
                        <div class="row g-3">

                            <div class="col-md-12">
                                <label class="form-label text-white fw-semibold">Xưng hô</label>
                                <select name="prefix" class="form-select">
                                    <option value="Ông">Ông</option>
                                    <option value="Bà">Bà</option>
                                    <option value="Anh">Anh</option>
                                    <option value="Chị">Chị</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label text-white fw-semibold">Họ và tên</label>
                                <input type="text" name="name" class="form-control"
                                       placeholder="Nhập họ và tên của bạn"
                                       value="{{ old('name', auth()->user()->name ?? '') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white fw-semibold">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control"
                                       placeholder="Nhập số điện thoại"
                                       value="{{ old('phone') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control"
                                       placeholder="Nhập địa chỉ email"
                                       value="{{ old('email', auth()->user()->email ?? '') }}" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label text-white fw-semibold">Nội dung</label>
                                <textarea name="message" class="form-control" rows="5"
                                          placeholder="Hãy chia sẻ ý tưởng hoặc yêu cầu của bạn..." required>{{ old('message') }}</textarea>
                            </div>

                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-contact w-100">
                                <i class="fas fa-paper-plane me-2"></i>Gửi liên hệ
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</section>

{{-- ===== FEATURES SECTION ===== --}}
<section class="contact-features py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-clock fa-3x text-danger"></i>
                    </div>
                    <h5 class="text-white">Phản hồi nhanh</h5>
                    <p class="text-light opacity-75">Chúng tôi cam kết phản hồi trong vòng 24 giờ</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-users fa-3x text-danger"></i>
                    </div>
                    <h5 class="text-white">Đội ngũ chuyên nghiệp</h5>
                    <p class="text-light opacity-75">Đội ngũ giàu kinh nghiệm trong ngành điện ảnh</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-handshake fa-3x text-danger"></i>
                    </div>
                    <h5 class="text-white">Hợp tác lâu dài</h5>
                    <p class="text-light opacity-75">Xây dựng mối quan hệ đối tác bền vững</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== MAP + INFO SECTION ===== --}}
<section class="contact-info">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <div class="map-container">
                    <iframe class="rounded-3 w-100" height="400"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3836.0000000000005!2d105.749445!3d21.036749!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3134520000000001%3A0x0000000000000000!2zQ2FvIMSQ4bqhaSBCw6FpLCBYdcOibiBQxZyw7MgdGFsYyBIw6AgTuG7mWksIEjDoCBO4buZaQ!5e0!3m2!1svi!2s!4v1709700000000!5m2!1svi!2s!4v1709700000000!5m2!1svi!2s"
                        allowfullscreen loading="lazy">
                    </iframe>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="contact-info-text">
                    <h3 class="mb-4">Thông tin liên hệ</h3>

                    <div class="contact-item mb-4">
                        <div class="d-flex align-items-start">
                            <div class="contact-icon me-3">
                                <i class="fas fa-map-marker-alt fa-lg text-danger"></i>
                            </div>
                            <div>
                                <h6 class="text-white mb-1">Trụ sở chính</h6>
                                <p class="text-light mb-0">Số 13, Phố Trịnh Văn Bô, Nam Từ Liêm, Hà Nội</p>
                            </div>
                        </div>
                    </div>

                    <div class="contact-item mb-4">
                        <div class="d-flex align-items-start">
                            <div class="contact-icon me-3">
                                <i class="fas fa-phone fa-lg text-danger"></i>
                            </div>
                            <div>
                                <h6 class="text-white mb-1">Điện thoại</h6>
                                <p class="text-light mb-0">0359445669</p>
                            </div>
                        </div>
                    </div>

                    <div class="contact-item mb-4">
                        <div class="d-flex align-items-start">
                            <div class="contact-icon me-3">
                                <i class="fas fa-envelope fa-lg text-danger"></i>
                            </div>
                            <div>
                                <h6 class="text-white mb-1">Email</h6>
                                <p class="text-light mb-0">gocinema@gmail.com</p>
                            </div>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="d-flex align-items-start">
                            <div class="contact-icon me-3">
                                <i class="fas fa-clock fa-lg text-danger"></i>
                            </div>
                            <div>
                                <h6 class="text-white mb-1">Giờ làm việc</h6>
                                <p class="text-light mb-0">Thứ 2 - Thứ 6: 8:00 - 18:00</p>
                            </div>
                        </div>
                    </div>
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