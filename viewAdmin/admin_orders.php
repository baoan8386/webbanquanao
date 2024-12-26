<div class="main">
    <main>
        <h2 style="text-align: center;">Danh Sách Đơn Hàng</h2>
        <?php
            // Lấy danh sách tất cả các đơn hàng
            $orders = getOrdersAdmin();

            echo "<table border='1' cellpadding='10' cellspacing='0' style='width: 100%; text-align: center;'>";
            echo '
                <tr>
                    <th>Mã Đơn Hàng</th>
                    <th>ID Người Dùng</th>
                    <th>Ngày Đặt Hàng</th>
                    <th>Trạng Thái</th>
                    <th>Tổng Tiền (VNĐ)</th>
                    <th>Phương Thức Thanh Toán</th>
                    <th>Địa Chỉ</th>
                    <th>Số Điện Thoại</th>
                    <th colspan="2">Tính Năng</th>
                </tr>
            ';

            // Duyệt qua từng đơn hàng và hiển thị
            foreach ($orders as $order) {
                extract($order);

                // Link xóa đơn hàng
                $deleteLink = "../controller/indexAdmin.php?act=donhang&delete_id=" . $order_id;
                //Link xem đơn hàng
                $detailLink = "indexAdmin.php?act=donhang&details_id=" . $order_id;

                echo '
                <tr>
                    <td>' . htmlspecialchars($order_id) . '</td>
                    <td>' . htmlspecialchars($id_user) . '</td>
                    <td>' . htmlspecialchars($order_date) . '</td>
                    <td>' . htmlspecialchars($trangthai) . '</td>
                    <td>' . number_format($tongtien, 2) . '</td>
                    <td>' . htmlspecialchars($pt_thanhtoan) . '</td>
                    <td>' . htmlspecialchars($diachi) . '</td>
                    <td>' . htmlspecialchars($phone) . '</td>
                    <td class=ac> <a href="' . $detailLink . '">Xem Chi Tiết</a></td>
                    <td class="ad">
                        ' . ($trangthai === 'dahuy' ? '<a onclick="return Deluser(\'Bạn có chắc chắn muốn xóa đơn hàng này?\')" href="' . $deleteLink . '">
                        <i class="bx bx-message-square-minus"></i></a>' : '<a>Không khả dụng</a>') . '
                    </td>
                </tr>
                ';
            }

            echo "</table>";

            // Hiển thị chi tiết đơn hàng nếu được yêu cầu
            if (isset($_GET['details_id'])) {
                $details_id = intval($_GET['details_id']);
                $orderDetails = getOrderDetails($details_id);

                if ($orderDetails) {
                    echo "<h3 style='text-align: center;'>Chi Tiết Đơn Hàng #{$details_id}</h3>";
                    echo "<table border='1' cellpadding='10' cellspacing='0' style='width: 100%; text-align: center;'>";
                    echo '
                        <tr>
                            <th>STT</th>
                            <th>Sản Phẩm</th>
                            <th>Hình Ảnh</th>
                            <th>Màu Sắc</th>
                            <th>Kích Thước</th>
                            <th>Số Lượng</th>
                            <th>Thành Tiền (VNĐ)</th>
                        </tr>
                    ';

                    foreach ($orderDetails as $index => $detail) {
                        echo '
                        <tr>
                            <td>' . ($index + 1) . '</td>
                            <td>' . htmlspecialchars($detail['tensp']) . '</td>
                            <td><img src="../view/images/' . htmlspecialchars($detail['hinh']) . '" style="width: 50px; height: 50px;" alt="Hình sản phẩm"></td>
                            <td>' . htmlspecialchars($detail['color_name']) . '</td>
                            <td>' . htmlspecialchars($detail['size_name']) . '</td>
                            <td>' . htmlspecialchars($detail['soluong']) . '</td>
                            <td>' . number_format($detail['thanh_tien'], 2) . '</td>
                        </tr>
                        ';
                    }

                    echo "</table>";
                } else {
                    echo "<p style='text-align: center;'>Không tìm thấy chi tiết đơn hàng.</p>";
                }
            }
        ?>
    </main>
</div>

<?php
    // Xử lý xóa đơn hàng khi admin nhấn vào nút xóa
    if (isset($_GET['delete_id'])) {
        $order_id = intval($_GET['delete_id']);
        if (donhang_delete($order_id)) {
            echo "<script>alert('Xóa đơn hàng thành công');</script>";
            echo "<script>window.location.href = 'indexAdmin.php?act=donhang';</script>";
        } else {
            echo "<script>alert('Xóa đơn hàng thất bại');</script>";
        }
    }
?>
