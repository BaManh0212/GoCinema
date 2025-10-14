# Hướng dẫn sử dụng Chức năng Quản lý Tài khoản & Đổi Điểm

## 🎯 Tổng quan

Hệ thống quản lý tài khoản và đổi điểm thưởng cho phép người dùng:
- Xem và cập nhật thông tin cá nhân
- Đổi mật khẩu
- Xem điểm tích lũy hiện tại
- Đổi điểm lấy combo ưu đãi
- Xem lịch sử giao dịch điểm

## 📋 Các tính năng chính

### 1. Quản lý thông tin tài khoản
- **URL**: `/account`
- **Chức năng**:
  - Cập nhật họ tên, email, số điện thoại
  - Đổi mật khẩu
  - Xem vai trò và điểm tích lũy
  - Xem 10 giao dịch điểm gần nhất

### 2. Đổi điểm thưởng
- **URL**: `/account/rewards`
- **Chức năng**:
  - Xem danh sách combo có thể đổi
  - Quy đổi: **1000đ = 1 điểm**
  - Đổi điểm lấy combo (nếu đủ điểm)
  - Xem chi tiết từng combo

### 3. Lịch sử điểm
- **URL**: `/account/point-history`
- **Chức năng**:
  - Xem toàn bộ lịch sử giao dịch điểm
  - Phân biệt: Tích lũy (+) / Sử dụng (-)
  - Thống kê tổng điểm tích lũy và đã sử dụng
  - Phân trang 20 giao dịch/trang

## 🚀 Cách sử dụng

### Đăng nhập
1. Truy cập: `http://localhost:8000/login`
2. Đăng nhập bằng tài khoản:
   - **Admin**: `admin@gocinema.vn` / `123456` (10,200 điểm)
   - **Quản lý**: `quanly@gocinema.vn` / `123456` (5,205 điểm)
   - **Nhân viên**: `nhanvien@gocinema.vn` / `123456` (2,165 điểm)
   - **Khách hàng VIP**: `khachhang@gocinema.vn` / `123456` (3,160 điểm)
   - **Khách hàng**: `user@gocinema.vn` / `123456` (978 điểm)

### Xem thông tin tài khoản
1. Sau khi đăng nhập, click vào tên user hoặc truy cập `/account`
2. Sidebar bên trái hiển thị:
   - Avatar
   - Tên và email
   - Số điểm hiện tại
3. Menu:
   - **Thông tin tài khoản**: Xem/sửa thông tin
   - **Đổi điểm thưởng**: Xem combo có thể đổi
   - **Lịch sử điểm**: Xem chi tiết giao dịch

### Cập nhật thông tin cá nhân
1. Vào `/account`
2. Card "Thông tin cá nhân":
   - Sửa họ tên, email, số điện thoại
   - Click **"Cập nhật thông tin"**
3. Thông báo xanh = thành công

### Đổi mật khẩu
1. Vào `/account`
2. Card "Đổi mật khẩu":
   - Nhập mật khẩu hiện tại
   - Nhập mật khẩu mới (tối thiểu 6 ký tự)
   - Xác nhận mật khẩu mới
   - Click **"Đổi mật khẩu"**
3. Kiểm tra: Đăng xuất và đăng nhập lại với mật khẩu mới

### Đổi điểm lấy combo
1. Vào `/account/rewards`
2. Xem danh sách combo:
   - **Combo Tiết Kiệm** (50 điểm)
   - **Combo Sinh Viên** (80 điểm)
   - **Combo Couple** (150 điểm)
   - **Combo Gia Đình** (250 điểm)
   - **Combo VIP** (500 điểm)
3. Combo có màu xanh = đủ điểm để đổi
4. Click **"Đổi ngay"** → Xác nhận
5. Thông báo thành công → Đến quầy nhận hàng

### Xem lịch sử điểm
1. Vào `/account/point-history`
2. Xem thống kê:
   - **Tổng điểm tích lũy** (màu xanh)
   - **Tổng điểm đã sử dụng** (màu đỏ)
3. Bảng lịch sử:
   - Thời gian giao dịch
   - Loại: Tích lũy (+) / Sử dụng (-)
   - Số điểm
   - Mô tả chi tiết
4. Phân trang ở dưới cùng

## 💾 Cấu trúc Database

### Bảng `nguoi_dung`
```sql
- id
- ho_ten
- email
- mat_khau
- so_dien_thoai
- vai_tro_id
- diem_tich_luy (điểm hiện tại)
- trang_thai
- created_at
- updated_at
```

### Bảng `lich_su_diem`
```sql
- id
- nguoi_dung_id (FK → nguoi_dung.id)
- diem (số điểm +/-)
- hanh_dong (enum: 'tich_luy', 'su_dung')
- mo_ta (mô tả giao dịch)
- created_at
- updated_at
```

### Bảng `combo`
```sql
- id
- ten (tên combo)
- gia (giá combo)
- mo_ta (mô tả)
- created_at
- updated_at
- deleted_at
```

### Bảng `combo_chi_tiet`
```sql
- id
- combo_id (FK → combo.id)
- san_pham_id (FK → san_pham.id)
- so_luong (số lượng sản phẩm)
```

## 🛠 Scripts hỗ trợ

### 1. `create_roles.php`
Tạo 4 vai trò trong hệ thống:
```bash
php create_roles.php
```

### 2. `create_demo_accounts.php`
Tạo 6 tài khoản demo với điểm khác nhau:
```bash
php create_demo_accounts.php
```

### 3. `create_sample_combos.php`
Tạo 5 combo mẫu và 4 sản phẩm:
```bash
php create_sample_combos.php
```

### 4. `add_points.php`
Thêm điểm thưởng cho users:
```bash
php add_points.php
```

## 🔑 API Routes

```php
// Quản lý tài khoản (yêu cầu đăng nhập)
GET     /account                        - Trang chính
GET     /account/rewards                - Trang đổi điểm
GET     /account/point-history          - Lịch sử điểm
PUT     /account/update-profile         - Cập nhật thông tin
PUT     /account/change-password        - Đổi mật khẩu
POST    /account/redeem-combo/{id}      - Đổi điểm lấy combo
```

## 📝 Model Methods

### NguoiDung Model

```php
// Thêm điểm
$user->themDiem(100, 'Thưởng đặt vé');

// Trừ điểm
$user->truDiem(50, 'Đổi combo');

// Lấy lịch sử điểm
$user->lichSuDiem()->get();

// Lấy điểm hiện tại
$user->diem; // accessor tự động
```

### LichSuDiem Model

```php
// Lấy lịch sử tích lũy
LichSuDiem::tichLuy()->get();

// Lấy lịch sử sử dụng
LichSuDiem::suDung()->get();

// Lấy theo user
LichSuDiem::where('nguoi_dung_id', $userId)->get();
```

## 🎨 Views

```
resources/views/account/
├── index.blade.php          - Trang quản lý tài khoản
├── rewards.blade.php        - Trang đổi điểm
└── point-history.blade.php  - Lịch sử giao dịch
```

## ⚙️ Controllers

### AccountController

- `index()` - Hiển thị trang tài khoản
- `rewards()` - Hiển thị trang đổi điểm
- `pointHistory()` - Hiển thị lịch sử điểm
- `updateProfile()` - Cập nhật thông tin
- `changePassword()` - Đổi mật khẩu
- `redeemCombo()` - Đổi điểm lấy combo

## 🧪 Test

### 1. Test đăng nhập
```bash
# Mở trình duyệt
http://localhost:8000/login

# Đăng nhập bằng admin@gocinema.vn / 123456
```

### 2. Test xem tài khoản
```bash
# Sau khi đăng nhập
http://localhost:8000/account
```

### 3. Test đổi điểm
```bash
# Vào trang đổi điểm
http://localhost:8000/account/rewards

# Click "Đổi ngay" combo có đủ điểm
```

### 4. Test lịch sử
```bash
# Xem lịch sử sau khi đổi điểm
http://localhost:8000/account/point-history
```

## 📊 Quy tắc tích điểm

- **Tích lũy**: 1000đ chi tiêu = 1 điểm
- **Sử dụng**: 1 điểm = 1000đ giảm giá
- **Không giới hạn**: Điểm không có hạn sử dụng
- **Tự động**: Điểm được cộng tự động khi đặt vé/mua combo

## ✅ Checklist hoàn thành

- [x] Model LichSuDiem với quan hệ
- [x] Cập nhật NguoiDung model (themDiem, truDiem)
- [x] AccountController với 6 methods
- [x] 3 views đẹp với Bootstrap 5
- [x] Routes đầy đủ với middleware auth
- [x] Script tạo combo mẫu
- [x] Script thêm điểm test
- [x] Accessor $user->diem
- [x] Validation form đầy đủ
- [x] Thông báo success/error
- [x] Phân trang lịch sử điểm

## 🎉 Hoàn tất!

Hệ thống quản lý tài khoản và đổi điểm đã sẵn sàng sử dụng!

**Truy cập**: http://localhost:8000/account (sau khi đăng nhập)
