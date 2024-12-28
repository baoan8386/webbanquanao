<?php

function sanpham_showAll($filter = '%') {
    $sql = "SELECT 
               sp.id, 
            sp.hinh, 
            sp.tensp, 
            sp.gia, 
            sp.iddanhmuc, 
            ct.color_id, 
            ct.size_id, 
            c.color_name, 
            sz.size_name, 
            ct.so_luong
            FROM sanpham sp
            LEFT JOIN sanpham_chitiet ct ON sp.id = ct.id_sanpham
            LEFT JOIN color c ON ct.color_id = c.color_id
            LEFT JOIN size sz ON ct.size_id = sz.size_id
            WHERE sp.tensp LIKE :filter";  // Tìm kiếm theo tên sản phẩm

    // Chuẩn bị và thực thi câu lệnh SQL với filter
    $conn = pdo_con();  // Kết nối CSDL
    $stmt = $conn->prepare($sql);  // Chuẩn bị câu lệnh SQL
    $stmt->execute(['filter' => $filter]);  // Truyền tham số filter
    return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Trả về kết quả dưới dạng mảng
}


function sanpham_showId_1(){
    $sql = "SELECT*FROM sanpham WHERE iddanhmuc=1 ORDER BY id DESC";
    return pdo_query($sql);
}
function sanpham_showId_2(){
    $sql = "SELECT*FROM sanpham WHERE iddanhmuc=2 ORDER BY id DESC";
    return pdo_query($sql);
}
function sanpham_showId_3(){
    $sql = "SELECT*FROM sanpham WHERE iddanhmuc=3 ORDER BY id DESC";
    return pdo_query($sql);
}

function sanpham_showId($id){
    $sql = "SELECT*FROM sanpham WHERE iddanhmuc=? ORDER BY id DESC";
    return pdo_query($sql, $id);
}

// function sanpham_showChiTiet($id){
//     $sql = "SELECT*FROM sanpham WHERE id=?";
//     return pdo_query_one($sql, $id);
// }

// function sanpham_insert($tensp,$avata,$gia,$danhmuc){
//     $sql = "INSERT INTO sanpham(tensp, hinh, gia, iddanhmuc) VALUES(?,?,?,?)";
//     pdo_execute($sql, $tensp,$avata,$gia,$danhmuc);
// }

function sanpham_update($tensp,$hinh,$gia,$danhmuc, $id){
    $sql = "UPDATE  sanpham SET tensp=?, hinh=?, gia=?, iddanhmuc=? WHERE id=?";
    pdo_execute($sql, $tensp,$hinh,$gia,$danhmuc, $id);
}

function sanpham_delete($id_sanpham, $color_id, $size_id) {
    try {
        // Xóa chi tiết sản phẩm liên quan đến mã sản phẩm, màu sắc, và kích thước
        $sql = "DELETE FROM sanpham_chitiet 
                WHERE id_sanpham = ? AND color_id = ? AND size_id = ?";
        pdo_execute($sql, $id_sanpham, $color_id, $size_id);

        // Kiểm tra nếu không còn chi tiết nào liên quan đến sản phẩm, thì xóa sản phẩm chính
        $sql = "SELECT COUNT(*) AS count FROM sanpham_chitiet WHERE id_sanpham = ?";
        $result = pdo_query_one($sql, $id_sanpham);
        if ($result['count'] == 0) {
            $sql = "DELETE FROM sanpham WHERE id = ?";
            pdo_execute($sql, $id_sanpham);
        }

        return true; // Trả về true nếu xóa thành công
    } catch (PDOException $e) {
        // Xử lý lỗi nếu có
        error_log("Lỗi khi xóa sản phẩm: " . $e->getMessage());
        return false;
    }
}




function sanpham_seach($name){
    $sql = "SELECT sp.id, 
                   sp.hinh, 
                   sp.tensp, 
                   sp.gia, 
                   sp.iddanhmuc, 
                   c.color_name, 
                   c.hex_code, 
                   sz.size_name, 
                   ct.so_luong
            FROM sanpham sp
            LEFT JOIN sanpham_chitiet ct ON sp.id = ct.id_sanpham
            LEFT JOIN color c ON ct.color_id = c.color_id
            LEFT JOIN size sz ON ct.size_id = sz.size_id
            WHERE sp.tensp LIKE :name"; 

    // Kết nối cơ sở dữ liệu và thực thi truy vấn với tham số
    $conn = pdo_con();
    $stmt = $conn->prepare($sql);
    $stmt->execute(['name' => '%' . $name . '%']);  // Truyền tham số vào câu truy vấn
    return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Trả về kết quả dưới dạng mảng
}


// Lấy chi tiết sản phẩm, bao gồm màu sắc và kích thước
function sanpham_showChiTiet($productId) {
    $sql = "SELECT sanpham.*, GROUP_CONCAT(color.color_name ORDER BY color.color_name ASC) AS mau,
                   GROUP_CONCAT(size.size_name ORDER BY size.size_name ASC) AS kichthuoc
            FROM sanpham
            LEFT JOIN sanpham_chitiet ON sanpham.id = sanpham_chitiet.id_sanpham
            LEFT JOIN color ON sanpham_chitiet.color_id = color.color_id
            LEFT JOIN size ON sanpham_chitiet.size_id = size.size_id
            WHERE sanpham.id = ?
            GROUP BY sanpham.id";
    return pdo_query_one($sql, $productId);
}

// Lấy thông tin chi tiết sản phẩm
function getSanPhamById($id) {
    $sql = "
    SELECT sp.*, sc.so_luong, c.color_name, s.size_name
    FROM sanpham sp
    JOIN sanpham_chitiet sc ON sp.id = sc.id_sanpham
    JOIN color c ON sc.color_id = c.color_id
    JOIN size s ON sc.size_id = s.size_id
    WHERE sp.id = ?";
    return pdo_query_one($sql, $id);
}
// Lấy danh sách màu sắc của sản phẩm, đảm bảo không trùng
function getColorsByProductId($productId) {
    $sql = "SELECT DISTINCT color.color_id, color.color_name 
            FROM color
            JOIN sanpham_chitiet ON color.color_id = sanpham_chitiet.color_id
            WHERE sanpham_chitiet.id_sanpham = ?";
    return pdo_query($sql, $productId);
}

function getSizesByProductId($id_sanpham) {
    // Kết nối cơ sở dữ liệu
    $conn = pdo_con();
    // Truy vấn SQL
    $sql = "SELECT DISTINCT sz.size_id, sz.size_name
            FROM sanpham_chitiet s
            JOIN size sz ON s.size_id = sz.size_id
            WHERE s.id_sanpham = :id_sanpham";
    try {
        // Chuẩn bị truy vấn
        $stmt = $conn->prepare($sql);
        // Thực thi truy vấn với tham số
        $stmt->execute(['id_sanpham' => $id_sanpham]);
        // Trả về dữ liệu dưới dạng mảng kết hợp
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Xử lý lỗi
        die("Lỗi truy vấn: " . $e->getMessage());
    } finally {
        // Đảm bảo đóng kết nối
        unset($conn);
    }
}

function sanpham_insert($tensp, $avata, $gia, $danhmuc) {
    // Câu lệnh SQL để thêm sản phẩm
    $sql = "INSERT INTO sanpham (tensp, hinh, gia, iddanhmuc) VALUES (?, ?, ?, ?)";
    
    // Gọi hàm pdo_execute để thực thi câu lệnh SQL
    pdo_execute($sql, $tensp, $avata, $gia, $danhmuc);
    
    // Lấy ID của sản phẩm vừa thêm
    $conn = pdo_con(); // Kết nối lại để lấy ID
    return $conn->lastInsertId(); // Trả về ID của sản phẩm vừa thêm
}


// Hàm thêm chi tiết sản phẩm vào bảng `sanpham_chitiet`
function sanpham_chitiet_insert($id_sanpham, $color_id, $size_id, $so_luong) {
    global $conn;
    $sql = "INSERT INTO sanpham_chitiet (id_sanpham, color_id, size_id, so_luong) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_sanpham, $color_id, $size_id, $so_luong]);
}

function sanpham_search($keyword) {
    // Kết nối cơ sở dữ liệu
    $conn = pdo_con(); // Sử dụng hàm kết nối đã có (pdo_con)
    
    // Truy vấn SQL
    $sql = "SELECT * FROM sanpham WHERE tensp LIKE :keyword";
    
    try {
        // Chuẩn bị truy vấn
        $stmt = $conn->prepare($sql);
        
        // Truyền tham số
        $stmt->bindValue(':keyword', '%' . $keyword . '%');
        
        // Thực thi truy vấn
        $stmt->execute();
        
        // Trả về kết quả
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Xử lý lỗi
        die("Lỗi truy vấn: " . $e->getMessage());
    } finally {
        // Đảm bảo đóng kết nối
        unset($conn);
    }
}


?>

