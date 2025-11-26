<?php
require "data.php"; // $flowers
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách hoa</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: auto; }
        .flower { margin-bottom: 30px; }
        .flower img { max-width: 100%; margin-top: 10px; }
        h1 { text-align: center; }
    </style>
</head>
<body>

<h1>14 Loại Hoa Tuyệt Đẹp Xuân – Hè</h1>
<?php if (!empty($flowers)): ?>
    <?php foreach ($flowers as $f): ?>
    <div class="flower">
        <h2><?= $f["ten"] ?></h2>
        <img src="<?= $f["anh"] ?>" alt="<?= $f["ten"] ?>">
        <p><?= $f["mo_ta"] ?></p>
    </div>
    <?php endforeach; ?>
<?php else: ?>
            <tr>
                <td colspan="7">Chưa có hoa nào trong mảng.</td>
            </tr>
<?php endif; ?>

</body>
</html>
