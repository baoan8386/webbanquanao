
<link rel="stylesheet" href="../view/css/products.css">
<div class="product">
    <div class="tille">
        <h3>Kết quả tìm kiếm</h3>
        <!-- <?php
        // echo count($dssanpham);
        ?> -->
    </div>
    <div class="sanpham responsive">
        <?php if (!empty($dssanpham)) {
                
            ?>
            
            <?php foreach ($dssanpham as $row) { ?>
                <div class="sanpham1">
                    <form method="POST" action="../controller/index.php?act=cart">
                        <nav style="background: #fff; margin: 2rem;">
                            <img src="../view/images/<?= $row['hinh'] ?>" alt="">
                            <div class="content">
                                <h3 class="tensp" style="width: 70%;"><?= substr($row['tensp'], 0, 45) ?></h3>
                                <p><?= $row['gia'] ?> VNĐ</p>
                                <input type="hidden" name="id_sanpham" value="<?= $row['id'] ?>" />
                                <input type="hidden" name="hinh" value="<?= $row['hinh'] ?>" />
                                <input type="hidden" name="gia" value="<?= $row['gia'] ?>" />
                                <input type="hidden" name="tensp" value="<?= $row['tensp'] ?>" />
                                <input type="hidden" name="soluong" value="1">
                                <div class="buy">
                                    <button><a href="../controller/index.php?act=chitiet&idsp=<?= $row['id'] ?>">Xem chi tiết</a></button>
                                    <button style="background-color: red;"><a href="../controller/index.php?act=chitiet&idsp=<?= $row['id'] ?>">Mua Ngay</a></button>
                                </div>
                            </div>
                        </nav>
                    </form>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>Không tìm thấy sản phẩm nào.</p>
        <?php } ?>
    </div>
</div>
<style>
    /* Đảm bảo rằng toàn bộ trang có margin và padding hợp lý */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Tạo một container cho tất cả các sản phẩm */
.product {
    padding: 20px;
}

/* Hiển thị sản phẩm theo chiều dọc */
.sanpham {
    display: block; /* Hiển thị theo dạng block, mỗi sản phẩm trên một dòng */
}

/* Style cho từng sản phẩm */
.sanpham1 {
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 20px; /* Khoảng cách giữa các sản phẩm */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}



/* Hình ảnh sản phẩm */
.sanpham1 img {
    width: 100%;
    height: auto;
    display: block;
}

/* Nội dung sản phẩm */
.content {
    padding: 15px;
}

/* Tiêu đề sản phẩm */
.tensp {
    font-size: 1.2rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Giá sản phẩm */
.sanpham1 p {
    font-size: 1.1rem;
    color: #e53935;
    margin-bottom: 15px;
}



/* Style cho phần tiêu đề tìm kiếm */
.tille h3 {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 20px;
    text-align: center;
}

</style>