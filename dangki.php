<?php
session_start();
$conn = new mysqli("localhost", "root", "", "Test1");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra nếu sinh viên chưa đăng nhập
if (!isset($_SESSION['MaSV'])) {
    die("⚠ Bạn cần đăng nhập để tiếp tục.");
}

$maSV = $_SESSION['MaSV'];

// Lấy thông tin sinh viên
$sql_sv = "SELECT * FROM sinhvien WHERE MaSV = ?";
$stmt = $conn->prepare($sql_sv);
$stmt->bind_param("s", $maSV);
$stmt->execute();
$result_sv = $stmt->get_result();
$sinhvien = $result_sv->fetch_assoc();

// Nếu sinh viên chưa có trong bảng Đăng Ký -> Thêm mới
$sql_check_dangky = "SELECT MaDK FROM DangKy WHERE MaSV = ?";
$stmt = $conn->prepare($sql_check_dangky);
$stmt->bind_param("s", $maSV);
$stmt->execute();
$result_check_dk = $stmt->get_result();

if ($result_check_dk->num_rows == 0) {
    $ngayDK = date("Y-m-d");
    $sql_insert_dk = "INSERT INTO DangKy (NgayDK, MaSV) VALUES (?, ?)";
    $stmt = $conn->prepare($sql_insert_dk);
    $stmt->bind_param("ss", $ngayDK, $maSV);
    $stmt->execute();
}

// Lấy MaDK của sinh viên
$sql_get_madk = "SELECT MaDK FROM DangKy WHERE MaSV = ?";
$stmt = $conn->prepare($sql_get_madk);
$stmt->bind_param("s", $maSV);
$stmt->execute();
$result_madk = $stmt->get_result();
$row_madk = $result_madk->fetch_assoc();
$maDK = $row_madk['MaDK'];

// Xử lý đăng ký học phần
if (isset($_POST['ma_hoc_phan'])) {
    $maHP = $_POST['ma_hoc_phan'];

    // Kiểm tra học phần đã đăng ký chưa
    $sql_check_hp = "SELECT * FROM ChiTietDangKy WHERE MaDK = ? AND MaHP = ?";
    $stmt = $conn->prepare($sql_check_hp);
    $stmt->bind_param("is", $maDK, $maHP);
    $stmt->execute();
    $result_check_hp = $stmt->get_result();

    if ($result_check_hp->num_rows == 0) {
        // Thêm mới vào bảng ChiTietDangKy
        $sql_insert_ctdk = "INSERT INTO ChiTietDangKy (MaDK, MaHP) VALUES (?, ?)";
        $stmt = $conn->prepare($sql_insert_ctdk);
        $stmt->bind_param("is", $maDK, $maHP);
        if ($stmt->execute()) {
            $_SESSION['message'] = "✅ Đăng ký học phần thành công!";
        } else {
            $_SESSION['message'] = "❌ Lỗi khi đăng ký học phần!";
        }
    } else {
        $_SESSION['message'] = "⚠ Học phần này đã được đăng ký trước đó!";
    }

    header("Location: dangki.php");
    exit();
}

// Xử lý xóa một học phần
if (isset($_POST['delete_ma_hp'])) {
    $maHP_delete = $_POST['delete_ma_hp'];

    $sql_delete_hp = "DELETE FROM ChiTietDangKy WHERE MaDK = ? AND MaHP = ?";
    $stmt = $conn->prepare($sql_delete_hp);
    $stmt->bind_param("is", $maDK, $maHP_delete);
    $stmt->execute();
    $_SESSION['message'] = "✅ Hủy học phần thành công!";
    header("Location: dangki.php");
    exit();
}

// Xử lý xóa tất cả học phần
if (isset($_POST['delete_all'])) {
    $sql_delete_all = "DELETE FROM ChiTietDangKy WHERE MaDK = ?";
$stmt = $conn->prepare($sql_delete_all);
    $stmt->bind_param("i", $maDK);
    $stmt->execute();
    $_SESSION['message'] = "✅ Hủy tất cả học phần thành công!";
    header("Location: dangki.php");
    exit();
}

// Lấy danh sách học phần đã đăng ký
$sql = "SELECT hp.MaHP, hp.TenHP, hp.SoTinChi 
        FROM ChiTietDangKy ctdk 
        JOIN HocPhan hp ON ctdk.MaHP = hp.MaHP
        WHERE ctdk.MaDK = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $maDK);
$stmt->execute();
$result = $stmt->get_result();

$total_courses = 0;
$total_credits = 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Học Phần</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 10px; text-align: center; }
        .btn { background-color: green; color: white; padding: 5px 10px; border: none; cursor: pointer; }
        .btn-delete { background-color: red; }
    </style>
</head>
<body>

    <?php
    // Hiển thị thông báo nếu có
    if (isset($_SESSION['message'])) {
        echo "<script>alert('" . $_SESSION['message'] . "');</script>";
        unset($_SESSION['message']);
    }
    ?>

    <h2>Đăng Ký Học Phần</h2>
    
    <h3>Danh sách học phần có sẵn</h3>
    <table>
        <tr>
            <th>Mã HP</th>
            <th>Tên Học Phần</th>
            <th>Số Tín Chỉ</th>
            <th>Hành động</th>
        </tr>
        <?php
        $sql_hp = "SELECT * FROM HocPhan";
        $result_hp = $conn->query($sql_hp);
        while ($row = $result_hp->fetch_assoc()):
        ?>
            <tr>
                <td><?= htmlspecialchars($row['MaHP']) ?></td>
                <td><?= htmlspecialchars($row['TenHP']) ?></td>
                <td><?= htmlspecialchars($row['SoTinChi']) ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="ma_hoc_phan" value="<?= htmlspecialchars($row['MaHP']) ?>">
                        <button type="submit" class="btn">Đăng Ký</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h3>Danh sách học phần đã đăng ký</h3>
    <table>
        <tr>
            <th>Mã HP</th>
            <th>Tên Học Phần</th>
            <th>Số Tín Chỉ</th>
            <th>Hành động</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['MaHP']) ?></td>
                <td><?= htmlspecialchars($row['TenHP']) ?></td>
                <td><?= htmlspecialchars($row['SoTinChi']) ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="delete_ma_hp" value="<?= htmlspecialchars($row['MaHP']) ?>">
                        <button type="submit" class="btn btn-delete">Xóa</button>
                    </form>
                </td>
            </tr>
            <?php 
            $total_courses++;
            $total_credits += $row['SoTinChi'];
            ?>
        <?php endwhile; ?>
    </table>

    <form method="POST">
        <button type="submit" name="delete_all" class="btn btn-delete">Xóa Tất Cả</button>
    </form>

    <p>Số lượng học phần đã đăng ký: <strong><?= $total_courses ?></strong></p>
    <p>Tổng số tín chỉ: <strong><?= $total_credits ?></strong></p>

</body>
</html>