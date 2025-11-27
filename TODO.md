# TODO: Thêm logic tích điểm khi đặt vé thành công

## Mục tiêu
Thêm 1 điểm cho mỗi 1000 VND chi tiêu khi đặt vé thành công.

## Các file cần chỉnh sửa
- [x] app/Http/Controllers/BookingController.php - phương thức processPayment (thanh toán online)
- [x] app/Http/Controllers/Staff/DonDatVeController.php - phương thức store (đặt vé tại quầy)
- [x] app/Http/Controllers/Staff/DonDatVeController.php - phương thức changeStatus (thay đổi trạng thái)

## Logic tích điểm
- Tính điểm = floor(tổng_tiền / 1000)
- Gọi $user->themDiem($diem, 'Tích điểm từ đơn đặt vé ' . $donDatVe->ma_don)

## Kiểm tra
- [ ] Test thanh toán online (MoMo/VNPay)
- [ ] Test đặt vé tại quầy
- [ ] Test thay đổi trạng thái đơn từ admin/staff
