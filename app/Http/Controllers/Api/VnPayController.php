<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiaoDich;
use App\Models\LopHocYeuCau;
use Illuminate\Support\Facades\DB;

class VnPayController extends Controller
{
    // 1. Tạo URL Thanh toán
    public function createPaymentUrl(Request $request)
    {
        $request->validate([
            'LopYeuCauID' => 'required|exists:LopHocYeuCau,LopYeuCauID',
            'SoTien' => 'required|numeric',
        ]);

        $user = $request->user();
        $lopHoc = LopHocYeuCau::find($request->LopYeuCauID);

        // Tạo mã giao dịch duy nhất
        $vnp_TxnRef = time() . "_" . $user->TaiKhoanID;

        // Lưu giao dịch tạm (Pending) vào DB
        GiaoDich::create([
            'LopYeuCauID' => $lopHoc->LopYeuCauID,
            'TaiKhoanID' => $user->TaiKhoanID,
            'SoTien' => $request->SoTien,
            'LoaiGiaoDich' => 'VNPAY',
            'GhiChu' => 'Thanh toán phí nhận lớp',
            'ThoiGian' => now(),
            'TrangThai' => 'ChoXuLy',
            'MaGiaoDich' => $vnp_TxnRef
        ]);

        // Cấu hình VNPAY
        $vnp_Url = env('VNP_URL');
        $vnp_Returnurl = env('VNP_RETURN_URL');
        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');

        $vnp_OrderInfo = "Thanh toan phi lop " . $lopHoc->LopYeuCauID;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $request->SoTien * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return response()->json([
            'success' => true,
            'message' => 'Tạo link thành công',
            'payment_url' => $vnp_Url
        ]);
    }

    // 2. Xử lý kết quả trả về (QUAN TRỌNG: Trả về HTML đẹp)
    public function vnpayReturn(Request $request)
    {
        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_TxnRef = $request->vnp_TxnRef;
        $vnp_ResponseCode = $request->vnp_ResponseCode;

        // Kiểm tra chữ ký
        if ($secureHash == $vnp_SecureHash) {
            if ($vnp_ResponseCode == '00') {
                // --- THANH TOÁN THÀNH CÔNG ---
                $giaoDich = GiaoDich::where('MaGiaoDich', $vnp_TxnRef)->first();

                if ($giaoDich && $giaoDich->TrangThai != 'ThanhCong') {
                    DB::beginTransaction();
                    try {
                        $giaoDich->update(['TrangThai' => 'ThanhCong']);

                        $lopHoc = LopHocYeuCau::find($giaoDich->LopYeuCauID);
                        if ($lopHoc) {
                            $lopHoc->update(['TrangThaiThanhToan' => 'DaThanhToan']);
                        }
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                    }
                }

                // [QUAN TRỌNG] Trả về giao diện HTML báo thành công
                return response()->make('
        <!DOCTYPE html>
        <html>
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Thanh toán thành công</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 40px 20px; background-color: #f0f2f5; }
                .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 400px; margin: 0 auto; }
                .icon { color: #4CAF50; font-size: 60px; margin-bottom: 15px; }
                h1 { color: #333; font-size: 22px; margin-bottom: 10px; }
                p { color: #666; margin-bottom: 30px; line-height: 1.5; }
                .btn { 
                    display: block; 
                    width: 100%; 
                    padding: 15px 0; 
                    background: #2196F3; 
                    color: white; 
                    text-decoration: none; 
                    border-radius: 30px; 
                    font-weight: bold; 
                    font-size: 16px;
                    box-shadow: 0 4px 6px rgba(33, 150, 243, 0.3);
                }
            </style>
        </head>
        <body>
            <div class="card">
                <div class="icon">✓</div>
                <h1>Thanh toán thành công!</h1>
                <p>Giao dịch đã được ghi nhận.<br>Nhấn nút bên dưới để quay về ứng dụng.</p>
                
            <a href="giasuapp://app/return" class="btn">Quay lại Ứng dụng</a>
                
            </div>
        </body>
        </html>
    ');

            } else {
                // --- THẤT BẠI ---
                return response()->make('<h2 style="color:red;text-align:center;margin-top:50px">Thanh toán thất bại hoặc bị hủy!</h2>');
            }
        } else {
            return response()->make('<h2 style="color:red;text-align:center;margin-top:50px">Chữ ký không hợp lệ!</h2>');
        }
    }
}