@extends('client.layouts.app')

@section('title', 'Voucher của tôi')

@section('content')
<style>
  :root{
    --bg: #0b1220;
    --card: #111827;
    --muted: #9ca3af;
    --text: #e6eef8;
    --primary: #6366f1;
    --accent: #f59e0b;
    --border: rgba(255,255,255,0.04);
    --radius: 12px;
  }

  body{ background:linear-gradient(180deg,var(--bg) 0%,#07101a 100%); color:var(--text); font-family:'Inter','Poppins',sans-serif; }
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

  /* CARD */
  .card-account{ background:var(--card); border-radius:var(--radius); border:1px solid var(--border); color:var(--text); }
  .card-account .card-header{ padding:14px 20px; border-bottom:1px solid var(--border); font-weight:700; font-size:1.05rem; display:flex; align-items:center; gap:10px; }
  .card-account .card-body{ padding:20px; }

  .voucher-card{
    background:var(--card); border:1px solid var(--border); border-radius:var(--radius);
    transition:0.2s; padding:20px;
  }
  .voucher-card:hover{ transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,0.2); }

  .badge-success{ background:#064e3b; color:#86efac; padding:6px 10px; border-radius:999px; font-weight:700 }
  .badge-danger{ background:#4c0519; color:#fca5a5; padding:6px 10px; border-radius:999px; font-weight:700 }
  .badge-secondary{ background:#374151; color:#d1d5db; padding:6px 10px; border-radius:999px; font-weight:700 }
  .badge-warning{ background:#78350f; color:#fcd34d; padding:6px 10px; border-radius:999px; font-weight:700 }
  .badge-info{ background:#0c4a6e; color:#7dd3fc; padding:6px 10px; border-radius:999px; font-weight:700 }

  .alert-custom{ background:rgba(99,102,241,0.12); border:1px solid rgba(99,102,241,0.15); color:var(--text); border-radius:var(--radius); }
  .btn-primary{ background:var(--primary); border:none; border-radius:8px; font-weight:700; padding:10px 18px; box-shadow:0 6px 18px rgba(99,102,241,0.12); }
  .btn-outline-secondary{ border-color:rgba(255,255,255,0.1); color:var(--text); }
  .btn-outline-secondary:hover{ background:var(--primary); color:#fff; }

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
        <div class="card-body">
          <h2 class="mb-2"><i class="fas fa-ticket-alt me-2"></i>Voucher vé phim của tôi</h2>
          <p class="mb-0">Quản lý và sử dụng các voucher giảm giá vé đã đổi</p>
          <small class="text-white-50">Voucher có hiệu lực 30 ngày kể từ ngày đổi</small>
        </div>
      </div>

      @if($vouchers->isEmpty())
        <div class="alert alert-custom text-center">
          <i class="fas fa-info-circle me-2"></i>Bạn chưa có voucher nào.
          <a href="{{ route('account.rewards') }}" class="text-primary fw-bold">Đổi điểm ngay</a> để nhận voucher ưu đãi!
        </div>
      @else
        @foreach($vouchers as $voucherNguoiDung)
          @php
            $voucher = $voucherNguoiDung->voucher;
            $conHieuLuc = $voucherNguoiDung->conSuDungDuoc();
            $trangThai = $voucherNguoiDung->trang_thai;
          @endphp

          <div class="voucher-card mb-3 {{ !$conHieuLuc ? 'opacity-75' : '' }}">
            <div class="row align-items-center">
              <div class="col-md-7">
                <div class="d-flex align-items-start">
                  <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                    <i class="fas fa-ticket-alt fa-lg"></i>
                  </div>
                  <div class="ms-3">
                    <h5 class="mb-1">{{ $voucher->ten }}</h5>
                    <p class="text-muted small mb-1">{{ $voucher->mo_ta ?? 'Không có mô tả' }}</p>
                    <div class="mt-2">
                      <span class="badge-success me-1">{{ $voucher->moTaGiaTri }}</span>
                      <span class="badge-info"><i class="fas fa-film me-1"></i>Chỉ dành cho VÉ</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-5 text-end">
                @if($trangThai == 'chua_su_dung')
                  @if($conHieuLuc)
                    <span class="badge-success mb-2"><i class="fas fa-check-circle"></i> Có thể sử dụng</span>
                  @else
                    <span class="badge-danger mb-2"><i class="fas fa-times-circle"></i> Đã hết hạn</span>
                  @endif
                @elseif($trangThai == 'da_su_dung')
                  <span class="badge-secondary mb-2"><i class="fas fa-check"></i> Đã sử dụng</span>
                @else
                  <span class="badge-warning mb-2"><i class="fas fa-ban"></i> Đã hủy</span>
                @endif

                <div class="small text-muted mb-2">
                  <div><i class="fas fa-calendar-plus"></i> Đổi: {{ $voucherNguoiDung->ngay_doi->format('d/m/Y H:i') }}</div>
                  <div><i class="fas fa-calendar-times"></i> HSD: {{ $voucherNguoiDung->ngay_han->format('d/m/Y') }} <span class="badge-info ms-1">30 ngày</span></div>
                  @if($conHieuLuc && $trangThai == 'chua_su_dung')
                    @php $ngayConLai = now()->diffInDays($voucherNguoiDung->ngay_han, false); @endphp
                    @if($ngayConLai <= 7 && $ngayConLai > 0)
                      <div class="text-warning fw-bold"><i class="fas fa-exclamation-triangle"></i> Còn {{ $ngayConLai }} ngày</div>
                    @endif
                  @endif
                </div>

                @if($conHieuLuc && $trangThai == 'chua_su_dung')
                  <div class="mt-2">
                    <small class="text-muted">Mã voucher:</small>
                    <div class="input-group input-group-sm">
                      <input type="text" class="form-control text-center fw-bold"
                             value="{{ $voucherNguoiDung->ma_code ?? 'VC' . str_pad($voucherNguoiDung->id,6,'0',STR_PAD_LEFT) }}"
                             readonly id="code-{{ $voucherNguoiDung->id }}">
                      <button class="btn btn-outline-secondary" type="button" onclick="copyCode({{ $voucherNguoiDung->id }})">
                        <i class="fas fa-copy"></i>
                      </button>
                    </div>
                    <small class="text-info">Sử dụng mã này tại quầy để nhận ưu đãi</small>
                  </div>
                @endif
              </div>
            </div>
          </div>
        @endforeach

        <div class="d-flex justify-content-center mt-4">{{ $vouchers->links() }}</div>
      @endif

      <div class="card-account mt-4">
        <div class="card-header"><i class="fas fa-question-circle text-info"></i> Hướng dẫn sử dụng voucher vé phim</div>
        <div class="card-body">
          <ul class="mb-0">
            <li>Voucher được lưu sau khi bạn đổi điểm thành công.</li>
            <li><strong class="text-primary">Hiệu lực 30 ngày</strong> kể từ ngày đổi.</li>
            <li>Mỗi voucher có mã riêng, bạn có thể sao chép để sử dụng.</li>
            <li>Voucher <strong class="text-danger">CHỈ ÁP DỤNG CHO VÉ PHIM</strong>, không áp dụng cho bắp nước.</li>
            <li>Xuất trình mã voucher khi đặt vé online hoặc tại quầy.</li>
            <li>Voucher hết hạn hoặc đã dùng không thể sử dụng lại.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function copyCode(id) {
  const input = document.getElementById('code-' + id);
  input.select();
  document.execCommand('copy');
  alert('🎟️ Đã sao chép mã voucher!');
}
</script>
@endsection
