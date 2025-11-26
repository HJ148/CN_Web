<?php
require "data.php";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản trị hoa</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 10px; border: 1px solid #ccc; vertical-align: top; }
        img { max-width: 120px; }
        h1 { text-align: center; }
    </style>
</head>
<body>

<h1>Quản trị danh sách hoa</h1>

<table>
    <tr>
        <th>Tên hoa</th>
        <th>Mô tả</th>
        <th>Ảnh</th>
        <th>Hành động</th>
    </tr>
    <?php if (!empty($flowers)): ?>
        <?php foreach ($flowers as $index => $f): ?>
        <tr>
            <td><?php echo htmlspecialchars($f["ten"]); ?></td>
            <td><?php echo htmlspecialchars($f["mo_ta"]); ?></td>
            <td><img src="<?= $f["anh"] ?>" alt=""></td>
            <td>
                <button>Thêm</button>
                <button>Sửa</button>
                <button>Xóa</button>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
            <tr>
                <td colspan="7">Chưa có hoa nào trong mảng.</td>
            </tr>
    <?php endif; ?>
</table>

</body>
</html>
