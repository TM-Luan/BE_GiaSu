-- Tạo database
CREATE DATABASE IF NOT EXISTS `giasu` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `giasu`;
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 10.123.0.78:3306
-- Thời gian đã tạo: Th10 18, 2025 lúc 05:17 AM
-- Phiên bản máy phục vụ: 8.0.16
-- Phiên bản PHP: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `hakchi39_minhluan`
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
  `BinhLuan` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayDanhGia` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `LanSua` int(11) NOT NULL DEFAULT '0' COMMENT 'Số lần đã sửa đánh giá (0=chưa sửa, 1=đã sửa 1 lần)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `DanhGia`
--

INSERT INTO `DanhGia` (`DanhGiaID`, `LopYeuCauID`, `TaiKhoanID`, `DiemSo`, `BinhLuan`, `NgayDanhGia`, `LanSua`) VALUES
(3, 6, 12, 5, 'hehehehe', '2025-11-17 08:14:15', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `DoiTuong`
--

CREATE TABLE `DoiTuong` (
  `DoiTuongID` int(11) NOT NULL,
  `TenDoiTuong` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
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
  `ThoiGian` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `TrangThai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ChoXacNhan',
  `GhiChu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MaGiaoDich` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LoaiGiaoDich` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `GiaoDich`
--

INSERT INTO `GiaoDich` (`GiaoDichID`, `LopYeuCauID`, `TaiKhoanID`, `SoTien`, `ThoiGian`, `TrangThai`, `GhiChu`, `MaGiaoDich`, `LoaiGiaoDich`) VALUES
(4, 6, 11, 324000, '2025-11-18 05:02:26', 'Thành công', 'Thanh toán phí nhận lớp 6', 'TXN_1763442146_11', 'MoMo'),
(5, 6, 11, 324000, '2025-11-18 05:02:50', 'Thành công', 'Thanh toán phí nhận lớp 6', 'TXN_1763442170_11', 'ZaloPay');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `GiaSu`
--

CREATE TABLE `GiaSu` (
  `GiaSuID` int(11) NOT NULL,
  `TaiKhoanID` int(11) NOT NULL,
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
  `MonID` int(11) DEFAULT NULL,
  `TrangThai` int(11) DEFAULT '2'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `GiaSu`
--

INSERT INTO `GiaSu` (`GiaSuID`, `TaiKhoanID`, `HoTen`, `DiaChi`, `GioiTinh`, `NgaySinh`, `AnhCCCD_MatTruoc`, `AnhCCCD_MatSau`, `BangCap`, `AnhBangCap`, `TruongDaoTao`, `ChuyenNganh`, `ThanhTich`, `KinhNghiem`, `AnhDaiDien`, `MonID`, `TrangThai`) VALUES
(6, 11, 'Tran Minh Luan', NULL, NULL, NULL, NULL, NULL, 'Bằng tốt nghiệp THCS và THPT', NULL, NULL, NULL, NULL, '2 năm', NULL, 15, 1),
(7, 13, 'Le Van Minh', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `KhieuNai`
--

CREATE TABLE `KhieuNai` (
  `KhieuNaiID` int(11) NOT NULL,
  `TaiKhoanID` int(11) NOT NULL,
  `NoiDung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `TrangThai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TiepNhan',
  `GiaiQuyet` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `PhanHoi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GiaoDichID` bigint(20) DEFAULT NULL,
  `LopYeuCauID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `KhoiLop`
--

CREATE TABLE `KhoiLop` (
  `KhoiLopID` int(11) NOT NULL,
  `BacHoc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
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
  `TrangThai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'DangDay',
  `DuongDan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `IsLapLai` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `LopHocYeuCau`
--

CREATE TABLE `LopHocYeuCau` (
  `LopYeuCauID` int(11) NOT NULL,
  `NguoiHocID` int(11) NOT NULL,
  `GiaSuID` int(11) DEFAULT NULL,
  `HinhThuc` enum('Online','Offline') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Offline',
  `HocPhi` double NOT NULL,
  `ThoiLuong` int(11) NOT NULL,
  `TrangThai` enum('TimGiaSu','DangHoc','HoanThanh','Huy') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TimGiaSu',
  `SoLuong` int(11) DEFAULT '1',
  `MoTa` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `MonID` int(11) NOT NULL,
  `KhoiLopID` int(11) NOT NULL,
  `DoiTuongID` int(11) NOT NULL,
  `SoBuoiTuan` int(11) DEFAULT NULL,
  `LichHocMongMuon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `LopHocYeuCau`
--

INSERT INTO `LopHocYeuCau` (`LopYeuCauID`, `NguoiHocID`, `GiaSuID`, `HinhThuc`, `HocPhi`, `ThoiLuong`, `TrangThai`, `SoLuong`, `MoTa`, `MonID`, `KhoiLopID`, `DoiTuongID`, `SoBuoiTuan`, `LichHocMongMuon`, `NgayTao`) VALUES
(6, 5, 6, 'Online', 90000, 60, 'DangHoc', 1, 'a', 29, 8, 1, 3, 'sang t4 t5 t6 t7', '2025-11-17 10:28:38'),
(7, 5, NULL, 'Offline', 333333, 60, 'TimGiaSu', 1, 'aaaaaaa', 15, 4, 2, 3, 't5', '2025-11-17 16:43:12');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `MonHoc`
--

CREATE TABLE `MonHoc` (
  `MonID` int(11) NOT NULL,
  `TenMon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
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
  `HoTen` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `NgaySinh` date DEFAULT NULL,
  `GioiTinh` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DiaChi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `AnhDaiDien` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TrangThai` int(11) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `NguoiHoc`
--

INSERT INTO `NguoiHoc` (`NguoiHocID`, `TaiKhoanID`, `HoTen`, `NgaySinh`, `GioiTinh`, `DiaChi`, `AnhDaiDien`, `TrangThai`) VALUES
(5, 12, 'Tran Minh Hieu', NULL, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(33, 'App\\Models\\TaiKhoan', 11, '1@gmail.com', '31ce1058bd84834f2ff17465168cfe49b8d99655853340bbb24862462bc89948', '[\"*\"]', '2025-11-17 22:02:57', NULL, '2025-11-17 21:29:28', '2025-11-17 22:02:57');

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
(10, 1),
(11, 2),
(13, 2),
(12, 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `TaiKhoan`
--

CREATE TABLE `TaiKhoan` (
  `TaiKhoanID` int(11) NOT NULL,
  `Email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `MatKhauHash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `SoDienThoai` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TrangThai` tinyint(4) NOT NULL DEFAULT '1',
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `TaiKhoan`
--

INSERT INTO `TaiKhoan` (`TaiKhoanID`, `Email`, `MatKhauHash`, `SoDienThoai`, `TrangThai`, `NgayTao`) VALUES
(10, 'admin@gmail.com', '$2y$12$6ZT9eCdNoTweRCOqGC6ngeL6MeGuM9kaYylBo/Udv6OFgCArUGdbi', '0000000000', 1, '2025-11-17 10:23:44'),
(11, '1@gmail.com', '$2y$12$BHno8O5vtiKKjtW4K35lD.SciReY32aKAT3J9ayhPWeV94UR4769.', '0934140224', 2, '2025-11-17 10:26:23'),
(12, '2@gmail.com', '$2y$12$62fZ092TPltCH9LUiwYBd.uiB0fq16zGdvVjVQBu9Z.AbBFKt5.cu', '0934140225', 1, '2025-11-17 10:26:41'),
(13, '3@gmail.com', '$2y$12$VlAWaBMgRyLysM7JEju0VOPSWXPyj2C36wLGy/RKfGfWRV7H7.gyu', '0934140226', 1, '2025-11-17 17:01:05');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ThoiGianDay`
--

CREATE TABLE `ThoiGianDay` (
  `ThoiGianDayID` int(11) NOT NULL,
  `SoBuoi` int(11) NOT NULL,
  `BuoiHoc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `VaiTro`
--

CREATE TABLE `VaiTro` (
  `VaiTroID` int(11) NOT NULL,
  `TenVaiTro` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `MoTa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
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
  `VaiTroNguoiGui` enum('GiaSu','NguoiHoc') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `TrangThai` enum('Pending','Accepted','Rejected','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `GhiChu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayTao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `NgayCapNhat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `YeuCauNhanLop`
--

INSERT INTO `YeuCauNhanLop` (`YeuCauID`, `LopYeuCauID`, `GiaSuID`, `NguoiGuiTaiKhoanID`, `VaiTroNguoiGui`, `TrangThai`, `GhiChu`, `NgayTao`, `NgayCapNhat`) VALUES
(8, 6, 6, 11, 'GiaSu', 'Accepted', NULL, '2025-11-17 03:29:15', '2025-11-17 03:30:25'),
(9, 7, 6, 11, 'GiaSu', 'Cancelled', NULL, '2025-11-17 12:22:48', '2025-11-17 12:29:53'),
(10, 7, 6, 11, 'GiaSu', 'Pending', NULL, '2025-11-17 12:29:58', '2025-11-17 12:29:58');

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
  ADD KEY `TaiKhoanID` (`TaiKhoanID`),
  ADD KEY `fk_giasu_monhoc` (`MonID`);

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
  MODIFY `DanhGiaID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `DoiTuong`
--
ALTER TABLE `DoiTuong`
  MODIFY `DoiTuongID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `GiaoDich`
--
ALTER TABLE `GiaoDich`
  MODIFY `GiaoDichID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `GiaSu`
--
ALTER TABLE `GiaSu`
  MODIFY `GiaSuID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `LichHocID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT cho bảng `LopHocYeuCau`
--
ALTER TABLE `LopHocYeuCau`
  MODIFY `LopYeuCauID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `NguoiHocID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT cho bảng `TaiKhoan`
--
ALTER TABLE `TaiKhoan`
  MODIFY `TaiKhoanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
  MODIFY `YeuCauID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  ADD CONSTRAINT `GiaSu_ibfk_1` FOREIGN KEY (`TaiKhoanID`) REFERENCES `TaiKhoan` (`TaiKhoanID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_giasu_monhoc` FOREIGN KEY (`MonID`) REFERENCES `MonHoc` (`MonID`) ON DELETE SET NULL;

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
