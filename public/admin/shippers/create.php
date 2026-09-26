<?php
require_once '/var/www/src/config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipperName = trim($_POST['shipper_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($shipperName === '') {
        $error = 'Vui lòng nhập tên shipper.';
    } else {
        $sql = "INSERT INTO shippers (ShipperName, Phone)
                VALUES (?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $shipperName, $phone);

        if ($stmt->execute()) {
            header('Location: /admin/shippers/');
            exit;
        } else {
            $error = 'Không thể thêm shipper.';
        }
    }
}

require_once '/var/www/src/includes/admin/header.php';
?>

<div class="container mt-4">
    <h1>Thêm shipper</h1>

    <hr>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="post">

        <div class="mb-3">
            <label class="form-label">
                Tên shipper
            </label>

            <input
                type="text"
                name="shipper_name"
                class="form-control"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">
                Số điện thoại
            </label>

            <input
                type="text"
                name="phone"
                class="form-control"
            >
        </div>

        <button type="submit" class="btn btn-primary">
            Lưu
        </button>

        <a href="/admin/shippers/" class="btn btn-secondary">
            Hủy
        </a>

    </form>
</div>

<?php
require_once '/var/www/src/includes/admin/footer.php';
?>