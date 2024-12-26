<?php
// Bao gồm model để sử dụng hàm getOrderDetails
include_once('../model/order_model.php');

// Kiểm tra xem người dùng đã đăng nhập chưa

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['id']; // Lấy ID người dùng từ session
$orders = getOrders($user_id); // Lấy danh sách đơn hàng của người dùng


// Kiểm tra yêu cầu hủy đơn hàng
if (isset($_GET['action']) && $_GET['action'] == 'cancel' && isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Gọi hàm cancelOrder để cập nhật trạng thái
    $result = cancelOrder($order_id); // Lưu kết quả trả về từ hàm cancelOrder
    if ($result === true) {
        // Thông báo thành công và chuyển hướng
        echo "<script>alert('Đơn hàng đã được hủy.'); window.location.href='index.php?act=order_details';</script>";
    } else {
        // Nếu có lỗi, thông báo cho người dùng
        echo "<script>alert('Có lỗi xảy ra khi hủy đơn hàng.'); window.location.href='index.php?act=order_details';</script>";
    }
}


?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Đơn hàng</title>
    <link rel="stylesheet" href="../view/css/carts.css">
    <link rel="stylesheet" href="../view/css/products.css">
    <link rel="stylesheet" href="../view/css/chitiedonhang.css">
</head>
<body>
    <h1>Danh sách Đơn hàng</h1>
    
    <table border="1">
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã đơn hàng</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
                

            </tr>
        </thead>
        <tbody>
    <?php foreach ($orders as $index => $order): ?>
        <tr>
            <td><?php echo $index + 1; ?></td>
            <td><?php echo $order['order_id']; ?></td>
            <td><?php echo $order['order_date']; ?></td>
            <td><?php echo number_format($order['tongtien'], 2); ?> VND</td>
            <td><?php echo $order['trangthai']; ?></td>
            <td>
                <a href="index.php?act=order_details&order_id=<?php echo $order['order_id']; ?>">Xem chi tiết</a>
                <!-- Nút hủy đơn hàng -->
                <?php if ($order['trangthai'] != 'dahuy'): ?>
                            <a href="index.php?act=order_details&action=cancel&order_id=<?php echo $order['order_id']; ?>" 
                               onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">Hủy</a>
                        <?php else: ?>
                            Đơn đã hủy
                        <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

    </table>

    <?php
    // Nếu có tham số 'order_id' trong URL, lấy chi tiết đơn hàng
    if (isset($_GET['order_id'])) {
        $order_id = $_GET['order_id'];
        $order_details = getOrderDetails($order_id);  // Gọi hàm từ model để lấy chi tiết đơn hàng
        echo '<h2>Chi tiết đơn hàng ' . $order_id . '</h2>';
        echo '<table border="1">
                <thead>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Hình ảnh</th>
                        <th>Màu sắc</th>
                        <th>Kích cỡ</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($order_details as $detail) {
            echo '<tr>';
            echo '<td>' . $detail['tensp'] . '</td>';
            echo '<td><img src="../view/images/' . (isset($detail['hinh']) ? $detail['hinh'] : 'default.jpg') . '" alt=""></td>';

            echo '<td>' . $detail['color_name'] . '</td>';
            echo '<td>' . $detail['size_name'] . '</td>';
            echo '<td>' . $detail['soluong'] . '</td>';
            echo '<td>' . number_format($detail['thanh_tien'], 0) . ' VND</td>';
            echo '</tr>';
        }
        echo '</tbody>
              </table>';
    }
    ?>
   
</body>
</html>
