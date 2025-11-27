# TODO: Bổ sung chức năng Staff Đặt Vé Tại Quầy

## 1. Thêm phương thức thanh toán quét mã
- [x] Cập nhật DonDatVeController.php: Thêm 'quet_ma' vào store() method
- [x] Cập nhật select_seats.blade.php: Thêm option chọn phương thức thanh toán (tiền mặt hoặc quét mã)

## 2. Giới hạn hủy vé chỉ trước 2 tiếng giờ chiếu
- [x] Cập nhật DonDatVeController.php: Thêm kiểm tra thời gian trong changeStatus() method

## 3. Thêm link vào sidebar staff
- [x] Cập nhật resources/views/admin/layouts/sidebar.blade.php: Thêm menu item "Đặt Vé Tại Quầy" dẫn đến route staff.donve.create

## 4. Test các thay đổi
- [x] Kiểm tra luồng đặt vé với cả hai phương thức thanh toán
- [x] Kiểm tra logic hủy vé với thời gian
- [x] Kiểm tra link sidebar hoạt động
