<?php
// model/thanhtoan.php

require_once('../model/config.php');

// Hàm lấy thông tin giỏ hàng của người dùng
function getCartItems($user_id) {
    if (empty($user_id)) {
        return []; // Trả về giỏ hàng rỗng nếu user_id không hợp lệ
    }

    $query = "
        SELECT cd.id_cart_detail, sp.id AS id_sanpham, sp.tensp, cd.soluong, cd.gia, 
               (cd.soluong * cd.gia) AS thanh_tien, sp.hinh, sc.id AS id_chitiet_sp,
               s.size_name, c.color_name,sc.so_luong
        FROM cart_details cd
        JOIN sanpham_chitiet sc ON cd.id_chitiet_sp = sc.id
        JOIN sanpham sp ON sc.id_sanpham = sp.id
        JOIN size s ON sc.size_id = s.size_id  -- Kết nối bảng size
        JOIN color c ON sc.color_id = c.color_id  -- Kết nối bảng color
        WHERE cd.id_cart IN (SELECT id_cart FROM cart WHERE id_user = ?)";

    try {
        $stmt = pdo_con()->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Lỗi truy vấn giỏ hàng: " . $e->getMessage());
    }
}


// Hàm xử lý thanh toán và tạo đơn hàng
function createOrder($user_id, $cart_items, $payment_method, $address, $phone) {
    if (empty($user_id) || empty($cart_items)) {
        return false; // Dừng xử lý nếu không có user_id hoặc giỏ hàng rỗng
    }

    $total = 0;
    
    foreach ($cart_items as $item) {
        $total += $item['thanh_tien'];
    }
    

    try {
        $conn = pdo_con();
        $conn->beginTransaction();

        // Thêm đơn hàng vào bảng dathang
        $query_order = "INSERT INTO dathang (id_user, tongtien, pt_thanhtoan, diachi, phone) VALUES (?, ?, ?, ?, ?)";
        $stmt_order = $conn->prepare($query_order);
        $stmt_order->execute([$user_id, $total, $payment_method, $address, $phone]);

        // Lấy order_id của đơn hàng vừa thêm
        $order_id = $conn->lastInsertId();

        
        // Thêm chi tiết đơn hàng vào bảng chitietdathang
        $query_details = "
            INSERT INTO chitietdathang (order_id, id_chitiet_sp, soluong, thanh_tien) 
            VALUES (?, ?, ?, ?)";


            // $query_update = "
            // UPDATE sanpham_chitiet 
            // SET so_luong = so_luong - ? 
            // WHERE id = ?";
           
            $query_update = "UPDATE sanpham_chitiet SET so_luong = so_luong - ? WHERE id = ?";


        foreach ($cart_items as $item) {
            $stmt_details = $conn->prepare($query_details);
            $stmt_details->execute([$order_id, $item['id_chitiet_sp'], $item['soluong'], $item['thanh_tien']]);
            
            $stmt_update = $conn->prepare($query_update);
            $stmt_update->execute([$item['soluong'], $item['id_chitiet_sp']]);

        }

        // Xóa giỏ hàng chi tiết sau khi thanh toán
        $query_delete = "
            DELETE FROM cart_details 
            WHERE id_cart IN (SELECT id_cart FROM cart WHERE id_user = ?)";
        $stmt_delete = $conn->prepare($query_delete);
        $stmt_delete->execute([$user_id]);

        $conn->commit();
        return true; // Thanh toán thành công
    } catch (PDOException $e) {
        $conn->rollBack();
        die("Đã xảy ra lỗi: " . $e->getMessage());
    }
}

?>


<?php

// Hàm lấy thông tin chi tiết đơn hàng
function getOrderDetails($order_id, $user_id) {
    $query = "
        SELECT d.order_id, d.order_date, d.trangthai, d.tongtien, d.pt_thanhtoan, d.diachi, d.phone
        FROM dathang d
        WHERE d.order_id = ? AND d.id_user = ?";
    
    try {
        $stmt = pdo_con()->prepare($query);
        $stmt->execute([$order_id, $user_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return false; // Nếu không có đơn hàng
        }

        // Lấy chi tiết sản phẩm trong đơn hàng
        $query_items = "
            SELECT sp.tensp, sp.gia, sp.hinh, s.size_name, c.color_name, cd.soluong, (cd.soluong * sp.gia) AS thanh_tien
            FROM chitietdathang cd
            JOIN sanpham_chitiet sc ON cd.id_chitiet_sp = sc.id
            JOIN sanpham sp ON sc.id_sanpham = sp.id
            JOIN size s ON sc.size_id = s.size_id
            JOIN color c ON sc.color_id = c.color_id
            WHERE cd.order_id = ?";
        
        $stmt_items = pdo_con()->prepare($query_items);
        $stmt_items->execute([$order_id]);
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        // Thêm thông tin sản phẩm vào kết quả trả về
        $order['items'] = $items;

        return $order;
    } catch (PDOException $e) {
        die("Lỗi truy vấn đơn hàng: " . $e->getMessage());
    }
}

?>



