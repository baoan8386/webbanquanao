<?php

    function login($username, $password) {
        // Câu truy vấn SQL để lấy thông tin người dùng theo tên đăng nhập
        $sql = "SELECT * FROM user WHERE username = ?";
        
        // Thực hiện câu truy vấn
        $user = pdo_query_one($sql, $username);  // Hàm pdo_query_one() giả sử trả về một dòng duy nhất

        // Nếu có kết quả, kiểm tra mật khẩu
        if ($user) {
            // Sử dụng password_verify để so sánh mật khẩu đã được mã hóa với mật khẩu nhập vào
            if (password_verify($password, $user['password'])) {
                // Nếu mật khẩu đúng, trả về thông tin người dùng
                return $user;
            } else {
                // Nếu mật khẩu sai
                return false;
            }
        }
        
        // Nếu không tìm thấy người dùng
        return false;
    }


    function login_Show(){
        $sql = "SELECT * FROM user";
        return pdo_query($sql);
    }
?>