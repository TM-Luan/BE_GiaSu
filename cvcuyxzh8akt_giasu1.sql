-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th10 20, 2025 lúc 10:13 PM
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
(6, 8, 17, 288000, '2025-11-19 04:04:31', 'Thành công', 'Thanh toán phí nhận lớp 8', 'TXN_1763525071_17', 'MoMo');

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
(9, 15, 'Trần Minh Luân', NULL, 'Nam', NULL, NULL, NULL, 'Bằng cử nhân', NULL, NULL, NULL, NULL, 'Chưa có kinh nghiệm', 'https://i.ibb.co/hG8s2hK/109e3788a14e.jpg', 25, 1),
(10, 17, 'Nguyễn Trọng Lễ', NULL, NULL, NULL, NULL, NULL, 'chứng chỉ tin học', NULL, NULL, NULL, NULL, 'Chưa có kinh nghiệm', 'https://i.ibb.co/BHs4P3LN/3513db476ad7.jpg', 26, 2),
(11, 19, 'conchimcuccu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2),
(12, 22, 'Lê Thành Tiến', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2);

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
(3, 16, 'vãiloz', '2025-11-19 15:30:07', 'TiepNhan', NULL, NULL, NULL, 8);

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
(93, 93, 8, '19:00:00', '20:00:00', '2025-11-23', 'SapToi', NULL, '2025-11-19 04:04:32', 1),
(94, 93, 8, '19:00:00', '20:00:00', '2025-11-30', 'SapToi', NULL, '2025-11-19 04:04:32', 1),
(95, 93, 8, '19:00:00', '20:00:00', '2025-12-07', 'SapToi', NULL, '2025-11-19 04:04:32', 1),
(96, 93, 8, '19:00:00', '20:00:00', '2025-12-14', 'SapToi', NULL, '2025-11-19 04:04:32', 1),
(97, 93, 8, '19:00:00', '20:00:00', '2025-11-24', 'SapToi', NULL, '2025-11-19 04:04:32', 1),
(98, 93, 8, '19:00:00', '20:00:00', '2025-12-01', 'SapToi', NULL, '2025-11-19 04:04:32', 1),
(99, 93, 8, '19:00:00', '20:00:00', '2025-12-08', 'SapToi', NULL, '2025-11-19 04:04:32', 1),
(100, 93, 8, '19:00:00', '20:00:00', '2025-12-15', 'SapToi', NULL, '2025-11-19 04:04:32', 1);

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
(8, 6, 10, 'Online', 120000, 60, 'DangHoc', 1, 'ddgggg', 27, 10, 2, 2, 'tối t2 t3 t4 t5', '2025-11-18 14:58:15'),
(9, 6, NULL, 'Online', 300000, 120, 'TimGiaSu', 1, 'nhận lớp đi e', 5, 6, 2, 2, 'thứ 2,3 4 5', '2025-11-19 22:04:58'),
(10, 6, NULL, 'Online', 200000000000, 120, 'TimGiaSu', 1, 'gei', 8, 3, 2, 3, 'thứ 7 chủ nhật thứ 9', '2025-11-19 22:27:19');

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
(6, 16, 'Trần Minh Hiếu', '2003-08-13', 'Nam', 'Ấp tân tây 5, Tân Đông,Đồng Tháp', 'https://i.ibb.co/RGND0Cnx/c63a292846ac.jpg', 1),
(7, 18, 'con chim cúc cu', NULL, NULL, NULL, NULL, 1),
(8, 20, 'luân cu bự', NULL, NULL, NULL, NULL, 1),
(9, 21, 'fffff', NULL, NULL, NULL, NULL, 1);

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
(48, 'App\\Models\\TaiKhoan', 14, 'admin@gmail.com', '2c2782dc592e084271889b6c3093aa946f1f01d6828cdff2c59a92cbee743065', '[\"*\"]', '2025-11-20 05:31:07', NULL, '2025-11-20 05:30:39', '2025-11-20 05:31:07');

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
(14, 1),
(15, 2),
(16, 3),
(17, 2),
(18, 3),
(19, 2),
(20, 3),
(21, 3),
(22, 2);

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
('0IYwRsHzQJXL4NRKC8GI6darHdiVEvJMnoyXwlSf', NULL, '93.158.92.11', 'Mozilla/5.0 (X11; CrOS x86_64 14541.0.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.3', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib05VVXBweW10TFhsUWk4UXd2MHRpWFBEY2c1RmtBaEdQNENvZDVOWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly90dXRvcmNvbmVjdHN0dWRlbnQub25saW5lL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1763645328),
('47zYySHr57z3ZysdFiAPDqODGmuZMLdq0sZHRVKC', NULL, '43.153.96.79', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZWk4TjdoZ1dLS3g2MFhrVVRpNlVzTXBtdDJTa0lDMzVGQ0xWR0I5RyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly93d3cudHV0b3Jjb25lY3RzdHVkZW50Lm9ubGluZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1763638098),
('9fEvUiq9kgi0CbrfLGE01hezKr18VR8AuMCi3dey', NULL, '23.27.145.196', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVzJnMXdHWGJVZlRQSkMwUkdsZ0F4MGM5TkJRdUcxek1ubVRMd1hqUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHBzOi8vdHV0b3Jjb25lY3RzdHVkZW50Lm9ubGluZS9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1763643408),
('ajb1VyZJn3CjzhpnXtv5vq707z99wtA3LWuSpJS7', NULL, '93.158.91.239', 'Mozilla/5.0 (X11; CrOS x86_64 14541.0.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.3', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoialhNb1hPVElSdlp2bHZXQUYwUmVWUTZkVzQzeVRkbWRRYm1ncWZlayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly90dXRvcmNvbmVjdHN0dWRlbnQub25saW5lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1763645328),
('KRGxe23ihOCTGypcOXohd7bLH2WWVe7zfRw0DJyL', NULL, '43.153.96.79', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaXpLTkpTSWZKYnhVdEhLY2htUWtER3QzNXNEUW9IeTJLUGV6VHlmSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly93d3cudHV0b3Jjb25lY3RzdHVkZW50Lm9ubGluZS9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1763638099),
('Lm5lxSsgldV9yw1skcOVZTQjfqFw2iLbXnQt9zmq', NULL, '149.57.180.42', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZlhpZG1rYUs0dFJqVDdUZDNvTXo5MnpyRjRqZHBFZGs4eXZLalQ0NCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHBzOi8vdHV0b3Jjb25lY3RzdHVkZW50Lm9ubGluZS9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1763647237),
('o3t4kD95KDxomcLb7huPVVmjKv7AnP3PrZOEBw8e', NULL, '178.128.168.48', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUDFPRzBpcEhEMXVvbzhmQUp2Y1R5RTJUYjdISnFmNlk3OFQyaXFDUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHBzOi8vdHV0b3Jjb25lY3RzdHVkZW50Lm9ubGluZS9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1763643243),
('S4h4YqesOQYxQIzdMULewCiYGMehI2aCEyXBEjur', NULL, '178.128.168.48', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR1JUZXQ4NUc1Mk9hQkNRN0xkT2hsMTVUMWFMdkM2akY1eVJqdjVNRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly90dXRvcmNvbmVjdHN0dWRlbnQub25saW5lL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1763643234);

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
(14, 'admin@gmail.com', '$2y$12$0NJwrD6JnRjYDV7d69Ucs.LovWsnudSgagom1yQnkw6y5mF0FUa7q', '0000000000', 1, '2025-11-18 12:58:45'),
(15, 'minhluandz303@gmail.com', '$2y$12$vDQePIOowzKV/nOljhJ5wOu4EJKe0xehTL7lNCE7V4Oc95CmAahiO', '0934140224', 1, '2025-11-18 13:03:08'),
(16, 'hakachi@gmail.com', '$2y$12$wz6DEdra98.vNAJXHCL0WeQRIRqZ4ArAkBDcBXYksNOuInSc.Kg6O', '0365137204', 1, '2025-11-18 13:04:36'),
(17, 'hakachi101@gmail.com', '$2y$12$f2.VEbMuFqAQ5zRBVWVEfu2vPy4.QY6O1i5AiJRqO2w2kV3w63kFu', '0854123678', 1, '2025-11-19 09:57:57'),
(18, 'conheocuame@gmail.c', '$2y$12$sQZAbSzC.VwN23ZHaEq/se9oM/aT/RicN6SNQoghoyrJvy2iDXi4a', '0909999999', 1, '2025-11-19 22:35:41'),
(19, 'hogiogohoy@gmail', '$2y$12$XlNvh74lY/7wOu9Jp/nNY.oTWm5i1RMTNEsZc/sAEFW0u5z7nKsC2', '0909999113', 1, '2025-11-19 22:37:18'),
(20, 'hogiogohoy@gmail.com', '$2y$12$bGcPQi3uQlgYvxObeI0VseOZm6zjyBmnB.wTb.ofXJYWlImTjuGku', '1111111111', 1, '2025-11-19 22:42:29'),
(21, 'ggggg@gmail', '$2y$12$4M8nhPCCjrykM.59DD9dweEHJigT1zkWaasJ5JTSRLA6D7jfe8jnC', '33333', 1, '2025-11-19 22:44:15'),
(22, '1@1', '$2y$12$dPHAHMeYGJLexrig6PnDBOTqc51NCFNwrU86LSArMNh9zBG3u9X.G', '0936137255', 1, '2025-11-20 09:07:00');

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
(11, 8, 9, 16, 'NguoiHoc', 'Cancelled', NULL, '2025-11-18 07:58:43', '2025-11-19 02:56:22'),
(12, 8, 9, 15, 'GiaSu', 'Cancelled', NULL, '2025-11-19 03:08:59', '2025-11-19 03:09:17'),
(13, 8, 9, 15, 'GiaSu', 'Rejected', NULL, '2025-11-19 03:09:22', '2025-11-19 03:17:06'),
(14, 8, 10, 16, 'NguoiHoc', 'Accepted', NULL, '2025-11-19 03:16:22', '2025-11-19 03:17:06'),
(15, 9, 9, 16, 'NguoiHoc', 'Pending', 'vãiloz', '2025-11-19 15:26:19', '2025-11-19 15:26:19'),
(16, 10, 9, 16, 'NguoiHoc', 'Pending', 'cứu bé', '2025-11-19 15:31:48', '2025-11-19 15:31:48');

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
  MODIFY `GiaoDichID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `GiaSu`
--
ALTER TABLE `GiaSu`
  MODIFY `GiaSuID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `KhieuNai`
--
ALTER TABLE `KhieuNai`
  MODIFY `KhieuNaiID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `KhoiLop`
--
ALTER TABLE `KhoiLop`
  MODIFY `KhoiLopID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `LichHoc`
--
ALTER TABLE `LichHoc`
  MODIFY `LichHocID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT cho bảng `LopHocYeuCau`
--
ALTER TABLE `LopHocYeuCau`
  MODIFY `LopYeuCauID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  MODIFY `NguoiHocID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT cho bảng `TaiKhoan`
--
ALTER TABLE `TaiKhoan`
  MODIFY `TaiKhoanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

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
  MODIFY `YeuCauID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
