-- -- Cập nhật trạng thái thanh toán cho dữ liệu cũ
-- -- Các lớp đã có giao dịch thành công
-- UPDATE LopHocYeuCau lop
-- INNER JOIN GiaoDich gd ON lop.LopYeuCauID = gd.LopYeuCauID
-- SET lop.TrangThaiThanhToan = 'DaThanhToan'
-- WHERE gd.TrangThai = 'Thành công';

-- -- Các lớp còn lại chưa thanh toán
-- UPDATE LopHocYeuCau
-- SET TrangThaiThanhToan = 'ChuaThanhToan'
-- WHERE TrangThaiThanhToan IS NULL;

ALTER TABLE LopHocYeuCau
ADD COLUMN TrangThaiThanhToan VARCHAR(50) NULL;
UPDATE LopHocYeuCau AS lop
INNER JOIN GiaoDich AS gd 
    ON lop.LopYeuCauID = gd.LopYeuCauID
SET lop.TrangThaiThanhToan = 'DaThanhToan'
WHERE gd.TrangThai = 'Thành công';
