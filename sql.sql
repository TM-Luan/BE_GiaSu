CREATE DATABASE  IF NOT EXISTS `giasu` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `giasu`;
-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: giasu
-- ------------------------------------------------------
-- Server version	8.0.43

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
-- Table structure for table `danhgia`
--

DROP TABLE IF EXISTS `danhgia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `danhgia` (
  `DanhGiaID` int NOT NULL AUTO_INCREMENT,
  `LopYeuCauID` int NOT NULL,
  `TaiKhoanID` int NOT NULL,
  `DiemSo` double NOT NULL,
  `BinhLuan` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayDanhGia` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `LanSua` int NOT NULL DEFAULT '0' COMMENT 'Số lần đã sửa đánh giá (0=chưa sửa, 1=đã sửa 1 lần)',
  PRIMARY KEY (`DanhGiaID`),
  KEY `LopYeuCauID` (`LopYeuCauID`),
  KEY `TaiKhoanID` (`TaiKhoanID`),
  CONSTRAINT `danhgia_ibfk_1` FOREIGN KEY (`LopYeuCauID`) REFERENCES `lophocyeucau` (`LopYeuCauID`) ON DELETE CASCADE,
  CONSTRAINT `danhgia_ibfk_2` FOREIGN KEY (`TaiKhoanID`) REFERENCES `taikhoan` (`TaiKhoanID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `danhgia`
--

LOCK TABLES `danhgia` WRITE;
/*!40000 ALTER TABLE `danhgia` DISABLE KEYS */;
INSERT INTO `danhgia` VALUES (1,1,4,4.5,'Gia sư dạy dễ hiểu, đúng giờ.','2025-10-07 21:00:00',0),(2,1,2,5,'Học viên hợp tác tốt, chuẩn bị bài.','2025-10-07 21:05:00',0);
/*!40000 ALTER TABLE `danhgia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doituong`
--

DROP TABLE IF EXISTS `doituong`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doituong` (
  `DoiTuongID` int NOT NULL AUTO_INCREMENT,
  `TenDoiTuong` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`DoiTuongID`),
  UNIQUE KEY `TenDoiTuong` (`TenDoiTuong`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doituong`
--

LOCK TABLES `doituong` WRITE;
/*!40000 ALTER TABLE `doituong` DISABLE KEYS */;
INSERT INTO `doituong` VALUES (1,'Học sinh'),(2,'Sinh viên');
/*!40000 ALTER TABLE `doituong` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `giaodich`
--

DROP TABLE IF EXISTS `giaodich`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `giaodich` (
  `GiaoDichID` bigint NOT NULL AUTO_INCREMENT,
  `LopYeuCauID` int NOT NULL,
  `TaiKhoanID` int NOT NULL,
  `SoTien` double NOT NULL,
  `ThoiGian` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `TrangThai` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ChoXacNhan',
  `GhiChu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`GiaoDichID`),
  KEY `LopYeuCauID` (`LopYeuCauID`),
  KEY `TaiKhoanID` (`TaiKhoanID`),
  CONSTRAINT `giaodich_ibfk_1` FOREIGN KEY (`LopYeuCauID`) REFERENCES `lophocyeucau` (`LopYeuCauID`) ON DELETE CASCADE,
  CONSTRAINT `giaodich_ibfk_2` FOREIGN KEY (`TaiKhoanID`) REFERENCES `taikhoan` (`TaiKhoanID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `giaodich`
--

LOCK TABLES `giaodich` WRITE;
/*!40000 ALTER TABLE `giaodich` DISABLE KEYS */;
INSERT INTO `giaodich` VALUES (1,1,4,200000,'2025-10-07 18:00:00','ThanhCong','Thanh toán buổi 1'),(2,1,4,200000,'2025-10-09 18:00:00','ChoXacNhan','Thanh toán buổi 2 (chờ đối soát)'),(3,2,6,180000,'2025-10-10 17:30:00','ChoXacNhan','Đặt cọc buổi đầu');
/*!40000 ALTER TABLE `giaodich` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `giasu`
--

DROP TABLE IF EXISTS `giasu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `giasu` (
  `GiaSuID` int NOT NULL AUTO_INCREMENT,
  `TaiKhoanID` int NOT NULL,
  `HoTen` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DiaChi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GioiTinh` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgaySinh` date DEFAULT NULL,
  `BangCap` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `KinhNghiem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `AnhDaiDien` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`GiaSuID`),
  KEY `TaiKhoanID` (`TaiKhoanID`),
  CONSTRAINT `giasu_ibfk_1` FOREIGN KEY (`TaiKhoanID`) REFERENCES `taikhoan` (`TaiKhoanID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `giasu`
--

LOCK TABLES `giasu` WRITE;
/*!40000 ALTER TABLE `giasu` DISABLE KEYS */;
INSERT INTO `giasu` VALUES (1,2,'Nguyễn Văn A','Q.1, TP.HCM','Nam','1995-05-10','Cử nhân Sư phạm Toán','3 năm',NULL),(2,3,'Trần Thị B','Q.3, TP.HCM','Nữ','1996-08-22','Cử nhân Ngôn ngữ Anh','2 năm',NULL);
/*!40000 ALTER TABLE `giasu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `khieunai`
--

DROP TABLE IF EXISTS `khieunai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `khieunai` (
  `KhieuNaiID` int NOT NULL AUTO_INCREMENT,
  `TaiKhoanID` int NOT NULL,
  `NoiDung` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `TrangThai` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TiepNhan',
  `GiaiQuyet` text COLLATE utf8mb4_unicode_ci,
  `PhanHoi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GiaoDichID` bigint DEFAULT NULL,
  `LopYeuCauID` int DEFAULT NULL,
  PRIMARY KEY (`KhieuNaiID`),
  KEY `TaiKhoanID` (`TaiKhoanID`),
  KEY `GiaoDichID` (`GiaoDichID`),
  KEY `LopYeuCauID` (`LopYeuCauID`),
  CONSTRAINT `khieunai_ibfk_1` FOREIGN KEY (`TaiKhoanID`) REFERENCES `taikhoan` (`TaiKhoanID`) ON DELETE CASCADE,
  CONSTRAINT `khieunai_ibfk_2` FOREIGN KEY (`GiaoDichID`) REFERENCES `giaodich` (`GiaoDichID`) ON DELETE SET NULL,
  CONSTRAINT `khieunai_ibfk_3` FOREIGN KEY (`LopYeuCauID`) REFERENCES `lophocyeucau` (`LopYeuCauID`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `khieunai`
--

LOCK TABLES `khieunai` WRITE;
/*!40000 ALTER TABLE `khieunai` DISABLE KEYS */;
INSERT INTO `khieunai` VALUES (1,6,'Chưa tìm được gia sư phù hợp cho lớp YC2.','2025-10-10 20:00:00','TiepNhan',NULL,NULL,NULL,2),(2,4,'Thanh toán buổi 2 đã chuyển nhưng chưa xác nhận.','2025-10-09 21:30:00','DangXuLy',NULL,NULL,2,1);
/*!40000 ALTER TABLE `khieunai` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `khoilop`
--

DROP TABLE IF EXISTS `khoilop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `khoilop` (
  `KhoiLopID` int NOT NULL AUTO_INCREMENT,
  `BacHoc` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`KhoiLopID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `khoilop`
--

LOCK TABLES `khoilop` WRITE;
/*!40000 ALTER TABLE `khoilop` DISABLE KEYS */;
INSERT INTO `khoilop` VALUES (1,'Lớp 1'),(2,'Lớp 2'),(3,'Lớp 3'),(4,'Lớp 4'),(5,'Lớp 5'),(6,'Lớp 6'),(7,'Lớp 7'),(8,'Lớp 8'),(9,'Lớp 9'),(10,'Lớp 10'),(11,'Lớp 11'),(12,'Lớp 12');
/*!40000 ALTER TABLE `khoilop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lichhoc`
--

DROP TABLE IF EXISTS `lichhoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lichhoc` (
  `LichHocID` int NOT NULL AUTO_INCREMENT,
  `LichHocGocID` int DEFAULT NULL,
  `LopYeuCauID` int NOT NULL,
  `ThoiGianBatDau` time NOT NULL,
  `ThoiGianKetThuc` time NOT NULL,
  `NgayHoc` date NOT NULL,
  `TrangThai` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'DangDay',
  `DuongDan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `IsLapLai` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`LichHocID`),
  KEY `LopYeuCauID` (`LopYeuCauID`),
  KEY `idx_lichhoc_goc` (`LichHocGocID`),
  KEY `idx_lichhoc_lap_lai` (`IsLapLai`),
  CONSTRAINT `fk_lichhoc_lichhocgoc` FOREIGN KEY (`LichHocGocID`) REFERENCES `lichhoc` (`LichHocID`) ON DELETE CASCADE,
  CONSTRAINT `lichhoc_ibfk_1` FOREIGN KEY (`LopYeuCauID`) REFERENCES `lophocyeucau` (`LopYeuCauID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lichhoc`
--

LOCK TABLES `lichhoc` WRITE;
/*!40000 ALTER TABLE `lichhoc` DISABLE KEYS */;
INSERT INTO `lichhoc` VALUES (1,NULL,1,'19:00:00','20:30:00','2025-10-07','DaHoc','https://meet.example.com/yc1-b1','2025-11-02 13:34:06',0),(2,NULL,1,'19:00:00','20:30:00','2025-10-09','SapToi','https://meet.example.com/yc1-b2','2025-11-02 13:34:06',0),(3,NULL,2,'18:30:00','20:30:00','2025-10-10','SapToi',NULL,'2025-11-02 13:34:06',0);
/*!40000 ALTER TABLE `lichhoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lophocyeucau`
--

DROP TABLE IF EXISTS `lophocyeucau`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lophocyeucau` (
  `LopYeuCauID` int NOT NULL AUTO_INCREMENT,
  `NguoiHocID` int NOT NULL,
  `GiaSuID` int DEFAULT NULL,
  `HinhThuc` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `HocPhi` double NOT NULL,
  `ThoiLuong` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TrangThai` enum('ChoDuyet','TimGiaSu','DangChonGiaSu','DangHoc','HoanThanh','Huy') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ChoDuyet',
  `SoLuong` int DEFAULT '1',
  `MoTa` text COLLATE utf8mb4_unicode_ci,
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
  CONSTRAINT `lophocyeucau_ibfk_1` FOREIGN KEY (`NguoiHocID`) REFERENCES `nguoihoc` (`NguoiHocID`) ON DELETE CASCADE,
  CONSTRAINT `lophocyeucau_ibfk_2` FOREIGN KEY (`GiaSuID`) REFERENCES `giasu` (`GiaSuID`),
  CONSTRAINT `lophocyeucau_ibfk_3` FOREIGN KEY (`MonID`) REFERENCES `monhoc` (`MonID`),
  CONSTRAINT `lophocyeucau_ibfk_4` FOREIGN KEY (`KhoiLopID`) REFERENCES `khoilop` (`KhoiLopID`),
  CONSTRAINT `lophocyeucau_ibfk_5` FOREIGN KEY (`DoiTuongID`) REFERENCES `doituong` (`DoiTuongID`),
  CONSTRAINT `lophocyeucau_ibfk_6` FOREIGN KEY (`ThoiGianDayID`) REFERENCES `thoigianday` (`ThoiGianDayID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lophocyeucau`
--

LOCK TABLES `lophocyeucau` WRITE;
/*!40000 ALTER TABLE `lophocyeucau` DISABLE KEYS */;
INSERT INTO `lophocyeucau` VALUES (1,1,1,'Online',200000,'90 phút/buổi','DangHoc',1,'Ôn thi giữa kỳ',1,2,1,1,'2025-10-05 08:00:00'),(2,2,NULL,'Offline',180000,'120 phút/buổi','TimGiaSu',1,'Giao tiếp cơ bản',2,1,1,2,'2025-10-06 09:00:00'),(3,3,2,'Online',250000,'120 phút/buổi','ChoDuyet',2,'Ôn luyện chương Dao động',3,3,1,3,'2025-10-06 10:00:00');
/*!40000 ALTER TABLE `lophocyeucau` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `monhoc`
--

DROP TABLE IF EXISTS `monhoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `monhoc` (
  `MonID` int NOT NULL AUTO_INCREMENT,
  `TenMon` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`MonID`),
  UNIQUE KEY `TenMon` (`TenMon`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monhoc`
--

LOCK TABLES `monhoc` WRITE;
/*!40000 ALTER TABLE `monhoc` DISABLE KEYS */;
INSERT INTO `monhoc` VALUES (13,'Âm Nhạc'),(11,'Công Nghệ'),(8,'Địa Lý'),(10,'Giáo Dục Công Dân'),(5,'Hóa Học'),(15,'Khoa Học Tự Nhiên'),(16,'Khoa Học Xã Hội'),(18,'Kỹ năng giao tiếp'),(19,'Kỹ năng thuyết trình'),(25,'Lập trình C++'),(26,'Lập trình Python'),(7,'Lịch Sử'),(23,'Luyện thi đại học'),(22,'Luyện thi IELTS'),(21,'Luyện thi TOEIC'),(24,'Luyện thi vào 10'),(20,'Luyện viết chữ đẹp'),(12,'Mỹ Thuật'),(2,'Ngữ Văn'),(6,'Sinh Học'),(14,'Thể Dục'),(3,'Tiếng Anh'),(29,'Tiếng Hàn'),(28,'Tiếng Nhật'),(30,'Tiếng Trung'),(9,'Tin Học'),(27,'Tin học cơ bản'),(17,'Tin học Văn phòng'),(1,'Toán'),(4,'Vật Lý');
/*!40000 ALTER TABLE `monhoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nguoihoc`
--

DROP TABLE IF EXISTS `nguoihoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nguoihoc` (
  `NguoiHocID` int NOT NULL AUTO_INCREMENT,
  `TaiKhoanID` int NOT NULL,
  `HoTen` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NgaySinh` date DEFAULT NULL,
  `GioiTinh` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DiaChi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `AnhDaiDien` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`NguoiHocID`),
  KEY `TaiKhoanID` (`TaiKhoanID`),
  CONSTRAINT `nguoihoc_ibfk_1` FOREIGN KEY (`TaiKhoanID`) REFERENCES `taikhoan` (`TaiKhoanID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nguoihoc`
--

LOCK TABLES `nguoihoc` WRITE;
/*!40000 ALTER TABLE `nguoihoc` DISABLE KEYS */;
INSERT INTO `nguoihoc` VALUES (1,4,'Lê Minh C','2010-03-12','Nam','Thủ Đức, TP.HCM',NULL),(2,5,'Phạm Gia D','2008-11-05','Nam','Q.7, TP.HCM',NULL),(3,6,'Hoàng Anh E','2006-01-28','Nữ','Q.5, TP.HCM',NULL);
/*!40000 ALTER TABLE `nguoihoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phanquyen`
--

DROP TABLE IF EXISTS `phanquyen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phanquyen` (
  `TaiKhoanID` int NOT NULL,
  `VaiTroID` int NOT NULL,
  PRIMARY KEY (`TaiKhoanID`,`VaiTroID`),
  KEY `VaiTroID` (`VaiTroID`),
  CONSTRAINT `phanquyen_ibfk_1` FOREIGN KEY (`TaiKhoanID`) REFERENCES `taikhoan` (`TaiKhoanID`) ON DELETE CASCADE,
  CONSTRAINT `phanquyen_ibfk_2` FOREIGN KEY (`VaiTroID`) REFERENCES `vaitro` (`VaiTroID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phanquyen`
--

LOCK TABLES `phanquyen` WRITE;
/*!40000 ALTER TABLE `phanquyen` DISABLE KEYS */;
INSERT INTO `phanquyen` VALUES (1,1),(2,2),(3,2),(4,3),(5,3),(6,3);
/*!40000 ALTER TABLE `phanquyen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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

--
-- Table structure for table `taikhoan`
--

DROP TABLE IF EXISTS `taikhoan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `taikhoan` (
  `TaiKhoanID` int NOT NULL AUTO_INCREMENT,
  `Email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `MatKhauHash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SoDienThoai` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TrangThai` tinyint NOT NULL DEFAULT '1',
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TaiKhoanID`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `SoDienThoai` (`SoDienThoai`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taikhoan`
--

LOCK TABLES `taikhoan` WRITE;
/*!40000 ALTER TABLE `taikhoan` DISABLE KEYS */;
INSERT INTO `taikhoan` VALUES (1,'admin@site.com','$2y$10$92IXUNpkjO0rOQ5byMi...','0900000001',1,'2025-10-01 09:00:00'),(2,'tutor1@site.com','$2y$10$92IXUNpkjO0rOQ5byMi...','0900000003',1,'2025-10-01 09:10:00'),(3,'tutor2@site.com','$2y$10$92IXUNpkjO0rOQ5byMi...','0900000004',1,'2025-10-01 09:15:00'),(4,'student1@site.com','$2y$10$92IXUNpkjO0rOQ5byMi...','0900000005',1,'2025-10-01 09:20:00'),(5,'student2@site.com','$2y$10$92IXUNpkjO0rOQ5byMi...','0900000006',1,'2025-10-01 09:25:00'),(6,'student3@site.com','$2y$10$92IXUNpkjO0rOQ5byMi...','0900000007',1,'2025-10-01 09:30:00');
/*!40000 ALTER TABLE `taikhoan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `thoigianday`
--

DROP TABLE IF EXISTS `thoigianday`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `thoigianday` (
  `ThoiGianDayID` int NOT NULL AUTO_INCREMENT,
  `SoBuoi` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `BuoiHoc` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ThoiLuong` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`ThoiGianDayID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `thoigianday`
--

LOCK TABLES `thoigianday` WRITE;
/*!40000 ALTER TABLE `thoigianday` DISABLE KEYS */;
INSERT INTO `thoigianday` VALUES (1,'3','T2-T4-T6 tối','90 phút'),(2,'2','T3-T5 tối','120 phút'),(3,'1','Cuối tuần','120 phút');
/*!40000 ALTER TABLE `thoigianday` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vaitro`
--

DROP TABLE IF EXISTS `vaitro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vaitro` (
  `VaiTroID` int NOT NULL AUTO_INCREMENT,
  `TenVaiTro` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `MoTa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`VaiTroID`),
  UNIQUE KEY `TenVaiTro` (`TenVaiTro`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vaitro`
--

LOCK TABLES `vaitro` WRITE;
/*!40000 ALTER TABLE `vaitro` DISABLE KEYS */;
INSERT INTO `vaitro` VALUES (1,'Admin','Quản trị hệ thống'),(2,'GiaSu','Tài khoản gia sư'),(3,'NguoiHoc','Tài khoản người học');
/*!40000 ALTER TABLE `vaitro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yeucaunhanlop`
--

DROP TABLE IF EXISTS `yeucaunhanlop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `yeucaunhanlop` (
  `YeuCauID` int NOT NULL AUTO_INCREMENT,
  `LopYeuCauID` int NOT NULL,
  `GiaSuID` int NOT NULL,
  `NguoiGuiTaiKhoanID` int NOT NULL,
  `VaiTroNguoiGui` enum('GiaSu','NguoiHoc') COLLATE utf8mb4_unicode_ci NOT NULL,
  `TrangThai` enum('Pending','Accepted','Rejected','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `GhiChu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `NgayCapNhat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`YeuCauID`),
  KEY `GiaSuID` (`GiaSuID`),
  KEY `NguoiGuiTaiKhoanID` (`NguoiGuiTaiKhoanID`),
  KEY `idx_lop_giasu` (`LopYeuCauID`,`GiaSuID`),
  CONSTRAINT `yeucaunhanlop_ibfk_1` FOREIGN KEY (`LopYeuCauID`) REFERENCES `lophocyeucau` (`LopYeuCauID`) ON DELETE CASCADE,
  CONSTRAINT `yeucaunhanlop_ibfk_2` FOREIGN KEY (`GiaSuID`) REFERENCES `giasu` (`GiaSuID`) ON DELETE CASCADE,
  CONSTRAINT `yeucaunhanlop_ibfk_3` FOREIGN KEY (`NguoiGuiTaiKhoanID`) REFERENCES `taikhoan` (`TaiKhoanID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yeucaunhanlop`
--

LOCK TABLES `yeucaunhanlop` WRITE;
/*!40000 ALTER TABLE `yeucaunhanlop` DISABLE KEYS */;
INSERT INTO `yeucaunhanlop` VALUES (1,2,1,2,'GiaSu','Pending','Tôi muốn nhận lớp này','2025-11-02 13:34:06','2025-11-02 13:34:06'),(2,3,2,4,'NguoiHoc','Pending','Mời cô đến dạy cho con tôi','2025-11-02 13:34:06','2025-11-02 13:34:06');
/*!40000 ALTER TABLE `yeucaunhanlop` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-02 13:35:20
