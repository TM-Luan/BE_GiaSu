CREATE DATABASE  IF NOT EXISTS `giasu` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `giasu`;
-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: ballast.proxy.rlwy.net    Database: railway
-- ------------------------------------------------------
-- Server version	9.4.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `DanhGia`
--

DROP TABLE IF EXISTS `DanhGia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `DanhGia` (
  `DanhGiaID` int NOT NULL AUTO_INCREMENT,
  `LopYeuCauID` int NOT NULL,
  `TaiKhoanID` int NOT NULL,
  `DiemSo` double NOT NULL,
  `BinhLuan` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayDanhGia` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `LanSua` int NOT NULL DEFAULT '0' COMMENT 'Số lần đã sửa đánh giá (0=chưa sửa, 1=đã sửa 1 lần)',
  PRIMARY KEY (`DanhGiaID`),
  KEY `LopYeuCauID` (`LopYeuCauID`),
  KEY `TaiKhoanID` (`TaiKhoanID`),
  CONSTRAINT `DanhGia_ibfk_1` FOREIGN KEY (`LopYeuCauID`) REFERENCES `LopHocYeuCau` (`LopYeuCauID`) ON DELETE CASCADE,
  CONSTRAINT `DanhGia_ibfk_2` FOREIGN KEY (`TaiKhoanID`) REFERENCES `TaiKhoan` (`TaiKhoanID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DanhGia`
--

LOCK TABLES `DanhGia` WRITE;
/*!40000 ALTER TABLE `DanhGia` DISABLE KEYS */;
INSERT INTO `DanhGia` VALUES (1,1,4,4.5,'Gia sư dạy dễ hiểu, đúng giờ.','2025-10-07 21:00:00',0),(2,1,2,5,'Học viên hợp tác tốt, chuẩn bị bài.','2025-10-07 21:05:00',0);
/*!40000 ALTER TABLE `DanhGia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DoiTuong`
--

DROP TABLE IF EXISTS `DoiTuong`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `DoiTuong` (
  `DoiTuongID` int NOT NULL AUTO_INCREMENT,
  `TenDoiTuong` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`DoiTuongID`),
  UNIQUE KEY `TenDoiTuong` (`TenDoiTuong`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DoiTuong`
--

LOCK TABLES `DoiTuong` WRITE;
/*!40000 ALTER TABLE `DoiTuong` DISABLE KEYS */;
INSERT INTO `DoiTuong` VALUES (1,'Học sinh'),(2,'Sinh viên');
/*!40000 ALTER TABLE `DoiTuong` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `GiaSu`
--

DROP TABLE IF EXISTS `GiaSu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `GiaSu` (
  `GiaSuID` int NOT NULL AUTO_INCREMENT,
  `TaiKhoanID` int NOT NULL,
  `HoTen` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `DiaChi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GioiTinh` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgaySinh` date DEFAULT NULL,
  `AnhCCCD_MatTruoc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `AnhCCCD_MatSau` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `BangCap` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `AnhBangCap` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TruongDaoTao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ChuyenNganh` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ThanhTich` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `KinhNghiem` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `AnhDaiDien` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`GiaSuID`),
  KEY `TaiKhoanID` (`TaiKhoanID`),
  CONSTRAINT `GiaSu_ibfk_1` FOREIGN KEY (`TaiKhoanID`) REFERENCES `TaiKhoan` (`TaiKhoanID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `GiaSu`
--

LOCK TABLES `GiaSu` WRITE;
/*!40000 ALTER TABLE `GiaSu` DISABLE KEYS */;
INSERT INTO `GiaSu` VALUES 
(1, 2, 'Nguyễn Văn A', 'Q.1, TP.HCM', 'Nam', '1995-05-10', 'cccd_front_1.jpg', 'cccd_back_1.jpg', 'Cử nhân Sư phạm Toán', 'bangcap_1.jpg', 'Đại học Sư phạm TP.HCM', 'Sư phạm Toán', 'Tốt nghiệp loại Giỏi\nGiải nhất Olympic Toán cấp trường\nHọc sinh đạt điểm cao trong kỳ thi THPT QG\nNhiều học sinh đạt giải HSG cấp thành phố', '3 năm kinh nghiệm dạy Toán THPT', NULL),
(2, 3, 'Trần Thị B', 'Q.3, TP.HCM', 'Nữ', '1996-08-22', 'cccd_front_2.jpg', 'cccd_back_2.jpg', 'Cử nhân Ngôn ngữ Anh', 'bangcap_2.jpg', 'Đại học KHXH&NV', 'Ngôn ngữ Anh', 'IELTS 8.0\nTốt nghiệp loại Khá\nNhiều học sinh đạt chứng chỉ IELTS 6.5+', '2 năm kinh nghiệm dạy tiếng Anh', NULL),
(3, 7, 'Nguyễn Văn A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 8, 'Tran Minh Luan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `GiaSu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `GiaoDich`
--

DROP TABLE IF EXISTS `GiaoDich`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `GiaoDich` (
  `GiaoDichID` bigint NOT NULL AUTO_INCREMENT,
  `LopYeuCauID` int NOT NULL,
  `TaiKhoanID` int NOT NULL,
  `SoTien` double NOT NULL,
  `ThoiGian` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `TrangThai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ChoXacNhan',
  `GhiChu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`GiaoDichID`),
  KEY `LopYeuCauID` (`LopYeuCauID`),
  KEY `TaiKhoanID` (`TaiKhoanID`),
  CONSTRAINT `GiaoDich_ibfk_1` FOREIGN KEY (`LopYeuCauID`) REFERENCES `LopHocYeuCau` (`LopYeuCauID`) ON DELETE CASCADE,
  CONSTRAINT `GiaoDich_ibfk_2` FOREIGN KEY (`TaiKhoanID`) REFERENCES `TaiKhoan` (`TaiKhoanID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `GiaoDich`
--

LOCK TABLES `GiaoDich` WRITE;
/*!40000 ALTER TABLE `GiaoDich` DISABLE KEYS */;
INSERT INTO `GiaoDich` VALUES (1,1,4,200000,'2025-10-07 18:00:00','ThanhCong','Thanh toán buổi 1'),(2,1,4,200000,'2025-10-09 18:00:00','ChoXacNhan','Thanh toán buổi 2 (chờ đối soát)'),(3,2,6,180000,'2025-10-10 17:30:00','ChoXacNhan','Đặt cọc buổi đầu');
/*!40000 ALTER TABLE `GiaoDich` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `KhieuNai`
--

DROP TABLE IF EXISTS `KhieuNai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `KhieuNai` (
  `KhieuNaiID` int NOT NULL AUTO_INCREMENT,
  `TaiKhoanID` int NOT NULL,
  `NoiDung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `TrangThai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TiepNhan',
  `GiaiQuyet` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `PhanHoi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GiaoDichID` bigint DEFAULT NULL,
  `LopYeuCauID` int DEFAULT NULL,
  PRIMARY KEY (`KhieuNaiID`),
  KEY `TaiKhoanID` (`TaiKhoanID`),
  KEY `GiaoDichID` (`GiaoDichID`),
  KEY `LopYeuCauID` (`LopYeuCauID`),
  CONSTRAINT `KhieuNai_ibfk_1` FOREIGN KEY (`TaiKhoanID`) REFERENCES `TaiKhoan` (`TaiKhoanID`) ON DELETE CASCADE,
  CONSTRAINT `KhieuNai_ibfk_2` FOREIGN KEY (`GiaoDichID`) REFERENCES `GiaoDich` (`GiaoDichID`) ON DELETE SET NULL,
  CONSTRAINT `KhieuNai_ibfk_3` FOREIGN KEY (`LopYeuCauID`) REFERENCES `LopHocYeuCau` (`LopYeuCauID`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `KhieuNai`
--

LOCK TABLES `KhieuNai` WRITE;
/*!40000 ALTER TABLE `KhieuNai` DISABLE KEYS */;
INSERT INTO `KhieuNai` VALUES (1,6,'Chưa tìm được gia sư phù hợp cho lớp YC2.','2025-10-10 20:00:00','TiepNhan',NULL,NULL,NULL,2),(2,4,'Thanh toán buổi 2 đã chuyển nhưng chưa xác nhận.','2025-10-09 21:30:00','DangXuLy',NULL,NULL,2,1);
/*!40000 ALTER TABLE `KhieuNai` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `KhoiLop`
--

DROP TABLE IF EXISTS `KhoiLop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `KhoiLop` (
  `KhoiLopID` int NOT NULL AUTO_INCREMENT,
  `BacHoc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`KhoiLopID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `KhoiLop`
--

LOCK TABLES `KhoiLop` WRITE;
/*!40000 ALTER TABLE `KhoiLop` DISABLE KEYS */;
INSERT INTO `KhoiLop` VALUES (1,'Lớp 1'),(2,'Lớp 2'),(3,'Lớp 3'),(4,'Lớp 4'),(5,'Lớp 5'),(6,'Lớp 6'),(7,'Lớp 7'),(8,'Lớp 8'),(9,'Lớp 9'),(10,'Lớp 10'),(11,'Lớp 11'),(12,'Lớp 12');
/*!40000 ALTER TABLE `KhoiLop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LichHoc`
--

DROP TABLE IF EXISTS `LichHoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `LichHoc` (
  `LichHocID` int NOT NULL AUTO_INCREMENT,
  `LichHocGocID` int DEFAULT NULL,
  `LopYeuCauID` int NOT NULL,
  `ThoiGianBatDau` time NOT NULL,
  `ThoiGianKetThuc` time NOT NULL,
  `NgayHoc` date NOT NULL,
  `TrangThai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'DangDay',
  `DuongDan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `IsLapLai` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`LichHocID`),
  KEY `LopYeuCauID` (`LopYeuCauID`),
  KEY `idx_lichhoc_goc` (`LichHocGocID`),
  KEY `idx_lichhoc_lap_lai` (`IsLapLai`),
  CONSTRAINT `fk_lichhoc_lichhocgoc` FOREIGN KEY (`LichHocGocID`) REFERENCES `LichHoc` (`LichHocID`) ON DELETE CASCADE,
  CONSTRAINT `LichHoc_ibfk_1` FOREIGN KEY (`LopYeuCauID`) REFERENCES `LopHocYeuCau` (`LopYeuCauID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LichHoc`
--

LOCK TABLES `LichHoc` WRITE;
/*!40000 ALTER TABLE `LichHoc` DISABLE KEYS */;
INSERT INTO `LichHoc` VALUES (4,4,1,'08:00:00','09:30:00','2025-11-05','SapToi','https://meet.example.com/toan-b1','2025-11-04 19:20:43',1),(5,4,1,'08:00:00','09:30:00','2025-11-12','SapToi','https://meet.example.com/toan-b2','2025-11-04 19:20:43',1),(6,4,1,'08:00:00','09:30:00','2025-11-19','SapToi','https://meet.example.com/toan-b3','2025-11-04 19:20:43',1),(7,4,1,'08:00:00','09:30:00','2025-11-26','SapToi','https://meet.example.com/toan-b4','2025-11-04 19:20:43',1),(8,4,1,'08:00:00','09:30:00','2025-12-03','SapToi','https://meet.example.com/toan-b5','2025-11-04 19:20:43',1),(9,4,1,'08:00:00','09:30:00','2025-12-10','SapToi','https://meet.example.com/toan-b6','2025-11-04 19:20:43',1),(10,4,1,'08:00:00','09:30:00','2025-12-17','SapToi','https://meet.example.com/toan-b7','2025-11-04 19:20:43',1),(11,4,1,'08:00:00','09:30:00','2025-12-24','SapToi','https://meet.example.com/toan-b8','2025-11-04 19:20:43',1);
/*!40000 ALTER TABLE `LichHoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LopHocYeuCau`
--

DROP TABLE IF EXISTS `LopHocYeuCau`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `LopHocYeuCau` (
  `LopYeuCauID` int NOT NULL AUTO_INCREMENT,
  `NguoiHocID` int NOT NULL,
  `GiaSuID` int DEFAULT NULL,
  `HinhThuc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `HocPhi` double NOT NULL,
  `ThoiLuong` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `TrangThai` enum('ChoDuyet','TimGiaSu','DangChonGiaSu','DangHoc','HoanThanh','Huy') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ChoDuyet',
  `SoLuong` int DEFAULT '1',
  `MoTa` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `MonID` int NOT NULL,
  `KhoiLopID` int NOT NULL,
  `DoiTuongID` int NOT NULL,
  `ThoiGianDayID` int NOT NULL,
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`LopYeuCauID`),
  KEY `NguoiHocID` (`NguoiHocID`),
  KEY `GiaSuID` (`GiaSuID`),
  KEY `MonID` (`MonID`),
  KEY `KhoiLopID` (`KhoiLopID`),
  KEY `DoiTuongID` (`DoiTuongID`),
  KEY `ThoiGianDayID` (`ThoiGianDayID`),
  CONSTRAINT `LopHocYeuCau_ibfk_1` FOREIGN KEY (`NguoiHocID`) REFERENCES `NguoiHoc` (`NguoiHocID`) ON DELETE CASCADE,
  CONSTRAINT `LopHocYeuCau_ibfk_2` FOREIGN KEY (`GiaSuID`) REFERENCES `GiaSu` (`GiaSuID`),
  CONSTRAINT `LopHocYeuCau_ibfk_3` FOREIGN KEY (`MonID`) REFERENCES `MonHoc` (`MonID`),
  CONSTRAINT `LopHocYeuCau_ibfk_4` FOREIGN KEY (`KhoiLopID`) REFERENCES `KhoiLop` (`KhoiLopID`),
  CONSTRAINT `LopHocYeuCau_ibfk_5` FOREIGN KEY (`DoiTuongID`) REFERENCES `DoiTuong` (`DoiTuongID`),
  CONSTRAINT `LopHocYeuCau_ibfk_6` FOREIGN KEY (`ThoiGianDayID`) REFERENCES `ThoiGianDay` (`ThoiGianDayID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LopHocYeuCau`
--

LOCK TABLES `LopHocYeuCau` WRITE;
/*!40000 ALTER TABLE `LopHocYeuCau` DISABLE KEYS */;
INSERT INTO `LopHocYeuCau` VALUES (1,1,1,'Online',200000,'90 phút/buổi','DangHoc',1,'Ôn thi giữa kỳ',1,2,1,1,'2025-10-05 08:00:00'),(2,2,NULL,'Offline',180000,'120 phút/buổi','TimGiaSu',1,'Giao tiếp cơ bản',2,1,1,2,'2025-10-06 09:00:00'),(3,3,2,'Online',250000,'120 phút/buổi','ChoDuyet',2,'Ôn luyện chương Dao động',3,3,1,3,'2025-10-06 10:00:00');
/*!40000 ALTER TABLE `LopHocYeuCau` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MonHoc`
--

DROP TABLE IF EXISTS `MonHoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `MonHoc` (
  `MonID` int NOT NULL AUTO_INCREMENT,
  `TenMon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`MonID`),
  UNIQUE KEY `TenMon` (`TenMon`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MonHoc`
--

LOCK TABLES `MonHoc` WRITE;
/*!40000 ALTER TABLE `MonHoc` DISABLE KEYS */;
INSERT INTO `MonHoc` VALUES (13,'Âm Nhạc'),(11,'Công Nghệ'),(8,'Địa Lý'),(10,'Giáo Dục Công Dân'),(5,'Hóa Học'),(15,'Khoa Học Tự Nhiên'),(16,'Khoa Học Xã Hội'),(18,'Kỹ năng giao tiếp'),(19,'Kỹ năng thuyết trình'),(25,'Lập trình C++'),(26,'Lập trình Python'),(7,'Lịch Sử'),(23,'Luyện thi đại học'),(22,'Luyện thi IELTS'),(21,'Luyện thi TOEIC'),(24,'Luyện thi vào 10'),(20,'Luyện viết chữ đẹp'),(12,'Mỹ Thuật'),(2,'Ngữ Văn'),(6,'Sinh Học'),(14,'Thể Dục'),(3,'Tiếng Anh'),(29,'Tiếng Hàn'),(28,'Tiếng Nhật'),(30,'Tiếng Trung'),(9,'Tin Học'),(27,'Tin học cơ bản'),(17,'Tin học Văn phòng'),(1,'Toán'),(4,'Vật Lý');
/*!40000 ALTER TABLE `MonHoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `NguoiHoc`
--

DROP TABLE IF EXISTS `NguoiHoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `NguoiHoc` (
  `NguoiHocID` int NOT NULL AUTO_INCREMENT,
  `TaiKhoanID` int NOT NULL,
  `HoTen` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `NgaySinh` date DEFAULT NULL,
  `GioiTinh` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DiaChi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `AnhDaiDien` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`NguoiHocID`),
  KEY `TaiKhoanID` (`TaiKhoanID`),
  CONSTRAINT `NguoiHoc_ibfk_1` FOREIGN KEY (`TaiKhoanID`) REFERENCES `TaiKhoan` (`TaiKhoanID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `NguoiHoc`
--

LOCK TABLES `NguoiHoc` WRITE;
/*!40000 ALTER TABLE `NguoiHoc` DISABLE KEYS */;
INSERT INTO `NguoiHoc` VALUES (1,4,'Lê Minh C','2010-03-12','Nam','Thủ Đức, TP.HCM',NULL),(2,5,'Phạm Gia D','2008-11-05','Nam','Q.7, TP.HCM',NULL),(3,6,'Hoàng Anh E','2006-01-28','Nữ','Q.5, TP.HCM',NULL);
/*!40000 ALTER TABLE `NguoiHoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PhanQuyen`
--

DROP TABLE IF EXISTS `PhanQuyen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `PhanQuyen` (
  `TaiKhoanID` int NOT NULL,
  `VaiTroID` int NOT NULL,
  PRIMARY KEY (`TaiKhoanID`,`VaiTroID`),
  KEY `VaiTroID` (`VaiTroID`),
  CONSTRAINT `PhanQuyen_ibfk_1` FOREIGN KEY (`TaiKhoanID`) REFERENCES `TaiKhoan` (`TaiKhoanID`) ON DELETE CASCADE,
  CONSTRAINT `PhanQuyen_ibfk_2` FOREIGN KEY (`VaiTroID`) REFERENCES `VaiTro` (`VaiTroID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PhanQuyen`
--

LOCK TABLES `PhanQuyen` WRITE;
/*!40000 ALTER TABLE `PhanQuyen` DISABLE KEYS */;
INSERT INTO `PhanQuyen` VALUES (1,1),(2,2),(3,2),(7,2),(8,2),(4,3),(5,3),(6,3);
/*!40000 ALTER TABLE `PhanQuyen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TaiKhoan`
--

DROP TABLE IF EXISTS `TaiKhoan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `TaiKhoan` (
  `TaiKhoanID` int NOT NULL AUTO_INCREMENT,
  `Email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `MatKhauHash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `SoDienThoai` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TrangThai` tinyint NOT NULL DEFAULT '1',
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TaiKhoanID`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `SoDienThoai` (`SoDienThoai`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TaiKhoan`
--

LOCK TABLES `TaiKhoan` WRITE;
/*!40000 ALTER TABLE `TaiKhoan` DISABLE KEYS */;
INSERT INTO `TaiKhoan` VALUES (1,'admin@site.com','$2y$10$92IXUNpkjO0rOQ5byMi...','0900000001',1,'2025-10-01 09:00:00'),(2,'tutor1@site.com','$2y$10$92IXUNpkjO0rOQ5byMi...','0900000003',1,'2025-10-01 09:10:00'),(3,'tutor2@site.com','$2y$10$92IXUNpkjO0rOQ5byMi...','0900000004',1,'2025-10-01 09:15:00'),(4,'student1@site.com','$2y$10$92IXUNpkjO0rOQ5byMi...','0900000005',1,'2025-10-01 09:20:00'),(5,'student2@site.com','$2y$10$92IXUNpkjO0rOQ5byMi...','0900000006',1,'2025-10-01 09:25:00'),(6,'student3@site.com','$2y$10$92IXUNpkjO0rOQ5byMi...','0900000007',1,'2025-10-01 09:30:00'),(7,'test@example.com','$2y$12$mnws/ZvjrE6nRMfej4PJN.J9ciUa/zXVWi9iS6xPI9/NdwHynz8Vy','0912345678',1,'2025-11-04 19:27:58'),(8,'1@gmail.com','$2y$12$kiusU12D7xQY0//81mc55OmvsCc6bp4dJgForFtkVqUxy6VlTH36C','0934140224',1,'2025-11-06 09:35:34');
/*!40000 ALTER TABLE `TaiKhoan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ThoiGianDay`
--

DROP TABLE IF EXISTS `ThoiGianDay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ThoiGianDay` (
  `ThoiGianDayID` int NOT NULL AUTO_INCREMENT,
  `SoBuoi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `BuoiHoc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ThoiLuong` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`ThoiGianDayID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ThoiGianDay`
--

LOCK TABLES `ThoiGianDay` WRITE;
/*!40000 ALTER TABLE `ThoiGianDay` DISABLE KEYS */;
INSERT INTO `ThoiGianDay` VALUES (1,'3','T2-T4-T6 tối','90 phút'),(2,'2','T3-T5 tối','120 phút'),(3,'1','Cuối tuần','120 phút');
/*!40000 ALTER TABLE `ThoiGianDay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `VaiTro`
--

DROP TABLE IF EXISTS `VaiTro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `VaiTro` (
  `VaiTroID` int NOT NULL AUTO_INCREMENT,
  `TenVaiTro` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `MoTa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`VaiTroID`),
  UNIQUE KEY `TenVaiTro` (`TenVaiTro`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `VaiTro`
--

LOCK TABLES `VaiTro` WRITE;
/*!40000 ALTER TABLE `VaiTro` DISABLE KEYS */;
INSERT INTO `VaiTro` VALUES (1,'Admin','Quản trị hệ thống'),(2,'GiaSu','Tài khoản gia sư'),(3,'NguoiHoc','Tài khoản người học');
/*!40000 ALTER TABLE `VaiTro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `YeuCauNhanLop`
--

DROP TABLE IF EXISTS `YeuCauNhanLop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `YeuCauNhanLop` (
  `YeuCauID` int NOT NULL AUTO_INCREMENT,
  `LopYeuCauID` int NOT NULL,
  `GiaSuID` int NOT NULL,
  `NguoiGuiTaiKhoanID` int NOT NULL,
  `VaiTroNguoiGui` enum('GiaSu','NguoiHoc') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `TrangThai` enum('Pending','Accepted','Rejected','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `GhiChu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `NgayCapNhat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`YeuCauID`),
  KEY `GiaSuID` (`GiaSuID`),
  KEY `NguoiGuiTaiKhoanID` (`NguoiGuiTaiKhoanID`),
  KEY `idx_lop_giasu` (`LopYeuCauID`,`GiaSuID`),
  CONSTRAINT `YeuCauNhanLop_ibfk_1` FOREIGN KEY (`LopYeuCauID`) REFERENCES `LopHocYeuCau` (`LopYeuCauID`) ON DELETE CASCADE,
  CONSTRAINT `YeuCauNhanLop_ibfk_2` FOREIGN KEY (`GiaSuID`) REFERENCES `GiaSu` (`GiaSuID`) ON DELETE CASCADE,
  CONSTRAINT `YeuCauNhanLop_ibfk_3` FOREIGN KEY (`NguoiGuiTaiKhoanID`) REFERENCES `TaiKhoan` (`TaiKhoanID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `YeuCauNhanLop`
--

LOCK TABLES `YeuCauNhanLop` WRITE;
/*!40000 ALTER TABLE `YeuCauNhanLop` DISABLE KEYS */;
INSERT INTO `YeuCauNhanLop` VALUES (1,2,1,2,'GiaSu','Pending','Tôi muốn nhận lớp này','2025-11-02 13:34:06','2025-11-02 13:34:06'),(2,3,2,4,'NguoiHoc','Pending','Mời cô đến dạy cho con tôi','2025-11-02 13:34:06','2025-11-02 13:34:06');
/*!40000 ALTER TABLE `YeuCauNhanLop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025_10_22_040803_create_personal_access_tokens_table',1),(2,'2025_10_31_075505_create_sessions_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (2,'App\\Models\\TaiKhoan',8,'1@gmail.com','1f2bc4b075048e5cbf2f7f861a86e3fe50b93acc45f71b49acb29798c5bc5350','[\"*\"]','2025-11-06 09:43:40',NULL,'2025-11-06 09:35:48','2025-11-06 09:43:40');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-06 16:57:09
