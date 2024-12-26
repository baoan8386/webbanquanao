<?php
// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['id'])) {
    $_SESSION['message'] = "Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.";
    header("Location:../controller/index.php?act=login");
    exit();
}
?>

<?php
// Bao gồm file model/cart.php
include_once('../model/cart.php');
$id_user = $_SESSION['id']; // Lấy id_user từ session

// Thêm sản phẩm vào giỏ hàng
if (isset($_POST['addcart'])) {
    $id_sanpham = $_POST['id_sanpham'];
    $so_luong = isset($_POST['so_luong']) ? $_POST['so_luong'] : 0;  // Kiểm tra và đảm bảo giá trị tồn tại
    $size_id = $_POST['size_id'];  // Lấy giá trị size_id từ form
    $color_id = $_POST['color_id'];  // Lấy giá trị color_id từ form

    // Gọi hàm addcart với tất cả tham số
    addcart($id_sanpham, $so_luong, $id_user, $size_id, $color_id);
}


// Cập nhật số lượng sản phẩm trong giỏ hàng
if (isset($_POST['update_cart'])) {
    $new_soluong = isset($_POST['soluong']) ? $_POST['soluong'] : 0;  // Kiểm tra nếu 'soluong' tồn tại
    $id_cart_detail = $_POST['id_cart_detail'];

    if ($new_soluong > 0) {
        update_cart($id_cart_detail, $new_soluong);
     // Áp dụng giảm giá nếu đủ điều kiện
     $discount_message = giamgia($id_user);
    } 
}

// Xóa sản phẩm khỏi giỏ hàng
if (isset($_GET['delid'])) {
    $id_cart_detail = $_GET['delid'];
    delete_product($id_cart_detail);
}

// Hiển thị giỏ hàng
function showcart_view() {
    global $id_user;
    $cart_items = showcart($id_user);
    $tong = 0;
    $i = 1;
    $tongsoluong = 0; 
    foreach ($cart_items as $row) {
        $tt = $row['gia'] * $row['soluong'];
        $tong += $tt;
        $tongsoluong += $row['soluong']; // Tính tổng số lượng sản phẩm

        
        echo '
            <tr>
                <td>' . $i++ . '</td>
                <td><img src="../view/images/' . (isset($row['hinh']) ? $row['hinh'] : 'default.jpg') . '" alt=""></td>
                <td>
                    <p><b>' . $row['tensp'] . '</b></p>
                </td>
                 <td>
                    <p>' . $row['size_name'] . '</p>
                  
                </td>
                <td>
                    
                    <p>' . $row['color_name'] . '</p>
                </td>
                <td><span>' . number_format($row['gia']) . ' VNĐ</span></td>
                <td>
                    <form action="" method="POST">
                        <input type="number" value="' . $row['soluong'] . '" name="soluong" min="1">
                        <input type="hidden" name="id_cart_detail" value="' . $row['id_cart_detail'] . '">
                        <button type="submit" name="update_cart"><i class="fa-solid fa-rotate-right"></i></button>
                    </form>
                </td>
                <td><span>' . number_format($tt) . ' VNĐ</span></td>
                <td><a href="?act=cart&delid=' . $row['id_cart_detail'] . '"><i class="fa-solid fa-trash"></i></a></td>
            </tr>
        ';
    }
    

   
    echo '
        <tr>
            <th colspan="5">Tổng Tiền </th>
            <th colspan="2" class="tong">' . number_format($tong) . ' VNĐ</th>
        </tr>
    ';


   












     // Kiểm tra nếu tổng số lượng >= 10 để áp dụng giảm giá
     $gia_giam = $tong;
     if ($tongsoluong >= 10) {
         $gia_giam *= 0.9; // Áp dụng giảm giá 10%
     }
 

    if ($tongsoluong >= 10) {
        echo '
            <tr>
                <th colspan="5">Tổng Tiền Sau Giảm Giá (10%)</th>
                <th colspan="2" class="tong">' . number_format($gia_giam) . ' VNĐ</th>
            </tr>
        ';
    }
}

?>



<link rel="stylesheet" href="../view/css/carts.css">
<link rel="stylesheet" href="../view/css/products.css">
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="banner">
    <img src="https://file.hstatic.net/200000538469/file/ready__4__4ee0893ec85d43779b232da34a1bb5db.png" alt=""
        height="280px">
</div>
<div class="main">
    <h2>GIỎ HÀNG</h2>
    <table>
        <tr class="nen">
            <th style="width: 200px;">Số Thứ Tự</th>
            <th >Hình ảnh</th>
            <th >Sản phẩm</th>
            <th >Kính thước</th>
            <th >Màu sắc</th>
            <th>Đơn giá</th>
            <th>Số lượng</th>
            <th colspan="2">Thành tiền</th>
        </tr>
        <?php showcart_view(); ?>


    </table>
    <div class="mobile">
    <?php showcart_view(); ?>

    </div>
    <div class="nut" style="display: grid;gap: 120px;grid-template-columns: 100px 100px; margin-left: 700px;">
        <a href="../controller/thanhtoan.php" style="text-decoration: none;"><button class="dathang">Thanh
                Toán</button></a>
        <a href="../controller/index.php" style="text-decoration: none;"><button class="dathang">Tiếp Tục Đặt
                Hàng</button></a>
        <a href="../controller/index.php?act=order_details" style="text-decoration: none;"><button class="dathang">Xem Đơn Hàng</button></a> 
    </div>

</div>

<style>
    /* -----------------nút reload----------------------- */



/* Định dạng chung */
/* Căn chỉnh form cập nhật */
form {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 10px 0;
}


/* Nút cập nhật */
form button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background-color: #007bff;
  color: #fff;
  border: none;
  border-radius: 5px;
  height: 35px;
  padding: 0 15px;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
  font-weight: bold;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

form button i {
  margin-left: 5px;
}

/* Hiệu ứng hover cho nút */
form button:hover {
  background-color: #0056b3;
  transform: scale(1.05);
}

/* Hiệu ứng active cho nút */
form button:active {
  background-color: #004085;
  transform: scale(0.95);
}

/* Thêm hiệu ứng khi disabled */
form button:disabled {
  background-color: #ccc;
  color: #666;
  cursor: not-allowed;
}


/* Banner */
.banner img {
    width: 100%;
    display: block;
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

.main h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

/* Bảng giỏ hàng */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    background-color: #fff;
}

table th, table td {
    text-align: center;
    padding: 10px;
    border: 1px solid #ddd;
}



.dathang {
    padding: 10px 15px;
    background-color: #28a745;
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.dathang:hover {
    background-color: #218838;
}

a {
    text-decoration: none;
}

/* Hiển thị trên thiết bị di động */
.mobile {
    display: none; /* Ẩn giỏ hàng trên di động mặc định */
}

@media (max-width: 768px) {
    .main {
        width: 95%;
    }

    table {
        display: none; /* Ẩn bảng trên màn hình nhỏ */
    }

    .mobile {
        display: block; /* Hiển thị giỏ hàng theo thiết kế mobile */
    }
}

</style>
