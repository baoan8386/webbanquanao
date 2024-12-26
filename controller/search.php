
<?php
session_start(); 
include_once '../model/sanpham.php';
include_once '../model/config.php';
include_once '../model/danhmuc.php';
include_once '../view/header.php';



if (isset($_GET['query']) && !empty($_GET['query'])) {
    $keyword = trim($_GET['query']);
    $dssanpham = sanpham_search($keyword);

    // Kiểm tra dữ liệu trả về
    if (!$dssanpham) {
        $error_message = "Không có sản phẩm nào phù hợp với từ khóa: $keyword";
        $dssanpham = []; // Đảm bảo $dssanpham luôn là một mảng
        $keyword = htmlspecialchars(trim($_GET['query']));
    }
    
} else {
    $dssanpham = []; // Không có sản phẩm nếu không nhập gì
}

// Include view để hiển thị kết quả
include_once '../view/search_results.php';
include "../view/footer.php";

