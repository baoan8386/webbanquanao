<?php
// Kết nối cơ sở dữ liệu
include_once('config.php');

// Hàm lấy danh sách đơn hàng
function getOrdersAdmin() {
    $sql = "SELECT * FROM dathang";
    return pdo_query($sql); // Hàm pdo_query() để lấy tất cả dữ liệu đơn hàng
}



function getOrders($user_id) {
    $query = "
        SELECT order_id, order_date, tongtien, trangthai
        FROM dathang
        WHERE id_user = ?";
    
    try {
        $stmt = pdo_con()->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Lỗi truy vấn đơn hàng: " . $e->getMessage());
    }
}

// Hàm lấy chi tiết đơn hàng
function getOrderDetails($order_id) {
    $sql = "SELECT cd.orderdetails_id, sp.tensp, sp.hinh, c.color_name, s.size_name, cd.soluong, cd.thanh_tien
            FROM chitietdathang cd
            JOIN sanpham_chitiet sc ON cd.id_chitiet_sp = sc.id
            JOIN sanpham sp ON sc.id_sanpham = sp.id
            JOIN color c ON sc.color_id = c.color_id
            JOIN size s ON sc.size_id = s.size_id
            WHERE cd.order_id = :order_id";  // Tham số cần được truyền vào

    $params = [':order_id' => $order_id];
    return pdo_query_with_params($sql, $params);  // Trả về dữ liệu với tham số đã truyền
}



// Hàm hủy đơn hàng và cộng lại số lượng vào kho
function cancelOrder($order_id) {
    $conn = pdo_con(); // Lấy kết nối PDO
    try {
        // Bắt đầu giao dịch (transaction) để đảm bảo tính toàn vẹn dữ liệu
        $conn->beginTransaction();

        // Lấy thông tin chi tiết sản phẩm trong đơn hàng
        $query = "
            SELECT cd.id_chitiet_sp, cd.soluong
            FROM chitietdathang cd
            WHERE cd.order_id = ?";
        $stmt = $conn->prepare($query);  // Dùng kết nối đã mở ($conn)
        $stmt->execute([$order_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Cập nhật lại số lượng sản phẩm vào kho
        $query_update_stock = "
            UPDATE sanpham_chitiet 
            SET so_luong = so_luong + ?
            WHERE id = ?";

        // Cập nhật số lượng cho mỗi sản phẩm trong đơn hàng đã hủy
        foreach ($items as $item) {
            $stmt_update_stock = $conn->prepare($query_update_stock); // Dùng kết nối đã mở ($conn)
            $stmt_update_stock->execute([$item['soluong'], $item['id_chitiet_sp']]);
        }

        // Cập nhật trạng thái đơn hàng thành 'dahuy'
        $sql = "UPDATE dathang SET trangthai = 'dahuy' WHERE order_id = :order_id";
        $params = [':order_id' => $order_id];
        pdo_query_cancel($sql, $params); // Lưu ý: Hàm này nên dùng đối tượng kết nối đúng

        // Commit giao dịch nếu không có lỗi
        $conn->commit(); // Dùng kết nối đã mở ($conn)

        return true; // Trả về true khi hủy thành công và cập nhật số lượng vào kho
    } catch (PDOException $e) {
        // Rollback nếu có lỗi xảy ra
        if ($conn) {
            $conn->rollBack(); // Dùng kết nối đã mở ($conn) để rollback
        }
        return false; // Trả về false nếu có lỗi
    }
}



// Hàm xóa đơn hàng và chi tiết đơn hàng
function donhang_delete($order_id) {
    // Bắt đầu giao dịch (transaction) để đảm bảo tính toàn vẹn dữ liệu
    try {
        // Xóa chi tiết đơn hàng liên quan trước
        $sql = "DELETE FROM chitietdathang WHERE order_id = ?";
        pdo_execute($sql, $order_id);  // Sử dụng pdo_execute để thực thi câu lệnh với tham số

        // Xóa đơn hàng
        $sql = "DELETE FROM dathang WHERE order_id = ?";
        pdo_execute($sql, $order_id);  // Sử dụng pdo_execute để thực thi câu lệnh với tham số
        
        return true;  // Trả về true khi xóa thành công
    } catch (PDOException $e) {
        // Nếu có lỗi, hiển thị thông báo lỗi
        return false;
    }
}



// // Hàm lấy danh sách tất cả đơn hàng của admin, bao gồm tên người dùng
// function getOrdersAdmin() {
//     $query = "
//         SELECT dathang.order_id, dathang.order_date, dathang.tongtien, dathang.trangthai, 
//                dathang.pt_thanhtoan, dathang.diachi, dathang.phone, user.name as username
//         FROM dathang
//         JOIN user ON dathang.id_user = user.id"; // Giả sử bảng users có trường id_user và name

//     try {
//         $stmt = pdo_con()->prepare($query);
//         $stmt->execute();
//         return $stmt->fetchAll(PDO::FETCH_ASSOC);
//     } catch (PDOException $e) {
//         die("Lỗi truy vấn đơn hàng: " . $e->getMessage());
//     }
// }

?>




