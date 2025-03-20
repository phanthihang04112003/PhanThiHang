<?php
session_start();
$conn = new mysqli("localhost", "root", "", "test1");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$error = ""; // Biến lưu lỗi đăng nhập

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $maSV = trim($_POST['MaSV']);

    if (!empty($maSV)) {
        // Chuẩn bị truy vấn
        $stmt = $conn->prepare("SELECT * FROM sinhvien WHERE MaSV = ?");
        $stmt->bind_param("s", $maSV);
        $stmt->execute();
        $result = $stmt->get_result();

        // Kiểm tra sinh viên có tồn tại không
        if ($result->num_rows > 0) {
            $_SESSION['MaSV'] = $maSV;
            header("Location: index.php"); // Chuyển hướng về index.php sau khi đăng nhập thành công
            exit();
        } else {
            $error = "⚠ Mã sinh viên không tồn tại!";
        }

        $stmt->close();
    } else {
        $error = "⚠ Vui lòng nhập MSSV!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #ff758c, #ff7eb3);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
        }
        h2 {
            color: #ff4081;
        }
        .input-box {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }
        .error {
            color: red;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 6px;
            background: #ff4081;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn:hover {
            background: #d81b60;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>ĐĂNG NHẬP</h2>

        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST">
            <input type="text" name="MaSV" class="input-box" placeholder="Nhập Mã Sinh Viên" required>
            <button type="submit" class="btn">Đăng Nhập</button>
        </form>
    </div>

</body>
</html>
