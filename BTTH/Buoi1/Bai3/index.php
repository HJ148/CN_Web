<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách tài khoản (đọc từ CSV)</title>
    <style>
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #555;
            padding: 8px 12px;
            text-align: center;
        }
        th {
            background-color: #333;
            color: white;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>

<h2>Danh sách tài khoản đọc từ tệp CSV</h2>

<?php
$filename = "65HTTT_Danh_sach_diem_danh.csv";

if (!file_exists($filename)) {
    echo "<p style='color:red; text-align:center;'>Không tìm thấy tệp $filename</p>";
    exit;
}

$file = fopen($filename, "r");

echo "<table>";
$header = fgetcsv($file); // đọc dòng tiêu đề

// In tiêu đề bảng
echo "<tr>";
foreach ($header as $col) {
    echo "<th>$col</th>";
}
echo "</tr>";

// In dữ liệu
while (($row = fgetcsv($file)) !== FALSE) {
    echo "<tr>";
    foreach ($row as $col) {
        echo "<td>$col</td>";
    }
    echo "</tr>";
}

echo "</table>";

fclose($file);
?>

</body>
</html>
