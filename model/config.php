<?php
    // Hàm này tạo kết nối đến cơ sở dữ liệu MySQL bằng PDO.
    function pdo_con(){
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "duan_quanao";

        try {
            // Tạo kết nối PDO với cơ sở dữ liệu
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // Cấu hình PDO để hiển thị lỗi nếu có vấn đề xảy ra
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Thiết lập mã hóa ký tự thành UTF-8
            $conn->exec("SET NAMES 'utf8'");
            // Trả về đối tượng kết nối PDO
            return $conn;
        } catch (PDOException $e) {
            // Nếu kết nối thất bại, in ra thông báo lỗi và kết thúc chương trình
            die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
        }
    }

    // Hàm này thực thi câu lệnh SQL (thường là các câu lệnh INSERT, UPDATE, DELETE) mà không trả về kết quả.
    function pdo_execute($sql){
        // Lấy tất cả các tham số sau tham số đầu tiên (sql)
        $sql_args = array_slice(func_get_args(), 1);
        try {
            // Tạo kết nối đến cơ sở dữ liệu
            $conn = pdo_con();
            // Chuẩn bị câu lệnh SQL
            $stmt = $conn->prepare($sql);
            // Thực thi câu lệnh với các tham số
            $stmt->execute($sql_args);
        } catch (PDOException $e) {
            // Nếu có lỗi, ném lỗi ra ngoài
            throw $e;
        } finally {
            // Đảm bảo kết nối được đóng sau khi thực thi
            unset($conn);
        }
    }

    // Hàm này thực thi câu lệnh SQL và trả về tất cả các kết quả dưới dạng mảng.
    function pdo_query($sql){
        // Lấy các tham số còn lại sau tham số đầu tiên (sql)
        $sql_args = array_slice(func_get_args(), 1);
        try {
            // Tạo kết nối đến cơ sở dữ liệu
            $conn = pdo_con();
            // Chuẩn bị câu lệnh SQL
            $stmt = $conn->prepare($sql);
            // Thực thi câu lệnh với các tham số
            $stmt->execute($sql_args);
            // Lấy tất cả các kết quả trả về và lưu vào biến $rows
            $rows = $stmt->fetchAll();
            // Trả về các kết quả dưới dạng mảng
            return $rows;
        } catch (PDOException $e) {
            // Nếu có lỗi, ném lỗi ra ngoài
            throw $e;
        } finally {
            // Đảm bảo kết nối được đóng sau khi thực thi
            unset($conn);
        }
    }

    // Hàm này thực thi câu lệnh SQL và trả về tất cả các kết quả dưới dạng mảng với tham số truyền vào.
    function pdo_query_with_params($sql, $params) {
        try {
            // Tạo kết nối đến cơ sở dữ liệu
            $conn = pdo_con();
            // Chuẩn bị câu lệnh SQL
            $stmt = $conn->prepare($sql);
            // Thực thi câu lệnh SQL với các tham số truyền vào
            $stmt->execute($params);
            // Lấy tất cả các kết quả và trả về dưới dạng mảng
            $rows = $stmt->fetchAll();
            return $rows;
        } catch (PDOException $e) {
            // Nếu có lỗi, ném lỗi ra ngoài
            throw $e;
        } finally {
            // Đảm bảo kết nối được đóng sau khi thực thi
            unset($conn);
        }
    }

    // Hàm này thực thi câu lệnh SQL và trả về một kết quả duy nhất.
    function pdo_query_one($sql){
        // Lấy tất cả các tham số sau tham số đầu tiên (sql)
        $sql_args = array_slice(func_get_args(), 1);
        try {
            // Tạo kết nối đến cơ sở dữ liệu
            $conn = pdo_con();
            // Chuẩn bị câu lệnh SQL
            $stmt = $conn->prepare($sql);
            // Thực thi câu lệnh với các tham số
            $stmt->execute($sql_args);
            // Lấy kết quả đầu tiên dưới dạng mảng liên kết
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        } catch (PDOException $e) {
            // Nếu có lỗi, ném lỗi ra ngoài
            throw $e;
        } finally {
            // Đảm bảo kết nối được đóng sau khi thực thi
            unset($conn);
        }
    }

    // Hàm này thực thi câu lệnh SQL mà không trả về bất kỳ kết quả nào.
    function pdo_query_cancel($sql, $params = []) {
        try {
            // Tạo kết nối đến cơ sở dữ liệu
            $conn = pdo_con();
            // Chuẩn bị câu lệnh SQL
            $stmt = $conn->prepare($sql);
            // Thực thi câu lệnh SQL với các tham số đã truyền vào
            $stmt->execute($params);
        } catch (PDOException $e) {
            // Nếu có lỗi, ném lỗi ra ngoài
            throw $e;
        } finally {
            // Đảm bảo kết nối được đóng sau khi thực thi
            unset($conn);
        }
    }
?>
