CREATE DATABASE IF NOT EXISTS quan_ly_phong_tro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quan_ly_phong_tro;
SET NAMES utf8mb4;
CREATE TABLE IF NOT EXISTS users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, email VARCHAR(150) NOT NULL UNIQUE,
 hash VARCHAR(255) NOT NULL, role ENUM('renter','landlord','admin') NOT NULL, phone VARCHAR(24) NOT NULL DEFAULT ''
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS rooms (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, owner INT UNSIGNED NOT NULL, title VARCHAR(160) NOT NULL,
 city VARCHAR(100) NOT NULL, district VARCHAR(120) NOT NULL, price INT UNSIGNED NOT NULL, area DECIMAL(7,1) NOT NULL,
 type VARCHAR(50) NOT NULL DEFAULT 'Phòng trọ', description TEXT NOT NULL, amenities VARCHAR(500) NOT NULL,
 image MEDIUMTEXT NOT NULL, available TINYINT NOT NULL DEFAULT 1, hidden TINYINT NOT NULL DEFAULT 0,
 archived TINYINT NOT NULL DEFAULT 0, FOREIGN KEY(owner) REFERENCES users(id)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS requests (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, room INT UNSIGNED NOT NULL, renter INT UNSIGNED NOT NULL,
 status ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending', created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(room) REFERENCES rooms(id), FOREIGN KEY(renter) REFERENCES users(id)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS seed_versions (version INT PRIMARY KEY) ENGINE=InnoDB;
START TRANSACTION;
INSERT IGNORE INTO seed_versions VALUES (1);
SET @seed = ROW_COUNT();
SET @hash = '$2y$10$56KvcxJcITiESbh73/fJ7uaTCDi5EhiKYExP6smXrXLEdiMHdAXBC';
INSERT INTO users(id,name,email,hash,role)
SELECT 1,'Minh Anh','nguoithue@tronha.vn',@hash,'renter' WHERE @seed=1 UNION ALL
SELECT 2,'Hoàng An','chutro@tronha.vn',@hash,'landlord' WHERE @seed=1 UNION ALL
SELECT 3,'Quản trị','admin@tronha.vn',@hash,'admin' WHERE @seed=1;
INSERT INTO rooms(id,owner,title,city,district,price,area,type,description,amenities,image,available)
SELECT seed.*, CONCAT('images/room-',seed.id,'.svg'), IF(seed.id=6,0,1) FROM (
SELECT 1 id,2 owner,'Studio đón nắng có ban công' title,'Hạ Long' city,'Bãi Cháy' district,3500000 price,28 area,'Studio' type,'Phòng nhiều ánh sáng, có ban công riêng.' description,'Wi-Fi, Điều hòa, Bếp riêng' amenities UNION ALL
SELECT 2,2,'Phòng gác lửng gần trường','Hà Nội','Cầu Giấy',2800000,24,'Phòng trọ','Gác lửng tách riêng khu ngủ, gần trường và chợ.','Wi-Fi, Chỗ để xe' UNION ALL
SELECT 3,2,'Căn hộ mini đầy đủ nội thất','TP. Hồ Chí Minh','Bình Thạnh',5200000,38,'Căn hộ mini','Có giường, tủ và khu bếp riêng.','Wi-Fi, Bếp riêng' UNION ALL
SELECT 4,2,'Studio thoáng mát gần biển','Đà Nẵng','Sơn Trà',4200000,32,'Studio','Cửa sổ lớn nhìn ra sân, thuận tiện đi bộ ra biển.','Điều hòa, Chỗ để xe' UNION ALL
SELECT 5,2,'Phòng trọ có bếp riêng','Hạ Long','Hồng Hải',2100000,20,'Phòng trọ','Phòng khép kín gần chợ và trường học.','Wi-Fi, Bếp riêng' UNION ALL
SELECT 6,2,'Căn hộ một phòng ngủ','Hà Nội','Thanh Xuân',6000000,45,'Căn hộ mini','Phòng ngủ tách riêng, có phòng khách và thang máy.','Wi-Fi, Thang máy'
) seed WHERE @seed=1;
INSERT INTO requests(room,renter,status) SELECT 5,1,'pending' WHERE @seed=1 UNION ALL SELECT 6,1,'accepted' WHERE @seed=1;
COMMIT;

CREATE TABLE IF NOT EXISTS tenants (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 room INT UNSIGNED NOT NULL,
 name VARCHAR(120) NOT NULL,
 phone VARCHAR(24) NOT NULL DEFAULT '',
 start_date DATE NOT NULL,
 FOREIGN KEY(room) REFERENCES rooms(id)
) ENGINE=InnoDB;
