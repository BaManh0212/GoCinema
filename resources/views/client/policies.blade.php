@extends('client.layouts.app')

@section('title', 'Quy định & Chính sách - GoCinema')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/policies.css') }}">
@endpush

@section('content')
{{-- ===== HERO SECTION ===== --}}
<section class="policy-hero">
    <div class="container-fluid">
        <div class="row align-items-center min-vh-100">
            <div class="col-12">
                <div class="hero-content text-center">
                    <h1 class="display-3 fw-bold mb-4">
                        <span class="text-gradient">Quy Định</span> & Chính Sách
                    </h1>
                    <p class="lead text-light opacity-75 mb-5 px-3">
                        Để đảm bảo trải nghiệm xem phim tốt nhất cho tất cả khách hàng,<br>
                        vui lòng đọc kỹ các quy định và chính sách dưới đây
                    </p>

                    {{-- Stats Cards --}}
                    <div class="stats-container mb-5">
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-3 col-6">
                                <div class="stat-card">
                                    <div class="stat-number">100%</div>
                                    <div class="stat-label">Bảo mật</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-card">
                                    <div class="stat-number">24/7</div>
                                    <div class="stat-label">Hỗ trợ</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-card">
                                    <div class="stat-number">15+</div>
                                    <div class="stat-label">Năm kinh nghiệm</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-card">
                                    <div class="stat-number">50K+</div>
                                    <div class="stat-label">Khách hàng</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== MAIN CONTENT ===== --}}
<section class="policy-content py-5">
    <div class="container">
        <div class="policy-container">

            {{-- Navigation Tabs --}}
            <div class="policy-nav mb-5">
                <div class="nav nav-pills justify-content-center" id="policy-tabs" role="tablist">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button" role="tab">
                        <i class="fas fa-scroll me-2"></i>Quy định chung
                    </button>
                    <button class="nav-link" id="tickets-tab" data-bs-toggle="pill" data-bs-target="#tickets" type="button" role="tab">
                        <i class="fas fa-ticket-alt me-2"></i>Chính sách vé
                    </button>
                    <button class="nav-link" id="privacy-tab" data-bs-toggle="pill" data-bs-target="#privacy" type="button" role="tab">
                        <i class="fas fa-shield-alt me-2"></i>Bảo mật
                    </button>
                    <button class="nav-link" id="membership-tab" data-bs-toggle="pill" data-bs-target="#membership" type="button" role="tab">
                        <i class="fas fa-star me-2"></i>Thành viên
                    </button>
                    <button class="nav-link" id="support-tab" data-bs-toggle="pill" data-bs-target="#support" type="button" role="tab">
                        <i class="fas fa-headset me-2"></i>Hỗ trợ
                    </button>
                </div>
            </div>

            {{-- Tab Content --}}
            <div class="tab-content" id="policy-tab-content">

                {{-- 1. Quy định chung --}}
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="policy-card">
                        <div class="card-header">
                            <div class="policy-icon">
                                <i class="fas fa-scroll"></i>
                            </div>
                            <div>
                                <h3 class="mb-1">Quy định chung</h3>
                                <p class="text-muted mb-0">Các quy tắc cơ bản khi sử dụng dịch vụ</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="policy-grid">
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Thời gian đến rạp</h6>
                                        <p>Khách hàng vui lòng đến rạp trước giờ chiếu ít nhất 15 phút để làm thủ tục vào rạp</p>
                                    </div>
                                </div>
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-ban"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Đồ ăn thức uống</h6>
                                        <p>Nghiêm cấm mang đồ ăn, thức uống từ bên ngoài vào phòng chiếu</p>
                                    </div>
                                </div>
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-video-slash"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Quay phim chụp ảnh</h6>
                                        <p>Không hút thuốc, quay phim, chụp ảnh hoặc ghi âm trong suốt quá trình chiếu phim</p>
                                    </div>
                                </div>
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-child"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Trẻ em</h6>
                                        <p>Trẻ em dưới 13 tuổi bắt buộc phải có người lớn đi cùng khi xem phim có giới hạn độ tuổi</p>
                                    </div>
                                </div>
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-volume-mute"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Trật tự</h6>
                                        <p>Giữ trật tự, không gây ồn ào ảnh hưởng đến khách hàng khác</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Chính sách vé --}}
                <div class="tab-pane fade" id="tickets" role="tabpanel">
                    <div class="policy-card">
                        <div class="card-header">
                            <div class="policy-icon">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div>
                                <h3 class="mb-1">Chính sách vé</h3>
                                <p class="text-muted mb-0">Quy định về mua và sử dụng vé</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="policy-grid">
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Hoàn tiền</h6>
                                        <p>Vé đã thanh toán thành công không được hoàn tiền hoặc đổi trả dưới mọi hình thức</p>
                                    </div>
                                </div>
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Thời gian thanh toán</h6>
                                        <p>Vé đặt trực tuyến cần được thanh toán trong vòng 10 phút, sau thời gian này hệ thống sẽ tự động hủy</p>
                                    </div>
                                </div>
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Vé giảm giá</h6>
                                        <p>Vé giảm giá chỉ áp dụng cho đối tượng quy định với giấy tờ tùy thân hợp lệ</p>
                                    </div>
                                </div>
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Khuyến mãi</h6>
                                        <p>Khuyến mãi có thể thay đổi theo từng thời điểm và không được cộng dồn</p>
                                    </div>
                                </div>
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Hạn sử dụng</h6>
                                        <p>Vé mua trước có thể được sử dụng trong vòng 30 ngày kể từ ngày mua</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Chính sách bảo mật --}}
                <div class="tab-pane fade" id="privacy" role="tabpanel">
                    <div class="policy-card">
                        <div class="card-header">
                            <div class="policy-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <h3 class="mb-1">Chính sách bảo mật</h3>
                                <p class="text-muted mb-0">Cam kết bảo vệ thông tin cá nhân</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="policy-grid">
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Mã hóa dữ liệu</h6>
                                        <p>Mọi thông tin cá nhân của khách hàng đều được mã hóa và bảo mật tuyệt đối</p>
                                    </div>
                                </div>
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Mục đích sử dụng</h6>
                                        <p>Thông tin chỉ được sử dụng cho mục đích hỗ trợ dịch vụ và cải thiện trải nghiệm</p>
                                    </div>
                                </div>
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-cookie-bite"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Cookie</h6>
                                        <p>Hệ thống sử dụng cookie để ghi nhớ sở thích và tối ưu hóa trải nghiệm người dùng</p>
                                    </div>
                                </div>
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-handshake-slash"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Không chia sẻ</h6>
                                        <p>Chúng tôi cam kết không chia sẻ thông tin cá nhân với bên thứ ba mà không có sự đồng ý</p>
                                    </div>
                                </div>
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Thanh toán an toàn</h6>
                                        <p>Dữ liệu thanh toán được xử lý thông qua các cổng thanh toán bảo mật quốc tế</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Chính sách thành viên --}}
                <div class="tab-pane fade" id="membership" role="tabpanel">
                    <div class="policy-card">
                        <div class="card-header">
                            <div class="policy-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div>
                                <h3 class="mb-1">Chính sách thành viên</h3>
                                <p class="text-muted mb-0">Quyền lợi dành cho thành viên</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="policy-grid">
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Tích điểm</h6>
                                        <p>Thành viên tích lũy 1 điểm cho mỗi 10,000 VNĐ chi tiêu tại rạp</p>
                                    </div>
                                </div>
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-birthday-cake"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Ưu đãi sinh nhật</h6>
                                        <p>Vé miễn phí + combo bắp nước (áp dụng trong tháng sinh)</p>
                                    </div>
                                </div>
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-medal"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Cấp độ thành viên</h6>
                                        <p>Silver (0-499), Gold (500-999), Diamond (1000+ điểm)</p>
                                    </div>
                                </div>
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-crown"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Quyền lợi VIP</h6>
                                        <p>Ưu tiên đặt vé, phòng VIP, parking miễn phí</p>
                                    </div>
                                </div>
                                <div class="policy-item">
                                    <div class="item-icon">
                                        <i class="fas fa-exchange-alt"></i>
                                    </div>
                                    <div class="item-content">
                                        <h6>Đổi quà</h6>
                                        <p>100 điểm = 1 vé miễn phí hoặc có thể đổi quà tại quầy</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 5. Liên hệ & Hỗ trợ --}}
                <div class="tab-pane fade" id="support" role="tabpanel">
                    <div class="policy-card">
                        <div class="card-header">
                            <div class="policy-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div>
                                <h3 class="mb-1">Liên hệ & Hỗ trợ</h3>
                                <p class="text-muted mb-0">Thông tin liên hệ và hỗ trợ khách hàng</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="contact-grid">
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="contact-content">
                                        <h6>Email hỗ trợ</h6>
                                        <a href="mailto:cskh@gocinema.vn" class="contact-link">cskh@gocinema.vn</a>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div class="contact-content">
                                        <h6>Hotline</h6>
                                        <a href="tel:0359445669" class="contact-link">0359445669</a>
                                        <span class="contact-note">(7:00 - 22:00 hàng ngày)</span>
                                    </div>
                                </div>
                            </div>

                            <div class="important-notes mt-4">
                                <div class="notes-header">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <h6>Lưu ý quan trọng</h6>
                                </div>
                                <ul class="notes-list">
                                    <li>Mọi khiếu nại cần có hóa đơn hoặc mã đặt vé</li>
                                    <li>Chính sách có thể thay đổi theo thông báo mới</li>
                                    <li>Vui lòng cập nhật thông tin mới nhất tại website</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
