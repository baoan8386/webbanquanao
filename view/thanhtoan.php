<?php

require_once('../model/thanhtoan.php');

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['id'])) {
    $_SESSION['message'] = "Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.";
    header("Location: ../controller/index.php?act=login");
    exit();
}

$id_user = $_SESSION['id']; // ID người dùng từ session

// Lấy thông tin giỏ hàng từ model
$cart_items = getCartItems($id_user);

$total = 0;

foreach ($cart_items as $item) {
    $total += $item['thanh_tien'];
}

// Xử lý form thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['pt_thanhtoan'] ?? '';
    $address = trim($_POST['diachi'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // if ($payment_method === 'chuyenkhoan') {
    //     header("Location: ../view/payments_momo.php");
    //     exit();
    // }

     // Kiểm tra đầu vào
     if (empty($address) || empty($phone) || !is_numeric($phone)) {
        echo "<script>alert('Vui lòng nhập địa chỉ và số điện thoại hợp lệ!');</script>";
    } else {
        // Gọi function xử lý thanh toán từ model một lần duy nhất
        if (createOrder($id_user, $cart_items, $payment_method, $address, $phone)) {
            // Chuyển hướng đến trang cảm ơn (nếu cần)
            if ($payment_method === 'chuyenkhoan') {
                header("Location: ../view/payments_momo.php");
                exit();
            } else {
                header("Location: ../view/thankyou.php");
                exit();
            }
        } else {
            echo "<script>alert('Có lỗi xảy ra khi thanh toán.');</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán</title>
    <link rel="stylesheet" href="../view/css/carts.css">
    <link rel="stylesheet" href="../view/css/products.css">
</head>
<body>
    <div class="banner">
        <img src="https://file.hstatic.net/200000538469/file/ready__4__4ee0893ec85d43779b232da34a1bb5db.png" alt="" height="280px">
    </div>

    <div class="main">
        <h2>THANH TOÁN</h2>
        <table>
            <tr class="nen">
                <th>Số Thứ Tự</th>
                <th >Hình ảnh</th>
                <th >Sản phẩm</th>
                <th >Kính thước</th>
                <th >Màu sắc</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th colspan="2">Thành tiền</th>
            </tr>
            <?php
            $i = 1;
            foreach ($cart_items as $item) {
                echo '
                    <tr>
                        <td>' . $i++ . '</td>
                        <td><img src="../view/images/' . $item['hinh'] . '" alt="' . $item['tensp'] . '" class="product-image"></td>
                        <td>' . $item['tensp'] . '</td>
                         <td>' . $item['size_name'] . '</td> 
                        <td>' . $item['color_name'] . '</td> 
                        <td>' . number_format($item['gia']) . ' VNĐ</td>
                        <td>' . $item['soluong'] . '</td>
                        <td>' . number_format($item['thanh_tien']) . ' VNĐ</td>
                    </tr>
                ';
            }
            
                
            ?>
            <tr>
                <th colspan="6">Tổng Tiền</th>
                <th colspan="2" class="tong"><?php echo number_format($total) . ' VNĐ'; ?></th>
            </tr>

            <?php $_SESSION['total_amount'] = $total; ?>

        </table>

        <form action="" method="POST">
            <h3>Thông tin thanh toán</h3>
            <label for="pt_thanhtoan">Phương thức thanh toán:</label>
            <select name="pt_thanhtoan" id="pt_thanhtoan">
                <option value="tienmat">Thanh toán khi nhận hàng</option>
                <option value="chuyenkhoan">Chuyển khoản ngân hàng</option>
            </select>

            <label for="diachi">Địa chỉ giao hàng:</label>
            <input type="text" name="diachi" id="diachi" required>

            <label for="phone">Số điện thoại:</label>
            <input type="text" name="phone" id="phone" required placeholder="Nhập số điện thoại">

            <button type="submit">Thanh Toán</button>
        </form>
    </div>
</body>
</html>




<style>
    /* Định dạng form thanh toán */
    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    /* Khu vực chính */
    .main {
        width: 80%;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Bảng hiển thị giỏ hàng */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    table th, table td {
        text-align: center;
        padding: 10px;
        border: 1px solid #ddd;
    }

    .product-image {
        width: 60px;
        height: auto;
        margin-left: 40px;
    }

    .tong {
        text-align: right;
        font-weight: bold;
        color: #e74c3c;
    }

    /* Form thanh toán */
    form input[type="text"],
    form select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        box-sizing: border-box;
    }

    form button {
        padding: 10px;
        background-color: #28a745;
        color: white;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    form button:hover {
        background-color: #218838;
    }
</style>
