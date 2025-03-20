<?php
include "config.php"; // Kết nối database

$selectedStudent = null; // Biến lưu thông tin sinh viên đã chọn

// Lấy danh sách sinh viên
$sql = "SELECT MaSV, HoTen FROM SinhVien";
$result = $conn->query($sql);

// Xử lý khi chọn sinh viên
if (isset($_POST["select"]) && !empty($_POST["MaSV"])) {
    $MaSV = mysqli_real_escape_string($conn, $_POST["MaSV"]);

    // Lấy thông tin sinh viên
    $query = "SELECT * FROM SinhVien WHERE MaSV = '$MaSV'";
    $res = $conn->query($query);

    if ($res->num_rows == 1) {
        $selectedStudent = $res->fetch_assoc();
    }
}

// Xử lý khi nhấn Xóa
if (isset($_POST["delete"]) && !empty($_POST["MaSV"])) {
    $MaSV = mysqli_real_escape_string($conn, $_POST["MaSV"]);
    $sqlDelete = "DELETE FROM SinhVien WHERE MaSV = '$MaSV'";

    if ($conn->query($sqlDelete) === TRUE) {
        echo "<script>alert('Xóa sinh viên thành công!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi xóa: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xóa Sinh Viên</title>
</head>
<body>
    <h2>XÓA THÔNG TIN</h2>

    <!-- Chọn sinh viên để hiển thị -->
    <form method="POST">
        <label for="MaSV">Chọn sinh viên:</label>
        <select name="MaSV" id="MaSV" required>
            <option value="">-- Chọn sinh viên --</option>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['MaSV']) ?>"
                    <?= isset($selectedStudent) && $selectedStudent['MaSV'] == $row['MaSV'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['HoTen']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="select">Xem</button>
    </form>

    <!-- Hiển thị thông tin sinh viên -->
    <?php if ($selectedStudent): ?>
        <p><strong>Họ Tên:</strong> <?= htmlspecialchars($selectedStudent['HoTen'] ?? 'Không có dữ liệu') ?></p>
        <p><strong>Giới Tính:</strong> <?= htmlspecialchars($selectedStudent['GioiTinh'] ?? 'Không có dữ liệu') ?></p>
        <p><strong>Ngày Sinh:</strong> <?= htmlspecialchars($selectedStudent['NgaySinh'] ?? 'Không có dữ liệu') ?></p>
        <p><strong>Mã Ngành:</strong> <?= htmlspecialchars($selectedStudent['MaNganh'] ?? 'Không có dữ liệu') ?></p>

        <!-- Hiển thị ảnh nếu có -->
        <?php if (!empty($selectedStudent['HinhAnh'])): ?>
            <p><img src="uploads/<?= htmlspecialchars($selectedStudent['HinhAnh']) ?>" alt="Ảnh Sinh Viên" width="150"></p>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="MaSV" value="<?= htmlspecialchars($selectedStudent['MaSV']) ?>">
            <button type="submit" name="delete">Xóa</button>
            <a href="index.php">Hủy</a>
        </form>
    <?php endif; ?>
</body>
</html>
