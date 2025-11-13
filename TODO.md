# TODO: Implement Suat Chieu Preview Feature

## Tasks
- [x] Sửa method `autoStore` trong SuatChieuController: Tạo mảng preview thay vì lưu DB, kiểm tra trùng và đánh dấu conflict.
- [x] Thêm method `storePreview` trong SuatChieuController: Lưu mảng suất từ preview vào DB.
- [x] Thêm route cho `storePreview` (POST /admin/suatchieu/store-preview).
- [x] Cập nhật view `create.blade.php`: Hiển thị bảng preview bên dưới form nếu có data, với nút xóa (JS) và nút "Lưu vào danh sách".
- [x] Thêm JS để xóa row trong bảng preview và cập nhật hidden inputs.
- [ ] Test tính năng: Tạo preview, xóa suất, lưu vào DB, kiểm tra trùng.

## Notes
- Preview data sẽ được truyền qua compact từ controller.
- Sử dụng session hoặc hidden inputs để giữ data giữa requests.
- Kiểm tra trùng: Cùng phòng, giờ chồng lấn với DB hiện tại.
- Highlight đỏ nếu conflict.
