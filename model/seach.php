<link rel="stylesheet" href="../view/css/products.css">

<div class="banner">
    <img src="../view/images/balo_simplecarry.jpg" alt="">
</div>
<div class="main">
    <?php
    $name = "";  
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["filter"];

        if ($name == "") {
        } else {
            $query = sanpham_seach($name);

            if($query != null){
                echo "<h3>Kết quả tìm kiếm: " . $name . "</h3>";
                echo "<div class='mainIn'>";
                foreach($query as $row) {
                    extract($row);
                    echo '
                    <div class="sanpham1">
                    <form method="POST" action="../controller/index.php?act=cart">
                        <img src="../view/images/' . $hinh . '" alt="">
                        <h3>' . substr($tensp, 0, 45) . '</h3>
                        <p>' . number_format($gia, 0, ',', '.') . ' VNĐ</p>
                        <p><strong>Màu: </strong>' . $color_name . '</p>
                        <p><strong>Size: </strong>' . $size_name . '</p>
                        <p><strong>Số lượng: </strong>' . $so_luong . '</p>
                        <input type="hidden" name="hinh" value="' . $hinh . '"/>
                        <input type="hidden" name="tensp" value="' . $tensp . '"/>
                        <input type="hidden" name="gia" value="' . $gia . '"/>
                        <div class="buy">
                            <button><a href="#">Xem chi tiết</a></button>
                            <input type="submit" value="Cho vào giỏ" name="addcart">
                        </div>
                    </form>
                </div>
                '; 
            }
                echo "</div>";
            } else {
                echo "<h3 style='margin-left:30px;'>Không tìm thấy kết quả: " . $name . " </h3>";
            }
        }
    }
    ?>
</div>
