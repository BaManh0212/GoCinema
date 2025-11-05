<footer class="text-light pt-5 pb-3 mt-5" style="background-color: #171515ff;">
    <div class="container" style="max-width: 1200px;">
        <div class="row gy-4">
            {{-- 🔹 Cột 1: Logo & giới thiệu --}}
            <div class="col-md-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ asset('uploads/rap/logo-datn.png') }}" alt="GoCinema Logo" 
                         style="height:40px; margin-right:10px;">
                    <h5 class="fw-bold mb-0 text-danger">GoCinema</h5>
                </div>
                <p class="small text-white-50">
                    GoCinema – Hệ thống đặt vé xem phim trực tuyến nhanh chóng, tiện lợi và an toàn.
                    Cập nhật liên tục các bộ phim hot nhất tại các rạp trên toàn quốc.
                </p>
            </div>

            {{-- 🔹 Cột 2: Liên kết nhanh --}}
            <div class="col-md-4">
                <h6 class="fw-bold text-uppercase mb-3">Liên kết nhanh</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('home') }}" class="footer-link">Trang chủ</a></li>
                    <li><a href="#" class="footer-link">Phim</a></li>
                    <li><a href="#" class="footer-link">Lịch chiếu</a></li>
                    <li><a href="#" class="footer-link">Khuyến mãi</a></li>
                    <li><a href="{{ route('contact.create') }}" class="footer-link">Liên hệ</a></li>
                </ul>
            </div>

            {{-- 🔹 Cột 3: Liên hệ --}}
            <div class="col-md-4">
                <h6 class="fw-bold text-uppercase mb-3">Liên hệ</h6>
                <ul class="list-unstyled small">
                    <li><i class="bi bi-geo-alt-fill text-danger me-2"></i>13 Trịnh Văn Bô, Hà Nội</li>
                    <li><i class="bi bi-telephone-fill text-danger me-2"></i>0359445669</li>
                    <li><i class="bi bi-envelope-fill text-danger me-2"></i>gocinema@gmail.com</li>
                </ul>
            </div>
        </div>

        <hr class="mt-4 mb-3 text-secondary">

        {{-- 🔹 Bản quyền --}}
        <div class="text-center small text-white-50">
            © {{ date('Y') }} GoCinema.
        </div>
    </div>
</footer>
