# 🎫 HỆ THỐNG VOUCHER VÉ PHIM - CÓ GIỚI HẠN SỐ LƯỢNG

## ✨ Tính năng mới (Cập nhật: 27/10/2025)

### 1. **Giới hạn số lượng voucher**
- Mỗi voucher có **số lượng tối đa** (trần)
- Hệ thống theo dõi **số lượng đã đổi**
- Người dùng **không thể đổi** khi voucher hết

### 2. **Quản lý Admin đầy đủ**
- ✅ **Thêm** voucher mới với số lượng
- ✅ **Sửa** thông tin voucher (bao gồm tăng/giảm số lượng)
- ✅ **Xóa** voucher (nếu chưa ai đổi)
- ✅ **Xem** chi tiết số lượng còn lại

---

## 📊 Cấu trúc Database

### Bảng `voucher` - Thêm 2 cột mới:

| Cột | Kiểu | Mặc định | Mô tả |
|-----|------|----------|-------|
| `so_luong_toi_da` | INT | 100 | Số lượng voucher tối đa |
| `so_luong_da_dung` | INT | 0 | Số lượng đã được đổi |

**Migration:** `2025_10_27_224049_add_so_luong_to_voucher_table.php`

---

## 🔧 Code Changes

### 1. **Model Voucher** (`app/Models/Voucher.php`)

```php
// Thêm vào $fillable
'so_luong_toi_da',
'so_luong_da_dung',

// Thêm vào $casts
'so_luong_toi_da' => 'integer',
'so_luong_da_dung' => 'integer',

// Methods mới
public function conVoucherDeDoi()
{
    return $this->so_luong_da_dung < $this->so_luong_toi_da;
}

public function getSoLuongConLaiAttribute()
{
    return max(0, $this->so_luong_toi_da - $this->so_luong_da_dung);
}
```

### 2. **AccountController** (`app/Http/Controllers/AccountController.php`)

```php
public function redeemVoucher($voucherId)
{
    // ... existing code ...
    
    // KIỂM TRA SỐ LƯỢNG
    if (!$voucher->conVoucherDeDoi()) {
        return back()->with('error', 'Voucher này đã hết!');
    }
    
    // ... existing code ...
    
    // TĂNG SỐ LƯỢNG ĐÃ DÙNG
    $voucher->increment('so_luong_da_dung');
    
    // ... existing code ...
}
```

### 3. **View Admin Index** (`resources/views/admin/voucher/index.blade.php`)

**Thêm cột "Số lượng":**
```blade
<th>Số lượng</th>

<!-- Trong tbody -->
<td>
    @php
        $conLai = $voucher->so_luong_toi_da - $voucher->so_luong_da_dung;
        $phanTram = ($voucher->so_luong_toi_da > 0) 
            ? ($conLai / $voucher->so_luong_toi_da * 100) : 0;
    @endphp
    <div class="d-flex flex-column align-items-center">
        <span class="badge {{ $phanTram > 50 ? 'bg-success' : ($phanTram > 20 ? 'bg-warning' : 'bg-danger') }}">
            {{ $conLai }}/{{ $voucher->so_luong_toi_da }}
        </span>
        <small class="text-muted">Đã dùng: {{ $voucher->so_luong_da_dung }}</small>
    </div>
</td>
```

**Thêm nút Sửa & Xóa:**
```blade
<a href="{{ route('admin.voucher.edit', $voucher->id) }}" 
   class="btn btn-sm btn-outline-warning">
    <i class="fas fa-edit"></i>
</a>
<button onclick="confirmDelete({{ $voucher->id }})" 
        class="btn btn-sm btn-outline-danger">
    <i class="fas fa-trash"></i>
</button>
```

### 4. **View User Rewards** (`resources/views/account/rewards.blade.php`)

**Hiển thị số lượng còn lại:**
```blade
@php
    $conLai = $voucher->so_luong_toi_da - $voucher->so_luong_da_dung;
    $phanTram = ($voucher->so_luong_toi_da > 0) 
        ? ($conLai / $voucher->so_luong_toi_da * 100) : 0;
@endphp

<small class="text-muted">Số lượng còn lại:</small>
<div>
    <span class="badge {{ $phanTram > 50 ? 'bg-success' : ($phanTram > 20 ? 'bg-warning' : 'bg-danger') }}">
        {{ $conLai }}/{{ $voucher->so_luong_toi_da }}
    </span>
    @if($conLai <= 10 && $conLai > 0)
        <span class="text-danger"><i class="fas fa-fire"></i> Sắp hết!</span>
    @elseif($conLai == 0)
        <span class="text-danger"><i class="fas fa-times-circle"></i> Đã hết!</span>
    @endif
</div>
```

**Nút đổi điểm:**
```blade
@if($coTheDoiDuoc)
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-exchange-alt me-2"></i>Đổi ngay
    </button>
@elseif(!$conVoucher)
    <button class="btn btn-danger" disabled>
        <i class="fas fa-ban me-2"></i>Đã hết
    </button>
@else
    <button class="btn btn-secondary" disabled>
        <i class="fas fa-lock me-2"></i>Chưa đủ điểm
    </button>
@endif
```

### 5. **Form Create/Edit**

**Thêm trường số lượng:**
```blade
<div class="col-md-4">
    <label for="so_luong_toi_da">Số lượng voucher *</label>
    <input type="number" 
           name="so_luong_toi_da" 
           value="{{ old('so_luong_toi_da', $voucher->so_luong_toi_da ?? 100) }}"
           min="1" 
           required>
    <!-- Chỉ trong form Edit: -->
    <small class="text-muted">
        Đã dùng: {{ $voucher->so_luong_da_dung }}/{{ $voucher->so_luong_toi_da }}
    </small>
</div>
```

---

## 🎨 Giao diện

### Admin Dashboard:
```
┌──────────────────────────────────────────────────────────────────┐
│ Voucher Giảm Giá Vé Phim         [Tải lại] [+ Thêm voucher]    │
├──────────────────────────────────────────────────────────────────┤
│ Tiêu đề            │ Số lượng    │ HSD      │ Thao tác          │
├────────────────────┼─────────────┼──────────┼───────────────────┤
│ Giảm 50% vé phim   │ 45/50 🟢   │ 181 ngày │ 👁 ✏️ 🗑️          │
│ Giảm 30.000đ       │ 98/100 🟢  │ 181 ngày │ 👁 ✏️ 🗑️          │
│ Giảm 20.000đ       │ 8/150 🔴   │ 181 ngày │ 👁 ✏️ 🗑️          │
│ Giảm 20% vé phim   │ 0/30 🔴    │ 181 ngày │ 👁 ✏️ 🗑️          │
└──────────────────────────────────────────────────────────────────┘
```

### User Rewards:
```
┌──────────────────────────────────────┐
│ 🎫 Giảm 50% giá vé phim    500 điểm │
├──────────────────────────────────────┤
│ Loại: Giảm theo %                    │
│ Giá trị: 50%                         │
│ Số lượng: 45/50 🟢                   │
│ 🎬 Chỉ dành cho VÉ PHIM              │
│ ⏰ Hiệu lực: 30 ngày từ ngày đổi     │
│                                      │
│         [🔄 Đổi ngay]               │
└──────────────────────────────────────┘
```

---

## 📈 Luồng hoạt động

### Khi người dùng đổi điểm:

1. ✅ Kiểm tra điểm đủ không
2. ✅ Kiểm tra voucher còn hiệu lực không
3. ✅ **Kiểm tra còn voucher để đổi không** ⬅️ MỚI
4. ✅ Trừ điểm người dùng
5. ✅ Tạo bản ghi `voucher_nguoi_dung`
6. ✅ **Tăng `so_luong_da_dung`** ⬅️ MỚI

### Khi admin sửa voucher:

- ✏️ Có thể **tăng số lượng** để phát hành thêm
- ✏️ Có thể **giảm số lượng** (nếu > đã dùng)
- ✏️ Xem được **đã dùng bao nhiêu/tổng số**

---

## 🎯 Tính năng Badge Màu

| Trạng thái | Màu | Điều kiện |
|------------|-----|-----------|
| 🟢 Nhiều | Xanh lá | Còn > 50% |
| 🟡 Ít | Vàng | Còn 20-50% |
| 🔴 Sắp hết | Đỏ | Còn < 20% |
| 🔥 Cảnh báo | "Sắp hết!" | ≤ 10 voucher |
| ❌ Hết | "Đã hết!" | = 0 voucher |

---

## 🧪 Test Cases

### 1. Test người dùng đổi hết voucher:
```php
// Voucher có so_luong_toi_da = 3
// User 1 đổi → so_luong_da_dung = 1 ✅
// User 2 đổi → so_luong_da_dung = 2 ✅
// User 3 đổi → so_luong_da_dung = 3 ✅
// User 4 đổi → ❌ "Voucher này đã hết!"
```

### 2. Test admin tăng số lượng:
```php
// Ban đầu: 50/50 (hết)
// Admin sửa so_luong_toi_da = 100
// Kết quả: 50/100 (còn 50) ✅
```

### 3. Test xóa voucher đã có người dùng:
```php
// Voucher có so_luong_da_dung > 0
// Admin xóa → ❌ Không cho phép (constraint foreign key)
```

---

## 📝 Seeder Data

```php
// VoucherSeeder.php
[
    'ten' => 'Giảm 50% giá vé phim',
    'so_luong_toi_da' => 50,  // Giới hạn 50 voucher
    'so_luong_da_dung' => 0,
    // ...
],
[
    'ten' => 'Giảm 30.000đ',
    'so_luong_toi_da' => 100,
    'so_luong_da_dung' => 0,
    // ...
],
// Tổng 8 vouchers
```

---

## 🚀 Migration & Deployment

```bash
# 1. Chạy migration
php artisan migrate --path=database/migrations/2025_10_27_224049_add_so_luong_to_voucher_table.php

# 2. Seed data mới
php artisan db:seed --class=VoucherSeeder

# 3. Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## ⚠️ Lưu ý quan trọng

1. **Không được giảm `so_luong_toi_da` xuống thấp hơn `so_luong_da_dung`**
2. **Khi xóa voucher**: Phải kiểm tra không ai đã đổi (`so_luong_da_dung = 0`)
3. **Transaction**: Đảm bảo `increment('so_luong_da_dung')` trong transaction
4. **Race condition**: Có thể cần lock khi nhiều người đổi cùng lúc

---

## 📊 Thống kê có thể thêm

```php
// Top voucher được đổi nhiều nhất
SELECT * FROM voucher 
ORDER BY so_luong_da_dung DESC 
LIMIT 10;

// Voucher sắp hết
SELECT * FROM voucher 
WHERE (so_luong_toi_da - so_luong_da_dung) <= 10 
AND kich_hoat = 1;

// Tỷ lệ đổi voucher
SELECT 
    ten,
    so_luong_da_dung,
    so_luong_toi_da,
    ROUND((so_luong_da_dung / so_luong_toi_da * 100), 2) as ty_le_phan_tram
FROM voucher;
```

---

## ✅ Checklist hoàn thành

- [x] Migration thêm `so_luong_toi_da` và `so_luong_da_dung`
- [x] Model Voucher cập nhật fillable & casts
- [x] Method `conVoucherDeDoi()` và `getSoLuongConLaiAttribute()`
- [x] AccountController kiểm tra số lượng khi đổi
- [x] View admin hiển thị số lượng
- [x] Thêm nút Sửa & Xóa cho admin
- [x] Form create/edit có trường số lượng
- [x] View user hiển thị số lượng còn lại
- [x] Badge màu theo phần trăm còn lại
- [x] Cảnh báo "Sắp hết" khi ≤ 10
- [x] Nút "Đã hết" khi = 0
- [x] Seeder cập nhật với số lượng

---

## 🎉 Kết quả

Hệ thống voucher vé phim giờ đã:
- ✅ Có giới hạn số lượng (trần)
- ✅ Theo dõi số lượng đã đổi
- ✅ Hiển thị trực quan bằng badge màu
- ✅ Cảnh báo khi sắp hết
- ✅ Chặn đổi khi hết voucher
- ✅ Admin quản lý đầy đủ (CRUD)
- ✅ Tăng tính khan hiếm, tạo cảm giác "phải nhanh tay"! 🔥
