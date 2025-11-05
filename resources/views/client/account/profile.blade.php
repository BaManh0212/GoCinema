@extends('client.layouts.app')

@section('title', 'Tài khoản của tôi')

@section('content')
<style>
  /* ===== GLOBAL ===== */
  :root{
    --bg: #0b1220;        /* main background */
    --card: #111827;      /* card background */
    --muted: #9ca3af;     /* muted text */
    --text: #e6eef8;      /* primary text */
    --primary: #6366f1;   /* primary action */
    --accent: #f59e0b;    /* accent (small use) */
    --border: rgba(255,255,255,0.04);
    --radius: 12px;
  }

  body {
    background: linear-gradient(180deg,var(--bg) 0%, #07101a 100%);
    color: var(--text);
    font-family: 'Inter', 'Poppins', sans-serif;
  }

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

  .account-sidebar .profile-top .avatar{
    width:88px; height:88px; border-radius:50%;
    display:inline-flex; align-items:center; justify-content:center;
    background: linear-gradient(180deg,#0f1724,#111827);
    border: 2px solid rgba(255,255,255,0.03);
    box-shadow: 0 6px 18px rgba(2,6,23,0.6);
    font-size:42px; color:var(--primary);
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

  /* ===== MAIN CARDS ===== */
  .card-account{ background:var(--card); border-radius:var(--radius); border:1px solid var(--border); }
  .card-account .card-header{
    padding:14px 20px; font-weight:700; font-size:1.02rem; color:var(--text); border-bottom:1px solid var(--border);
    display:flex; align-items:center; gap:10px;
  }
  .card-account .card-body{ padding:20px; }

  .form-label{ color:var(--muted); font-weight:600; }
  .form-control{
    background:#0b1220; border:1px solid rgba(255,255,255,0.04); color:var(--text); border-radius:8px; padding:10px 12px;
  }
  .form-control:focus{ box-shadow:0 6px 18px rgba(99,102,241,0.08); border-color:var(--primary); outline:none; }
  .form-control[disabled]{ color:var(--muted); background:transparent; }

  .btn-primary{
    background:var(--primary); border:none; color:#fff; padding:10px 18px; border-radius:8px; font-weight:700;
    box-shadow: 0 6px 18px rgba(99,102,241,0.12);
  }
  .btn-accent{ background:var(--accent); border:none; color:#fff; padding:10px 18px; border-radius:8px; font-weight:700 }

  /* table */
  .table thead th{ color:var(--muted); border-bottom:1px solid var(--border); font-weight:700 }
  .table tbody td{ color:var(--text); vertical-align:middle }
  .badge-success{ background:#064e3b; color:#86efac; padding:6px 10px; border-radius:999px; font-weight:700 }
  .badge-danger{ background:#4c0519; color:#fca5a5; padding:6px 10px; border-radius:999px; font-weight:700 }

  /* responsive tweaks */
  @media(max-width:991px){
    .account-sidebar{ margin-bottom:18px }
  }

</style>

<div class="container container-account">
  <div class="row g-4">

    {{-- SIDEBAR --}}
    <div class="col-lg-3 col-md-4">
      <aside class="account-sidebar">
        <div class="profile-top">
          <div class="avatar"><i class="fas fa-user"></i></div>
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

    {{-- MAIN --}}
    <div class="col-lg-9 col-md-8">

      {{-- THÔNG TIN CÁ NHÂN --}}
      <div class="card-account mb-4">
        <div class="card-header"><i class="fas fa-user"></i> Thông tin cá nhân</div>
        <div class="card-body">
          <form action="{{ route('account.update-profile') }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Họ và tên</label>
                <input type="text" name="ho_ten" class="form-control" value="{{ $user->ho_ten }}" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Số điện thoại</label>
                <input type="text" name="so_dien_thoai" class="form-control" value="{{ $user->so_dien_thoai }}">
              </div>

              <div class="col-md-6">
                <label class="form-label">Vai trò</label>
                <input type="text" class="form-control" value="{{ $user->vaiTro->ten ?? 'Khách hàng' }}" disabled>
              </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
              <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i> Cập nhật</button>
            </div>
          </form>
        </div>
      </div>

      {{-- ĐỔI MẬT KHẨU (giữ chung trang) --}}
      <div class="card-account mb-4">
        <div class="card-header"><i class="fas fa-key"></i> Đổi mật khẩu</div>
        <div class="card-body">
          <form action="{{ route('account.change-password') }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Mật khẩu hiện tại</label>
                <input type="password" name="current_password" class="form-control" required>
              </div>

              <div class="col-md-4">
                <label class="form-label">Mật khẩu mới</label>
                <input type="password" name="new_password" class="form-control" required>
              </div>

              <div class="col-md-4">
                <label class="form-label">Xác nhận mật khẩu</label>
                <input type="password" name="new_password_confirmation" class="form-control" required>
              </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
              <button type="submit" class="btn btn-accent"><i class="fas fa-lock me-2"></i> Đổi mật khẩu</button>
            </div>
          </form>
        </div>
      </div>

      {{-- LỊCH SỬ ĐIỂM (chỉ 5 bản ghi gần nhất) --}}
      <div class="card-account">
        <div class="card-header"><i class="fas fa-history"></i> Lịch sử điểm gần đây</div>
        <div class="card-body">
          @if($lichSuDiem->isEmpty())
            <p class="text-muted text-center py-4 mb-0">Chưa có lịch sử giao dịch điểm.</p>
          @else
            <div class="table-responsive">
              <table class="table table-borderless align-middle mb-0">
                <thead>
                  <tr>
                    <th>⏰ Thời gian</th>
                    <th>📋 Loại</th>
                    <th>⭐ Điểm</th>
                    <th>📝 Mô tả</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($lichSuDiem->take(5) as $ls)
                    <tr>
                      <td>{{ $ls->created_at->format('d/m/Y H:i') }}</td>
                      <td>
                        @if($ls->hanh_dong == 'tich_luy')
                          <span class="badge-success">Tích lũy</span>
                        @else
                          <span class="badge-danger">Sử dụng</span>
                        @endif
                      </td>
                      <td class="fw-bold">
                        {{ $ls->hanh_dong == 'tich_luy' ? '+' : '-' }}{{ number_format($ls->diem) }}
                      </td>
                      <td class="text-muted">{{ $ls->mo_ta ?? 'Không có mô tả' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @if($lichSuDiem->count() > 5)
              <div class="mt-3 text-center">
                <a href="{{ route('account.point-history') }}" class="btn btn-primary">Xem toàn bộ lịch sử</a>
              </div>
            @endif
          @endif
        </div>
      </div>

    </div>
  </div>
</div>

@endsection
