# Trọ Nhà

Website quản lý phòng trọ bằng PHP 8, MySQL/MariaDB và PDO. Giao diện dùng CSS, JavaScript và hình minh họa cục bộ.

## Chạy với XAMPP

1. Đặt dự án trong thư mục htdocs/Quanlyphongtro. Bật Apache và MySQL.
2. Nhập **database/phongtro.sql** trong phpMyAdmin. Tệp tạo hoặc bổ sung các bảng trong **quan_ly_phong_tro**. Với cơ sở dữ liệu của phiên bản trước, nhập lại để bổ sung bảng tenants. Dữ liệu mẫu chỉ được tạo một lần.
3. Mở [trang chủ](http://localhost/Quanlyphongtro/). Kết nối mặc định là 127.0.0.1:3307, user root, mật khẩu trống. Có thể đặt DB_HOST, DB_PORT, DB_NAME, DB_USER và DB_PASSWORD cho Apache trong môi trường của bạn.
4. Cho phép Apache ghi vào uploads/rooms để chủ trọ tải ảnh lên.

## Tài khoản mẫu

| Vai trò | Email | Mật khẩu |
| --- | --- | --- |
| Người thuê | nguoithue@tronha.vn | Demo1234! |
| Chủ trọ | chutro@tronha.vn | Demo1234! |
| Quản trị viên | admin@tronha.vn | Demo1234! |

Tài khoản demo chỉ dùng để thử trên máy. Dữ liệu riêng đã nhập trong localStorage của phiên bản HTML cũ vẫn thuộc trình duyệt và không được tự động nhập vào MySQL. Tài khoản mới, phòng, hồ sơ người thuê và yêu cầu được lưu trong MySQL. PHP kiểm tra phiên, vai trò và quyền sở hữu tại máy chủ.

Xem [cấu trúc và quyền truy cập](docs/cau-truc.md).
