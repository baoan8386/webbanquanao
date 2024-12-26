<div class="main">
    <main>
        <h2 style="text-align: center;">Danh Sách Sản Phẩm</h2> <br>

        <form action="../controller/indexAdmin.php?act=layout_tim" id="tim" method="POST">
            <input type="text" name="filter" placeholder="Tìm Kiếm sản phẩm">
            <input id="timkiem" type="submit" name="timkiem" value="Tìm kiếm">
        </form>

        <?php
        // Kiểm tra nếu có yêu cầu xóa sản phẩm
        if (isset($_GET['id'])) {
            $product_id = intval($_GET['id']);
            if (sanpham_delete($product_id)) {
                echo "<script>alert('Xóa sản phẩm thành công');</script>";
            } else {
                echo "<script>alert('Xóa sản phẩm thất bại');</script>";
            }
        }

        // Truy vấn để lấy dữ liệu từ các bảng `sanpham`, `size`, `color`, và `sanpham_chitiet`
        $query = sanpham_showAll();  // Gọi hàm để lấy dữ liệu sản phẩm

        echo "<table>";
        echo '
            <tr>
                <td class="a"></td>
                <td class="a"></td>
                <td class="a"></td>
                <td class="a"></td>
                <td class="a"></td>
                <td class="a"></td>
                <td class="a"></td>
                <td class="a"></td>
                <td class="a"></td>
                <td class="a"></td>
                <td class="a"></td>
            </tr>';
        echo '
            <tr>
                <td class="an"></td>
                <td class="an"></td>
                <td class="an"></td>
                <td class="an"></td>
                <td class="an"></td>
                <td class="an"></td>
                <td class="an"></td>
                <td class="an"></td>
                <td class="an"></td>
                <td class="them"><a href="../controller/indexAdmin.php?act=themsanpham"><i class="bx bx-import"></i> Thêm</a></td>
            </tr>';
        echo '
            <tr>
                <th>ID</th>
                <th>HÌNH ẢNH</th>
                <th colspan="3">TÊN SẢN PHẨM</th>
                <th>GIÁ</th>
                <th>PHÂN LOẠI</th>
                <th>MÀU</th>
                <th>SIZE</th>
                <th>SỐ LƯỢNG</th>
                <th colspan="2">Tính năng</th>
            </tr>';

        // Duyệt qua kết quả trả về từ hàm `sanpham_showAll()` và hiển thị dữ liệu
        foreach ($query as $row) {
            extract($row);

            // Đường dẫn để xóa sản phẩm
            $dellink = "../controller/indexAdmin.php?act=sanpham&id=" . $id;

            echo '
                <tr>
                    <td>' . $id . '</td>
                    <td><img src="../view/images/' . $hinh . '" alt=""></td>
                    <td colspan="3">' . $tensp . '</td>
                    <td>' . number_format($gia, 0, ',', '.') . 'đ</td>
                    <td>' . $iddanhmuc . '</td>
                    <td>' . $color_name . '</td>
                    <td>' . $size_name . '</td>
                    <td>' . $so_luong . '</td>
                    <td class="ac"><a href="../controller/indexAdmin.php?act=suasanpham&page_layout=sua&id=' . $id . '"><i class="bx bx-edit"></i></a></td>
                    <td class="ad"><a onclick="return Del()" href="' . $dellink . '"><i class="bx bx-message-square-minus"></i></a></td>
                </tr>';
        }

        echo "</table>";
        ?>

    </main>
</div>
