@extends('client.layouts.app')

@section('title', 'Lịch sử điểm')

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
  .container-account{ padding:40px 20px; }

  /* SIDEBAR */
  .account-sidebar {
    background:var(--card); border-radius:var(--radius); border:1px solid var(--border);
  }
  .account-sidebar .profile-top {
    padding:28px 20px; text-align:center; border-bottom:1px solid var(--border);
  }
  .account-sidebar .avatar {
    width:88px; height:88px; border-radius:50%; background:linear-gradient(180deg,#0f1724,#111827);
    border:2px solid rgba(255,255,255,0.03); box-shadow:0 6px 18px rgba(2,6,23,0.6);
    display:inline-flex; align-items:center; justify-content:center; font-size:42px; color:var(--primary);
  }
  .account-sidebar h5 { margin-top:12px; margin-bottom:4px; font-weight:600; color:var(--text); }
  .account-sidebar p { margin:0; color:var(--muted); font-size:0.9rem; }
  .account-sidebar .points {
    display:inline-block; margin-top:12px; padding:6px 12px; border-radius:999px;
    background:rgba(99,102,241,0.12); color:var(--primary); font-weight:600; border:1px solid rgba(99,102,241,0.08);
  }
  .account-sidebar .list-group { padding:12px 8px; }
  .account-sidebar .list-group a {
    display:flex; align-items:center; gap:12px; padding:12px 18px; margin:6px 8px;
    border-radius:10px; color:var(--muted); font-weight:600; text-decoration:none; border:1px solid transparent;
  }
  .account-sidebar .list-group a i { width:28px; text-align:center; }
  .account-sidebar .list-group a:hover { background:#0f1a2b; color:var(--text); }
  .account-sidebar .list-group a.active { background:var(--primary); color:#fff; box-shadow:0 6px 20px rgba(99,102,241,0.12); }

  /* MAIN CONTENT */
  .card-account { background:var(--card); border-radius:var(--radius); border:1px solid var(--border); color:var(--text); }
  .card-account .card-body { padding:24px; }

  .badge-success { background:#064e3b; color:#86efac; padding:6px 10px; border-radius:999px; font-weight:700 }
  .badge-danger { background:#4c0519; color:#fca5a5; padding:6px 10px; border-radius:999px; font-weight:700 }
  .text-soft { color:#cbd5e1!important; opacity:.9 }
  .text-soft i { color:var(--primary); opacity:.8 }

  table { color:var(--text); }
  thead { background:rgba(255,255,255,0.05); }
  tbody tr:hover { background:rgba(255,255,255,0.04); }

  .pagination .page-link {
    background:var(--card); border:1px solid var(--border); color:var(--text);
  }
  .pagination .active .page-link {
    background:var(--primary); border-color:var(--primary);
  }

  @media(max-width:991px){ .account-sidebar{ margin-bottom:18px } }
</style>

<div class="container container-account">
  <div class="row g-4">

    {{-- SIDEBAR --}}
    <div class="col-lg-3 col-md-4">
      <aside class="account-sidebar">
        <div class="profile-top">
          <div class="avatar"><i class="fas fa-user"></i></div>
          <h5>{{ $user->ho_ten }}</h5>
          <p>{{ $user->email }}</p>
          <div class="points"><i class="fas fa-star"></i> {{ number_format($user->diem) }} điểm</div>
        </div>

        <div class="list-group">
          <a href="{{ route('account.index') }}"><i class="fas fa-user"></i> Thông tin tài khoản</a>
          <a href="{{ route('account.rewards') }}"><i class="fas fa-gift"></i> Đổi điểm thưởng</a>
          <a href="{{ route('account.my-vouchers') }}"><i class="fas fa-ticket-alt"></i> Voucher của tôi</a>
          <a href="{{ route('account.point-history') }}" class="active"><i class="fas fa-history"></i> Lịch sử điểm</a>
        </div>
      </aside>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="col-lg-9 col-md-8">
      <div class="card-account mb-4">
        <div class="card-body">
          <h4 class="mb-4"><i class="fas fa-history text-primary me-2"></i>Lịch sử giao dịch điểm</h4>

          @if($lichSuDiem->isEmpty())
            <div class="text-center py-5">
              <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
              <p class="text-soft">Chưa có lịch sử giao dịch điểm</p>
            </div>
          @else
            {{-- THỐNG KÊ --}}
            <div class="row mb-4">
              <div class="col-md-6">
                <div class="card-account" style="background:#064e3b;">
                  <div class="card-body text-center">
                    <h6 class="text-soft">Tổng điểm tích lũy</h6>
                    <h3 class="text-success fw-bold">
                      <i class="fas fa-plus-circle me-2"></i>
                      {{ number_format($lichSuDiem->where('hanh_dong','tich_luy')->sum('diem')) }}
                    </h3>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card-account" style="background:#4c0519;">
                  <div class="card-body text-center">
                    <h6 class="text-soft">Tổng điểm đã sử dụng</h6>
                    <h3 class="text-danger fw-bold">
                      <i class="fas fa-minus-circle me-2"></i>
                      {{ number_format($lichSuDiem->where('hanh_dong','su_dung')->sum('diem')) }}
                    </h3>
                  </div>
                </div>
              </div>
            </div>

            {{-- BẢNG LỊCH SỬ --}}
            <div class="table-responsive">
              <table class="table align-middle">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Thời gian</th>
                    <th>Loại giao dịch</th>
                    <th>Điểm</th>
                    <th>Mô tả</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($lichSuDiem as $index => $ls)
                    <tr>
                      <td>{{ $lichSuDiem->firstItem() + $index }}</td>
                      <td class="text-soft"><i class="fas fa-clock me-1"></i>{{ $ls->created_at->format('d/m/Y H:i') }}</td>
                      <td>
                        @if($ls->hanh_dong == 'tich_luy')
                          <span class="badge-success"><i class="fas fa-plus me-1"></i>Tích lũy</span>
                        @else
                          <span class="badge-danger"><i class="fas fa-minus me-1"></i>Sử dụng</span>
                        @endif
                      </td>
                      <td>
                        @if($ls->hanh_dong == 'tich_luy')
                          <strong class="text-success"><i class="fas fa-arrow-up me-1"></i>+{{ number_format($ls->diem) }}</strong>
                        @else
                          <strong class="text-danger"><i class="fas fa-arrow-down me-1"></i>-{{ number_format($ls->diem) }}</strong>
                        @endif
                      </td>
                      <td>{{ $ls->mo_ta ?: 'N/A' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            {{-- PAGINATION --}}
            <div class="d-flex justify-content-center mt-4">
              {{ $lichSuDiem->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
