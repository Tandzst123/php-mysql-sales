<?php
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);

        $uploadDir = __DIR__ . '/products/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $destination = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $destination)) {
            $message = "Upload thành công: " . htmlspecialchars($fileName);
        } else {
            $message = "Upload thất bại.";
        }
    } else {
        $message = "Vui lòng chọn file.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Test Upload</title>
</head>
<body>

<h1>Test upload hình ảnh</h1>

<?php if ($message): ?>
    <p><?= $message ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="image" accept="image/*">
    <button type="submit">Upload</button>
</form>

</body>
</html>