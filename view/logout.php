<?php
session_start(); // Bắt đầu session

if (isset($_SESSION['name'])) {
    // Xóa từng session cụ thể
    unset($_SESSION['name']);
    unset($_SESSION['avata']);
    unset($_SESSION['giohang']);
    unset($_SESSION['id']); // Xóa toàn bộ session liên quan khác nếu có

    // Xóa tất cả các biến session
    session_unset(); // Xóa tất cả các biến session đang tồn tại
    // Hủy toàn bộ session
    session_destroy();

    // Ngăn trình duyệt lưu cache, buộc tải lại dữ liệu mới
    header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
    header("Pragma: no-cache"); // HTTP 1.0
    header("Expires: 0"); // Proxies

    // Chuyển hướng về trang đăng nhập
    header("Location: ../controller/index.php?act=login");
    exit(); // Kết thúc script
} else {
    // Nếu không có session, chuyển thẳng về trang login
    header("Location: ../controller/index.php?act=login");
    exit();
}
?>