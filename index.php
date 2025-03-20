<?php
include "config.php";
$result = $conn->query("SELECT * FROM SinhVien JOIN NganhHoc ON SinhVien.MaNganh = NganhHoc.MaNganh");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Sinh Viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .table-hover tbody tr:hover {
            background: #f1f1f1;
        }
        .student-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #dee2e6;
        }
        .btn-sm {
            padding: 5px 10px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar-dark .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .btn-primary {
            background: #d63384;
            border: none;
        }
        .btn-primary:hover {
            background: #bf296f;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- Thanh menu -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark rounded">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Test1</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Sinh Viên</a></li>
                        <li class="nav-item"><a class="nav-link" href="hocphan.php">Học Phần</a></li>
                        <li class="nav-item"><a class="nav-link" href="dangki.php">Đăng Ký</a></li>
                        <li class="nav-item"><a class="nav-link" href="dangnhap.php">Đăng Nhập</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <h2 class="mt-3 text-center text-primary">📌 QUẢN LÝ SINH VIÊN</h2>
        <a href="create.php" class="btn btn-primary mb-3">➕ Thêm Sinh Viên</a>

        <!-- Bảng danh sách sinh viên -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Mã SV</th>
                        <th>Họ Tên</th>
                        <th>Giới Tính</th>
                        <th>Ngày Sinh</th>
                        <th>Hình Ảnh</th>
                        <th>Ngành Học</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row["MaSV"]) ?></td>
                            <td><?= htmlspecialchars($row["HoTen"]) ?></td>
                            <td><?= htmlspecialchars($row["GioiTinh"]) ?></td>
                            <td><?= date("d/m/Y", strtotime($row["NgaySinh"])) ?></td>
                            <td>
                                <img src="<?= htmlspecialchars($row["Hinh"]) ?>" alt="Hình SV" class="student-img">
                            </td>
                            <td><?= htmlspecialchars($row["TenNganh"]) ?></td>
                            <td>
                                <a href="edit.php?MaSV=<?= $row['MaSV'] ?>" class="btn btn-warning btn-sm">✏ Sửa</a>
                                <a href="detail.php?MaSV=<?= $row['MaSV'] ?>" class="btn btn-info btn-sm">🔍 Chi Tiết</a>
                                <a href="delete.php?MaSV=<?= $row['MaSV'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('⚠ Bạn có chắc muốn xóa không?')">🗑 Xóa</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
