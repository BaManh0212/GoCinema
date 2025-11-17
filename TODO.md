# TODO: Implement Seat Management Per Showtime

## 1. Create migration for ghe_suat_chieu table
- [x] Create migration file for ghe_suat_chieu table (suat_chieu_id, ghe_id, trang_thai)

## 2. Create GheSuatChieu model
- [x] Create app/Models/GheSuatChieu.php with relationships

## 3. Update SuatChieuController::gheIndex
- [x] Load seat statuses per showtime from ghe_suat_chieu table
- [x] Pass seat data to view

## 4. Update view resources/views/admin/suatchieu/ghe_index.blade.php
- [x] Display seats with per-showtime status
- [x] Allow toggling seat availability per showtime
- [x] Add JavaScript for AJAX updates

## 5. Add method in SuatChieuController to update seat status
- [x] Add updateGheTrangThai method to toggle seat status per showtime

## 6. Update routes
- [x] Add route for updating seat status per showtime

## 7. Testing
- [x] Run php artisan migrate
- [ ] Test seat toggling per showtime
