<link rel="stylesheet" href="../view/css/products.css">
<link rel="stylesheet" href="../view/css/chitietsp.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="ten">
    <p>Trang chủ</p><i class="fa-solid fa-caret-right"></i>
    <p style="font-weight: bold;"> Sản phẩm</p>
</div>

<div class="chitiet">
    <div class="chitiet_trai">
        <?php
        if (isset($_GET['idsp'])) {
            $id = $_GET['idsp'];
            $sanpham = getSanPhamById($id); // Lấy chi tiết sản phẩm từ cơ sở dữ liệu
            if ($sanpham) {
                extract($sanpham); // Giải nén dữ liệu sản phẩm
                echo '
                <div class="slideshow">
                    <img src="../view/images/' . htmlspecialchars($hinh) . '" alt="Product Image 1">
                    <img src="../view/images/' . htmlspecialchars($hinh) . '" alt="Product Image 2">
                    <img src="../view/images/' . htmlspecialchars($hinh) . '" alt="Product Image 3">
                </div>
                <img src="../view/images/' . htmlspecialchars($hinh) . '" alt="Main Product Image">
                ';
            }
        }
        ?>
    </div>
    <div class="chitiet_phai">
<?php
if (isset($_GET['idsp'])) {
    $id = $_GET['idsp'];
    $sanpham = getSanPhamById($id); // Lấy thông tin sản phẩm
    if ($sanpham) {
        extract($sanpham);

        // Lấy danh sách màu sắc và kích thước
        $colors = getColorsByProductId($id);
        $sizes = getSizesByProductId($id);


       
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $color_id = $_POST['color_id'];
            $size_id = $_POST['size_id'];
            $quantity = $_POST['so_luong'];

        }
        
        $tinh_trang = ($so_luong == 0) ? 'Hết hàng' : 'Còn hàng';

        echo '
        <form method="POST" action="../controller/index.php?act=cart">
            <h3>' . htmlspecialchars($tensp) . '</h3>
            <hr>
            <div class="giasp"><span>' . number_format($gia) . ' VNĐ</span></div>
            <p><b>Mã sản phẩm:</b> ' . htmlspecialchars($id) . '</p>
            <p><b>Tình trạng:</b> ' . htmlspecialchars($tinh_trang) . '</p>

             <!-- Hiển thị số lượng còn trong kho -->
            <!--<p><b>Số lượng trong kho:</b> ' . htmlspecialchars($so_luong) . '</p>-->


            
            <input type="hidden" name="id_sanpham" value="' . htmlspecialchars($id) . '"/>
            <input type="hidden" name="hinh" value="' . htmlspecialchars($hinh) . '"/>
            <input type="hidden" name="tensp" value="' . htmlspecialchars($tensp) . '"/>
            <input type="hidden" name="gia" value="' . htmlspecialchars($gia) . '"/>
            

            <!-- Chọn màu sắc -->
            <p><b>Màu sắc:</b></p>
            <select name="color_id" required>
                <option value="" disabled selected>Chọn màu</option>';
        foreach ($colors as $color) {
            echo '<option value="' . htmlspecialchars($color['color_id']) . '">' . htmlspecialchars($color['color_name']) . '</option>';
        }
        echo '
            </select>

            <!-- Chọn kích thước -->
            <p><b>Kích thước:</b></p>
            <select name="size_id" required>
                <option value="" disabled selected>Chọn kích thước</option>';
        foreach ($sizes as $size) {
            echo '<option value="' . htmlspecialchars($size['size_id']) . '">' . htmlspecialchars($size['size_name']) . '</option>';
        }
        echo '
            </select>

            <!-- Nhập số lượng -->
            <p><b>Số lượng:</b></p>
            <input type="number" name="so_luong" style="text-align: center; width: 100px; height: 25px; border: 1px solid grey; border-radius: 5px;" min="1" max="99" value="1" required />

            <!-- Nút thêm vào giỏ hàng -->
            <div class="buy">
                <input class="add" type="submit" value="Thêm vào giỏ hàng" name="addcart" style="width: 40%">
            </div>

            <h4>ĐỊA CHỈ MUA HÀNG</h4>
            <p><i class="fa-solid fa-store"></i> 180 Đ. Cao Lỗ, Phường 4, Quận 8, Hồ Chí Minh</p>
            <h4>Gọi mua hàng: <span>0329006325</span></h4>
        </form>
        ';
    } else {
        echo '<p>Sản phẩm không tồn tại.</p>';
    }
}
?>
</div>

</div>

<div class="binhluan">
    <h3>BÌNH LUẬN</h3>
    <iframe src="../controller/binhluan.php?idsp=<?= htmlspecialchars($_GET['idsp'] ?? '') ?>" frameborder="0"></iframe>
</div>   




