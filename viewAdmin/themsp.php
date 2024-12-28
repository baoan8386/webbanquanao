


    <?php
// Kết nối với CSDL
$conn = pdo_con();

if (isset($_POST['them'])) {
    $tensp = $_POST['tensp'];
    $avata = basename($_FILES['hinh']['name']);
    $target = "../view/images/"; // Đường dẫn lưu ảnh
    $target_file = $target . $avata;
    $gia = $_POST['gia'];
    $danhmuc = $_POST['danhmuc'];
    $color_id = $_POST['color']; // ID màu
    $size_id = $_POST['size'];   // ID size
    $so_luong = $_POST['so_luong']; // Số lượng sản phẩm

    // Kiểm tra các trường không trống
    if ($tensp != "" && $avata != "" && $gia != "" && $danhmuc != "" && $color_id != "" && $size_id != "" && $so_luong != "") {
        // Chèn sản phẩm vào bảng sanpham
        $sql_sanpham = "INSERT INTO sanpham (tensp, hinh, gia, iddanhmuc) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_sanpham);
        $stmt->execute([$tensp, $avata, $gia, $danhmuc]);
        $last_id = $conn->lastInsertId(); // Lấy ID của sản phẩm vừa thêm

        // Thêm chi tiết sản phẩm vào bảng sanpham_chitiet
        $sql_chitiet = "INSERT INTO sanpham_chitiet (id_sanpham, color_id, size_id, so_luong) VALUES (?, ?, ?, ?)";
        $stmt_chitiet = $conn->prepare($sql_chitiet);
        $stmt_chitiet->execute([$last_id, $color_id, $size_id, $so_luong]);

        // Di chuyển tệp hình ảnh
        move_uploaded_file($_FILES['hinh']['tmp_name'], $target_file);
        echo "<script>alert('Thêm sản phẩm thành công');window.location.href='indexAdmin.php?act=sanpham';</script>";
    } else {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin');</script>";
    }
}
?>

<div class="main">
    <main>
        <form action="" method="POST" class="sua" enctype="multipart/form-data" onsubmit="return KiemTra()" style="width:400px; height:auto; padding-bottom: 10%; margin-left: 0;margin-top: 0;text-align: center;">
            <h2 style="text-align: center;">Thêm sản phẩm</h2><br>

            <input type="text" id="tensp" placeholder="Tên Sản Phẩm" name="tensp" required><br>
            <div id="loi1" class="loi"></div><br>
            <input type="text" id="gia" placeholder="Giá Sản Phẩm" name="gia" required><br>
            <div id="loi2" class="loi"></div><br>
            <input type="text" id="danhmuc" placeholder="Id danh mục" name="danhmuc" required><br>
            <div id="loi5" class="loi"></div><br>
            
            <!-- Form chọn màu -->
            <label for="color">Màu sắc:</label>
            <select id="color" name="color" required>
                <option value="">Chọn màu</option>
                <?php
                // Lấy danh sách màu từ bảng `color`
                $sql_color = "SELECT * FROM color";
                foreach ($conn->query($sql_color) as $color) {
                    echo "<option value='{$color['color_id']}'>{$color['color_name']}</option>";
                }
                ?>
            </select><br><br>

            <!-- Form chọn size -->
            <label for="size">Kích cỡ:</label>
            <select id="size" name="size" required>
                <option value="">Chọn size</option>
                <?php
                // Lấy danh sách size từ bảng `size`
                $sql_size = "SELECT * FROM size";
                foreach ($conn->query($sql_size) as $size) {
                    echo "<option value='{$size['size_id']}'>{$size['size_name']}</option>";
                }
                ?>
            </select><br><br>

            <!-- Form nhập số lượng -->
            <input type="number" id="so_luong" placeholder="Số lượng" name="so_luong" required><br><br>

            <!-- Form chọn hình ảnh -->
            <input type="file" id="hinh" name="hinh" style="border: none;" required><br>
            <div id="loi6" class="loi"></div><br>
            <input type="submit" value="Thêm Sản Phẩm" name="them" style="width: 150px;">
        </form>
    </main>
</div>
