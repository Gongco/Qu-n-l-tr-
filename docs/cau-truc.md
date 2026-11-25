# Cấu trúc và quyền truy cập

| Khu vực | Quyền |
| --- | --- |
| pages/home.php, pages/room_detail.php | Mọi người xem, lọc phòng và xem chi tiết |
| pages/profile.php | Thành viên sửa hồ sơ của mình |
| pages/requests.php | Người thuê xem yêu cầu của mình |
| landlord/rooms, landlord/tenants | Chủ trọ quản lý phòng và người thuê của mình |
| landlord/requests.php | Chủ trọ chấp nhận hoặc từ chối yêu cầu cho phòng của mình |
| admin/index.php | Admin xem tổng quan, kiểm duyệt tin và xem lịch sử |

Bảng users liên kết với rooms qua chủ trọ và với requests qua người gửi. Mỗi yêu cầu và hồ sơ người thuê (tenants) thuộc một phòng. Khi chủ trọ chấp nhận một yêu cầu, hệ thống cập nhật tình trạng phòng và từ chối các yêu cầu còn chờ trong cùng một giao dịch MySQL.

