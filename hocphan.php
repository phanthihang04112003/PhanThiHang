<?php
include "config.php"; // Kết nối database

// Truy vấn lấy danh sách học phần
$sql = "SELECT MaHP, TenHP, SoTinChi FROM hocphan"; // Sửa đúng tên bảng và cột
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Học Phần</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        .btn {
            background-color: green;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>DANH SÁCH HỌC PHẦN</h2>
    
    <table>
        <tr>
            <th>Mã Học Phần</th>
            <th>Tên Học Phần</th>
            <th>Số Tín Chỉ</th>
            <th>Hành Động</th>
        </tr>
        <?php 
        if ($result && $result->num_rows > 0): 
            while ($row = $result->fetch_assoc()): 
        ?>
                <tr>
                    <td><?= htmlspecialchars($row['MaHP']) ?></td>
                    <td><?= htmlspecialchars($row['TenHP']) ?></td>
                    <td><?= htmlspecialchars($row['SoTinChi']) ?></td>
                    <td>
                        <form method="POST" action="dangki.php">
                            <input type="hidden" name="ma_hoc_phan" value="<?= htmlspecialchars($row['MaHP']) ?>">
                            <button type="submit" class="btn">Đăng Ký</button>
                        </form>
                    </td>
                </tr>
        <?php 
            endwhile; 
        else:
            echo "<tr><td colspan='4'>Không có học phần nào.</td></tr>";
        endif;
        ?>
    </table>
</body>
</html>