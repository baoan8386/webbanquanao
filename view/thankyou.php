<?php
session_start(); // Bắt đầu session


if (!isset($_SESSION['id'])) {
    die("Bạn chưa đăng nhập.");
}

$id_user = $_SESSION['id']; //


require_once '../model/config.php'; 

// Lấy thông tin đơn hàng mới nhất của người dùng
$query = "SELECT * FROM dathang WHERE id_user = :id_user ORDER BY order_date DESC LIMIT 1";
$params = [':id_user' => $id_user]; // Tham số cho truy vấn

// Thực hiện truy vấn
$order_result = pdo_query_with_params($query, $params);

// Kiểm tra nếu có đơn hàng
if (count($order_result) > 0) {
    $order = $order_result[0]; // Lấy dòng đầu tiên từ kết quả
    $order_id = $order['order_id'];  // Lấy mã đơn hàng
    $tongtien = number_format($order['tongtien'], 0, ',', '.') . ' VNĐ';
    $diachi = $order['diachi'];
    $phone = $order['phone']; // Lấy số điện thoại từ cơ sở dữ liệu
    $pt_thanhtoan = $order['pt_thanhtoan'] == 'tienmat' ? 'Thanh toán khi nhận hàng' : 'Chuyển khoản ngân hàng';
} else {
    die("Không tìm thấy đơn hàng.");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cảm ơn</title>
    <link rel="stylesheet" href="../view/css/thankyou.css">
</head>
<body>
    <div class="container">
        <div class="thankyou-message">
            <h1>Cảm ơn bạn đã đặt hàng!</h1>
            <p>Đơn hàng của bạn đã được xác nhận và sẽ được xử lý sớm nhất.</p>
        </div>

        <div class="order-info">
            <h2>Thông tin đơn hàng</h2>
            <table>
                <tr>
                    <th>Mã đơn hàng:</th>
                    <td><?php echo $order_id; ?></td>
                </tr>
                <tr>
                    <th>Tổng tiền:</th>
                    <td><?php echo $tongtien; ?></td>
                </tr>
                <tr>
                    <th>Phương thức thanh toán:</th>
                    <td><?php echo $pt_thanhtoan; ?></td>
                </tr>
                <tr>
                    <th>Địa chỉ giao hàng:</th>
                    <td><?php echo $diachi; ?></td>
                </tr>
                <tr>
                    <th>Số điện thoại:</th>
                    <td><?php echo $phone; ?></td>
                </tr>
            </table>
        </div>

        <div class="next-steps">
            <a href="../controller/index.php" class="btn">Trở về trang chủ</a>

             <!-- Nút xem đơn hàng -->
             <a href="../controller/index.php?act=order_details" class="btn">Xem Đơn Hàng</a>
        </div>
    </div>
</body>
</html>
