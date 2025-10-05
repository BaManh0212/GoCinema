# 🚀 HƯỚNG DẪN NHANH - CINEMA BOOKING PROJECT

## ✅ ĐÃ HOÀN THÀNH

### 1. Đã sửa vấn đề users table
- Model `User` giờ dùng bảng `nguoi_dung`
- Authentication sẽ hoạt động bình thường

### 2. Đã tạo 33 Models với đầy đủ relationships

### 3. Đã sửa tất cả migrations quan trọng
- ✅ Thêm onDelete constraints (cascade, restrict, set null)
- ✅ Thêm unique constraints
- ✅ Thêm indexes
- ✅ Thêm soft deletes
- ✅ Thêm default values

---

## 📋 CHECKLIST NGAY BÂY GIỜ

### Bước 1: Chạy Migrations (5 phút)
```bash
# Tạo database nếu chưa có
mysql -u root -p
CREATE DATABASE IF NOT EXISTS gocinema CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# Tạo file .env nếu chưa có
cp .env.example .env

# Generate app key
php artisan key:generate

# Chạy migrations
php artisan migrate:fresh
```

**✅ Kiểm tra:** Nếu không có lỗi, bạn có 40+ bảng trong database!

---

### Bước 2: Tạo Seeders (30 phút - 1 giờ)

**Ưu tiên tạo seeders cho:**

#### A. Master Data (Cần thiết để chạy app)
```bash
php artisan make:seeder VaiTroSeeder
php artisan make:seeder QuyenHanSeeder
php artisan make:seeder RapSeeder
php artisan make:seeder DinhDangSeeder
php artisan make:seeder PhuongThucThanhToanSeeder
php artisan make:seeder TheLoaiSeeder
php artisan make:seeder DanhMucSeeder
php artisan make:seeder NgonNguSeeder
```

#### B. Demo Data
```bash
php artisan make:seeder UserSeeder
php artisan make:seeder PhimSeeder
php artisan make:seeder PhongChieuSeeder
php artisan make:seeder GheSeeder
```

**Ví dụ VaiTroSeeder:**
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VaiTro;

class VaiTroSeeder extends Seeder
{
    public function run(): void
    {
        $vaiTros = [
            ['ten' => 'Quản lý', 'mo_ta' => 'Quản lý hệ thống'],
            ['ten' => 'Nhân viên', 'mo_ta' => 'Nhân viên bán vé'],
            ['ten' => 'Khách hàng', 'mo_ta' => 'Khách hàng đặt vé online'],
        ];

        foreach ($vaiTros as $vaiTro) {
            VaiTro::create($vaiTro);
        }
    }
}
```

**Sau đó chạy:**
```bash
php artisan db:seed
```

---

### Bước 3: Tạo API Routes (15 phút)

**File `routes/api.php`** (tạo file này):
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PhimController;
use App\Http\Controllers\Api\SuatChieuController;
use App\Http\Controllers\Api\BookingController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Movies
Route::get('/phim', [PhimController::class, 'index']);
Route::get('/phim/{id}', [PhimController::class, 'show']);

// Showtimes
Route::get('/suat-chieu', [SuatChieuController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Booking
    Route::post('/booking/reserve-seats', [BookingController::class, 'reserveSeats']);
    Route::post('/booking/checkout', [BookingController::class, 'checkout']);
});
```

---

### Bước 4: Tạo Controllers Cơ Bản (30 phút)

```bash
# Auth
php artisan make:controller Api/AuthController
php artisan make:controller Api/PhimController --api
php artisan make:controller Api/SuatChieuController --api
php artisan make:controller Api/BookingController
```

**Ví dụ PhimController:**
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Phim;
use Illuminate\Http\Request;

class PhimController extends Controller
{
    public function index()
    {
        $phim = Phim::with(['theLoai', 'danhMuc'])
            ->orderBy('ngay_cong_chieu', 'desc')
            ->paginate(20);
            
        return response()->json($phim);
    }

    public function show($id)
    {
        $phim = Phim::with(['theLoai', 'dienVien', 'daoDien', 'danhGia'])
            ->findOrFail($id);
            
        return response()->json($phim);
    }
}
```

---

## 🎯 ROADMAP 4 TUẦN

### Tuần 1 (HIỆN TẠI)
- ✅ Models & Migrations
- [ ] Seeders
- [ ] Basic API Routes
- [ ] Basic Controllers

### Tuần 2
- [ ] Authentication hoàn chỉnh
- [ ] Movie CRUD
- [ ] Booking flow cơ bản
- [ ] Frontend layout

### Tuần 3
- [ ] Payment integration
- [ ] Voucher/Discount system
- [ ] Reports
- [ ] Admin dashboard

### Tuần 4
- [ ] Testing
- [ ] Bug fixing
- [ ] Performance optimization
- [ ] Documentation
- [ ] Chuẩn bị demo

---

## 🔥 3 ĐIỀU QUAN TRỌNG NHẤT

### 1. **Luôn dùng Models để query**
❌ **SAI:**
```php
DB::table('nguoi_dung')->where('id', 1)->first();
```

✅ **ĐÚNG:**
```php
User::find(1);
User::with('donDatVe')->find(1);
```

### 2. **Luôn validate input**
```php
// Tạo Request classes
php artisan make:request BookingRequest
```

### 3. **Luôn xử lý exceptions**
```php
try {
    $booking = DonDatVe::create($data);
    return response()->json($booking, 201);
} catch (\Exception $e) {
    return response()->json(['error' => 'Booking failed'], 500);
}
```

---

## 📞 TEAM COMMUNICATION

### Khi gặp lỗi Migration:
```bash
# Xóa tất cả và chạy lại
php artisan migrate:fresh --seed

# Rollback 1 step
php artisan migrate:rollback --step=1

# Xem status
php artisan migrate:status
```

### Khi merge code:
1. Pull code mới nhất
2. Chạy `php artisan migrate`
3. Chạy `php artisan db:seed` (nếu có seeders mới)
4. Test API endpoints
5. Commit & push

---

## 🛠️ TOOLS HỮU ÍCH

### Debug queries:
```php
\DB::enableQueryLog();
$users = User::all();
dd(\DB::getQueryLog());
```

### Tinker (Test models):
```bash
php artisan tinker

>>> $user = User::find(1);
>>> $user->donDatVe()->count();
```

### Generate API documentation:
```bash
# Sau này có thể dùng
composer require darkaonline/l5-swagger
```

---

## ✨ TIPS & TRICKS

### 1. Eager Loading (Tránh N+1 queries)
```php
// Chậm
$phim = Phim::all();
foreach ($phim as $p) {
    echo $p->theLoai->ten; // N+1 query!
}

// Nhanh
$phim = Phim::with('theLoai')->all();
```

### 2. Scopes giúp code sạch hơn
```php
// Thay vì
$users = User::where('kich_hoat', true)
    ->where('loai_tai_khoan', 'khach_hang')
    ->get();

// Dùng scope
$users = User::kichHoat()->khachHang()->get();
```

### 3. Accessors cho computed fields
```php
// Trong Model Phim
public function getDiemTrungBinhAttribute()
{
    return $this->danhGia()->avg('so_sao');
}

// Sử dụng
$phim = Phim::find(1);
echo $phim->diem_trung_binh; // Tự động tính!
```

---

## 🎓 HỌC THÊM

- **Laravel Daily:** https://www.youtube.com/@LaravelDaily
- **Laravel Docs:** https://laravel.com/docs/11.x
- **Laravel Best Practices:** https://github.com/alexeymezenin/laravel-best-practices

---

**Good luck! 🚀**
