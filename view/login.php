<!-- <link rel="stylesheet" href="../view/css/lo_re.css"> -->
<link rel="stylesheet" href="../view/css/products.css">
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<?php
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra xem username và password có khác rỗng không
    if (!empty($username) && !empty($password)) {
        $user = login($username, $password);  // Sử dụng hàm login đã sửa
        if ($user) {
            // Lưu thông tin người dùng vào session
            $_SESSION['id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['avata'] = $user['avata'];

            // Chuyển hướng sau khi đăng nhập thành công
            if (isset($_SESSION['redirect_to'])) {
                $redirect_to = $_SESSION['redirect_to'];
                unset($_SESSION['redirect_to']);
                header("Location: $redirect_to");
                exit();
            } else {
                echo "<script>alert('Xin chào " . $_SESSION['name'] . ". Bạn đã đăng nhập thành công.');</script>";
                header("Location: index.php");
                exit();
            }
        } else {
            echo "<script>alert('Tên đăng nhập hoặc mật khẩu không chính xác.')</script>";
        }
    } else {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin')</script>";
    }
}

?>

<div class="main">
    <?php
    if (isset($_SESSION['name'])) {
        echo '  
        <div class="userlogin">
            <h2>Xin Chào: <span>' . $_SESSION['name'] . '</span></h2>
            <div class="user-info">
                <img src="../view/images/' . $_SESSION['avata'] . '" alt="Avatar" class="user-avatar">
            </div>
            <table class="bang">
                <tr><th colspan="2">Thông Tin Tài Khoản</th></tr>
                <tr>
                    <th>Tên Đăng Nhập</th>
                    <td>' . $_SESSION['username'] . '</td>
                    
                </tr>
                <tr>
                    <th>Password</th>
                    <td><input type="password" value="********" readonly /></td> <!-- Ẩn mật khẩu -->
                </tr>

                <tr>
                    <th>Email</th>
                    <td><input type="text" value="' . $_SESSION['email'] . '" readonly /></td>
                </tr>
                <tr>
                    <td class="sub"><a href="index.php?act=suauser">Sửa</a></td>
                    <td class="sub"><a href="../view/logout.php">Đăng Xuất</a></td>
                </tr>
            </table> 
        </div>';
    } else {
        echo '
        <div class="dangky">
            <form action="index.php?act=login" method="POST">
                <h2>ĐĂNG NHẬP</h2>
                <input type="text" placeholder="User name" name="username"><br>
                <input type="password" placeholder="Password" name="password"><br>
                <input type="checkbox"><label for="">Nhớ mật khẩu?</label><br>
                <div class="two">
                    <input type="submit" value="Đăng nhập" name="login" class="dangnhap">
                    <button><a href="../controller/index.php?act=register">Đăng ký</a></button>
                </div>
                <div class="loginkhac">
                    <button class="facebook"><i class="fa-brands fa-facebook"></i> Facebook</button>
                    <button class="gmail"><a href="../controller/indexadmin.php" style="text-decoration: none; color: black;">Gmail</a></button>
                    <button class="google"><i class="fa-brands fa-google"></i> Google</button>
                </div>
            </form>
        </div>';
    }
    ?>
</div>

<style>
/* Toàn bộ bố cục chính */
.main {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 10px;
    background-color: #f9f9f9;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    font-family: Arial, sans-serif;
}

/* Khung người dùng đã đăng nhập */
.userlogin {
    text-align: center;
}

.userlogin h2 {
    font-size: 24px;
    margin-bottom: 10px;
    color: #333;
}

.userlogin .user-info {
    margin: 20px 0;
    text-align: center;
}

.user-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #357ae8;
    margin: 0 auto;
}

.bang {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    text-align: center;
}

.bang th, .bang td {
    border: 1px solid #ddd;
    padding: 10px;
}

.bang th {
    background-color: #357ae8;
    color: white;
    font-weight: bold;
}

.bang td {
    font-size: 14px;
    color: #555;
}

.bang input[type="password"],
.bang input[type="text"] {
    border: none;
    background-color: transparent;
    text-align: center;
    font-size: 14px;
    color: #333;
    outline: none;
}

/* Email khung */
.email-field {
    display: block;
    width: 80%;
    margin: 10px auto;
    padding: 8px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #fff;
    text-align: center;
}

/* Nút sửa và đăng xuất */
.sub a {
    display: inline-block;
    text-decoration: none;
    color: white;
    background-color: #357ae8;
    padding: 8px 16px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.sub a:hover {
    background-color: #45a049;
}

/* Khung đăng nhập */
.dangky {
    text-align: center;
}

.dangky h2 {
    font-size: 22px;
    margin-bottom: 20px;
    color: #333;
}

.dangky input[type="text"],
.dangky input[type="password"] {
    width: 90%;
    margin: 10px 0;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
}

.dangky input[type="submit"],
.dangky button {
    margin-top: 10px;
    padding: 10px 20px;
    font-size: 14px;
    border: none;
    border-radius: 5px;
    color: white;
    background-color: #357ae8;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.dangky button a{
    color: white;
}

.dangky input[type="submit"]:hover,
.dangky button:hover {
    background-color: #357ae8;
}

/* Nút đăng nhập qua mạng xã hội */
.loginkhac {
    margin-top: 20px;
}

.loginkhac button {
    margin: 5px;
    padding: 10px;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.loginkhac .facebook {
    background-color: #3b5998;
}

.loginkhac .facebook:hover {
    background-color: #334d84;
}

.loginkhac .gmail {
    background-color: #db4437;
}

.loginkhac .gmail:hover {
    background-color: #c33d2e;
}

.loginkhac .google {
    background-color: #4285f4;
}

.loginkhac .google:hover {
    background-color: #357ae8;
}
</style>
