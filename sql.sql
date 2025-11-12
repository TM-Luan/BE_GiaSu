
CREATE DATABASE  IF NOT EXISTS `giasu` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `giasu`;
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th10 12, 2025 lúc 09:25 PM
-- Phiên bản máy phục vụ: 10.5.29-MariaDB-log
-- Phiên bản PHP: 8.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `cvcuyxzh8akt_giasu1`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `DanhGia`
--

CREATE TABLE `DanhGia` (
  `DanhGiaID` int(11) NOT NULL,
  `LopYeuCauID` int(11) NOT NULL,
  `TaiKhoanID` int(11) NOT NULL,
  `DiemSo` double NOT NULL,
  `BinhLuan` varchar(500) DEFAULT NULL,
  `NgayDanhGia` datetime NOT NULL DEFAULT current_timestamp(),
  `LanSua` int(11) NOT NULL DEFAULT 0 COMMENT 'Số lần đã sửa đánh giá (0=chưa sửa, 1=đã sửa 1 lần)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `DanhGia`
--

INSERT INTO `DanhGia` (`DanhGiaID`, `LopYeuCauID`, `TaiKhoanID`, `DiemSo`, `BinhLuan`, `NgayDanhGia`, `LanSua`) VALUES
(1, 1, 4, 4.5, 'Gia sư dạy dễ hiểu, đúng giờ.', '2025-10-07 21:00:00', 0),
(2, 1, 2, 5, 'Học viên hợp tác tốt, chuẩn bị bài.', '2025-10-07 21:05:00', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `DoiTuong`
--

CREATE TABLE `DoiTuong` (
  `DoiTuongID` int(11) NOT NULL,
  `TenDoiTuong` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `DoiTuong`
--

INSERT INTO `DoiTuong` (`DoiTuongID`, `TenDoiTuong`) VALUES
(3, 'Giáo viên'),
(1, 'Người đi làm'),
(2, 'Sinh viên');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `GiaoDich`
--

CREATE TABLE `GiaoDich` (
  `GiaoDichID` bigint(20) NOT NULL,
  `LopYeuCauID` int(11) NOT NULL,
  `TaiKhoanID` int(11) NOT NULL,
  `SoTien` double NOT NULL,
  `ThoiGian` datetime NOT NULL DEFAULT current_timestamp(),
  `TrangThai` varchar(50) NOT NULL DEFAULT 'ChoXacNhan',
  `GhiChu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `GiaoDich`
--

INSERT INTO `GiaoDich` (`GiaoDichID`, `LopYeuCauID`, `TaiKhoanID`, `SoTien`, `ThoiGian`, `TrangThai`, `GhiChu`) VALUES
(1, 1, 4, 200000, '2025-10-07 18:00:00', 'ThanhCong', 'Thanh toán buổi 1'),
(2, 1, 4, 200000, '2025-10-09 18:00:00', 'ChoXacNhan', 'Thanh toán buổi 2 (chờ đối soát)'),
(3, 2, 6, 180000, '2025-10-10 17:30:00', 'ChoXacNhan', 'Đặt cọc buổi đầu');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `GiaSu`
--

CREATE TABLE `GiaSu` (
  `GiaSuID` int(11) NOT NULL,
  `TaiKhoanID` int(11) NOT NULL,
  `HoTen` varchar(150) NOT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `GioiTinh` varchar(10) DEFAULT NULL,
  `NgaySinh` date DEFAULT NULL,
  `AnhCCCD_MatTruoc` varchar(255) DEFAULT NULL,
  `AnhCCCD_MatSau` varchar(255) DEFAULT NULL,
  `BangCap` varchar(255) DEFAULT NULL,
  `AnhBangCap` varchar(255) DEFAULT NULL,
  `TruongDaoTao` varchar(255) DEFAULT NULL,
  `ChuyenNganh` varchar(255) DEFAULT NULL,
  `ThanhTich` text DEFAULT NULL,
  `KinhNghiem` varchar(255) DEFAULT NULL,
  `AnhDaiDien` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `GiaSu`
--

INSERT INTO `GiaSu` (`GiaSuID`, `TaiKhoanID`, `HoTen`, `DiaChi`, `GioiTinh`, `NgaySinh`, `AnhCCCD_MatTruoc`, `AnhCCCD_MatSau`, `BangCap`, `AnhBangCap`, `TruongDaoTao`, `ChuyenNganh`, `ThanhTich`, `KinhNghiem`, `AnhDaiDien`) VALUES
(1, 2, 'Nguyễn Văn A', 'Q.1, TP.HCM', 'Nam', '1995-05-10', 'cccd_front_1.jpg', 'cccd_back_1.jpg', 'Cử nhân Sư phạm Toán', 'bangcap_1.jpg', 'Đại học Sư phạm TP.HCM', 'Sư phạm Toán', 'Tốt nghiệp loại Giỏi\nGiải nhất Olympic Toán cấp trường\nHọc sinh đạt điểm cao trong kỳ thi THPT QG\nNhiều học sinh đạt giải HSG cấp thành phố', '3 năm kinh nghiệm dạy Toán THPT', NULL),
(2, 3, 'Trần Thị B', 'Q.3, TP.HCM', 'Nữ', '1996-08-22', 'cccd_front_2.jpg', 'cccd_back_2.jpg', 'Cử nhân Ngôn ngữ Anh', 'bangcap_2.jpg', 'Đại học KHXH&NV', 'Ngôn ngữ Anh', 'IELTS 8.0\nTốt nghiệp loại Khá\nNhiều học sinh đạt chứng chỉ IELTS 6.5+', '2 năm kinh nghiệm dạy tiếng Anh', NULL),
(3, 7, 'Nguyễn Văn A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 8, 'Tran Minh Luan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `KhieuNai`
--

CREATE TABLE `KhieuNai` (
  `KhieuNaiID` int(11) NOT NULL,
  `TaiKhoanID` int(11) NOT NULL,
  `NoiDung` text NOT NULL,
  `NgayTao` datetime NOT NULL DEFAULT current_timestamp(),
  `TrangThai` varchar(50) NOT NULL DEFAULT 'TiepNhan',
  `GiaiQuyet` text DEFAULT NULL,
  `PhanHoi` varchar(255) DEFAULT NULL,
  `GiaoDichID` bigint(20) DEFAULT NULL,
  `LopYeuCauID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `KhieuNai`
--

INSERT INTO `KhieuNai` (`KhieuNaiID`, `TaiKhoanID`, `NoiDung`, `NgayTao`, `TrangThai`, `GiaiQuyet`, `PhanHoi`, `GiaoDichID`, `LopYeuCauID`) VALUES
(1, 6, 'Chưa tìm được gia sư phù hợp cho lớp YC2.', '2025-10-10 20:00:00', 'TiepNhan', NULL, NULL, NULL, 2),
(2, 4, 'Thanh toán buổi 2 đã chuyển nhưng chưa xác nhận.', '2025-10-09 21:30:00', 'DangXuLy', NULL, NULL, 2, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `KhoiLop`
--

CREATE TABLE `KhoiLop` (
  `KhoiLopID` int(11) NOT NULL,
  `BacHoc` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `KhoiLop`
--

INSERT INTO `KhoiLop` (`KhoiLopID`, `BacHoc`) VALUES
(1, 'Lớp 1'),
(2, 'Lớp 2'),
(3, 'Lớp 3'),
(4, 'Lớp 4'),
(5, 'Lớp 5'),
(6, 'Lớp 6'),
(7, 'Lớp 7'),
(8, 'Lớp 8'),
(9, 'Lớp 9'),
(10, 'Lớp 10'),
(11, 'Lớp 11'),
(12, 'Lớp 12');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `LichHoc`
--

CREATE TABLE `LichHoc` (
  `LichHocID` int(11) NOT NULL,
  `LichHocGocID` int(11) DEFAULT NULL,
  `LopYeuCauID` int(11) NOT NULL,
  `ThoiGianBatDau` time NOT NULL,
  `ThoiGianKetThuc` time NOT NULL,
  `NgayHoc` date NOT NULL,
  `TrangThai` varchar(50) DEFAULT 'DangDay',
  `DuongDan` varchar(255) DEFAULT NULL,
  `NgayTao` datetime NOT NULL DEFAULT current_timestamp(),
  `IsLapLai` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `LichHoc`
--

INSERT INTO `LichHoc` (`LichHocID`, `LichHocGocID`, `LopYeuCauID`, `ThoiGianBatDau`, `ThoiGianKetThuc`, `NgayHoc`, `TrangThai`, `DuongDan`, `NgayTao`, `IsLapLai`) VALUES
(4, 4, 1, '08:00:00', '09:30:00', '2025-11-05', 'SapToi', 'https://meet.example.com/toan-b1', '2025-11-04 19:20:43', 1),
(5, 4, 1, '08:00:00', '09:30:00', '2025-11-12', 'SapToi', 'https://meet.example.com/toan-b2', '2025-11-04 19:20:43', 1),
(6, 4, 1, '08:00:00', '09:30:00', '2025-11-19', 'SapToi', 'https://meet.example.com/toan-b3', '2025-11-04 19:20:43', 1),
(7, 4, 1, '08:00:00', '09:30:00', '2025-11-26', 'SapToi', 'https://meet.example.com/toan-b4', '2025-11-04 19:20:43', 1),
(8, 4, 1, '08:00:00', '09:30:00', '2025-12-03', 'SapToi', 'https://meet.example.com/toan-b5', '2025-11-04 19:20:43', 1),
(9, 4, 1, '08:00:00', '09:30:00', '2025-12-10', 'SapToi', 'https://meet.example.com/toan-b6', '2025-11-04 19:20:43', 1),
(10, 4, 1, '08:00:00', '09:30:00', '2025-12-17', 'SapToi', 'https://meet.example.com/toan-b7', '2025-11-04 19:20:43', 1),
(11, 4, 1, '08:00:00', '09:30:00', '2025-12-24', 'SapToi', 'https://meet.example.com/toan-b8', '2025-11-04 19:20:43', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `LopHocYeuCau`
--

CREATE TABLE `LopHocYeuCau` (
  `LopYeuCauID` int(11) NOT NULL,
  `NguoiHocID` int(11) NOT NULL,
  `GiaSuID` int(11) DEFAULT NULL,
  `HinhThuc` enum('Online','Offline') NOT NULL DEFAULT 'Offline',
  `HocPhi` double NOT NULL,
  `ThoiLuong` int(11) NOT NULL,
  `TrangThai` enum('TimGiaSu','DangHoc','HoanThanh','Huy') NOT NULL DEFAULT 'TimGiaSu',
  `SoLuong` int(11) DEFAULT 1,
  `MoTa` text DEFAULT NULL,
  `MonID` int(11) NOT NULL,
  `KhoiLopID` int(11) NOT NULL,
  `DoiTuongID` int(11) NOT NULL,
  `SoBuoiTuan` int(11) DEFAULT NULL,
  `LichHocMongMuon` varchar(255) DEFAULT NULL,
  `NgayTao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `LopHocYeuCau`
--

INSERT INTO `LopHocYeuCau` (`LopYeuCauID`, `NguoiHocID`, `GiaSuID`, `HinhThuc`, `HocPhi`, `ThoiLuong`, `TrangThai`, `SoLuong`, `MoTa`, `MonID`, `KhoiLopID`, `DoiTuongID`, `SoBuoiTuan`, `LichHocMongMuon`, `NgayTao`) VALUES
(1, 1, 1, 'Online', 200000, 90, 'DangHoc', 1, 'Ôn thi giữa kỳ', 1, 2, 1, NULL, NULL, '2025-10-05 08:00:00'),
(2, 2, NULL, 'Offline', 180000, 120, 'TimGiaSu', 1, 'Giao tiếp cơ bản', 2, 1, 1, NULL, NULL, '2025-10-06 09:00:00'),
(3, 3, 2, 'Online', 250000, 120, 'TimGiaSu', 2, 'Ôn luyện chương Dao động', 3, 3, 1, NULL, NULL, '2025-10-06 10:00:00'),
(4, 4, NULL, 'Online', 120000, 90, 'TimGiaSu', 1, NULL, 8, 2, 1, NULL, NULL, '2025-11-11 21:52:37'),
(5, 4, 4, 'Online', 120000, 90, 'DangHoc', 1, 'llolo', 10, 3, 1, 3, 't2,t3', '2025-11-12 09:22:39');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_10_22_040803_create_personal_access_tokens_table', 1),
(2, '2025_10_31_075505_create_sessions_table', 1),
(3, '2025_11_12_021922_modify_lop_hoc_yeu_cau_for_schedule_fields', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `MonHoc`
--

CREATE TABLE `MonHoc` (
  `MonID` int(11) NOT NULL,
  `TenMon` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `MonHoc`
--

INSERT INTO `MonHoc` (`MonID`, `TenMon`) VALUES
(13, 'Âm Nhạc'),
(11, 'Công Nghệ'),
(8, 'Địa Lý'),
(10, 'Giáo Dục Công Dân'),
(5, 'Hóa Học'),
(15, 'Khoa Học Tự Nhiên'),
(16, 'Khoa Học Xã Hội'),
(18, 'Kỹ năng giao tiếp'),
(19, 'Kỹ năng thuyết trình'),
(25, 'Lập trình C++'),
(26, 'Lập trình Python'),
(7, 'Lịch Sử'),
(23, 'Luyện thi đại học'),
(22, 'Luyện thi IELTS'),
(21, 'Luyện thi TOEIC'),
(24, 'Luyện thi vào 10'),
(20, 'Luyện viết chữ đẹp'),
(12, 'Mỹ Thuật'),
(2, 'Ngữ Văn'),
(6, 'Sinh Học'),
(14, 'Thể Dục'),
(3, 'Tiếng Anh'),
(29, 'Tiếng Hàn'),
(28, 'Tiếng Nhật'),
(30, 'Tiếng Trung'),
(9, 'Tin Học'),
(27, 'Tin học cơ bản'),
(17, 'Tin học Văn phòng'),
(1, 'Toán'),
(4, 'Vật Lý');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `NguoiHoc`
--

CREATE TABLE `NguoiHoc` (
  `NguoiHocID` int(11) NOT NULL,
  `TaiKhoanID` int(11) NOT NULL,
  `HoTen` varchar(150) NOT NULL,
  `NgaySinh` date DEFAULT NULL,
  `GioiTinh` varchar(10) DEFAULT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `AnhDaiDien` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `NguoiHoc`
--

INSERT INTO `NguoiHoc` (`NguoiHocID`, `TaiKhoanID`, `HoTen`, `NgaySinh`, `GioiTinh`, `DiaChi`, `AnhDaiDien`) VALUES
(1, 4, 'Lê Minh C', '2010-03-12', 'Nam', 'Thủ Đức, TP.HCM', NULL),
(2, 5, 'Phạm Gia D', '2008-11-05', 'Nam', 'Q.7, TP.HCM', NULL),
(3, 6, 'Hoàng Anh E', '2006-01-28', 'Nữ', 'Q.5, TP.HCM', NULL),
(4, 9, '2', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(2, 'App\\Models\\TaiKhoan', 8, '1@gmail.com', '1f2bc4b075048e5cbf2f7f861a86e3fe50b93acc45f71b49acb29798c5bc5350', '[\"*\"]', '2025-11-06 09:43:40', NULL, '2025-11-06 09:35:48', '2025-11-06 09:43:40'),
(12, 'App\\Models\\TaiKhoan', 9, '2@gmail.com', '88768f3c492a2b83dafee4b2439251dadcc138b239688aff6c3b0de098c292e3', '[\"*\"]', '2025-11-12 06:35:22', NULL, '2025-11-11 21:37:12', '2025-11-12 06:35:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `PhanQuyen`
--

CREATE TABLE `PhanQuyen` (
  `TaiKhoanID` int(11) NOT NULL,
  `VaiTroID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `PhanQuyen`
--

INSERT INTO `PhanQuyen` (`TaiKhoanID`, `VaiTroID`) VALUES
(1, 1),
(2, 2),
(3, 2),
(4, 3),
(5, 3),
(6, 3),
(7, 2),
(8, 2),
(9, 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `TaiKhoan`
--

CREATE TABLE `TaiKhoan` (
  `TaiKhoanID` int(11) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `MatKhauHash` varchar(255) NOT NULL,
  `SoDienThoai` varchar(20) DEFAULT NULL,
  `TrangThai` tinyint(4) NOT NULL DEFAULT 1,
  `NgayTao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `TaiKhoan`
--

INSERT INTO `TaiKhoan` (`TaiKhoanID`, `Email`, `MatKhauHash`, `SoDienThoai`, `TrangThai`, `NgayTao`) VALUES
(1, 'admin@site.com', '$2y$10$92IXUNpkjO0rOQ5byMi...', '0900000001', 1, '2025-10-01 09:00:00'),
(2, 'tutor1@site.com', '$2y$10$92IXUNpkjO0rOQ5byMi...', '0900000003', 1, '2025-10-01 09:10:00'),
(3, 'tutor2@site.com', '$2y$10$92IXUNpkjO0rOQ5byMi...', '0900000004', 1, '2025-10-01 09:15:00'),
(4, 'student1@site.com', '$2y$10$92IXUNpkjO0rOQ5byMi...', '0900000005', 1, '2025-10-01 09:20:00'),
(5, 'student2@site.com', '$2y$10$92IXUNpkjO0rOQ5byMi...', '0900000006', 1, '2025-10-01 09:25:00'),
(6, 'student3@site.com', '$2y$10$92IXUNpkjO0rOQ5byMi...', '0900000007', 1, '2025-10-01 09:30:00'),
(7, 'test@example.com', '$2y$12$mnws/ZvjrE6nRMfej4PJN.J9ciUa/zXVWi9iS6xPI9/NdwHynz8Vy', '0912345678', 1, '2025-11-04 19:27:58'),
(8, '1@gmail.com', '$2y$12$kiusU12D7xQY0//81mc55OmvsCc6bp4dJgForFtkVqUxy6VlTH36C', '0934140224', 1, '2025-11-06 09:35:34'),
(9, '2@gmail.com', '$2y$12$8IOrbkQFgIfvgOFwq.g5aeTvnmxVE2GgorYdiQ429XpkbaoqEXHMC', '0927838292', 1, '2025-11-11 21:52:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ThoiGianDay`
--

CREATE TABLE `ThoiGianDay` (
  `ThoiGianDayID` int(11) NOT NULL,
  `SoBuoi` int(11) NOT NULL,
  `BuoiHoc` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ThoiGianDay`
--

INSERT INTO `ThoiGianDay` (`ThoiGianDayID`, `SoBuoi`, `BuoiHoc`) VALUES
(1, 3, 'T2-T4-T6 tối'),
(2, 2, 'T3-T5 tối'),
(3, 1, 'Cuối tuần');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `VaiTro`
--

CREATE TABLE `VaiTro` (
  `VaiTroID` int(11) NOT NULL,
  `TenVaiTro` varchar(50) NOT NULL,
  `MoTa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `VaiTro`
--

INSERT INTO `VaiTro` (`VaiTroID`, `TenVaiTro`, `MoTa`) VALUES
(1, 'Admin', 'Quản trị hệ thống'),
(2, 'GiaSu', 'Tài khoản gia sư'),
(3, 'NguoiHoc', 'Tài khoản người học');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `YeuCauNhanLop`
--

CREATE TABLE `YeuCauNhanLop` (
  `YeuCauID` int(11) NOT NULL,
  `LopYeuCauID` int(11) NOT NULL,
  `GiaSuID` int(11) NOT NULL,
  `NguoiGuiTaiKhoanID` int(11) NOT NULL,
  `VaiTroNguoiGui` enum('GiaSu','NguoiHoc') NOT NULL,
  `TrangThai` enum('Pending','Accepted','Rejected','Cancelled') NOT NULL DEFAULT 'Pending',
  `GhiChu` varchar(255) DEFAULT NULL,
  `NgayTao` datetime NOT NULL DEFAULT current_timestamp(),
  `NgayCapNhat` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `YeuCauNhanLop`
--

INSERT INTO `YeuCauNhanLop` (`YeuCauID`, `LopYeuCauID`, `GiaSuID`, `NguoiGuiTaiKhoanID`, `VaiTroNguoiGui`, `TrangThai`, `GhiChu`, `NgayTao`, `NgayCapNhat`) VALUES
(1, 2, 1, 2, 'GiaSu', 'Pending', 'Tôi muốn nhận lớp này', '2025-11-02 13:34:06', '2025-11-02 13:34:06'),
(2, 3, 2, 4, 'NguoiHoc', 'Pending', 'Mời cô đến dạy cho con tôi', '2025-11-02 13:34:06', '2025-11-02 13:34:06'),
(3, 4, 4, 8, 'GiaSu', 'Rejected', NULL, '2025-11-12 02:23:56', '2025-11-12 02:34:43'),
(4, 5, 4, 8, 'GiaSu', 'Accepted', NULL, '2025-11-12 02:24:06', '2025-11-12 02:24:49'),
(5, 4, 4, 9, 'NguoiHoc', 'Pending', NULL, '2025-11-12 02:34:50', '2025-11-12 02:34:50'),
(6, 4, 3, 9, 'NguoiHoc', 'Pending', 'fasdas', '2025-11-12 13:35:20', '2025-11-12 13:35:20');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `DanhGia`
--
ALTER TABLE `DanhGia`
  ADD PRIMARY KEY (`DanhGiaID`),
  ADD KEY `LopYeuCauID` (`LopYeuCauID`),
  ADD KEY `TaiKhoanID` (`TaiKhoanID`);

--
-- Chỉ mục cho bảng `DoiTuong`
--
ALTER TABLE `DoiTuong`
  ADD PRIMARY KEY (`DoiTuongID`),
  ADD UNIQUE KEY `TenDoiTuong` (`TenDoiTuong`);

--
-- Chỉ mục cho bảng `GiaoDich`
--
ALTER TABLE `GiaoDich`
  ADD PRIMARY KEY (`GiaoDichID`),
  ADD KEY `LopYeuCauID` (`LopYeuCauID`),
  ADD KEY `TaiKhoanID` (`TaiKhoanID`);

--
-- Chỉ mục cho bảng `GiaSu`
--
ALTER TABLE `GiaSu`
  ADD PRIMARY KEY (`GiaSuID`),
  ADD KEY `TaiKhoanID` (`TaiKhoanID`);

--
-- Chỉ mục cho bảng `KhieuNai`
--
ALTER TABLE `KhieuNai`
  ADD PRIMARY KEY (`KhieuNaiID`),
  ADD KEY `TaiKhoanID` (`TaiKhoanID`),
  ADD KEY `GiaoDichID` (`GiaoDichID`),
  ADD KEY `LopYeuCauID` (`LopYeuCauID`);

--
-- Chỉ mục cho bảng `KhoiLop`
--
ALTER TABLE `KhoiLop`
  ADD PRIMARY KEY (`KhoiLopID`);

--
-- Chỉ mục cho bảng `LichHoc`
--
ALTER TABLE `LichHoc`
  ADD PRIMARY KEY (`LichHocID`),
  ADD KEY `LopYeuCauID` (`LopYeuCauID`),
  ADD KEY `idx_lichhoc_goc` (`LichHocGocID`),
  ADD KEY `idx_lichhoc_lap_lai` (`IsLapLai`);

--
-- Chỉ mục cho bảng `LopHocYeuCau`
--
ALTER TABLE `LopHocYeuCau`
  ADD PRIMARY KEY (`LopYeuCauID`),
  ADD KEY `NguoiHocID` (`NguoiHocID`),
  ADD KEY `GiaSuID` (`GiaSuID`),
  ADD KEY `MonID` (`MonID`),
  ADD KEY `KhoiLopID` (`KhoiLopID`),
  ADD KEY `DoiTuongID` (`DoiTuongID`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `MonHoc`
--
ALTER TABLE `MonHoc`
  ADD PRIMARY KEY (`MonID`),
  ADD UNIQUE KEY `TenMon` (`TenMon`);

--
-- Chỉ mục cho bảng `NguoiHoc`
--
ALTER TABLE `NguoiHoc`
  ADD PRIMARY KEY (`NguoiHocID`),
  ADD KEY `TaiKhoanID` (`TaiKhoanID`);

--
-- Chỉ mục cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Chỉ mục cho bảng `PhanQuyen`
--
ALTER TABLE `PhanQuyen`
  ADD PRIMARY KEY (`TaiKhoanID`,`VaiTroID`),
  ADD KEY `VaiTroID` (`VaiTroID`);

--
-- Chỉ mục cho bảng `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Chỉ mục cho bảng `TaiKhoan`
--
ALTER TABLE `TaiKhoan`
  ADD PRIMARY KEY (`TaiKhoanID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `SoDienThoai` (`SoDienThoai`);

--
-- Chỉ mục cho bảng `ThoiGianDay`
--
ALTER TABLE `ThoiGianDay`
  ADD PRIMARY KEY (`ThoiGianDayID`);

--
-- Chỉ mục cho bảng `VaiTro`
--
ALTER TABLE `VaiTro`
  ADD PRIMARY KEY (`VaiTroID`),
  ADD UNIQUE KEY `TenVaiTro` (`TenVaiTro`);

--
-- Chỉ mục cho bảng `YeuCauNhanLop`
--
ALTER TABLE `YeuCauNhanLop`
  ADD PRIMARY KEY (`YeuCauID`),
  ADD KEY `GiaSuID` (`GiaSuID`),
  ADD KEY `NguoiGuiTaiKhoanID` (`NguoiGuiTaiKhoanID`),
  ADD KEY `idx_lop_giasu` (`LopYeuCauID`,`GiaSuID`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `DanhGia`
--
ALTER TABLE `DanhGia`
  MODIFY `DanhGiaID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `DoiTuong`
--
ALTER TABLE `DoiTuong`
  MODIFY `DoiTuongID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `GiaoDich`
--
ALTER TABLE `GiaoDich`
  MODIFY `GiaoDichID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `GiaSu`
--
ALTER TABLE `GiaSu`
  MODIFY `GiaSuID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `KhieuNai`
--
ALTER TABLE `KhieuNai`
  MODIFY `KhieuNaiID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `KhoiLop`
--
ALTER TABLE `KhoiLop`
  MODIFY `KhoiLopID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `LichHoc`
--
ALTER TABLE `LichHoc`
  MODIFY `LichHocID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `LopHocYeuCau`
--
ALTER TABLE `LopHocYeuCau`
  MODIFY `LopYeuCauID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `MonHoc`
--
ALTER TABLE `MonHoc`
  MODIFY `MonID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT cho bảng `NguoiHoc`
--
ALTER TABLE `NguoiHoc`
  MODIFY `NguoiHocID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `TaiKhoan`
--
ALTER TABLE `TaiKhoan`
  MODIFY `TaiKhoanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `ThoiGianDay`
--
ALTER TABLE `ThoiGianDay`
  MODIFY `ThoiGianDayID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `VaiTro`
--
ALTER TABLE `VaiTro`
  MODIFY `VaiTroID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `YeuCauNhanLop`
--
ALTER TABLE `YeuCauNhanLop`
  MODIFY `YeuCauID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `DanhGia`
--
ALTER TABLE `DanhGia`
  ADD CONSTRAINT `DanhGia_ibfk_1` FOREIGN KEY (`LopYeuCauID`) REFERENCES `LopHocYeuCau` (`LopYeuCauID`) ON DELETE CASCADE,
  ADD CONSTRAINT `DanhGia_ibfk_2` FOREIGN KEY (`TaiKhoanID`) REFERENCES `TaiKhoan` (`TaiKhoanID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `GiaoDich`
--
ALTER TABLE `GiaoDich`
  ADD CONSTRAINT `GiaoDich_ibfk_1` FOREIGN KEY (`LopYeuCauID`) REFERENCES `LopHocYeuCau` (`LopYeuCauID`) ON DELETE CASCADE,
  ADD CONSTRAINT `GiaoDich_ibfk_2` FOREIGN KEY (`TaiKhoanID`) REFERENCES `TaiKhoan` (`TaiKhoanID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `GiaSu`
--
ALTER TABLE `GiaSu`
  ADD CONSTRAINT `GiaSu_ibfk_1` FOREIGN KEY (`TaiKhoanID`) REFERENCES `TaiKhoan` (`TaiKhoanID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `KhieuNai`
--
ALTER TABLE `KhieuNai`
  ADD CONSTRAINT `KhieuNai_ibfk_1` FOREIGN KEY (`TaiKhoanID`) REFERENCES `TaiKhoan` (`TaiKhoanID`) ON DELETE CASCADE,
  ADD CONSTRAINT `KhieuNai_ibfk_2` FOREIGN KEY (`GiaoDichID`) REFERENCES `GiaoDich` (`GiaoDichID`) ON DELETE SET NULL,
  ADD CONSTRAINT `KhieuNai_ibfk_3` FOREIGN KEY (`LopYeuCauID`) REFERENCES `LopHocYeuCau` (`LopYeuCauID`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `LichHoc`
--
ALTER TABLE `LichHoc`
  ADD CONSTRAINT `LichHoc_ibfk_1` FOREIGN KEY (`LopYeuCauID`) REFERENCES `LopHocYeuCau` (`LopYeuCauID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lichhoc_lichhocgoc` FOREIGN KEY (`LichHocGocID`) REFERENCES `LichHoc` (`LichHocID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `LopHocYeuCau`
--
ALTER TABLE `LopHocYeuCau`
  ADD CONSTRAINT `LopHocYeuCau_ibfk_1` FOREIGN KEY (`NguoiHocID`) REFERENCES `NguoiHoc` (`NguoiHocID`) ON DELETE CASCADE,
  ADD CONSTRAINT `LopHocYeuCau_ibfk_2` FOREIGN KEY (`GiaSuID`) REFERENCES `GiaSu` (`GiaSuID`),
  ADD CONSTRAINT `LopHocYeuCau_ibfk_3` FOREIGN KEY (`MonID`) REFERENCES `MonHoc` (`MonID`),
  ADD CONSTRAINT `LopHocYeuCau_ibfk_4` FOREIGN KEY (`KhoiLopID`) REFERENCES `KhoiLop` (`KhoiLopID`),
  ADD CONSTRAINT `LopHocYeuCau_ibfk_5` FOREIGN KEY (`DoiTuongID`) REFERENCES `DoiTuong` (`DoiTuongID`);

--
-- Các ràng buộc cho bảng `NguoiHoc`
--
ALTER TABLE `NguoiHoc`
  ADD CONSTRAINT `NguoiHoc_ibfk_1` FOREIGN KEY (`TaiKhoanID`) REFERENCES `TaiKhoan` (`TaiKhoanID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `PhanQuyen`
--
ALTER TABLE `PhanQuyen`
  ADD CONSTRAINT `PhanQuyen_ibfk_1` FOREIGN KEY (`TaiKhoanID`) REFERENCES `TaiKhoan` (`TaiKhoanID`) ON DELETE CASCADE,
  ADD CONSTRAINT `PhanQuyen_ibfk_2` FOREIGN KEY (`VaiTroID`) REFERENCES `VaiTro` (`VaiTroID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `YeuCauNhanLop`
--
ALTER TABLE `YeuCauNhanLop`
  ADD CONSTRAINT `YeuCauNhanLop_ibfk_1` FOREIGN KEY (`LopYeuCauID`) REFERENCES `LopHocYeuCau` (`LopYeuCauID`) ON DELETE CASCADE,
  ADD CONSTRAINT `YeuCauNhanLop_ibfk_2` FOREIGN KEY (`GiaSuID`) REFERENCES `GiaSu` (`GiaSuID`) ON DELETE CASCADE,
  ADD CONSTRAINT `YeuCauNhanLop_ibfk_3` FOREIGN KEY (`NguoiGuiTaiKhoanID`) REFERENCES `TaiKhoan` (`TaiKhoanID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
