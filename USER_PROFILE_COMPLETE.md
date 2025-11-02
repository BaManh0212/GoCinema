# 👤 TRANG TÀI KHOẢN CÁ NHÂN (USER PROFILE)

## 📋 Tổng quan
Trang profile người dùng hoàn chỉnh với 4 tabs chính:
1. **Thông tin cá nhân** - Cập nhật profile
2. **Lịch sử vé** - Xem các vé đã đặt
3. **Điểm thưởng & Voucher** - Quản lý điểm và voucher
4. **Đổi mật khẩu** - Thay đổi mật khẩu

---

## 🎯 Các tính năng đã triển khai

### 1. **Header Profile**
```blade
- Avatar gradient tím
- Hiển thị: Tên, Email, Số điện thoại, Điểm thưởng
- Nút "Về trang chủ"
- Animation float cho avatar
```

### 2. **Tab: Thông tin cá nhân**
**Form cập nhật profile:**
- ✅ Họ và tên (required)
- ✅ Email (required, unique)
- ✅ Số điện thoại (optional)
- ✅ Ngày tham gia (readonly)
- ✅ Validation đầy đủ
- ✅ Nút: Đặt lại | Lưu thay đổi

**Route:**
```php
PUT /account/update-profile
Controller: AccountController@updateProfile
```

### 3. **Tab: Lịch sử vé**
**Bảng hiển thị:**
- Mã đơn
- Tên phim
- Suất chiếu (ngày + giờ)
- Ghế đã đặt (badges)
- Tổng tiền
- Trạng thái (Đã thanh toán/Chờ/Đã hủy)
- Ngày đặt

**Tính năng:**
- ✅ Empty state khi chưa có vé
- ✅ Nút "Đặt vé ngay" 
- ✅ Phân trang
- ✅ Quan hệ: booking -> suatChieu -> phim
- ✅ Quan hệ: booking -> chiTietVes -> ghe

### 4. **Tab: Điểm thưởng & Voucher**

#### **4.1 Thẻ thống kê (3 cards gradient):**
1. **Card Điểm thưởng** (gradient hồng)
   - Icon: fas fa-star
   - Số điểm hiện có

2. **Card Voucher** (gradient xanh dương)
   - Icon: fas fa-ticket-alt
   - Số lượng voucher đang có

3. **Card Lịch sử** (gradient xanh lá)
   - Icon: fas fa-history
   - Số giao dịch điểm

#### **4.2 Voucher của tôi:**
```blade
- Hiển thị 3 voucher gần nhất
- Mỗi voucher card:
  + Tên voucher
  + Giá trị (đã format với giam_toi_da)
  + HSD
  + Badge trạng thái (Chưa dùng/Đã dùng)
- Nút: "Đổi voucher mới"
- Nút: "Xem tất cả voucher" (nếu >3)
```

#### **4.3 Lịch sử điểm gần đây:**
```blade
Bảng hiển thị 5 giao dịch mới nhất:
- Thời gian
- Nội dung (mô tả)
- Điểm (+/- với màu xanh/đỏ)
- Số dư sau giao dịch
- Nút: "Xem tất cả"
```

### 5. **Tab: Đổi mật khẩu**
**Form đổi mật khẩu:**
- ✅ Mật khẩu hiện tại (required)
- ✅ Mật khẩu mới (required, min:6)
- ✅ Xác nhận mật khẩu mới (required, confirmed)
- ✅ Alert thông tin: "Mật khẩu mới phải có ít nhất 6 ký tự"
- ✅ Validation backend
- ✅ Kiểm tra mật khẩu hiện tại đúng
- ✅ Hash mật khẩu mới
- ✅ Nút: Đặt lại | Đổi mật khẩu

**Route:**
```php
PUT /account/change-password
Controller: AccountController@changePassword
```

---

## 🎨 Giao diện

### **Design System:**
```css
- Header: Gradient tím (#667eea → #764ba2)
- Cards gradient cho điểm: 3 màu khác nhau
- Tabs custom: Viền dưới màu primary khi active
- Hover effects: translateY(-2px) + shadow
- Animation: Float cho avatar (3s infinite)
- Bootstrap 5 components
- Font Awesome icons
```

### **Responsive:**
- Header: Flexbox với avatar + info + button
- Tabs: Stack trên mobile
- Grid: col-md-4 cho cards, col-md-6/8 cho forms
- Table: table-responsive wrapper

---

## 🔧 Backend Implementation

### **AccountController.php:**

#### **Method: index()**
```php
public function index()
{
    $user = Auth::user();
    
    // Lịch sử điểm
    $lichSuDiem = LichSuDiem::where('nguoi_dung_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();
    
    // Lịch sử đặt vé
    $bookings = DonDatVe::with(['suatChieu.phim', 'chiTietVes.ghe'])
        ->where('nguoi_dung_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->paginate(10);
    
    // Voucher của người dùng
    $myVouchers = VoucherNguoiDung::with('voucher')
        ->where('nguoi_dung_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();
    
    return view('account.profile', compact('user', 'lichSuDiem', 'bookings', 'myVouchers'));
}
```

#### **Method: updateProfile()**
```php
- Validate: ho_ten (required), email (unique), so_dien_thoai
- Update user data
- Return với session success
```

#### **Method: changePassword()**
```php
- Validate: current_password, new_password (min:6, confirmed)
- Check current password với Hash::check()
- Update với Hash::make()
- Return với session success/error
```

### **Models sử dụng:**
```php
- NguoiDung (User)
- DonDatVe (Bookings)
  ├─ suatChieu (SuatChieu)
  │   └─ phim (Phim)
  └─ chiTietVes (ChiTietVe)
      └─ ghe (Ghe)
- VoucherNguoiDung
  └─ voucher (Voucher)
- LichSuDiem (Point History)
```

---

## 📍 Routes

```php
Route::middleware(['auth'])->group(function () {
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::put('/update-profile', [AccountController::class, 'updateProfile'])->name('update-profile');
        Route::put('/change-password', [AccountController::class, 'changePassword'])->name('change-password');
        
        // Các routes khác đã có:
        Route::get('/rewards', [AccountController::class, 'rewards'])->name('rewards');
        Route::get('/my-vouchers', [AccountController::class, 'myVouchers'])->name('my-vouchers');
        Route::get('/point-history', [AccountController::class, 'pointHistory'])->name('point-history');
    });
});
```

---

## ✅ Checklist tính năng

### **Thông tin cá nhân:**
- [x] Form cập nhật profile
- [x] Validation đầy đủ
- [x] Email unique check
- [x] Success/Error messages
- [x] Icons cho từng field

### **Lịch sử vé:**
- [x] Bảng hiển thị đầy đủ thông tin
- [x] Relationship loading (eager loading)
- [x] Status badges với màu
- [x] Format ngày giờ
- [x] Phân trang
- [x] Empty state

### **Điểm & Voucher:**
- [x] 3 cards thống kê gradient
- [x] Hiển thị voucher (top 3)
- [x] Voucher cards với status
- [x] Lịch sử điểm (top 5)
- [x] Điểm +/- với màu khác nhau
- [x] Links đến trang chi tiết

### **Đổi mật khẩu:**
- [x] Form 3 fields
- [x] Validation backend
- [x] Check mật khẩu hiện tại
- [x] Hash mật khẩu mới
- [x] Confirmed validation
- [x] Alert thông tin

### **UI/UX:**
- [x] Header gradient đẹp
- [x] Tabs navigation custom
- [x] Hover effects
- [x] Animation
- [x] Responsive design
- [x] Icons đầy đủ
- [x] Empty states
- [x] Loading states

---

## 🚀 Cách sử dụng

### **Truy cập:**
```
URL: /account
Method: GET
Middleware: auth
```

### **Test flow:**

1. **Login vào hệ thống**
2. **Truy cập /account**
3. **Test từng tab:**
   - Tab 1: Cập nhật thông tin → Submit → Check success
   - Tab 2: Xem lịch sử vé → Check pagination
   - Tab 3: Xem điểm & voucher → Click các links
   - Tab 4: Đổi mật khẩu → Test validation → Submit

---

## 📦 Files đã tạo/sửa

### **Created:**
```
resources/views/account/profile.blade.php (hoàn toàn mới)
```

### **Modified:**
```
app/Http/Controllers/AccountController.php
- Updated index() method
- Thêm eager loading
- Thêm pagination
```

### **Dependencies:**
```
- Bootstrap 5 (đã có)
- Font Awesome 6 (đã có)
- Laravel Pagination (đã có)
```

---

## 🎯 Kết quả

✅ **Trang tài khoản cá nhân hoàn chỉnh** với đầy đủ tính năng:
1. ✅ Hiển thị & cập nhật thông tin cá nhân
2. ✅ Xem lịch sử vé đã đặt
3. ✅ Quản lý điểm thưởng & voucher
4. ✅ Đổi mật khẩu an toàn

**Giao diện hiện đại, responsive, có animation!** 🎨
