<link rel="stylesheet" href="../view/css/index.css">
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
