@extends('client.layouts.app')

@section('title', 'Quy định & Chính sách - GoCinema')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/policies.css') }}">
@endpush

@section('content')
<div class="policy-hero">
    <div class="container">
        <h1 class="text-center mb-4">Quy Định & Chính Sách</h1>
        <p class="text-center text-white-50 mb-5">
            Để đảm bảo trải nghiệm xem phim tốt nhất cho tất cả khách hàng, vui lòng đọc kỹ các quy định dưới đây
        </p>

        <div class="policy-container">
            {{-- 1. Quy định chung --}}
            <div class="policy-section">
                <div class="policy-icon">
                    <i class="fas fa-scroll"></i>
                </div>
                <h2>1. Quy định chung</h2>
                <ul class="policy-list">
                    <li>Khách hàng vui lòng đến rạp trước giờ chiếu ít nhất 15 phút để làm thủ tục vào rạp</li>
                    <li>Nghiêm cấm mang đồ ăn, thức uống từ bên ngoài vào phòng chiếu</li>
                    <li>Không hút thuốc, quay phim, chụp ảnh hoặc ghi âm trong suốt quá trình chiếu phim</li>
                    <li>Trẻ em dưới 13 tuổi bắt buộc phải có người lớn đi cùng khi xem phim có giới hạn độ tuổi</li>
                    <li>Giữ trật tự, không gây ồn ào ảnh hưởng đến khách hàng khác</li>
                </ul>
            </div>

            {{-- 2. Chính sách vé --}}
            <div class="policy-section">
                <div class="policy-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h2>2. Chính sách vé</h2>
                <ul class="policy-list">
                    <li>Vé đã thanh toán thành công không được hoàn tiền hoặc đổi trả dưới mọi hình thức</li>
                    <li>Vé đặt trực tuyến cần được thanh toán trong vòng 10 phút, sau thời gian này hệ thống sẽ tự động hủy</li>
                    <li>Vé giảm giá chỉ áp dụng cho đối tượng quy định (học sinh, sinh viên, người cao tuổi) với giấy tờ tùy thân hợp lệ</li>
                    <li>Khuyến mãi có thể thay đổi theo từng thời điểm và không được cộng dồn với các ưu đãi khác</li>
                    <li>Vé mua trước có thể được sử dụng trong vòng 30 ngày kể từ ngày mua</li>
                </ul>
            </div>

            {{-- 3. Chính sách bảo mật --}}
            <div class="policy-section">
                <div class="policy-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h2>3. Chính sách bảo mật</h2>
                <ul class="policy-list">
                    <li>Mọi thông tin cá nhân của khách hàng đều được mã hóa và bảo mật tuyệt đối</li>
                    <li>Thông tin chỉ được sử dụng cho mục đích hỗ trợ dịch vụ và cải thiện trải nghiệm khách hàng</li>
                    <li>Hệ thống sử dụng cookie để ghi nhớ sở thích và tối ưu hóa trải nghiệm người dùng</li>
                    <li>Chúng tôi cam kết không chia sẻ thông tin cá nhân với bên thứ ba mà không có sự đồng ý</li>
                    <li>Dữ liệu thanh toán được xử lý thông qua các cổng thanh toán bảo mật quốc tế</li>
                </ul>
            </div>

            {{-- 4. Chính sách thành viên --}}
            <div class="policy-section">
                <div class="policy-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h2>4. Chính sách thành viên</h2>
                <ul class="policy-list">
                    <li>Thành viên tích lũy 1 điểm cho mỗi 10,000 VNĐ chi tiêu tại rạp</li>
                    <li>Ưu đãi sinh nhật: Vé miễn phí + combo bắp nước (áp dụng trong tháng sinh)</li>
                    <li>Cấp độ thành viên: Silver (0-499 điểm), Gold (500-999 điểm), Diamond (1000+ điểm)</li>
                    <li>Quyền lợi cao cấp: Ưu tiên đặt vé, phòng VIP, parking miễn phí</li>
                    <li>100 điểm = 1 vé miễn phí hoặc có thể đổi quà tại quầy</li>
                </ul>
            </div>

            {{-- 5. Liên hệ & Hỗ trợ --}}
            <div class="policy-section">
                <div class="policy-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h2>5. Liên hệ & Hỗ trợ</h2>
                <div class="contact-info">
                    <p>
                        <i class="fas fa-envelope"></i>
                        <strong>Email hỗ trợ:</strong>
                        <a href="mailto:cskh@gocinema.vn">cskh@gocinema.vn</a>
                    </p>
                    <p>
                        <i class="fas fa-phone"></i>
                        <strong>Hotline:</strong>
                        <a href="tel:0359445669">0359445669</a>
                        <span class="text-muted">(Hoạt động 7:00 - 22:00 hàng ngày)</span>
                    </p>
                </div>

                <div class="important-notes mt-4">
                    <h6 class="text-danger"><i class="fas fa-exclamation-triangle"></i> Lưu ý quan trọng:</h6>
                    <ul>
                        <li>Mọi khiếu nại cần có hóa đơn hoặc mã đặt vé</li>
                        <li>Chính sách có thể thay đổi theo thông báo mới</li>
                        <li>Vui lòng cập nhật thông tin mới nhất tại website</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection