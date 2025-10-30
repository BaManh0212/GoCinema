# 🧪 HƯỚNG DẪN TEST CHỨC NĂNG VOUCHER

## ✅ Đã Clear Cache
```bash
php artisan route:clear    ✓
php artisan config:clear   ✓
php artisan view:clear     ✓
```

---

## 📋 DANH SÁCH ROUTES VOUCHER

### Admin Routes (Đã kiểm tra - TẤT CẢ ĐÃ CÓ ✅)

```
GET     /admin/voucher                        → Danh sách voucher
GET     /admin/voucher/create                 → Form tạo voucher MỚI
POST    /admin/voucher                        → Lưu voucher mới
GET     /admin/voucher/{id}                   → Xem chi tiết
GET     /admin/voucher/{id}/edit              → Form SỬA voucher
PUT     /admin/voucher/{id}                   → Cập nhật voucher
DELETE  /admin/voucher/{id}                   → Xóa voucher
POST    /admin/voucher/{id}/toggle-status     → Bật/tắt voucher
GET     /admin/voucher-statistics             → Thống kê voucher
```

### User Routes
```
GET     /account/rewards                      → Trang đổi điểm
GET     /account/my-vouchers                  → Voucher đã đổi
POST    /account/redeem-voucher/{id}          → Đổi điểm lấy voucher
```

---

## 🔍 CÁCH TEST TỪNG CHỨC NĂNG

### 1️⃣ TEST DANH SÁCH VOUCHER

**Bước 1**: Mở trình duyệt
```
URL: http://localhost/admin/voucher
```

**Kết quả mong đợi**:
- ✅ Thấy bảng danh sách voucher
- ✅ Có 8 voucher từ seeder
- ✅ Các cột: Tiêu đề, Loại, Trạng thái, Điểm cần, Giá trị, HSD, Kích hoạt, Thao tác
- ✅ Có nút "Thêm voucher mới"
- ✅ Có form tìm kiếm/lọc

**Nếu lỗi**:
- ❌ "404 Not Found" → Kiểm tra route trong web.php
- ❌ "Class not found" → Check namespace VoucherController
- ❌ "View not found" → Check file admin/voucher/index.blade.php tồn tại

---

### 2️⃣ TEST TẠO VOUCHER MỚI

**Bước 1**: Click nút "Thêm voucher mới"
```
URL sẽ chuyển đến: http://localhost/admin/voucher/create
```

**Kết quả mong đợi**:
- ✅ Hiển thị form với các trường:
  - Tên voucher (text)
  - Điểm cần (number)
  - Loại voucher (select: Phần trăm/Số tiền)
  - Giá trị (number)
  - Giá trị đơn hàng tối thiểu (number)
  - Áp dụng cho (select: Vé/Sản phẩm/Tất cả)
  - Số lần sử dụng (number)
  - Ngày bắt đầu (date)
  - Ngày kết thúc (date)
  - Checkbox "Kích hoạt"
- ✅ Có nút "Lưu voucher" và "Hủy"

**Nếu lỗi**:
- ❌ "404 Not Found" → Route 'admin.voucher.create' chưa có
- ❌ "Method not found" → Check method create() trong VoucherController
- ❌ "View not found" → Check file create.blade.php

**Bước 2**: Điền form với dữ liệu test
```
Tên voucher: "Test - Giảm 100.000đ"
Điểm cần: 1000
Loại: Số tiền
Giá trị: 100000
Giá trị đơn hàng tối thiểu: 500000
Áp dụng cho: Tất cả
Số lần sử dụng: 1
Ngày bắt đầu: Hôm nay
Ngày kết thúc: 30 ngày sau
Kích hoạt: ✓ Check
```

**Bước 3**: Click "Lưu voucher"
```
Form sẽ POST đến: http://localhost/admin/voucher
```

**Kết quả mong đợi**:
- ✅ Redirect về trang danh sách `/admin/voucher`
- ✅ Thấy thông báo "Tạo voucher đổi điểm thành công!"
- ✅ Voucher mới xuất hiện trong bảng

**Nếu lỗi**:
- ❌ "Validation error" → Kiểm tra dữ liệu nhập vào
- ❌ "Column not found" → Check tên cột trong database khớp với code
- ❌ "CSRF token mismatch" → Form thiếu @csrf

---

### 3️⃣ TEST SỬA VOUCHER

**Bước 1**: Trong danh sách, click nút "Sửa" (icon bút) của 1 voucher
```
URL: http://localhost/admin/voucher/{id}/edit
VD: http://localhost/admin/voucher/1/edit
```

**Kết quả mong đợi**:
- ✅ Hiển thị form giống form tạo
- ✅ Các trường đã điền sẵn dữ liệu hiện tại
- ✅ Title: "Sửa voucher đổi điểm"

**Nếu lỗi**:
- ❌ "404 Not Found" → Route 'admin.voucher.edit' chưa có
- ❌ "Model not found" → Voucher với ID đó không tồn tại
- ❌ "View not found" → Check file edit.blade.php

**Bước 2**: Sửa một số thông tin (VD: đổi tên voucher)
```
Tên cũ: "Giảm 50% cho vé phim"
Tên mới: "Giảm 50% cho vé phim - CẬP NHẬT"
```

**Bước 3**: Click "Cập nhật voucher"
```
Form sẽ PUT đến: http://localhost/admin/voucher/{id}
```

**Kết quả mong đợi**:
- ✅ Redirect về `/admin/voucher`
- ✅ Thấy thông báo "Cập nhật voucher thành công!"
- ✅ Tên voucher đã thay đổi trong danh sách

**Nếu lỗi**:
- ❌ "Method not allowed" → Form thiếu @method('PUT')
- ❌ "Validation error" → Dữ liệu không hợp lệ

---

### 4️⃣ TEST BẬT/TẮT VOUCHER (Toggle)

**Bước 1**: Trong danh sách, click công tắc bật/tắt
```
AJAX POST đến: http://localhost/admin/voucher/{id}/toggle-status
```

**Kết quả mong đợi**:
- ✅ Trang reload
- ✅ Trạng thái đổi: "Đang kích hoạt" ⇆ "Vô hiệu"
- ✅ Thấy thông báo "Đã kích hoạt voucher thành công!" hoặc "Đã vô hiệu hóa voucher thành công!"

**Nếu lỗi**:
- ❌ "404 Not Found" → Route toggle-status chưa có
- ❌ Không có gì xảy ra → Check JavaScript trong view

---

### 5️⃣ TEST XÓA VOUCHER

**Bước 1**: Click nút "Xóa" (icon thùng rác) của voucher chưa ai đổi
```
Form DELETE đến: http://localhost/admin/voucher/{id}
```

**Kết quả mong đợi**:
- ✅ Hiện popup confirm "Bạn có chắc muốn xóa?"
- ✅ Click OK → Voucher bị xóa
- ✅ Thấy thông báo "Xóa voucher thành công!"

**Trường hợp đặc biệt**:
- Nếu voucher đã có người đổi:
  - ❌ Không cho xóa
  - ✅ Hiện thông báo "Không thể xóa voucher này vì đã có người dùng đổi!"

---

## 🐛 TROUBLESHOOTING (Khắc phục lỗi)

### Lỗi 1: "Target class [App\Http\Controllers\Admin\VoucherController] does not exist"

**Nguyên nhân**: Laravel không tìm thấy controller

**Cách fix**:
```bash
# 1. Check file tồn tại
ls app/Http/Controllers/Admin/VoucherController.php

# 2. Chạy lại composer autoload
composer dump-autoload

# 3. Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

### Lỗi 2: "View [admin.voucher.create] not found"

**Nguyên nhân**: File view không tồn tại hoặc sai đường dẫn

**Cách fix**:
```bash
# Check file tồn tại
ls resources/views/admin/voucher/create.blade.php

# Nếu không có, tạo lại file
# (Sử dụng code đã cung cấp ở trên)
```

---

### Lỗi 3: "SQLSTATE[42S22]: Column not found"

**Nguyên nhân**: Tên cột trong code không khớp với database

**Cách fix**:
```bash
# 1. Check migration đã chạy chưa
php artisan migrate:status

# 2. Nếu thiếu migration, chạy lại
php artisan migrate

# 3. Check cấu trúc bảng voucher
php artisan tinker
>>> DB::select('DESCRIBE voucher');
```

**Các cột bắt buộc phải có**:
- ✅ id
- ✅ ten
- ✅ loai
- ✅ gia_tri
- ✅ gia_tri_don_hang_toi_thieu
- ✅ ap_dung_cho
- ✅ so_lan_su_dung
- ✅ diem_can ← Đã thêm từ migration mới
- ✅ kich_hoat ← Đã thêm từ migration mới
- ✅ ngay_bat_dau
- ✅ ngay_ket_thuc
- ✅ created_at
- ✅ updated_at

---

### Lỗi 4: Form submit không làm gì cả

**Nguyên nhân**: Có thể thiếu CSRF token hoặc action URL sai

**Cách fix**:
```blade
<!-- Check trong form có @csrf không -->
<form method="POST" action="{{ route('admin.voucher.store') }}">
    @csrf  ← Phải có dòng này
    ...
</form>

<!-- Check route name đúng không -->
{{ route('admin.voucher.store') }}  ← Phải trả về URL đúng
```

---

### Lỗi 5: "403 Forbidden" hoặc "401 Unauthorized"

**Nguyên nhân**: Người dùng chưa đăng nhập hoặc không có quyền admin

**Cách fix**:
```
1. Đăng nhập với tài khoản admin (vai_tro_id = 1)
2. Check middleware trong routes/web.php:
   Route::middleware(['auth', 'role:quan_ly'])
3. Check bảng vai_tro và nguoi_dung
```

---

## 📝 CHECKLIST KIỂM TRA NHANH

### Controller ✅
- [x] File `app/Http/Controllers/Admin/VoucherController.php` tồn tại
- [x] Có đầy đủ 9 methods: index, create, store, show, edit, update, destroy, toggleStatus, statistics
- [x] Namespace đúng: `namespace App\Http\Controllers\Admin;`

### Views ✅
- [x] `resources/views/admin/voucher/index.blade.php` ✓
- [x] `resources/views/admin/voucher/create.blade.php` ✓
- [x] `resources/views/admin/voucher/edit.blade.php` ✓

### Routes ✅
- [x] Import controller: `use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;`
- [x] Resource route: `Route::resource('voucher', AdminVoucherController::class)`
- [x] Toggle route: `Route::post('voucher/{id}/toggle-status', ...)`
- [x] Routes nằm trong middleware admin: `Route::prefix('admin')->middleware(['auth', 'role:quan_ly'])`

### Model ✅
- [x] File `app/Models/Voucher.php` tồn tại
- [x] Có đầy đủ fillable, casts, relationships
- [x] Có các methods hỗ trợ: conHieuLuc(), moTaGiaTri, moTaApDung

### Database ✅
- [x] Migration đã chạy: `2025_10_16_232750_add_diem_can_and_kich_hoat_to_voucher_table.php`
- [x] Bảng voucher có cột `diem_can` và `kich_hoat`
- [x] Đã seed 8 voucher mẫu

---

## 🚀 CÁCH TEST NHANH NHẤT

### Test 1 phút:
```bash
# 1. Clear cache
php artisan route:clear
php artisan config:clear
php artisan view:clear

# 2. Kiểm tra routes
php artisan route:list | findstr "voucher"

# 3. Mở trình duyệt
http://localhost/admin/voucher

# 4. Click "Thêm voucher mới"

# 5. Điền form và submit
```

### Nếu TẤT CẢ đều OK:
✅ Trang danh sách hiển thị
✅ Click "Thêm mới" → Form hiện ra
✅ Submit form → Lưu thành công
✅ Click "Sửa" → Form edit hiện ra
✅ Submit sửa → Cập nhật thành công
✅ Click toggle → Bật/tắt OK
✅ Click xóa → Xóa thành công

### Nếu có lỗi:
❌ Chụp màn hình lỗi
❌ Check console browser (F12)
❌ Check log Laravel: `storage/logs/laravel.log`
❌ Gửi thông tin lỗi để được hỗ trợ

---

## 💡 LƯU Ý QUAN TRỌNG

1. **Phải đăng nhập với tài khoản ADMIN** (vai_tro_id = 1 hoặc tên role = 'quan_ly')

2. **Routes phải nằm trong middleware admin**:
   ```php
   Route::prefix('admin')
       ->middleware(['auth', 'role:quan_ly'])
   ```

3. **Cache có thể gây lỗi**, luôn clear cache khi có thay đổi:
   ```bash
   php artisan route:clear
   php artisan config:clear
   php artisan view:clear
   ```

4. **Kiểm tra URL đúng**:
   - ✓ `/admin/voucher` (có admin prefix)
   - ✗ `/voucher` (sai, thiếu admin)

5. **Form phải có @csrf**:
   ```blade
   <form method="POST">
       @csrf  ← BẮT BUỘC
   </form>
   ```

---

## 📞 CẦN HỖ TRỢ?

Nếu vẫn gặp lỗi, cung cấp thông tin:
1. URL đang truy cập
2. Lỗi hiển thị (screenshot)
3. Log trong `storage/logs/laravel.log`
4. Đã đăng nhập với tài khoản admin chưa

Tôi sẽ hỗ trợ ngay! 🚀
