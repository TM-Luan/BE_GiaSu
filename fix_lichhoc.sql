-- ⚡ FIX: Thêm cột IsLapLai và LichHocGocID vào bảng LichHoc/lichhoc
-- Chạy file này nếu gặp lỗi "Unknown column 'IsLapLai'"

-- Thêm cột LichHocGocID (để tham chiếu lịch gốc khi lặp lại)
ALTER TABLE LichHoc 
ADD COLUMN LichHocGocID INT DEFAULT NULL AFTER LichHocID,
ADD INDEX idx_lichhoc_goc (LichHocGocID),
ADD CONSTRAINT fk_lichhoc_lichhocgoc 
  FOREIGN KEY (LichHocGocID) REFERENCES LichHoc(LichHocID) ON DELETE CASCADE;

-- Thêm cột IsLapLai (đánh dấu lịch có lặp lại hay không)
ALTER TABLE LichHoc 
ADD COLUMN IsLapLai TINYINT(1) NOT NULL DEFAULT 0 AFTER NgayTao,
ADD INDEX idx_lichhoc_lap_lai (IsLapLai);

-- Cập nhật dữ liệu cũ
UPDATE LichHoc SET LichHocGocID = NULL, IsLapLai = 0;

-- Kiểm tra kết quả
SELECT LichHocID, LichHocGocID, LopYeuCauID, NgayHoc, IsLapLai FROM LichHoc;
