<?php
include "config.php"; // Kết nối database

// Kiểm tra nếu có MaSV trong URL
if (!isset($_GET["MaSV"])) {
    echo "<script>alert('Không tìm thấy sinh viên!'); window.location='index.php';</script>";
    exit();
}

$MaSV = mysqli_real_escape_string($conn, $_GET["MaSV"]);

// Lấy thông tin sinh viên
$sql = "SELECT * FROM SinhVien WHERE MaSV = '$MaSV'";
$result = $conn->query($sql);

if ($result->num_rows != 1) {
    echo "<script>alert('Không tìm thấy sinh viên!'); window.location='index.php';</script>";
    exit();
}

$row = $result->fetch_assoc();

// Xử lý cập nhật thông tin sinh viên
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $HoTen = mysqli_real_escape_string($conn, $_POST["HoTen"]);
    $GioiTinh = mysqli_real_escape_string($conn, $_POST["GioiTinh"]);
    $NgaySinh = mysqli_real_escape_string($conn, $_POST["NgaySinh"]);
    $MaNganh = mysqli_real_escape_string($conn, $_POST["MaNganh"]);
    $Hinh = $row["Hinh"]; // Giữ ảnh cũ mặc định

    // Xử lý upload ảnh mới nếu có
    if (!empty($_FILES["Hinh"]["name"])) {
        $target_dir = "uploads/";
        $file_ext = pathinfo($_FILES["Hinh"]["name"], PATHINFO_EXTENSION);
        $new_file_name = $MaSV . "_" . time() . "." . $file_ext;
        $target_file = $target_dir . $new_file_name;

        // Kiểm tra định dạng file hợp lệ
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (in_array(strtolower($file_ext), $allowed_types)) {
            if (move_uploaded_file($_FILES["Hinh"]["tmp_name"], $target_file)) {
                $Hinh = $target_file; // Cập nhật ảnh mới
            } else {
                echo "<script>alert('Lỗi khi tải ảnh lên!');</script>";
            }
        } else {
            echo "<script>alert('Chỉ chấp nhận file ảnh JPG, JPEG, PNG, GIF!');</script>";
        }
    }

    // Cập nhật dữ liệu vào database
    $sql = "UPDATE SinhVien SET 
            HoTen='$HoTen', 
            GioiTinh='$GioiTinh', 
            NgaySinh='$NgaySinh', 
            Hinh='$Hinh', 
            MaNganh='$MaNganh' 
            WHERE MaSV='$MaSV'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Cập nhật thành công!'); window.location='index.php';</script>";
        exit();
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Sinh Viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Quản Lý Sinh Viên</a>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="index.php">Sinh Viên</a></li>
                    <li class="nav-item"><a class="nav-link" href="hocphan.php">Học Phần</a></li>
                    <li class="nav-item"><a class="nav-link" href="dangky.php">Đăng Ký</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Đăng Nhập</a></li>
                </ul>
            </div>
        </nav>

        <h2 class="mt-3 text-center">Sửa Thông Tin Sinh Viên</h2>
        <form method="POST" enctype="multipart/form-data" class="w-50 mx-auto border p-4 shadow">
            <input type="hidden" name="MaSV" value="<?= htmlspecialchars($row['MaSV']) ?>">

            <div class="mb-3">
                <label class="form-label">Họ Tên</label>
                <input type="text" name="HoTen" class="form-control" value="<?= htmlspecialchars($row['HoTen']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Giới Tính</label>
                <select name="GioiTinh" class="form-control">
                    <option value="Nam" <?= ($row['GioiTinh'] == 'Nam') ? 'selected' : '' ?>>Nam</option>
                    <option value="Nữ" <?= ($row['GioiTinh'] == 'Nữ') ? 'selected' : '' ?>>Nữ</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Ngày Sinh</label>
                <input type="date" name="NgaySinh" class="form-control" value="<?= htmlspecialchars($row['NgaySinh']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Hình Ảnh</label>
                <input type="file" name="Hinh" class="form-control">
                <input type="hidden" name="HinhCu" value="<?= htmlspecialchars($row['Hinh']) ?>">
                <br>
                <?php if (!empty($row['Hinh'])): ?>
                    <img src="<?= htmlspecialchars($row['Hinh']) ?>" alt="Ảnh sinh viên" class="img-thumbnail" width="150">
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Mã Ngành</label>
                <input type="text" name="MaNganh" class="form-control" value="<?= htmlspecialchars($row['MaNganh']) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</body>
</html>
