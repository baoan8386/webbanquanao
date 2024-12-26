<?php
// Kết nối với cơ sở dữ liệu (PDO)
$conn = pdo_con();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    // Lấy thông tin sản phẩm từ bảng `sanpham`
    $sql_sanpham = "SELECT * FROM sanpham WHERE id = ?";
    $stmt = $conn->prepare($sql_sanpham);
    $stmt->execute([$id]);
    $rows = $stmt->fetch();

    // Lấy chi tiết sản phẩm từ bảng `sanpham_chitiet` (bao gồm màu sắc và kích thước)
    $sql_chitiet = "SELECT * FROM sanpham_chitiet WHERE id_sanpham = ?";
    $stmt_chitiet = $conn->prepare($sql_chitiet);
    $stmt_chitiet->execute([$id]);
    $chitiet = $stmt_chitiet->fetchAll();

} else {
    echo "<script>alert('ID sản phẩm không hợp lệ!');</script>";
    exit;
}

if (isset($_POST['sua'])) {
    $tensp = trim($_POST['tensp']);
    $gia = floatval($_POST['gia']);
    $danhmuc = intval($_POST['danhmuc']);
    $hinh = $rows['hinh']; // Mặc định là ảnh hiện tại nếu không upload mới

    // Xử lý upload hình ảnh mới (nếu có)
    if (isset($_FILES['hinh']) && $_FILES['hinh']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../view/images/";
        $target_file = $target_dir . basename($_FILES["hinh"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra loại file upload
        $valid_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $valid_types)) {
            // Upload file
            if (move_uploaded_file($_FILES["hinh"]["tmp_name"], $target_file)) {
                $hinh = basename($_FILES["hinh"]["name"]); // Lấy tên file mới
            } else {
                echo "<script>alert('Không thể upload file!');</script>";
            }
        } else {
            echo "<script>alert('Chỉ cho phép upload các định dạng JPG, JPEG, PNG, GIF!');</script>";
        }
    }

    // Kiểm tra các trường dữ liệu không rỗng trước khi cập nhật
    if (!empty($tensp) && $gia > 0 && $danhmuc > 0) {
        // Cập nhật thông tin sản phẩm vào bảng `sanpham`
        $sql_update_sanpham = "UPDATE sanpham SET tensp = ?, hinh = ?, gia = ?, iddanhmuc = ? WHERE id = ?";
        $stmt_update_sanpham = $conn->prepare($sql_update_sanpham);
        $stmt_update_sanpham->execute([$tensp, $hinh, $gia, $danhmuc, $id]);

        // Cập nhật chi tiết sản phẩm vào bảng `sanpham_chitiet`
        foreach ($_POST['color'] as $index => $color_id) {
            $size_id = $_POST['size'][$index];
            $so_luong = $_POST['so_luong'][$index];

            $sql_update_chitiet = "UPDATE sanpham_chitiet SET color_id = ?, size_id = ?, so_luong = ? WHERE id_sanpham = ? AND color_id = ? AND size_id = ?";
            $stmt_update_chitiet = $conn->prepare($sql_update_chitiet);
            $stmt_update_chitiet->execute([$color_id, $size_id, $so_luong, $id, $color_id, $size_id]);
        }

        echo "<script>alert('Sửa sản phẩm thành công');</script>";
    } else {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin hợp lệ!');</script>";
    }
}
?>
  
  <div class="main">
    <main>
        <form action="" method="POST" class="sua" enctype="multipart/form-data"
            style="width: 400px; height: auto; padding-bottom: 10%; margin-left: 0; margin-top: 0; text-align: center;">
            <h2>Sửa sản phẩm</h2>
            <br>
            <input type="text" placeholder="Tên Sản Phẩm" name="tensp" required value="<?php echo htmlspecialchars($rows['tensp']); ?>">
            <br><br>
            <input type="text" placeholder="Giá Sản Phẩm" name="gia" required value="<?php echo htmlspecialchars($rows['gia']); ?>">
            <br><br>
            <input type="text" placeholder="ID danh mục" name="danhmuc" required value="<?php echo htmlspecialchars($rows['iddanhmuc']); ?>">
            <br><br>
            <label>Ảnh sản phẩm hiện tại:</label>
            <img src="../view/images/<?php echo htmlspecialchars($rows['hinh']); ?>" alt="Hình sản phẩm" style="max-width: 100px;">
            <br><br>
            <input type="file" name="hinh" style="border: none;"> 
            <br><br>

            <!-- Hiển thị các lựa chọn màu sắc và kích thước đã chọn -->
            <label for="color">Màu sắc:</label>
            <select name="color[]" required>
                <option value="">Chọn màu</option>
                <?php
                // Lấy danh sách màu từ bảng `color`
                $sql_color = "SELECT * FROM color";
                foreach ($conn->query($sql_color) as $color) {
                    $selected = in_array($color['color_id'], array_column($chitiet, 'color_id')) ? 'selected' : '';
                    echo "<option value='{$color['color_id']}' $selected>{$color['color_name']}</option>";
                }
                ?>
            </select>
            <br><br>

            <label for="size">Kích cỡ:</label>
            <select name="size[]" required>
                <option value="">Chọn size</option>
                <?php
                // Lấy danh sách size từ bảng `size`
                $sql_size = "SELECT * FROM size";
                foreach ($conn->query($sql_size) as $size) {
                    $selected = in_array($size['size_id'], array_column($chitiet, 'size_id')) ? 'selected' : '';
                    echo "<option value='{$size['size_id']}' $selected>{$size['size_name']}</option>";
                }
                ?>
            </select>
            <br><br>

            <label for="so_luong">Số lượng:</label>
            <input type="number" name="so_luong[]" required value="<?php echo $chitiet[0]['so_luong']; ?>">
            <br><br>

            <input type="submit" value="Sửa" name="sua" style="width: 150px;">
        </form>
    </main>
</div>
