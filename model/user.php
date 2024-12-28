<?php
    function show_user(){
        $sql = "SELECT*FROM user";
        return pdo_query($sql);
    }

    function show_user_id($id){
        $sql = "SELECT*FROM user WHERE id=?";
        return pdo_query_one($sql, $id);
    }
    function add_user($username,$password,$name,$email, $avata){
        $sql = "INSERT INTO user(username, password, name, email,  avata) 
        VALUES(?,?,?,?,?)";
         pdo_execute($sql, $username,$password,$name,$email, $avata);
    }
    function update_user_avata($username,$password,$name, $avata,$email,$id){
        $sql = "UPDATE  user SET username=?, password=?, name=?, avata=?, email=? WHERE id=?";
         pdo_execute($sql, $username,$password,$name, $avata,$email,$id);
    }

    function update_user($username,$password,$name, $email,$id){
        $sql = "UPDATE  user SET username=?, password=?, name=?, email=? WHERE id=?";
         pdo_execute($sql, $username,$password,$name, $email,$id);
    }
    function delete_user($id) {
        try {
            // Kiểm tra xem người dùng có bình luận nào không
            $sql = "SELECT COUNT(*) FROM binhluan WHERE iduser = ?";
            
            // Thực hiện truy vấn
            $stmt = pdo_query($sql, $id); // pdo_query sẽ trả về một mảng kết quả hoặc PDOStatement
            
            // Lấy kết quả trả về (số lượng bình luận)
            $comment_count = $stmt[0]['COUNT(*)']; // Assuming pdo_query returns an associative array
    
            if ($comment_count > 0) {
                // Xóa các bình luận của người dùng nếu có
                $sql = "DELETE FROM binhluan WHERE iduser = ?";
                pdo_execute($sql, $id);
            }
    
            // Xóa người dùng
            $sql = "DELETE FROM user WHERE id = ?";
            pdo_execute($sql, $id);
    
            echo "Xóa người dùng và các bình luận liên quan thành công!";
        } catch (PDOException $e) {
            // Xử lý lỗi
            error_log("Lỗi khi xóa người dùng: " . $e->getMessage());
            echo "Xóa người dùng thất bại!";
        }
    }
    
    
    
?>