# 🎬 GoCinema - Chức năng Quản lý Tài khoản & Đổi Điểm

## ✨ Tính năng đã hoàn thành

### 1. **Quản lý Tài khoản** (`/account`)
- ✅ Xem thông tin cá nhân (họ tên, email, SĐT, vai trò, điểm)
- ✅ Cập nhật thông tin cá nhân
- ✅ Đổi mật khẩu (có validate mật khẩu cũ)
- ✅ Xem 10 giao dịch điểm gần nhất
- ✅ Sidebar menu điều hướng

### 2. **Đổi Điểm Thưởng** (`/account/rewards`)
- ✅ Hiển thị danh sách combo với chi tiết sản phẩm
- ✅ Quy đổi: **1000đ = 1 điểm**
- ✅ Kiểm tra đủ điểm trước khi đổi
- ✅ Đổi điểm lấy combo (trừ điểm tự động)
- ✅ Ghi lịch sử giao dịch
- ✅ Hướng dẫn tích điểm

### 3. **Lịch Sử Điểm** (`/account/point-history`)
- ✅ Thống kê tổng điểm tích lũy và đã sử dụng
- ✅ Bảng chi tiết tất cả giao dịch
- ✅ Phân biệt loại: Tích lũy (+) / Sử dụng (-)
- ✅ Phân trang 20 giao dịch/trang
- ✅ Hiển thị thời gian, điểm, mô tả

## 🗂 Cấu trúc Files

```
app/
├── Http/Controllers/
│   └── AccountController.php          ← Controller chính (6 methods)
│
├── Models/
│   ├── NguoiDung.php                  ← Thêm themDiem(), truDiem(), accessor
│   └── LichSuDiem.php                 ← Model mới cho lịch sử điểm
│
resources/views/account/
├── index.blade.php                    ← Trang quản lý tài khoản
├── rewards.blade.php                  ← Trang đổi điểm
└── point-history.blade.php            ← Lịch sử giao dịch điểm

routes/
└── web.php                            ← Thêm 6 routes mới

database/migrations/
└── 2025_10_03_060345_create_lich_su_diem_table.php

Scripts hỗ trợ:
├── create_roles.php                   ← Tạo 4 vai trò
├── create_demo_accounts.php           ← Tạo 6 tài khoản demo
├── create_sample_combos.php           ← Tạo 5 combo mẫu
└── add_points.php                     ← Thêm điểm cho users
```

## 🚀 Hướng dẫn chạy

### Bước 1: Chuẩn bị dữ liệu

```bash
# 1. Tạo vai trò
php create_roles.php

# 2. Tạo tài khoản demo
php create_demo_accounts.php

# 3. Tạo combo mẫu
php create_sample_combos.php

# 4. Thêm điểm test
php add_points.php
```

### Bước 2: Khởi động server

```bash
# Đảm bảo server Laravel đang chạy
php artisan serve
```

### Bước 3: Truy cập

1. **Đăng nhập**: http://localhost:8000/login
2. **Tài khoản test**:
   - Admin: `admin@gocinema.vn` / `123456` (10,200 điểm)
   - Khách hàng VIP: `khachhang@gocinema.vn` / `123456` (3,160 điểm)
   - User: `user@gocinema.vn` / `123456` (978 điểm)

3. **Vào trang quản lý**: http://localhost:8000/account

## 📊 Database Schema

### Bảng chính

**nguoi_dung** (Người dùng)
- `diem_tich_luy`: Điểm hiện tại của user

**lich_su_diem** (Lịch sử điểm)
- `nguoi_dung_id`: FK → nguoi_dung.id
- `diem`: Số điểm (+/-)
- `hanh_dong`: 'tich_luy' hoặc 'su_dung'
- `mo_ta`: Mô tả giao dịch

**combo** (Combo ưu đãi)
- `ten`: Tên combo
- `gia`: Giá combo (quy đổi ra điểm)
- `mo_ta`: Mô tả

**combo_chi_tiet** (Chi tiết combo)
- `combo_id`: FK → combo.id
- `san_pham_id`: FK → san_pham.id
- `so_luong`: Số lượng sản phẩm

## 🔑 Routes API

```php
// Tất cả routes yêu cầu middleware ['auth']
Route::prefix('account')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])
        ->name('index');
    
    Route::get('/rewards', [AccountController::class, 'rewards'])
        ->name('rewards');
    
    Route::get('/point-history', [AccountController::class, 'pointHistory'])
        ->name('point-history');
    
    Route::put('/update-profile', [AccountController::class, 'updateProfile'])
        ->name('update-profile');
    
    Route::put('/change-password', [AccountController::class, 'changePassword'])
        ->name('change-password');
    
    Route::post('/redeem-combo/{comboId}', [AccountController::class, 'redeemCombo'])
        ->name('redeem-combo');
});
```

## 💡 Sử dụng trong Code

### Thêm điểm cho user
```php
$user = Auth::user();
$user->themDiem(100, 'Thưởng đặt vé phim Avatar');
// Tự động: +100 điểm và ghi lịch sử
```

### Trừ điểm khi đổi combo
```php
$user = Auth::user();
$combo = Combo::find($comboId);
$diemCanThiet = ceil($combo->gia / 1000);

try {
    $user->truDiem($diemCanThiet, "Đổi combo: {$combo->ten}");
    // Thành công
} catch (\Exception $e) {
    // Không đủ điểm
    echo $e->getMessage();
}
```

### Lấy điểm hiện tại
```php
$user = Auth::user();
echo $user->diem; // Accessor tự động
// hoặc
echo $user->diem_tich_luy; // Trực tiếp
```

### Lấy lịch sử điểm
```php
$user = Auth::user();

// 10 giao dịch gần nhất
$lichSu = $user->lichSuDiem()
    ->orderBy('created_at', 'desc')
    ->take(10)
    ->get();

// Chỉ lấy tích lũy
$tichLuy = $user->lichSuDiem()->tichLuy()->get();

// Chỉ lấy sử dụng
$suDung = $user->lichSuDiem()->suDung()->get();
```

## 🎨 UI Features

### Responsive Design
- ✅ Bootstrap 5
- ✅ Font Awesome icons
- ✅ Sidebar menu đẹp
- ✅ Badge hiển thị điểm
- ✅ Alert thông báo
- ✅ Table responsive

### Color Coding
- **Xanh (success)**: Tích lũy điểm (+)
- **Đỏ (danger)**: Sử dụng điểm (-)
- **Xám (secondary)**: Chưa đủ điểm
- **Gradient**: Header trang đổi điểm

### Interactive Elements
- Form validation với error messages
- Confirm dialog khi đổi điểm
- Alert dismissible
- Pagination links
- Hover effects

## 📝 Quy tắc Nghiệp vụ

### Tích điểm
1. **Tỷ lệ**: 1000đ = 1 điểm
2. **Tự động**: Cộng điểm khi thanh toán vé/combo
3. **Không hạn**: Điểm không có thời hạn sử dụng

### Đổi điểm
1. **Kiểm tra**: Phải đủ điểm mới đổi được
2. **Trừ tự động**: Điểm trừ ngay khi confirm
3. **Ghi lịch sử**: Mọi giao dịch đều được lưu
4. **Không hoàn**: Không hoàn điểm sau khi đổi

### Bảo mật
1. **Auth required**: Tất cả routes yêu cầu đăng nhập
2. **User own data**: Chỉ xem được dữ liệu của mình
3. **Password hash**: Mật khẩu được hash bằng bcrypt
4. **CSRF protection**: Form có CSRF token

## 🧪 Test Cases

### Test 1: Xem thông tin tài khoản
1. Đăng nhập với `admin@gocinema.vn`
2. Vào `/account`
3. ✅ Hiển thị đúng thông tin
4. ✅ Hiển thị điểm hiện tại
5. ✅ Hiển thị 10 giao dịch gần nhất

### Test 2: Cập nhật thông tin
1. Vào `/account`
2. Sửa họ tên, email
3. Click "Cập nhật thông tin"
4. ✅ Thông báo thành công
5. ✅ Dữ liệu được lưu

### Test 3: Đổi mật khẩu
1. Vào `/account`
2. Nhập mật khẩu cũ: `123456`
3. Nhập mật khẩu mới: `newpass123`
4. Click "Đổi mật khẩu"
5. ✅ Thông báo thành công
6. Đăng xuất và đăng nhập lại
7. ✅ Đăng nhập được với mật khẩu mới

### Test 4: Đổi điểm (đủ điểm)
1. Đăng nhập với `admin@gocinema.vn` (10,200 điểm)
2. Vào `/account/rewards`
3. Click "Đổi ngay" Combo Sinh Viên (80 điểm)
4. Confirm
5. ✅ Thông báo thành công
6. ✅ Điểm giảm xuống 10,120
7. Vào `/account/point-history`
8. ✅ Có giao dịch mới với -80 điểm

### Test 5: Đổi điểm (không đủ)
1. Đăng nhập với `user@gocinema.vn` (978 điểm)
2. Vào `/account/rewards`
3. ✅ Combo VIP (500 điểm) hiển thị "Đổi ngay"
4. ✅ Các combo khác > 978 điểm bị disable
5. Click combo bị disable
6. ✅ Không thể click

### Test 6: Lịch sử điểm
1. Vào `/account/point-history`
2. ✅ Hiển thị thống kê
3. ✅ Bảng chi tiết đầy đủ
4. ✅ Phân trang hoạt động
5. ✅ Màu sắc phân biệt +/-

## 📦 Combo mẫu đã tạo

| Combo | Giá | Điểm cần | Nội dung |
|-------|-----|----------|----------|
| Combo Tiết Kiệm | 50,000đ | 50 | 1 bắp + 1 nước suối |
| Combo Sinh Viên | 80,000đ | 80 | 1 bắp lớn + 1 coca |
| Combo Couple | 150,000đ | 150 | 2 bắp + 2 coca + 2 hotdog |
| Combo Gia Đình | 250,000đ | 250 | 3 bắp + 3 coca + 3 hotdog |
| Combo VIP | 500,000đ | 500 | 5 bắp + 5 coca + 5 hotdog |

## 🎯 Next Steps (Tùy chọn mở rộng)

- [ ] Thêm hình ảnh cho combo
- [ ] Tích hợp thanh toán tự động cộng điểm
- [ ] Email thông báo khi đổi điểm
- [ ] Xếp hạng thành viên (Bronze, Silver, Gold)
- [ ] Voucher/coupon code
- [ ] Lịch sử đổi combo (quản lý đơn hàng)
- [ ] QR code để nhận hàng
- [ ] Push notification
- [ ] Điểm sinh nhật/sự kiện đặc biệt
- [ ] Giới thiệu bạn bè nhận thưởng

## 📞 Hỗ trợ

Nếu gặp lỗi hoặc cần hỗ trợ:
1. Kiểm tra database đã migrate chưa
2. Kiểm tra server Laravel đang chạy
3. Xem log tại `storage/logs/laravel.log`
4. Đọc file `ACCOUNT_REWARDS_GUIDE.md` để biết chi tiết

## ✅ Tóm tắt

**Files mới tạo**: 7 files
- 1 Model (LichSuDiem)
- 1 Controller (AccountController)
- 3 Views (index, rewards, point-history)
- 2 Documentation (README, GUIDE)

**Files chỉnh sửa**: 2 files
- NguoiDung.php (thêm methods)
- web.php (thêm routes)

**Scripts hỗ trợ**: 4 files
- create_roles.php
- create_demo_accounts.php
- create_sample_combos.php
- add_points.php

**Tổng cộng**: 13 files

---

🎉 **Chức năng quản lý tài khoản và đổi điểm đã hoàn thành!**

Truy cập: http://localhost:8000/account
