<?php
    // Hàm này dùng để truy vấn thông tin quản trị viên theo tên người dùng.
    function admin_query($user){
        // Câu lệnh SQL để chọn tất cả dữ liệu từ bảng `admin` nơi `username` khớp với tham số truyền vào.
        $sql = "SELECT * FROM admin WHERE username=?";
        // Thực thi câu lệnh SQL và trả về kết quả truy vấn. `pdo_query` sẽ thay thế `?` với giá trị `$user`.
        return pdo_query($sql, $user);
    }

    // Hàm này trả về tất cả thông tin quản trị viên dựa trên ID.
    function show_admin_id($id){
        // Câu lệnh SQL để chọn tất cả dữ liệu từ bảng `admin` nơi `id` khớp với tham số truyền vào.
        $sql = "SELECT * FROM admin WHERE id=?";
        // Thực thi câu lệnh SQL và trả về kết quả truy vấn. `pdo_query` thay thế `?` với giá trị `$id`.
        return pdo_query($sql, $id);
    }

    // Hàm này trả về thông tin của một quản trị viên duy nhất theo ID.
    function show_admin_id_one($id){
        // Câu lệnh SQL giống như trong `show_admin_id`, nhưng trả về một kết quả duy nhất.
        $sql = "SELECT * FROM admin WHERE id=?";
        // Thực thi câu lệnh SQL và trả về một dòng duy nhất kết quả. `pdo_query_one` sẽ chỉ trả về một dòng, nếu có.
        return pdo_query_one($sql, $id);
    }

    // Hàm này dùng để cập nhật tên người dùng (`username`) của quản trị viên theo ID.
    function update_admin_user($id, $user){
        // Câu lệnh SQL để cập nhật tên người dùng trong bảng `admin` nơi `id` khớp với tham số truyền vào.
        $sql = "UPDATE admin SET username=? WHERE id=?";
        // Thực thi câu lệnh SQL với giá trị `$user` và `$id` thay thế cho `?` trong câu lệnh.
        pdo_execute($sql, $user, $id);
    }

    // Hàm này dùng để cập nhật mật khẩu (`password`) của quản trị viên theo ID.
    function update_admin_pass($id, $pass){
        // Câu lệnh SQL để cập nhật mật khẩu trong bảng `admin` nơi `id` khớp với tham số truyền vào.
        $sql = "UPDATE admin SET password=? WHERE id=?";
        // Thực thi câu lệnh SQL với giá trị `$pass` và `$id` thay thế cho `?` trong câu lệnh.
        pdo_execute($sql, $pass, $id);
    }
?>
