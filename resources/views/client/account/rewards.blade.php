@extends('client.layouts.app')

@section('title', 'Đổi điểm thưởng')

@section('content')
<style>
  :root {
    --bg: #0b1220;
    --card: #111827;
    --muted: #9ca3af;
    --text: #e6eef8;
    --primary: #6366f1;
    --accent: #f59e0b;
    --border: rgba(255,255,255,0.04);
    --radius: 12px;
  }

  body { background:linear-gradient(180deg,var(--bg) 0%,#07101a 100%); color:var(--text); font-family:'Inter','Poppins',sans-serif; }
  .container-account{
    padding: 40px 20px;
  }

  /* ===== SIDEBAR ===== */
  .account-sidebar{
    background: var(--card);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    overflow: hidden;
  }

  .account-sidebar .profile-top{
    padding: 28px 20px;
    text-align: center;
    border-bottom: 1px solid var(--border);
  }

  /* ===== AVATAR UPLOAD ===== */
  .avatar-wrapper {
    position: relative;
    display: inline-block;
  }
  .avatar-wrapper img {
    width: 88px; height: 88px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255,255,255,0.06);
    box-shadow: 0 6px 18px rgba(2,6,23,0.6);
    transition: 0.3s;
  }
  .avatar-wrapper:hover img {
    opacity: 0.4;
  }
  .avatar-overlay {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    background: rgba(0,0,0,0.4);
    color: #fff;
    font-size: 18px;
    transition: 0.3s;
  }
  .avatar-wrapper:hover .avatar-overlay {
    opacity: 1;
  }
  .avatar-overlay label {
    cursor: pointer;
  }
  #avatarInput {
    display: none;
  }

  .account-sidebar .profile-top h5{ margin-top:12px; margin-bottom:4px; font-weight:600; color:var(--text)}
  .account-sidebar .profile-top p{ margin:0; color:var(--muted); font-size:0.9rem }

  .account-sidebar .points{
    display:inline-block; margin-top:12px; padding:6px 12px; border-radius:999px;
    background: rgba(99,102,241,0.12); color:var(--primary); font-weight:600; font-size:0.95rem;
    border:1px solid rgba(99,102,241,0.08);
  }

  .account-sidebar .list-group{ padding:12px 8px; }
  .account-sidebar .list-group a{
    display:flex; align-items:center; gap:12px; padding:12px 18px; margin:6px 8px; border-radius:10px;
    color:var(--muted); text-decoration:none; font-weight:600; border:1px solid transparent;
  }
  .account-sidebar .list-group a i{ width:28px; text-align:center }
  .account-sidebar .list-group a:hover{ background:#0f1a2b; color:var(--text); }
  .account-sidebar .list-group a.active{ background:var(--primary); color:#fff; box-shadow:0 6px 20px rgba(99,102,241,0.12); }

  /* MAIN CONTENT */
  .card-account { background:var(--card); border-radius:var(--radius); border:1px solid var(--border); color:var(--text); }
  .card-account .card-body { padding:20px; }

  .voucher-card {
    background:var(--card); border:1px solid var(--border); border-radius:var(--radius);
    transition:0.2s; padding:20px;
  }
  .voucher-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,0.2); }

  .badge-success { background:#064e3b; color:#86efac; padding:6px 10px; border-radius:999px; font-weight:700 }
  .badge-danger { background:#4c0519; color:#fca5a5; padding:6px 10px; border-radius:999px; font-weight:700 }
  .badge-secondary { background:#374151; color:#d1d5db; padding:6px 10px; border-radius:999px; font-weight:700 }
  .badge-warning { background:#78350f; color:#fcd34d; padding:6px 10px; border-radius:999px; font-weight:700 }
  .badge-info { background:#0c4a6e; color:#7dd3fc; padding:6px 10px; border-radius:999px; font-weight:700 }

  .btn-primary {
    background:var(--primary); border:none; border-radius:8px; font-weight:700;
    padding:10px 18px; box-shadow:0 6px 18px rgba(99,102,241,0.12);
  }
  .btn-primary:hover { opacity:0.9; transform:translateY(-2px); }

  .alert-custom {
    background:rgba(99,102,241,0.12); border:1px solid rgba(99,102,241,0.15);
    color:var(--text); border-radius:var(--radius);
  }
  .text-soft {
    color: #cbd5e1 !important; /* sáng hơn text-muted */
    opacity: 0.9;
}
  .text-soft i {
    color: var(--primary);
    opacity: 0.8;
}


  @media(max-width:991px){ .account-sidebar{ margin-bottom:18px } }
</style>

<div class="container container-account">
  <div class="row g-4">

        {{-- SIDEBAR --}}
    <div class="col-lg-3 col-md-4">
      <aside class="account-sidebar">
        <div class="profile-top">
          <form action="{{ route('account.update-avatar') }}" method="POST" enctype="multipart/form-data" id="avatarForm">
            @csrf
            <div class="avatar-wrapper">
              <img id="previewAvatar" src="{{ $user->avatar_url }}" alt="Avatar">
              <div class="avatar-overlay">
                <label for="avatarInput"><i class="fas fa-camera"></i></label>
              </div>
              <input type="file" name="avatar" id="avatarInput" accept="image/*">
            </div>
          </form>

          <h5>{{ $user->ho_ten }}</h5>
          <p class="small">{{ $user->email }}</p>
          <div class="points"><i class="fas fa-star"></i>&nbsp; {{ number_format($user->diem) }} điểm</div>
        </div>

        <div class="list-group">
          <a href="{{ route('account.index') }}" class="{{ request()->routeIs('account.index') ? 'active' : '' }}">
            <i class="fas fa-user"></i> <span>Thông tin tài khoản</span>
          </a>
          <a href="{{ route('account.rewards') }}" class="{{ request()->routeIs('account.rewards') ? 'active' : '' }}">
            <i class="fas fa-gift"></i> <span>Đổi điểm thưởng</span>
          </a>
          <a href="{{ route('account.my-vouchers') }}" class="{{ request()->routeIs('account.my-vouchers') ? 'active' : '' }}">
            <i class="fas fa-ticket-alt"></i> <span>Voucher của tôi</span>
          </a>
          <a href="{{ route('account.point-history') }}" class="{{ request()->routeIs('account.point-history') ? 'active' : '' }}">
            <i class="fas fa-history"></i> <span>Lịch sử điểm</span>
          </a>
        </div>
      </aside>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="col-lg-9 col-md-8">

      <div class="card-account mb-4" style="background:linear-gradient(135deg,#6366f1 0%,#a855f7 100%); color:#fff;">
        <div class="card-body text-center">
          <h2 class="mb-2"><i class="fas fa-ticket-alt me-2"></i>Đổi điểm lấy voucher VÉ PHIM</h2>
          <p class="mb-0">Bạn có <strong>{{ number_format($user->diem) }} điểm</strong> - Hãy đổi lấy voucher giảm giá ngay!</p>
          <small class="text-white-50">Voucher có hiệu lực 30 ngày kể từ ngày đổi</small>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success alert-custom"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger alert-custom"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}</div>
      @endif

      @if($vouchers->isEmpty())
        <div class="alert alert-custom text-center">
          <i class="fas fa-info-circle me-2"></i>Hiện tại chưa có voucher nào để đổi điểm.
        </div>
      @else
        <div class="row g-3">
          @foreach($vouchers as $voucher)
            @php
              $duDiem = $user->diem >= $voucher->diem_can;
              $conVoucher = $voucher->conVoucherDeDoi();
              $coTheDoiDuoc = $duDiem && $conVoucher;
              $conLai = $voucher->so_luong_toi_da - $voucher->so_luong_da_dung;
              $phanTram = ($voucher->so_luong_toi_da > 0) ? ($conLai / $voucher->so_luong_toi_da * 100) : 0;
            @endphp

            <div class="col-md-6">
              <div class="voucher-card {{ !$coTheDoiDuoc ? 'opacity-75' : '' }}">
                <div class="d-flex justify-content-between align-items-start mb-3">
                  <h5 class="mb-0"><i class="fas fa-ticket-alt text-primary me-2"></i>{{ $voucher->ten }}</h5>
                  <span class="badge-info">{{ number_format($voucher->diem_can) }} điểm</span>
                </div>

                <div class="d-flex justify-content-between small mb-2">
                  <span><i class="fas fa-tags text-success"></i> Loại: {{ $voucher->loai == 'phan_tram' ? 'Giảm %' : 'Giảm tiền' }}</span>
                  <span><i class="fas fa-money-bill text-success"></i> {{ $voucher->moTaGiaTri }}</span>
                </div>

                <div class="mb-2">
                  <span class="badge {{ $phanTram > 50 ? 'badge-success' : ($phanTram > 20 ? 'badge-warning' : 'badge-danger') }}">
                    {{ $conLai }}/{{ $voucher->so_luong_toi_da }} còn lại
                  </span>
                </div>

                <div class="small text-soft mb-3"><i class="fas fa-clock me-1"></i>Hiệu lực: 30 ngày</div>

                @if($coTheDoiDuoc)
                  <form action="{{ route('account.redeem-voucher', $voucher->id) }}" method="POST"
                        onsubmit="return confirm('Bạn có chắc muốn đổi {{ number_format($voucher->diem_can) }} điểm lấy voucher này?')">
                    @csrf
                    <button class="btn btn-primary w-100"><i class="fas fa-exchange-alt me-2"></i>Đổi ngay</button>
                  </form>
                @elseif(!$conVoucher)
                  <button class="btn btn-danger w-100" disabled><i class="fas fa-ban me-2"></i>Đã hết</button>
                @else
                  <button class="btn btn-secondary w-100" disabled><i class="fas fa-lock me-2"></i>Chưa đủ điểm</button>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      @endif

      <div class="card-account mt-4">
        <div class="card-body">
          <h5><i class="fas fa-question-circle text-info me-2"></i>Cách tích điểm & sử dụng voucher</h5>
          <ul class="mb-0">
            <li>Mỗi lần đặt vé xem phim, bạn sẽ nhận được điểm thưởng.</li>
            <li>1000đ chi tiêu = 1 điểm tích lũy.</li>
            <li>Dùng điểm để đổi lấy <strong class="text-danger">voucher vé phim</strong>.</li>
            <li><strong class="text-primary">Voucher có hiệu lực 30 ngày</strong> từ ngày đổi.</li>
            <li>Voucher lưu trong mục "Voucher của tôi" sau khi đổi.</li>
            <li>Voucher chỉ áp dụng cho vé xem phim.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
