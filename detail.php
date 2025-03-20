<?php
include "config.php"; // Kết nối database

$student = null; // Biến lưu thông tin sinh viên

// Kiểm tra xem có MaSV trên URL không
if (isset($_GET['MaSV'])) {
    $MaSV = mysqli_real_escape_string($conn, $_GET['MaSV']);

    // Truy vấn lấy thông tin sinh viên theo MaSV
    $sql = "SELECT * FROM SinhVien WHERE MaSV = '$MaSV'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        echo "<script>alert('Không tìm thấy sinh viên!'); window.location='index.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Không có thông tin sinh viên!'); window.location='index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Chi Tiết</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Thông tin chi tiết sinh viên</h2>
        <div class="border p-4 shadow w-50 mx-auto">
            <?php if ($student): ?>
                <p><strong>Họ Tên:</strong> <?= htmlspecialchars($student['HoTen']) ?></p>
                <p><strong>Giới Tính:</strong> <?= htmlspecialchars($student['GioiTinh']) ?></p>
                <p><strong>Ngày Sinh:</strong> <?= date("d/m/Y", strtotime($student['NgaySinh'])) ?></p>

                <!-- Hiển thị ảnh nếu có -->
                <?php if (!empty($student['Hinh'])): ?>
                    <p><img src="<?= htmlspecialchars($student['Hinh']) ?>" alt="Ảnh Sinh Viên" width="150"></p>
                <?php endif; ?>

                <p><strong>Mã Ngành:</strong> <?= htmlspecialchars($student['MaNganh']) ?></p>

                <a href="edit.php?MaSV=<?= htmlspecialchars($student['MaSV']) ?>" class="btn btn-warning">Chỉnh sửa</a>
                <a href="index.php" class="btn btn-secondary">Quay lại danh sách</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
