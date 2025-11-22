-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th10 21, 2025 lúc 11:47 PM
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
-- Cơ sở dữ liệu: `cvcuyxzh8akt_giasu`
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
  `GhiChu` varchar(255) DEFAULT NULL,
  `MaGiaoDich` varchar(100) DEFAULT NULL,
  `LoaiGiaoDich` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `GiaoDich`
--

INSERT INTO `GiaoDich` (`GiaoDichID`, `LopYeuCauID`, `TaiKhoanID`, `SoTien`, `ThoiGian`, `TrangThai`, `GhiChu`, `MaGiaoDich`, `LoaiGiaoDich`) VALUES
(25, 15, 24, 1200000, '2025-11-21 16:44:55', 'Thành công', 'Thanh toán phí nhận lớp 15', 'TXN_1763743495_24', 'ChuyenKhoan');

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
  `AnhDaiDien` varchar(255) DEFAULT NULL,
  `MonID` int(11) DEFAULT NULL,
  `TrangThai` int(11) DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `GiaSu`
--

INSERT INTO `GiaSu` (`GiaSuID`, `TaiKhoanID`, `HoTen`, `DiaChi`, `GioiTinh`, `NgaySinh`, `AnhCCCD_MatTruoc`, `AnhCCCD_MatSau`, `BangCap`, `AnhBangCap`, `TruongDaoTao`, `ChuyenNganh`, `ThanhTich`, `KinhNghiem`, `AnhDaiDien`, `MonID`, `TrangThai`) VALUES
(13, 24, 'Trần Minh Luân', NULL, 'Nam', '2004-07-22', 'cccd/TV8x872fGMlf7yt1Hn0vd4CraCx2FA2hCOFxZ1Xv.png', 'cccd/WLmlMS4XR0pPft8zFT4ZTLdnEN7qTDO8ST4MCqPi.png', NULL, 'degrees/enxKgo72Fh4uqRTzjuHzdsq7OTlqW4sQWAJVPuOa.png', NULL, NULL, NULL, NULL, 'avatars/rc2LsgpNOAjk8oyfjpGwgJnY6B7szAnvdGBNz3hU.png', NULL, 1);

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
(4, 25, 'kkkkkkk', '2025-11-21 02:23:59', 'TiepNhan', NULL, NULL, NULL, NULL),
(5, 25, 'ngu', '2025-11-21 07:55:09', 'TiepNhan', NULL, NULL, NULL, NULL);

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
(194, NULL, 15, '19:00:00', '20:30:00', '2025-11-18', 'ChuaDienRa', NULL, '2025-11-21 16:45:37', 0),
(195, NULL, 15, '19:00:00', '20:30:00', '2025-11-20', 'ChuaDienRa', NULL, '2025-11-21 16:45:37', 0),
(196, NULL, 15, '19:00:00', '20:30:00', '2025-11-25', 'ChuaDienRa', NULL, '2025-11-21 16:45:37', 0),
(197, NULL, 15, '19:00:00', '20:30:00', '2025-11-27', 'ChuaDienRa', NULL, '2025-11-21 16:45:37', 0),
(198, NULL, 15, '19:00:00', '20:30:00', '2025-12-02', 'ChuaDienRa', NULL, '2025-11-21 16:45:37', 0),
(199, NULL, 15, '19:00:00', '20:30:00', '2025-12-04', 'ChuaDienRa', NULL, '2025-11-21 16:45:37', 0),
(200, NULL, 15, '19:00:00', '20:30:00', '2025-12-09', 'ChuaDienRa', NULL, '2025-11-21 16:45:37', 0),
(201, NULL, 15, '19:00:00', '20:30:00', '2025-12-11', 'ChuaDienRa', NULL, '2025-11-21 16:45:37', 0);

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
  `NgayTao` datetime NOT NULL DEFAULT current_timestamp(),
  `TrangThaiThanhToan` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `LopHocYeuCau`
--

INSERT INTO `LopHocYeuCau` (`LopYeuCauID`, `NguoiHocID`, `GiaSuID`, `HinhThuc`, `HocPhi`, `ThoiLuong`, `TrangThai`, `SoLuong`, `MoTa`, `MonID`, `KhoiLopID`, `DoiTuongID`, `SoBuoiTuan`, `LichHocMongMuon`, `NgayTao`, `TrangThaiThanhToan`) VALUES
(14, 12, NULL, 'Online', 300000, 90, 'TimGiaSu', 1, 'oke', 8, 9, 3, 3, 'T2,T4,T6', '2025-11-21 20:05:58', NULL),
(15, 11, 13, 'Online', 500000, 90, 'DangHoc', 1, NULL, 11, 2, 3, 2, 'T3 T5', '2025-11-21 16:32:35', 'DaThanhToan');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `AnhDaiDien` varchar(255) DEFAULT NULL,
  `TrangThai` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `NguoiHoc`
--

INSERT INTO `NguoiHoc` (`NguoiHocID`, `TaiKhoanID`, `HoTen`, `NgaySinh`, `GioiTinh`, `DiaChi`, `AnhDaiDien`, `TrangThai`) VALUES
(11, 25, 'Trần Minh Hiếu', '2004-11-15', 'Nam', 'hihi', 'https://i.ibb.co/3YQG8DgV/392a7399e8d7.jpg', 1),
(12, 26, 'Minh', NULL, NULL, NULL, NULL, 1),
(13, 27, 'Minh', NULL, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Liên kết với TaiKhoanID',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'system' COMMENT 'Loại thông báo: request_received, invite_received, request_result...',
  `related_id` int(11) DEFAULT NULL COMMENT 'ID của đối tượng liên quan (Lớp học, Yêu cầu...)',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `related_id`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 24, 'Lời mời dạy mới', 'Người dùng đã mời bạn dạy lớp: Lập trình C++ - ', 'invitation_received', 11, 1, '2025-11-20 08:38:56', '2025-11-20 08:39:42'),
(2, 24, 'Lời mời dạy mới', 'Người dùng đã mời bạn dạy lớp: Lập trình C++ - ', 'invitation_received', 11, 1, '2025-11-20 08:43:58', '2025-11-20 08:49:01'),
(3, 25, 'Yêu cầu bị từ chối', 'Yêu cầu kết nối lớp học của bạn đã bị từ chối.', 'request_rejected', 11, 1, '2025-11-20 08:45:38', '2025-11-20 08:46:54'),
(4, 25, 'Yêu cầu dạy mới', 'Một gia sư đã đăng ký dạy lớp: Lập trình C++ - ', 'request_received', 11, 1, '2025-11-20 08:46:30', '2025-11-20 08:46:40'),
(5, 24, 'Yêu cầu được chấp nhận', 'Yêu cầu dạy lớp Lập trình C++  của bạn đã được chấp nhận!', 'request_accepted', 11, 1, '2025-11-20 08:46:41', '2025-11-20 08:49:03'),
(6, 24, 'Lời mời dạy mới', 'Người dùng đã mời bạn dạy lớp: Địa Lý - ', 'invitation_received', 12, 1, '2025-11-20 20:23:13', '2025-11-20 20:47:31'),
(7, 24, 'Lời mời dạy mới', 'Người dùng đã mời bạn dạy lớp: Giáo Dục Công Dân - ', 'invitation_received', 13, 1, '2025-11-20 20:47:04', '2025-11-20 20:47:29'),
(8, 25, 'Yêu cầu được chấp nhận', 'Yêu cầu dạy lớp Giáo Dục Công Dân  của bạn đã được chấp nhận!', 'request_accepted', 13, 0, '2025-11-20 20:47:36', '2025-11-20 20:47:36'),
(9, 25, 'Yêu cầu bị từ chối', 'Yêu cầu kết nối lớp học của bạn đã bị từ chối.', 'request_rejected', 12, 0, '2025-11-20 20:47:39', '2025-11-20 20:47:39'),
(10, 25, 'Yêu cầu dạy mới', 'Một gia sư đã đăng ký dạy lớp: Địa Lý - ', 'request_received', 12, 0, '2025-11-21 00:49:38', '2025-11-21 00:49:38'),
(11, 24, 'Yêu cầu được chấp nhận', 'Yêu cầu dạy lớp Địa Lý  của bạn đã được chấp nhận!', 'request_accepted', 12, 1, '2025-11-21 00:50:34', '2025-11-21 00:54:16');

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
(33, 'App\\Models\\TaiKhoan', 11, '1@gmail.com', '31ce1058bd84834f2ff17465168cfe49b8d99655853340bbb24862462bc89948', '[\"*\"]', '2025-11-17 22:02:57', NULL, '2025-11-17 21:29:28', '2025-11-17 22:02:57'),
(48, 'App\\Models\\TaiKhoan', 14, 'admin@gmail.com', '2c2782dc592e084271889b6c3093aa946f1f01d6828cdff2c59a92cbee743065', '[\"*\"]', '2025-11-20 05:31:07', NULL, '2025-11-20 05:30:39', '2025-11-20 05:31:07'),
(51, 'App\\Models\\TaiKhoan', 25, 'hakachi@gmail.com', '7c1b86fc50316938cac9210a4208035954df257ff6911ff3bfa4da2ab052c224', '[\"*\"]', '2025-11-20 08:59:50', NULL, '2025-11-20 08:48:39', '2025-11-20 08:59:50'),
(52, 'App\\Models\\TaiKhoan', 24, 'minhluandz303@gmail.com', 'a1f8c1027fcef34009b032140eeda1066395b49f5b3de5193f9f2470d11fcca9', '[\"*\"]', '2025-11-20 08:58:29', NULL, '2025-11-20 08:48:46', '2025-11-20 08:58:29'),
(61, 'App\\Models\\TaiKhoan', 24, 'minhluandz303@gmail.com', 'cc673bacaf9e1d042e68fb617274cba6c650593400e468c19315eaa488897671', '[\"*\"]', '2025-11-21 06:23:49', NULL, '2025-11-20 20:47:19', '2025-11-21 06:23:49'),
(63, 'App\\Models\\TaiKhoan', 24, 'minhluandz303@gmail.com', 'ae07978a022c50261b955bc3fa5497230fc7809c3308e505b3227d5026bb3836', '[\"*\"]', '2025-11-20 21:13:36', NULL, '2025-11-20 21:12:25', '2025-11-20 21:13:36'),
(68, 'App\\Models\\TaiKhoan', 25, 'hakachi@gmail.com', '77eb74f164921b516b72c5202652642b0864fca599b90485328f263d565cec87', '[\"*\"]', '2025-11-21 00:58:12', NULL, '2025-11-21 00:58:11', '2025-11-21 00:58:12'),
(69, 'App\\Models\\TaiKhoan', 25, 'hakachi@gmail.com', '791825c4c4026dc05e8f3d01d8f68d202431eb13adb9ae3b8b00ff36c3e2ae15', '[\"*\"]', '2025-11-21 03:25:17', NULL, '2025-11-21 03:24:48', '2025-11-21 03:25:17'),
(71, 'App\\Models\\TaiKhoan', 24, 'minhluandz303@gmail.com', '8a38f3bcf9e5a12d6da63dbb3a8202662a8f05edd9a133f74054d61ce8f37d87', '[\"*\"]', '2025-11-21 09:31:35', NULL, '2025-11-21 06:55:01', '2025-11-21 09:31:35');

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
(23, 1),
(24, 2),
(25, 3),
(26, 3),
(27, 3);

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

--
-- Đang đổ dữ liệu cho bảng `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('2DT7j8ryxxgwmrTkaiTzNHeSRcgcWnrtGIUVPdpS', 25, '123.20.13.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUzdKQ2pLM1d6RmVHZW91YVFWc3pSbXNKRHlta21DU25ZVjdkQTA0ciI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjA6Imh0dHA6Ly90dXRvcmNvbmVjdHN0dWRlbnQub25saW5lL25ndW9paG9jL2xvcC1ob2MvMTUvZGUtbmdoaSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI1O30=', 1763742772),
('7dqAw65haitQeLIkjhUv96mOKQoMH8SYW9JdYabX', NULL, '171.242.198.15', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiejJOVGYxV3hJSDhJSlZ0MHV3WEpMaU5iZVB4THZ2ajBSeU9uWWFMbCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTM6Imh0dHBzOi8vdHV0b3Jjb25lY3RzdHVkZW50Lm9ubGluZS9yZWdpc3Rlcj9yb2xlPXR1dG9yIjt9fQ==', 1763737835),
('hliDdiLdmacxOMMqAQTBhUyqUF8jWIkuPccMEnQc', NULL, '173.252.107.58', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib0FlVlpmelBDdnZ6VHFWeVNRMGtPbzBVbmQ1RDBHWk5tWk9BWEd4aSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly90dXRvcmNvbmVjdHN0dWRlbnQub25saW5lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1763736467),
('L8ee8uIPYAURIzgBrjeWEr9pobCu3N4g1crVMRE1', NULL, '213.226.101.222', 'Python/3.11 aiohttp/3.11.11', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMzJKYTNxVlVJc0ZGNTlCTUpoTTRZZ0lBUkVlSTJWUHJCVkNTR3Y3VSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly90dXRvcmNvbmVjdHN0dWRlbnQub25saW5lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1763735698),
('nOIeyIkd4zP2SDaAEBdNo9b9lRL4vUgTz4U1diV4', NULL, '66.220.149.38', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZDJMOFNwNmhjM25odURJZjJvYjMzeURzaWJ1akFCTWR3TWo2YzkwUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTgxOiJodHRwOi8vdHV0b3Jjb25lY3RzdHVkZW50Lm9ubGluZS8/ZmJjbGlkPUl3WlhoMGJnTmhaVzBDTVRFQWMzSjBZd1poY0hCZmFXUU1NalUyTWpneE1EUXdOVFU0QUFFZU5Cc2RFclF5RU1fWFNmRklpY0JGYXZMd3lva0RDeDVRMV9KYTlTeTRUd2hSWUtBSFlQbnpucFZCbnNrX2FlbV9xTEw2RnN0SEh0MDZiX0h0RUF3dEhRIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1763736420),
('RArgNfbZp1KXYvVoi6j7dTTEN0snDlf33T4fMhCp', NULL, '176.53.219.119', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia2x5UmZsMW9jOFJyc2xRSEJNUTcyUzZTdDVYWUtsekE5T2txZnlGZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly90dXRvcmNvbmVjdHN0dWRlbnQub25saW5lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1763738577),
('RzgmoTtU0WFr3AnR3vWsV47ghGdr7EXMvM0StuZD', 24, '116.111.187.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiYnZveXRtUmpmR2ZWaTkyV2t6NGIzTDBDUjB0SzNJV3VFdnlDdXRDVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDc6Imh0dHA6Ly90dXRvcmNvbmVjdHN0dWRlbnQub25saW5lL2dpYXN1L2xpY2gtaG9jIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjQ7czo1MjoibG9naW5fYWRtaW5fNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyMzt9', 1763743616);

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
(23, 'admin@gmail.com', '$2y$12$KzpCotB8W2EaoN8uVixfK.C18RxqrcCnDh7P2B6Nuik30X85ZJYK2', '0900000000', 1, '2025-11-20 22:30:24'),
(24, 'minhluandz303@gmail.com', '$2y$12$59v2GUA8Wb5kc/Be3oXKeO3oQJAXI9Xdd1z4n2j/lM9aVdQEnGEgK', '0389137204', 1, '2025-11-20 22:31:58'),
(25, 'hakachi@gmail.com', '$2y$12$OvVMvlc7MrSQC67zL1aJ7eoSxMC23KmS9ZbzlRWwo4nXpwzAnXQsK', '0938140224', 1, '2025-11-20 22:33:36'),
(26, 'minh@gmail.com', '$2y$12$5K.g6Caw5ESDj0Ct1fgWj.keb4CfdFVElobJt4zxARoX38SftPYq6', '0912374859', 1, '2025-11-21 20:02:03'),
(27, 'minh1@gmail.com', '$2y$12$BXE1q64nesvD6J4SFhiDCen2Qqe00bm6P1YBMRsB6n30iNiueimYe', '0901310113', 1, '2025-11-21 21:56:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ThoiGianDay`
--

CREATE TABLE `ThoiGianDay` (
  `ThoiGianDayID` int(11) NOT NULL,
  `SoBuoi` int(11) NOT NULL,
  `BuoiHoc` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(23, 14, 13, 24, 'GiaSu', 'Pending', NULL, '2025-11-21 16:30:15', '2025-11-21 16:30:15'),
(24, 15, 13, 24, 'GiaSu', 'Accepted', NULL, '2025-11-21 16:32:44', '2025-11-21 16:32:52');

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
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

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
  MODIFY `GiaoDichID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `GiaSu`
--
ALTER TABLE `GiaSu`
  MODIFY `GiaSuID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `KhieuNai`
--
ALTER TABLE `KhieuNai`
  MODIFY `KhieuNaiID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `KhoiLop`
--
ALTER TABLE `KhoiLop`
  MODIFY `KhoiLopID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `LichHoc`
--
ALTER TABLE `LichHoc`
  MODIFY `LichHocID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;

--
-- AUTO_INCREMENT cho bảng `LopHocYeuCau`
--
ALTER TABLE `LopHocYeuCau`
  MODIFY `LopYeuCauID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  MODIFY `NguoiHocID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT cho bảng `TaiKhoan`
--
ALTER TABLE `TaiKhoan`
  MODIFY `TaiKhoanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

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
  MODIFY `YeuCauID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
-- Các ràng buộc cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `TaiKhoan` (`TaiKhoanID`) ON DELETE CASCADE;

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
