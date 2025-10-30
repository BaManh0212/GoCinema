# 🎫 VOUCHER VÉ PHIM - GIẢM TỐI ĐA (Kiểu Shopee)

## ✨ Tính năng mới (Cập nhật: 27/10/2025)

### Giảm tối đa cho voucher phần trăm

Giống như Shopee, voucher phần trăm giờ có **giới hạn giảm tối đa**:

```
❌ Trước:  "Giảm 50%"
✅ Sau:    "Giảm 50% (tối đa 50.000đ)"

❌ Trước:  "Giảm 20%"  
✅ Sau:    "Giảm 20% (tối đa 30.000đ)"
```

---

## 📊 Database

### Thêm cột mới vào bảng `voucher`:

| Cột | Kiểu | Nullable | Mô tả |
|-----|------|----------|-------|
| `giam_toi_da` | DECIMAL(12,2) | YES | Giảm tối đa (cho voucher %), VD: 50000 |

**Migration:** `2025_10_27_225625_add_giam_toi_da_to_voucher_table.php`

```php
$table->decimal('giam_toi_da', 12, 2)->nullable()->after('gia_tri')
      ->comment('Giảm tối đa (cho voucher %), VD: Giảm 10% tối đa 50k');
```

---

## 🎯 Logic hoạt động

### 1. Voucher Phần trăm (%) **CÓ** giới hạn:
```
Vé: 200.000đ
Voucher: Giảm 50% (tối đa 50.000đ)

Tính toán:
- 50% của 200.000đ = 100.000đ
- Nhưng giới hạn tối đa = 50.000đ
→ Giảm thực tế: 50.000đ ✅
→ Thanh toán: 150.000đ
```

### 2. Voucher Phần trăm (%) **KHÔNG** giới hạn:
```
Vé: 200.000đ
Voucher: Giảm 50% (không giới hạn)

Tính toán:
- 50% của 200.000đ = 100.000đ
→ Giảm: 100.000đ ✅
→ Thanh toán: 100.000đ
```

### 3. Voucher Số tiền (đ):
```
Vé: 200.000đ
Voucher: Giảm 30.000đ

→ Giảm: 30.000đ ✅
→ Thanh toán: 170.000đ
```

---

## 💻 Code Changes

### 1. **Model Voucher** (`app/Models/Voucher.php`)

```php
// Thêm vào $fillable
'giam_toi_da',

// Thêm vào $casts
'giam_toi_da' => 'decimal:2',

// Method getMoTaGiaTriAttribute() - cập nhật
public function getMoTaGiaTriAttribute()
{
    if ($this->loai === 'phan_tram') {
        $moTa = number_format($this->gia_tri, 0) . '%';
        
        // Nếu có giới hạn giảm tối đa (như Shopee)
        if ($this->giam_toi_da > 0) {
            $moTa .= ' (tối đa ' . number_format($this->giam_toi_da, 0) . 'đ)';
        }
        
        return $moTa;
    }
    return number_format($this->gia_tri, 0) . 'đ';
}
```

### 2. **VoucherSeeder** (`database/seeders/VoucherSeeder.php`)

```php
[
    'ten' => 'Giảm 50% giá vé phim',
    'loai' => 'phan_tram',
    'gia_tri' => 50,
    'giam_toi_da' => 50000, // ⬅️ MỚI: Giảm tối đa 50k
    // ...
],
[
    'ten' => 'Giảm 20% giá vé phim',
    'loai' => 'phan_tram',
    'gia_tri' => 20,
    'giam_toi_da' => 30000, // ⬅️ MỚI: Giảm tối đa 30k
    // ...
],
[
    'ten' => 'Giảm 40% giá vé phim',
    'loai' => 'phan_tram',
    'gia_tri' => 40,
    'giam_toi_da' => 60000, // ⬅️ MỚI: Giảm tối đa 60k
    // ...
],
[
    'ten' => 'Giảm 30.000đ',
    'loai' => 'so_tien',
    'gia_tri' => 30000,
    'giam_toi_da' => null, // ⬅️ Voucher số tiền không cần giới hạn
    // ...
],
```

### 3. **Form Create** (`resources/views/admin/voucher/create.blade.php`)

```blade
<!-- Thay đổi layout từ 3 cột 4-4-4 → 4 cột 3-3-3-3 -->
<div class="row mb-3">
    <div class="col-md-3">
        <label>Loại voucher *</label>
        <select id="loai" name="loai" onchange="toggleGiamToiDa()">
            <option value="phan_tram">Phần trăm (%)</option>
            <option value="so_tien">Số tiền (đ)</option>
        </select>
    </div>

    <div class="col-md-3">
        <label>Giá trị *</label>
        <input type="number" name="gia_tri" placeholder="VD: 10 hoặc 50000">
    </div>

    <!-- ⬅️ MỚI: Trường giảm tối đa (chỉ hiện khi chọn %) -->
    <div class="col-md-3" id="giam_toi_da_group" style="display: none;">
        <label>Giảm tối đa</label>
        <input type="number" name="giam_toi_da" placeholder="VD: 50000">
        <small class="text-muted">Chỉ cho voucher %</small>
    </div>

    <div class="col-md-3">
        <label>Áp dụng cho *</label>
        <select name="ap_dung_cho">...</select>
    </div>
</div>

<!-- JavaScript toggle -->
<script>
function toggleGiamToiDa() {
    const loai = document.getElementById('loai').value;
    const giamToiDaGroup = document.getElementById('giam_toi_da_group');
    
    if (loai === 'phan_tram') {
        giamToiDaGroup.style.display = 'block';
    } else {
        giamToiDaGroup.style.display = 'none';
        document.getElementById('giam_toi_da').value = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleGiamToiDa();
});
</script>
```

### 4. **Form Edit** (`resources/views/admin/voucher/edit.blade.php`)

Tương tự form create, có thêm:
```blade
<div class="col-md-3" id="giam_toi_da_group">
    <label>Giảm tối đa</label>
    <input type="number" 
           name="giam_toi_da" 
           value="{{ old('giam_toi_da', $voucher->giam_toi_da) }}">
    <small class="text-muted">Chỉ cho voucher %</small>
</div>
```

---

## 🎨 Hiển thị giao diện

### Admin Index:
```
┌────────────────────────────────────────────────────┐
│ Tiêu đề              │ Giá trị voucher            │
├──────────────────────┼────────────────────────────┤
│ Giảm 50% vé phim     │ 50% (tối đa 50.000đ) ✨    │
│ Giảm 30.000đ         │ 30.000đ                    │
│ Giảm 20% vé phim     │ 20% (tối đa 30.000đ) ✨    │
│ Giảm 40% vé phim     │ 40% (tối đa 60.000đ) ✨    │
└────────────────────────────────────────────────────┘
```

### User Rewards:
```
┌──────────────────────────────────────────┐
│ 🎫 Giảm 50% giá vé phim        500 điểm │
├──────────────────────────────────────────┤
│ Loại: Giảm theo %                        │
│ Giá trị: 50% (tối đa 50.000đ) ✨        │
│ Số lượng: 45/50 🟢                       │
│ 🎬 Chỉ dành cho VÉ PHIM                  │
│ ⏰ Hiệu lực: 30 ngày từ ngày đổi         │
│                                          │
│            [🔄 Đổi ngay]                │
└──────────────────────────────────────────┘
```

---

## 📈 Ví dụ thực tế

### Case 1: Vé rẻ
```
Vé: 80.000đ
Voucher: Giảm 50% (tối đa 50.000đ)

50% × 80.000đ = 40.000đ < 50.000đ
→ Giảm: 40.000đ
→ Thanh toán: 40.000đ
```

### Case 2: Vé đắt
```
Vé: 200.000đ
Voucher: Giảm 50% (tối đa 50.000đ)

50% × 200.000đ = 100.000đ > 50.000đ ❌
→ Giảm: 50.000đ (chặn lại) ✅
→ Thanh toán: 150.000đ
```

### Case 3: Voucher không giới hạn
```
Vé: 200.000đ
Voucher: Giảm 50% (không giới hạn)

50% × 200.000đ = 100.000đ
→ Giảm: 100.000đ ✅
→ Thanh toán: 100.000đ
```

---

## 🎯 Lợi ích

### 1. **Kiểm soát chi phí**
- Tránh giảm quá nhiều cho vé đắt
- Giống Shopee → người dùng quen thuộc

### 2. **Linh hoạt**
- Voucher % có thể có/không có giới hạn
- Voucher số tiền không cần giới hạn

### 3. **UX tốt**
- Trường "Giảm tối đa" chỉ hiện khi chọn %
- Tự động ẩn/hiện bằng JavaScript
- Hiển thị rõ ràng: "50% (tối đa 50.000đ)"

---

## 🔄 Migration & Deployment

```bash
# 1. Chạy migration
php artisan migrate --path=database/migrations/2025_10_27_225625_add_giam_toi_da_to_voucher_table.php

# 2. Seed data mới
php artisan db:seed --class=VoucherSeeder

# 3. Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## 📝 Data mẫu

```php
// Voucher phần trăm CÓ giới hạn
[
    'ten' => 'Giảm 50% giá vé phim',
    'loai' => 'phan_tram',
    'gia_tri' => 50,
    'giam_toi_da' => 50000, // ← Giảm tối đa 50k
],

// Voucher phần trăm KHÔNG giới hạn
[
    'ten' => 'Giảm 10% vé phim',
    'loai' => 'phan_tram',
    'gia_tri' => 10,
    'giam_toi_da' => null, // ← Không giới hạn
],

// Voucher số tiền (không cần giới hạn)
[
    'ten' => 'Giảm 30.000đ',
    'loai' => 'so_tien',
    'gia_tri' => 30000,
    'giam_toi_da' => null, // ← Voucher số tiền không dùng field này
],
```

---

## ⚠️ Lưu ý

### 1. **Chỉ áp dụng cho voucher phần trăm**
- Voucher số tiền → `giam_toi_da = null`
- Voucher % có thể có/không có giới hạn

### 2. **JavaScript toggle**
- Khi chọn "Phần trăm (%)" → Hiện trường "Giảm tối đa"
- Khi chọn "Số tiền (đ)" → Ẩn trường "Giảm tối đa" và xóa giá trị

### 3. **Hiển thị**
- Nếu `loai = 'phan_tram'` và `giam_toi_da > 0` → "50% (tối đa 50.000đ)"
- Nếu `loai = 'phan_tram'` và `giam_toi_da = null` → "50%"
- Nếu `loai = 'so_tien'` → "30.000đ"

---

## 🎉 Kết quả

Hệ thống voucher giờ đã:
- ✅ Có giới hạn giảm tối đa (như Shopee)
- ✅ Hiển thị rõ ràng "Giảm X% (tối đa Yđ)"
- ✅ Form tự động ẩn/hiện trường theo loại voucher
- ✅ Linh hoạt: voucher % có thể có/không có giới hạn
- ✅ Kiểm soát chi phí tốt hơn
- ✅ UX thân thiện, người dùng đã quen (Shopee style)

**So sánh:**
```
Shopee:  "Giảm 10% tối đa 50k" ✅
GoCinema: "Giảm 10% (tối đa 50.000đ)" ✅ Giống!
```
