# 🎯 Hướng dẫn Quản lý Điểm và Tài khoản - Admin Panel

## ✨ Tổng quan chức năng

Admin có thể quản lý toàn bộ người dùng và điểm tích lũy của hệ thống GoCinema, bao gồm:

### 1. **Quản lý Người dùng** (`/admin/nguoi-dung`)
- ✅ Xem danh sách tất cả người dùng
- ✅ Tìm kiếm và lọc (theo tên, email, vai trò, trạng thái)
- ✅ Thêm người dùng mới
- ✅ Sửa thông tin người dùng
- ✅ Xem chi tiết và lịch sử điểm của từng người
- ✅ Khóa/Mở khóa tài khoản
- ✅ Xóa người dùng

### 2. **Quản lý Điểm tích lũy** (`/admin/diem-tich-luy`)
- ✅ Xem tất cả lịch sử giao dịch điểm
- ✅ Thêm điểm thủ công cho người dùng
- ✅ Trừ điểm từ người dùng
- ✅ Xem thống kê tổng quan
- ✅ Xem top người dùng có nhiều điểm nhất
- ✅ Thống kê theo tháng
- ✅ Xóa giao dịch

---

## 📋 Chi tiết các tính năng

### 1. Quản lý Người dùng

#### **Trang danh sách** (`GET /admin/nguoi-dung`)

**Chức năng:**
- Hiển thị bảng danh sách tất cả người dùng
- Tìm kiếm theo: tên, email, số điện thoại
- Lọc theo: vai trò, trạng thái (hoạt động/khóa)
- Sắp xếp theo: ngày tạo, điểm, tên
- Phân trang 20 người/trang

**Thông tin hiển thị:**
- ID, Họ tên, Email, SĐT
- Vai trò (Admin, Quản lý, Nhân viên, Khách hàng)
- Điểm tích lũy hiện tại
- Trạng thái (Hoạt động/Khóa)
- Ngày tạo tài khoản

**Thao tác:**
- 👁️ **Xem**: Chi tiết người dùng và lịch sử điểm
- ✏️ **Sửa**: Chỉnh sửa thông tin
- 🔒 **Khóa/Mở**: Thay đổi trạng thái tài khoản
- 🗑️ **Xóa**: Xóa người dùng (có confirm)

**Bộ lọc:**
```
- Tìm kiếm: [Nhập tên, email, SĐT...]
- Vai trò: [Tất cả | Admin | Quản lý | Nhân viên | Khách hàng]
- Trạng thái: [Tất cả | Hoạt động | Khóa]
- Sắp xếp: [Ngày tạo | Điểm | Tên]
```

---

#### **Thêm người dùng** (`GET /admin/nguoi-dung/create`)

**Form input:**
- **Họ tên** (*) - String, max 255
- **Email** (*) - Email format, unique
- **Mật khẩu** (*) - Min 6 ký tự
- **Số điện thoại** - String, max 15
- **Vai trò** (*) - Select dropdown
- **Điểm tích lũy** - Integer, min 0 (mặc định 0)
- **Trạng thái** - Checkbox (mặc định hoạt động)

**Validation:**
- Email phải unique trong hệ thống
- Mật khẩu tối thiểu 6 ký tự
- Vai trò bắt buộc chọn

**Sau khi lưu:**
- Redirect về danh sách người dùng
- Thông báo "Tạo người dùng thành công!"

---

#### **Chi tiết người dùng** (`GET /admin/nguoi-dung/{id}`)

**Hiển thị:**

**Card 1: Thông tin cơ bản**
- Avatar icon
- Họ tên, Email
- Badge trạng thái
- ID, Vai trò, SĐT, Ngày tạo

**Card 2: Thống kê điểm**
- Điểm hiện tại (lớn, nổi bật)
- Tổng tích lũy (+)
- Tổng sử dụng (-)
- Button "Thêm/Trừ điểm"

**Card 3: Lịch sử giao dịch điểm**
- Bảng 20 giao dịch/trang
- Cột: #, Thời gian, Loại, Điểm, Mô tả
- Màu xanh (+) / đỏ (-)
- Phân trang

---

#### **Sửa người dùng** (`GET /admin/nguoi-dung/{id}/edit`)

**Form input:**
- Họ tên (*)
- Email (*) - Unique trừ chính nó
- Mật khẩu mới - Để trống nếu không đổi
- Số điện thoại
- Vai trò (*)
- **Điểm tích lũy** - Disabled, chỉ xem
  - Link sang trang Thêm/Trừ điểm
- Trạng thái - Switch

**Validation:**
- Email unique (trừ chính nó)
- Mật khẩu min 6 (nếu có nhập)

**Lưu ý:**
- Không cho đổi điểm trực tiếp
- Phải dùng chức năng "Thêm/Trừ điểm" để có lịch sử

---

#### **Khóa/Mở khóa** (`POST /admin/nguoi-dung/{id}/toggle-status`)

**Chức năng:**
- Toggle trạng thái tài khoản
- Hoạt động ↔ Khóa
- Redirect về trang trước
- Thông báo "Cập nhật trạng thái thành công!"

---

#### **Xóa người dùng** (`DELETE /admin/nguoi-dung/{id}`)

**Bảo vệ:**
- Không cho xóa tài khoản đang đăng nhập
- Có confirm trước khi xóa

**Sau khi xóa:**
- Xóa luôn lịch sử điểm (cascade)
- Redirect về danh sách
- Thông báo "Xóa người dùng thành công!"

---

### 2. Quản lý Điểm tích lũy

#### **Trang danh sách** (`GET /admin/diem-tich-luy`)

**Thống kê tổng quan (4 cards):**
- 👥 **Tổng người dùng**: Số người đang có trong hệ thống
- ⬆️ **Tổng tích lũy**: Tổng điểm đã thêm vào hệ thống
- ⬇️ **Tổng sử dụng**: Tổng điểm đã dùng
- ⭐ **Tổng điểm hiện tại**: Tổng điểm của tất cả user

**Bộ lọc:**
```
- Tìm kiếm: [Tên hoặc email người dùng]
- Loại: [Tất cả | Tích lũy | Sử dụng]
- Từ ngày: [Date picker]
- Đến ngày: [Date picker]
```

**Bảng lịch sử:**
- Cột: ID, Người dùng, Thời gian, Loại, Điểm, Mô tả, Thao tác
- Click vào tên người dùng → Chi tiết người đó
- Badge màu: Xanh (Tích lũy) / Đỏ (Sử dụng)
- Button xóa giao dịch

---

#### **Thêm/Trừ điểm** (`GET /admin/diem-tich-luy/create`)

**Form đẹp với preview realtime:**

**Bước 1: Chọn người dùng**
- Dropdown select với thông tin: Tên (Email) - Số điểm
- Sau khi chọn → Hiển thị điểm hiện tại

**Bước 2: Chọn loại giao dịch**
- 2 button toggle:
  - ✅ **Thêm điểm** (màu xanh)
  - ❌ **Trừ điểm** (màu đỏ)

**Bước 3: Nhập số điểm**
- Input number, min 1
- Realtime preview:
  ```
  Xem trước:
  [Thêm/Trừ] [X] điểm
  Điểm sau giao dịch: [Y] điểm
  ```

**Bước 4: Nhập mô tả**
- Textarea, required
- Placeholder: "Lý do thêm/trừ điểm"

**Validation:**
- Người dùng: required
- Điểm: required, min 1
- Loại: required
- Mô tả: required, max 255

**Xử lý:**
- Gọi `$nguoiDung->themDiem()` hoặc `truDiem()`
- Tự động ghi lịch sử với suffix "(Admin)"
- Redirect về danh sách
- Thông báo thành công

**Bảo vệ:**
- Khi trừ điểm: Kiểm tra đủ điểm không
- Nếu không đủ → Thông báo lỗi, giữ form

---

#### **Thống kê** (`GET /admin/diem-tich-luy/statistics`)

**1. Top 10 người dùng có nhiều điểm nhất**

Bảng hiển thị:
- **Hạng 1**: 🏆 Badge vàng
- **Hạng 2**: 🥈 Badge bạc
- **Hạng 3**: 🥉 Badge đồng
- Hạng 4-10: Badge xám

Thông tin:
- Họ tên (link đến chi tiết)
- Email
- Vai trò
- Số điểm (lớn, nổi bật với icon ⭐)

---

**2. Thống kê theo tháng (năm hiện tại)**

Bảng 12 tháng:
```
| Tháng | Tích lũy (+) | Sử dụng (-) | Chênh lệch |
|-------|--------------|-------------|------------|
| T1    | +1,000       | -500        | +500       |
| T2    | +2,000       | -800        | +1,200     |
| ...   | ...          | ...         | ...        |
|-------|--------------|-------------|------------|
| Tổng  | +50,000      | -30,000     | +20,000    |
```

Màu sắc:
- Xanh: Tích lũy (+)
- Đỏ: Sử dụng (-)
- Chênh lệch: Xanh nếu dương, Đỏ nếu âm

---

## 🛠 Routes API

```php
// Quản lý người dùng
GET     /admin/nguoi-dung                    → index()      [Danh sách]
GET     /admin/nguoi-dung/create             → create()     [Form thêm]
POST    /admin/nguoi-dung                    → store()      [Lưu mới]
GET     /admin/nguoi-dung/{id}               → show()       [Chi tiết]
GET     /admin/nguoi-dung/{id}/edit          → edit()       [Form sửa]
PUT     /admin/nguoi-dung/{id}               → update()     [Cập nhật]
DELETE  /admin/nguoi-dung/{id}               → destroy()    [Xóa]
POST    /admin/nguoi-dung/{id}/toggle-status → toggleStatus() [Đổi trạng thái]

// Quản lý điểm
GET     /admin/diem-tich-luy                 → index()      [Danh sách giao dịch]
GET     /admin/diem-tich-luy/create          → create()     [Form thêm/trừ]
POST    /admin/diem-tich-luy                 → store()      [Xử lý thêm/trừ]
GET     /admin/diem-tich-luy/statistics      → statistics() [Thống kê]
GET     /admin/diem-tich-luy/{nguoiDungId}   → show()       [Chi tiết user]
DELETE  /admin/diem-tich-luy/{id}            → destroy()    [Xóa giao dịch]
```

---

## 🔐 Phân quyền

**Middleware:**
```php
Route::middleware(['auth', 'role:quan_ly'])
```

**Yêu cầu:**
- Phải đăng nhập
- Vai trò: `quan_ly` (Admin/Quản lý)

**Bảo vệ:**
- Không cho xóa tài khoản đang đăng nhập
- Không cho trừ điểm nếu không đủ
- Validate đầy đủ input

---

## 💾 Database

### Bảng `nguoi_dung`
```sql
- id
- ho_ten
- email (unique)
- mat_khau (hashed)
- so_dien_thoai
- vai_tro_id (FK → vai_tro.id)
- diem_tich_luy (default 0)
- trang_thai (1: active, 0: locked)
- created_at
- updated_at
```

### Bảng `lich_su_diem`
```sql
- id
- nguoi_dung_id (FK → nguoi_dung.id, cascade)
- diem (số điểm +/-)
- hanh_dong (enum: 'tich_luy', 'su_dung')
- mo_ta (mô tả giao dịch)
- created_at
- updated_at
```

---

## 🎨 UI/UX Features

### Bootstrap 5 Components
- ✅ Cards với header màu
- ✅ Tables responsive
- ✅ Badges (success, danger, info, warning)
- ✅ Buttons groups
- ✅ Form validation
- ✅ Alerts dismissible
- ✅ Pagination links

### Font Awesome Icons
- 👥 fa-users: Danh sách user
- ⭐ fa-star: Điểm
- 📈 fa-arrow-up: Tích lũy
- 📉 fa-arrow-down: Sử dụng
- 🏆 fa-trophy: Top users
- 📊 fa-chart-bar: Thống kê
- 👁️ fa-eye: Xem
- ✏️ fa-edit: Sửa
- 🗑️ fa-trash: Xóa
- 🔒 fa-lock: Khóa

### Interactive Elements
- **Realtime preview** khi thêm/trừ điểm
- **Toggle buttons** cho loại giao dịch
- **Confirm dialog** trước khi xóa
- **Search & Filter** với auto-submit option
- **Pagination** với page numbers
- **Tooltips** trên các button actions

---

## 🧪 Test Cases

### Test 1: Thêm người dùng mới
1. Đăng nhập với admin
2. Vào `/admin/nguoi-dung`
3. Click "Thêm người dùng"
4. Điền form:
   - Họ tên: "Nguyễn Văn Test"
   - Email: "test@example.com"
   - Mật khẩu: "123456"
   - Vai trò: Khách hàng
   - Điểm: 100
5. Click "Lưu"
6. ✅ Thông báo thành công
7. ✅ Xuất hiện trong danh sách

### Test 2: Thêm điểm cho user
1. Vào `/admin/diem-tich-luy/create`
2. Chọn user: "Nguyễn Văn Test"
3. ✅ Hiển thị "Điểm hiện tại: 100"
4. Chọn "Thêm điểm"
5. Nhập: 50 điểm
6. ✅ Preview: "Thêm 50 điểm, Sau: 150"
7. Mô tả: "Thưởng tích cực"
8. Click "Xác nhận"
9. ✅ Thông báo thành công
10. Kiểm tra user → Điểm = 150

### Test 3: Trừ điểm (đủ điểm)
1. Vào `/admin/diem-tích-luy/create`
2. Chọn user: "Nguyễn Văn Test" (150 điểm)
3. Chọn "Trừ điểm"
4. Nhập: 30 điểm
5. ✅ Preview: "Trừ 30 điểm, Sau: 120"
6. Mô tả: "Đổi quà"
7. Click "Xác nhận"
8. ✅ Thành công, điểm = 120

### Test 4: Trừ điểm (không đủ)
1. Chọn user có 50 điểm
2. Chọn "Trừ điểm"
3. Nhập: 100 điểm
4. Click "Xác nhận"
5. ✅ Thông báo lỗi: "Không đủ điểm..."
6. ✅ Form giữ nguyên dữ liệu

### Test 5: Khóa tài khoản
1. Vào danh sách người dùng
2. Click icon 🔒 của user nào đó
3. ✅ Badge chuyển từ "Hoạt động" → "Khóa"
4. ✅ Icon chuyển từ 🔒 → 🔓
5. Click lại
6. ✅ Mở khóa thành công

### Test 6: Xóa người dùng
1. Click icon 🗑️
2. ✅ Confirm dialog xuất hiện
3. Click OK
4. ✅ User bị xóa
5. ✅ Lịch sử điểm bị xóa luôn

### Test 7: Xem thống kê
1. Vào `/admin/diem-tich-luy/statistics`
2. ✅ Hiển thị Top 10 users
3. ✅ Hiển thị thống kê 12 tháng
4. ✅ Tính tổng đúng

---

## 📦 Files đã tạo

### Controllers (2 files)
```
app/Http/Controllers/Admin/
├── NguoiDungController.php      (200 lines)
└── DiemTichLuyController.php    (160 lines)
```

### Views (8 files)
```
resources/views/admin/
├── nguoi-dung/
│   ├── index.blade.php          (Danh sách)
│   ├── create.blade.php         (Form thêm)
│   ├── show.blade.php           (Chi tiết)
│   └── edit.blade.php           (Form sửa)
└── diem-tich-luy/
    ├── index.blade.php          (Danh sách giao dịch)
    ├── create.blade.php         (Form thêm/trừ điểm)
    └── statistics.blade.php     (Thống kê)
```

### Routes (1 file updated)
```
routes/web.php
- Thêm 13 routes mới
```

---

## ✅ Tổng kết

**Đã hoàn thành:**
- ✅ 2 Controllers với đầy đủ methods
- ✅ 7 Views đẹp, responsive
- ✅ 13 Routes với middleware
- ✅ CRUD đầy đủ cho người dùng
- ✅ Thêm/Trừ điểm với validation
- ✅ Thống kê tổng quan và chi tiết
- ✅ Tìm kiếm, lọc, sắp xếp
- ✅ Phân trang
- ✅ Realtime preview
- ✅ Form validation
- ✅ Thông báo success/error
- ✅ Confirm dialogs
- ✅ Responsive UI

**Tính năng nổi bật:**
- 🎨 UI đẹp với Bootstrap 5
- ⚡ Realtime preview khi thêm/trừ điểm
- 📊 Thống kê chi tiết theo tháng
- 🏆 Top 10 users ranking
- 🔍 Search & Filter mạnh mẽ
- 🛡️ Validation đầy đủ
- 💾 Lịch sử giao dịch đầy đủ

---

🎉 **Hệ thống quản lý điểm và tài khoản trong Admin đã hoàn thành!**

Truy cập: 
- Người dùng: http://localhost:8000/admin/nguoi-dung
- Điểm tích lũy: http://localhost:8000/admin/diem-tich-luy
