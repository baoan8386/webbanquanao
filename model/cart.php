<?php

include_once('config.php');

// Thêm sản phẩm vào giỏ hàng
function addcart($id_sanpham, $soluong, $id_user, $size_id, $color_id) {
    // Lấy chi tiết sản phẩm từ bảng sanpham_chitiet dựa trên id_sanpham, size_id, và color_id
    $query_sanpham_detail = "SELECT sc.id, s.gia 
                             FROM sanpham_chitiet sc 
                             JOIN sanpham s ON sc.id_sanpham = s.id 
                             WHERE sc.id_sanpham = ? AND sc.size_id = ? AND sc.color_id = ?";
    $sanpham_detail = pdo_query_one($query_sanpham_detail, $id_sanpham, $size_id, $color_id);

    if ($sanpham_detail) {
        $gia = $sanpham_detail['gia']; // Lấy giá sản phẩm
        $id_chitiet_sp = $sanpham_detail['id']; // Lấy id chi tiết sản phẩm

        // Kiểm tra xem sản phẩm đã có trong giỏ hàng của người dùng chưa (kèm size và màu)
        $query_check = "SELECT * 
                        FROM cart_details 
                        WHERE id_cart = (SELECT id_cart FROM cart WHERE id_user = ?) 
                        AND id_chitiet_sp = ?";
        $result_check = pdo_query($query_check, $id_user, $id_chitiet_sp);

        if (count($result_check) > 0) {
            // Nếu sản phẩm đã có trong giỏ hàng, cập nhật số lượng
            $query_update = "UPDATE cart_details 
                             SET soluong = soluong + ? 
                             WHERE id_cart = (SELECT id_cart FROM cart WHERE id_user = ?) 
                             AND id_chitiet_sp = ?";
            pdo_execute($query_update, $soluong, $id_user, $id_chitiet_sp);
        } else {
            // Nếu chưa có, thêm sản phẩm mới vào giỏ hàng
            $query_check_cart = "SELECT id_cart FROM cart WHERE id_user = ?";
            $result_check_cart = pdo_query($query_check_cart, $id_user);

            if (count($result_check_cart) == 0) {
                // Nếu chưa có giỏ hàng, tạo giỏ hàng mới
                $query_create_cart = "INSERT INTO cart (id_user) VALUES (?)";
                pdo_execute($query_create_cart, $id_user);

                // Lấy ID giỏ hàng vừa tạo sau khi thực thi câu lệnh
                $conn = pdo_con(); // Tạo lại kết nối PDO tạm thời
                $id_cart = $conn->lastInsertId(); // Lấy ID của giỏ hàng mới
                unset($conn); // Giải phóng kết nối
            } else {
                // Nếu đã có giỏ hàng, lấy ID giỏ hàng
                $id_cart = $result_check_cart[0]['id_cart'];
            }

            // Thêm sản phẩm vào giỏ hàng chi tiết
            $query_insert_cart_detail = "INSERT INTO cart_details (id_cart, id_chitiet_sp, soluong, gia) 
                                         VALUES (?, ?, ?, ?)";
            pdo_execute($query_insert_cart_detail, $id_cart, $id_chitiet_sp, $soluong, $gia);
        }
    } else {
        die("Không tìm thấy chi tiết sản phẩm với kích thước và màu sắc đã chọn.");
    }
}

// Hiển thị giỏ hàng
function showcart($id_user) {
    // Truy vấn để lấy thông tin chi tiết sản phẩm trong giỏ hàng của người dùng
    $query = "SELECT cd.id_cart_detail, sp.id AS id_sanpham, sp.tensp, sp.gia, cd.soluong, sp.hinh, 
                     s.size_name, c.color_name
              FROM cart_details cd
              JOIN sanpham_chitiet sc ON cd.id_chitiet_sp = sc.id
              JOIN sanpham sp ON sc.id_sanpham = sp.id
              JOIN color c ON sc.color_id = c.color_id
              JOIN size s ON sc.size_id = s.size_id
              WHERE cd.id_cart IN (SELECT id_cart FROM cart WHERE id_user = ?)";
    return pdo_query($query, $id_user); // Trả về kết quả truy vấn
}

// Hiển thị giỏ hàng trên mobile
function showcart_mobile($id_user) {
    // Truy vấn để lấy thông tin sản phẩm cho giỏ hàng trên di động, không lấy thông tin về size và màu
    $query = "SELECT cd.id_cart_detail, sp.id, sp.tensp, sp.gia, cd.soluong 
              FROM cart_details cd
              JOIN sanpham_chitiet sc ON cd.id_chitiet_sp = sc.id
              JOIN sanpham sp ON sc.id_sanpham = sp.id
              WHERE cd.id_cart IN (SELECT id_cart FROM cart WHERE id_user = ?)";
    
    $conn = pdo_con(); // Tạo kết nối PDO
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_user]); // Dùng execute() với array tham số
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch kết quả dưới dạng mảng kết hợp
}

// Cập nhật số lượng sản phẩm trong giỏ hàng
function update_cart($id_cart_detail, $new_soluong) {
    // Cập nhật số lượng sản phẩm trong giỏ hàng
    $query_update = "UPDATE cart_details SET soluong = ? WHERE id_cart_detail = ?";
    pdo_execute($query_update, $new_soluong, $id_cart_detail);
}

// Xóa sản phẩm khỏi giỏ hàng
function delete_product($id_cart_detail) {
    // Xóa sản phẩm khỏi giỏ hàng
    $query_delete = "DELETE FROM cart_details WHERE id_cart_detail = ?";
    pdo_execute($query_delete, $id_cart_detail);
}

// Áp dụng giảm giá khi mua từ 10 sản phẩm trở lên
function giamgia($id_user) {
    // Tính tổng số lượng sản phẩm trong giỏ hàng của người dùng
    $query_tong_soluong = "SELECT SUM(cd.soluong) AS tongsoluong 
                             FROM cart_details cd
                             JOIN cart c ON cd.id_cart = c.id_cart
                             WHERE c.id_user = ?";
    $tongsoluong = pdo_query_one($query_tong_soluong, $id_user)['tongsoluong'];

    // Nếu tổng số lượng >= 10, áp dụng giảm giá 10%
    if ($tongsoluong >= 10) {
        $query_capnhat_giamgia = "UPDATE cart_details cd
                                  JOIN cart c ON cd.id_cart = c.id_cart
                                  SET cd.gia = cd.gia * 0.9
                                  WHERE c.id_user = ?";
        pdo_execute($query_capnhat_giamgia, $id_user); // Cập nhật giá các sản phẩm
    } 
}

// Áp dụng giảm giá cho sản phẩm có số lượng = 10
function giamgia1sp($id_user) {
    // Lấy danh sách sản phẩm trong giỏ hàng của người dùng
    $query_sanpham = "SELECT cd.id_cart_detail, cd.soluong 
                      FROM cart_details cd
                      JOIN cart c ON cd.id_cart = c.id_cart
                      WHERE c.id_user = ?";
    $cart_items = pdo_query($query_sanpham, $id_user);

    // Áp dụng giảm giá cho sản phẩm có số lượng bằng 10
    foreach ($cart_items as $item) {
        if ($item['soluong'] == 10) {
            // Cập nhật giá của sản phẩm nếu số lượng = 10
            $query_capnhat_giamgia = "UPDATE cart_details 
                                      SET gia = gia * 0.9 
                                      WHERE id_cart_detail = ?";
            pdo_execute($query_capnhat_giamgia, $item['id_cart_detail']);
        }
    }
}
?>
