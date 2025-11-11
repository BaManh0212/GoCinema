# TODO: Làm cho các trang client/movies và client/account chuyên nghiệp như trang home

## Thông tin thu thập
- Trang home có theme tối hiện đại với hiệu ứng glassmorphism, biến CSS nhất quán, và hoạt hình mượt mà.
- Các trang movies và account có style khác nhau—một số tối nhưng không bóng bẩy, và một trang account sáng.
- Cần cập nhật CSS trong mỗi file để sử dụng cùng biến và style: nền tối, card glassmorphism, hiệu ứng hover, v.v. Bao gồm thay đổi account/index.blade.php sáng thành tối.

## Kế hoạch
- Cập nhật CSS trong resources/views/client/movies/index.blade.php để khớp với home.
- Cập nhật CSS trong resources/views/client/movies/category.blade.php.
- Cập nhật CSS trong resources/views/client/movies/show.blade.php (đã có một số, nhưng làm nhất quán).
- Cập nhật resources/views/client/account/index.blade.php (thay đổi thành tối).
- Đảm bảo nhất quán trong account/profile.blade.php (đã tối).
- Đảm bảo nhất quán trong account/my-vouchers.blade.php (đã tối).
- Đảm bảo nhất quán trong account/point-history.blade.php (đã tối).
- Đảm bảo nhất quán trong account/rewards.blade.php (đã tối).

## Các file phụ thuộc
- Tất cả các file blade trong client/movies và client/account.

## Các bước tiếp theo
- Sau khi cập nhật, kiểm tra các trang.
- Sử dụng browser_action nếu cần để xác minh.
