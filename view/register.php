<link rel="stylesheet" href="../view/css/lo_re.css">
<link rel="stylesheet" href="../view/css/products.css">

<div class="main">

    <div class="dangky">
        <form action="index.php?act=register" method="post" enctype="multipart/form-data"
            onsubmit="return KiemTraUser();">
            <h2>ĐĂNG KÝ</h2>
            <?php
            if (isset($_GET['error'])) { ?>
            <p class="error"><?php echo $_GET['error']; ?></p>
            <?php } ?>
            <input type="text" placeholder="User name" name="username" id="username"><br>
            <div id="loi1"></div>
            <input type="text" placeholder="Họ và tên" name="name" id="name"><br>
            <div id="loi2"></div>
            <input type="email" placeholder="email" name="email" id="email"><br>
            <div id="loi3"></div>
            <input type="password" placeholder="Password" name="password" id="password"><br>
            <div id="loi4"></div>
            <input type="password" placeholder="Nhập lại Password" name="repassword" id="repassword"><br>
            <div id="loi5"></div>
            <input type="file" id="hinh" name="hinh" style="border: none; padding-top: 10px;">
            <div id="loi6"></div>
            <input type="submit" name="dk" value="Đăng Ký"><br>
        </form>
        <a href="../view/logout.php">
            <input type="submit" name="dk" value="Đăng Nhập">
        </a>
    </div>

</div>

<?php
if (isset($_POST["dk"])) {
    // Kiểm tra các trường nhập liệu không để trống
    if ($_POST['username'] != '' && $_POST['email'] != '' && $_POST['name'] != '' && $_POST['password'] != '' && $_POST['repassword'] != '') {
        $user = $_POST['username'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $repassword = $_POST['repassword'];
        $avata = basename($_FILES['hinh']['name']);
        $target = "../view/images/";
        $target_file = $target . $avata;

        // Kiểm tra xem username đã tồn tại hay chưa
        $user_query = regiter_query($user); // Gọi đúng biến $user để kiểm tra
        if ($user_query) {
            echo "<script>alert('Username đã tồn tại')</script>";
        } else if ($password !== $repassword) {
            echo "<script>alert('Password không trùng khớp')</script>";
        } else {
            // Mã hóa
            $password_hashed = password_hash($password, PASSWORD_BCRYPT);

            // Thêm người dùng mới vào cơ sở dữ liệu
            regiter_add($user, $password_hashed, $name, $email, $avata);
            
            // Kiểm tra và tải ảnh đại diện
            if ($_FILES['hinh']['name']) {
                if (move_uploaded_file($_FILES['hinh']['tmp_name'], $target_file)) {
                    echo "<script>alert('Đăng ký tài khoản thành công')</script>";
                } else {
                    echo "<script>alert('Đăng ký tài khoản thành công nhưng không thể tải ảnh đại diện')</script>";
                }
            } else {
                echo "<script>alert('Đăng ký tài khoản thành công, nhưng không có ảnh đại diện.')</script>";
            }
        }
    } else {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin')</script>";
    }
}
?>
