<?php

if (isset($_POST['payUrl'])) {
    // Cấu hình thông tin MoMo
    $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
    $partnerCode = "MOMO";
    $accessKey = "F8BBA842ECF85";
    $secretKey = "K951B6PE1waDMi640xX08PD3vg6EkVlz";
    $orderId = time() . ""; // Mã đơn hàng duy nhất
    $redirectUrl = "http://localhost/DoAnChuyenNganhPHP/view/thankyou.php";
    $ipnUrl = "http://localhost/DoAnChuyenNganhPHP/view/thankyou.php";
    $extraData = ""; // Dữ liệu bổ sung

    // Thông tin thanh toán
    session_start(); // Khởi động session
    $total = $_SESSION['total_amount'];
    $requestId = time() . "";
    $amount = $total;
    $orderInfo = "Thanh toán MoMo đơn hàng #$orderId";
    $requestType = "captureWallet";

    // Tạo chữ ký
    $rawSignature = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
    $signature = hash_hmac("sha256", $rawSignature, $secretKey);

    // Chuẩn bị dữ liệu gửi đến API
    $data = [
        'partnerCode' => $partnerCode,
        'accessKey' => $accessKey,
        'requestId' => $requestId,
        'amount' => $amount,
        'orderId' => $orderId,
        'orderInfo' => $orderInfo,
        'redirectUrl' => $redirectUrl,
        'ipnUrl' => $ipnUrl,
        'extraData' => $extraData,
        'requestType' => $requestType,
        'signature' => $signature,
    ];

    $data_string = json_encode($data);

    // Gửi yêu cầu POST đến API
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string),
    ]);
    // Tắt kiểm tra SSL (chỉ cho thử nghiệm)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $result = curl_exec($ch);
    curl_close($ch);

    // Kiểm tra lỗi cURL nếu có
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        exit();
    }

    // Giải mã phản hồi JSON
    $response = json_decode($result, true);

    // Kiểm tra kết quả từ API
    if ($response === null) {
        echo "Lỗi: Không thể giải mã phản hồi từ MoMo.";
        exit();
    }

    if (isset($response['resultCode']) && $response['resultCode'] == 0) {
        // Thành công, chuyển hướng đến URL thanh toán
        if (isset($response['payUrl'])) {
            header("Location: " . $response['payUrl']);
            exit();
        } else {
            echo "Không tìm thấy URL thanh toán.";
        }
    } else {
        // Hiển thị lỗi nếu thanh toán thất bại
        echo "Lỗi khi tạo yêu cầu thanh toán: " . ($response['message'] ?? 'Không có thông báo lỗi');
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán với MoMo</title>
    <style>
    /* Reset mặc định */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Arial', sans-serif;
        background-color: #f0f2f5;
        color: #333;
        padding: 30px 10px;

    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    h1 {
        font-size: 28px;
        color: #007bff;
        text-align: center;
        margin-bottom: 20px;
    }

    .card {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin-top: 30px;
        transition: box-shadow 0.3s, transform 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .card-body {
        text-align: center;
    }

    h4 {
        font-size: 20px;
        margin-bottom: 25px;
    }

    .btn {
        padding: 12px 30px;
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        border: none;
        border-radius: 8px;
        transition: background-color 0.3s, transform 0.3s;
        cursor: pointer;
        width: wordwrap;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .btn-direct {
        background-color: #007bff;
        color: #fff;
    }

    .btn-direct:hover {
        background-color: #0056b3;
    }

    .btn-momo {
        background-color: #8E44AD;
        margin-top: 20px;
        color: #fff;
    }

    .btn-momo:hover {
        background-color: #6D2993;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 20px;
        }

        h1 {
            font-size: 24px;
        }

        .btn {
            font-size: 16px;
            padding: 10px 20px;
        }
    }
</style>
</head>
<body>
<div class="container mt-5">
   
   <div class="row justify-content-center">
       <div class="col-md-6">
           <div class="card">
               <div class="card-body text-center">
                <h1>Thanh toán với MoMo</h1>
                <form method="POST">
                    <button type="submit" name="payUrl" class="btn btn-momo">Thanh toán ngay</button>
                </form>
               </div>
           </div>
       </div>
   </div>
</div>

    
</body>
</html>