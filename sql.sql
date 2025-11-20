CREATE DATABASE IF NOT EXISTS `giasu` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `giasu`;
-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: giasu1
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
  `BinhLuan` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayDanhGia` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `LanSua` int NOT NULL DEFAULT '0' COMMENT 'Số lần đã sửa đánh giá (0=chưa sửa, 1=đã sửa 1 lần)',
  PRIMARY KEY (`DanhGiaID`),
  KEY `LopYeuCauID` (`LopYeuCauID`),
  KEY `TaiKhoanID` (`TaiKhoanID`),
  CONSTRAINT `DanhGia_ibfk_1` FOREIGN KEY (`LopYeuCauID`) REFERENCES `lophocyeucau` (`LopYeuCauID`) ON DELETE CASCADE,
  CONSTRAINT `DanhGia_ibfk_2` FOREIGN KEY (`TaiKhoanID`) REFERENCES `taikhoan` (`TaiKhoanID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `danhgia`
--

LOCK TABLES `danhgia` WRITE;
/*!40000 ALTER TABLE `danhgia` DISABLE KEYS */;
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
  `TenDoiTuong` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`DoiTuongID`),
  UNIQUE KEY `TenDoiTuong` (`TenDoiTuong`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doituong`
--

LOCK TABLES `doituong` WRITE;
/*!40000 ALTER TABLE `doituong` DISABLE KEYS */;
INSERT INTO `doituong` VALUES (3,'Giáo viên'),(1,'Người đi làm'),(2,'Sinh viên');
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
  `TrangThai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ChoXacNhan',
  `GhiChu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MaGiaoDich` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LoaiGiaoDich` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`GiaoDichID`),
  KEY `LopYeuCauID` (`LopYeuCauID`),
  KEY `TaiKhoanID` (`TaiKhoanID`),
  CONSTRAINT `GiaoDich_ibfk_1` FOREIGN KEY (`LopYeuCauID`) REFERENCES `lophocyeucau` (`LopYeuCauID`) ON DELETE CASCADE,
  CONSTRAINT `GiaoDich_ibfk_2` FOREIGN KEY (`TaiKhoanID`) REFERENCES `taikhoan` (`TaiKhoanID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `giaodich`
--

LOCK TABLES `giaodich` WRITE;
/*!40000 ALTER TABLE `giaodich` DISABLE KEYS */;
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
  `ThanhTich` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `KinhNghiem` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `AnhDaiDien` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MonID` int DEFAULT NULL,
  `TrangThai` int DEFAULT '2',
  PRIMARY KEY (`GiaSuID`),
  KEY `TaiKhoanID` (`TaiKhoanID`),
  KEY `fk_giasu_monhoc` (`MonID`),
  CONSTRAINT `fk_giasu_monhoc` FOREIGN KEY (`MonID`) REFERENCES `monhoc` (`MonID`) ON DELETE SET NULL,
  CONSTRAINT `GiaSu_ibfk_1` FOREIGN KEY (`TaiKhoanID`) REFERENCES `taikhoan` (`TaiKhoanID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `giasu`
--

LOCK TABLES `giasu` WRITE;
/*!40000 ALTER TABLE `giasu` DISABLE KEYS */;
INSERT INTO `giasu` VALUES (6,11,'Tran Minh Luan','ap 5','Nam','2003-11-21','https://i.ibb.co/1JsvnMvS/ff9014ee2161.jpg','https://i.ibb.co/qM1mjpkk/37a04e86e013.jpg','Bằng cử nhân','https://i.ibb.co/RGgs6CMy/d8aa1f216ff1.jpg','HUIT','CNPM','11111','2 năm','https://i.ibb.co/JjD261y8/1aa9463a4abf.jpg',7,1),(7,13,'Le Van Minh',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2),(8,14,'llllll',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2),(9,16,'hhhh',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2);
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
  CONSTRAINT `KhieuNai_ibfk_1` FOREIGN KEY (`TaiKhoanID`) REFERENCES `taikhoan` (`TaiKhoanID`) ON DELETE CASCADE,
  CONSTRAINT `KhieuNai_ibfk_2` FOREIGN KEY (`GiaoDichID`) REFERENCES `giaodich` (`GiaoDichID`) ON DELETE SET NULL,
  CONSTRAINT `KhieuNai_ibfk_3` FOREIGN KEY (`LopYeuCauID`) REFERENCES `lophocyeucau` (`LopYeuCauID`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `khieunai`
--

LOCK TABLES `khieunai` WRITE;
/*!40000 ALTER TABLE `khieunai` DISABLE KEYS */;
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
  `BacHoc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `TrangThai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'DangDay',
  `DuongDan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `IsLapLai` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`LichHocID`),
  KEY `LopYeuCauID` (`LopYeuCauID`),
  KEY `idx_lichhoc_goc` (`LichHocGocID`),
  KEY `idx_lichhoc_lap_lai` (`IsLapLai`),
  CONSTRAINT `fk_lichhoc_lichhocgoc` FOREIGN KEY (`LichHocGocID`) REFERENCES `lichhoc` (`LichHocID`) ON DELETE CASCADE,
  CONSTRAINT `LichHoc_ibfk_1` FOREIGN KEY (`LopYeuCauID`) REFERENCES `lophocyeucau` (`LopYeuCauID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lichhoc`
--

LOCK TABLES `lichhoc` WRITE;
/*!40000 ALTER TABLE `lichhoc` DISABLE KEYS */;
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
  `HinhThuc` enum('Online','Offline') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Offline',
  `HocPhi` double NOT NULL,
  `ThoiLuong` int NOT NULL,
  `TrangThai` enum('TimGiaSu','DangHoc','HoanThanh','Huy') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TimGiaSu',
  `SoLuong` int DEFAULT '1',
  `MoTa` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `MonID` int NOT NULL,
  `KhoiLopID` int NOT NULL,
  `DoiTuongID` int NOT NULL,
  `SoBuoiTuan` int DEFAULT NULL,
  `LichHocMongMuon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`LopYeuCauID`),
  KEY `NguoiHocID` (`NguoiHocID`),
  KEY `GiaSuID` (`GiaSuID`),
  KEY `MonID` (`MonID`),
  KEY `KhoiLopID` (`KhoiLopID`),
  KEY `DoiTuongID` (`DoiTuongID`),
  CONSTRAINT `LopHocYeuCau_ibfk_1` FOREIGN KEY (`NguoiHocID`) REFERENCES `nguoihoc` (`NguoiHocID`) ON DELETE CASCADE,
  CONSTRAINT `LopHocYeuCau_ibfk_2` FOREIGN KEY (`GiaSuID`) REFERENCES `giasu` (`GiaSuID`),
  CONSTRAINT `LopHocYeuCau_ibfk_3` FOREIGN KEY (`MonID`) REFERENCES `monhoc` (`MonID`),
  CONSTRAINT `LopHocYeuCau_ibfk_4` FOREIGN KEY (`KhoiLopID`) REFERENCES `khoilop` (`KhoiLopID`),
  CONSTRAINT `LopHocYeuCau_ibfk_5` FOREIGN KEY (`DoiTuongID`) REFERENCES `doituong` (`DoiTuongID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lophocyeucau`
--

LOCK TABLES `lophocyeucau` WRITE;
/*!40000 ALTER TABLE `lophocyeucau` DISABLE KEYS */;
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
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
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
  `TenMon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `HoTen` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `NgaySinh` date DEFAULT NULL,
  `GioiTinh` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DiaChi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `AnhDaiDien` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TrangThai` int DEFAULT '1',
  PRIMARY KEY (`NguoiHocID`),
  KEY `TaiKhoanID` (`TaiKhoanID`),
  CONSTRAINT `NguoiHoc_ibfk_1` FOREIGN KEY (`TaiKhoanID`) REFERENCES `taikhoan` (`TaiKhoanID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nguoihoc`
--

LOCK TABLES `nguoihoc` WRITE;
/*!40000 ALTER TABLE `nguoihoc` DISABLE KEYS */;
INSERT INTO `nguoihoc` VALUES (5,12,'Tran Minh Hieu',NULL,NULL,NULL,NULL,1),(6,15,'1',NULL,'Nữ',NULL,NULL,1);
/*!40000 ALTER TABLE `nguoihoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL COMMENT 'Liên kết với TaiKhoanID',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'system' COMMENT 'Loại thông báo: request_received, invite_received, request_result...',
  `related_id` int DEFAULT NULL COMMENT 'ID của đối tượng liên quan (Lớp học, Yêu cầu...)',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_foreign` (`user_id`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `taikhoan` (`TaiKhoanID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (33,'App\\Models\\TaiKhoan',11,'1@gmail.com','31ce1058bd84834f2ff17465168cfe49b8d99655853340bbb24862462bc89948','[\"*\"]','2025-11-17 22:19:17',NULL,'2025-11-17 21:29:28','2025-11-17 22:19:17'),(55,'App\\Models\\TaiKhoan',11,'1@gmail.com','22c8870a1977203e06d5aa5c7e7043ec817a6a6707690e06a7527be143660d81','[\"*\"]','2025-11-20 08:02:57',NULL,'2025-11-20 08:00:54','2025-11-20 08:02:57');
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
  CONSTRAINT `PhanQuyen_ibfk_1` FOREIGN KEY (`TaiKhoanID`) REFERENCES `taikhoan` (`TaiKhoanID`) ON DELETE CASCADE,
  CONSTRAINT `PhanQuyen_ibfk_2` FOREIGN KEY (`VaiTroID`) REFERENCES `vaitro` (`VaiTroID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phanquyen`
--

LOCK TABLES `phanquyen` WRITE;
/*!40000 ALTER TABLE `phanquyen` DISABLE KEYS */;
INSERT INTO `phanquyen` VALUES (10,1),(11,2),(13,2),(14,2),(16,2),(12,3),(15,3);
/*!40000 ALTER TABLE `phanquyen` ENABLE KEYS */;
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
INSERT INTO `sessions` VALUES ('dUGO682bjsRoZCt3MkH72GEp32K1bnHqIWJj4xPA',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoibmVtT3N4Z0RDSkRvWGc2S1R2cVlTTjk4MENNVWRiU0ZmazBDSUcyZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1763639634),('NSWfwc5anBxL2szLSc48eW7EPX1szJ989ZPSOPGC',10,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZjdjQ1hVYnlIaE5Ib1J6Q04xa21GTm1mNzNrV3ZmU25RaUczMU1jZiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO31zOjUyOiJsb2dpbl9hZG1pbl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjEwO30=',1763639750),('YhBnWKCpf7ZVCCbtebaD7RuaOgSq6ETX4PTOllhd',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZVVlaXJpZWh5RkU3dkptcmIzcHhFQWFFVGVXdGJFand4V3BQbGNRTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1763639634);
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
  `Email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `MatKhauHash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `SoDienThoai` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TrangThai` tinyint NOT NULL DEFAULT '1',
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TaiKhoanID`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `SoDienThoai` (`SoDienThoai`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taikhoan`
--

LOCK TABLES `taikhoan` WRITE;
/*!40000 ALTER TABLE `taikhoan` DISABLE KEYS */;
INSERT INTO `taikhoan` VALUES (10,'admin@gmail.com','$2y$12$6ZT9eCdNoTweRCOqGC6ngeL6MeGuM9kaYylBo/Udv6OFgCArUGdbi','0000000000',1,'2025-11-17 10:23:44'),(11,'1@gmail.com','$2y$12$BHno8O5vtiKKjtW4K35lD.SciReY32aKAT3J9ayhPWeV94UR4769.','0934140224',2,'2025-11-17 10:26:23'),(12,'2@gmail.com','$2y$12$62fZ092TPltCH9LUiwYBd.uiB0fq16zGdvVjVQBu9Z.AbBFKt5.cu','0934140225',1,'2025-11-17 10:26:41'),(13,'3@gmail.com','$2y$12$VlAWaBMgRyLysM7JEju0VOPSWXPyj2C36wLGy/RKfGfWRV7H7.gyu','0934140226',1,'2025-11-17 17:01:05'),(14,'8@gmail.com','$2y$12$PUZpi54WJyk6D7.nYbJaDerGs.S2iQCk3ncJYripAqxaN.k2dYqRy','0120318729',1,'2025-11-18 12:55:58'),(15,'9@gmail.com','$2y$12$29ik6kO/2VybyA2rvWeCW.XzF4.Gdl6zZJBqfTYpMBDA/vhBgjuJi','01974912231',1,'2025-11-18 13:29:04'),(16,'1@gmail','$2y$12$sF/AlTTWvDPJQePDKQ/6QOSNvCNXlbQdtlQcI6qhFtT8rkResq6Qe','0987654321',1,'2025-11-20 09:18:28');
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
  `SoBuoi` int NOT NULL,
  `BuoiHoc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`ThoiGianDayID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `thoigianday`
--

LOCK TABLES `thoigianday` WRITE;
/*!40000 ALTER TABLE `thoigianday` DISABLE KEYS */;
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
  `TenVaiTro` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `MoTa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `VaiTroNguoiGui` enum('GiaSu','NguoiHoc') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `TrangThai` enum('Pending','Accepted','Rejected','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `GhiChu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `NgayCapNhat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`YeuCauID`),
  KEY `GiaSuID` (`GiaSuID`),
  KEY `NguoiGuiTaiKhoanID` (`NguoiGuiTaiKhoanID`),
  KEY `idx_lop_giasu` (`LopYeuCauID`,`GiaSuID`),
  CONSTRAINT `YeuCauNhanLop_ibfk_1` FOREIGN KEY (`LopYeuCauID`) REFERENCES `lophocyeucau` (`LopYeuCauID`) ON DELETE CASCADE,
  CONSTRAINT `YeuCauNhanLop_ibfk_2` FOREIGN KEY (`GiaSuID`) REFERENCES `giasu` (`GiaSuID`) ON DELETE CASCADE,
  CONSTRAINT `YeuCauNhanLop_ibfk_3` FOREIGN KEY (`NguoiGuiTaiKhoanID`) REFERENCES `taikhoan` (`TaiKhoanID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yeucaunhanlop`
--

LOCK TABLES `yeucaunhanlop` WRITE;
/*!40000 ALTER TABLE `yeucaunhanlop` DISABLE KEYS */;
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

-- Dump completed on 2025-11-20 22:04:36
