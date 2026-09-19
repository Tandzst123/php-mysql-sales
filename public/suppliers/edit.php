<?php

$pageTitle = 'Sửa nhà cung cấp';

require_once '/var/www/src/config/database.php';

$error = '';

$supplierID = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($supplierID <= 0) {
    die('Mã nhà cung cấp không hợp lệ.');
}

/* Lấy dữ liệu hiện tại */
$sql = "
    SELECT
        SupplierID,
        SupplierName,
        ContactName,
        Address,
        City,
        PostalCode,
        Country,
        Phone
    FROM suppliers
    WHERE SupplierID = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $supplierID);
$stmt->execute();

$result = $stmt->get_result();
$supplier = $result->fetch_assoc();

$stmt->close();

if (!$supplier) {
    die('Không tìm thấy nhà cung cấp.');
}

/* Xử lý cập nhật */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $supplierName = trim($_POST['supplier_name'] ?? '');
    $contactName = trim($_POST['contact_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postalCode = trim($_POST['postal_code'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($supplierName === '') {

        $error = 'Tên nhà cung cấp không được để trống.';

    } else {

        $sql = "
            UPDATE suppliers
            SET
                SupplierName = ?,
                ContactName = ?,
                Address = ?,
                City = ?,
                PostalCode = ?,
                Country = ?,
                Phone = ?
            WHERE SupplierID = ?
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            'sssssssi',
            $supplierName,
            $contactName,
            $address,
            $city,
            $postalCode,
            $country,
            $phone,
            $supplierID
        );

        if ($stmt->execute()) {

            $stmt->close();

            header('Location: /suppliers/');
            exit;

        } else {

            $error = 'Không thể cập nhật nhà cung cấp.';
        }

        $stmt->close();
    }
}

require_once '/var/www/src/includes/header.php';
require_once '/var/www/src/includes/navbar.php';

?>

<div class="container mt-4">

    <h2 class="mb-4">Sửa nhà cung cấp</h2>

    <?php if ($error !== ''): ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form method="post">

        <div class="mb-3">

            <label class="form-label">
                Mã nhà cung cấp
            </label>

            <input
                type="text"
                class="form-control"
                value="<?= (int)$supplier['SupplierID'] ?>"
                disabled
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                Tên nhà cung cấp
            </label>

            <input
                type="text"
                class="form-control"
                name="supplier_name"
                value="<?= htmlspecialchars(
                    $_POST['supplier_name']
                    ?? $supplier['SupplierName']
                ) ?>"
                required
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                Người liên hệ
            </label>

            <input
                type="text"
                class="form-control"
                name="contact_name"
                value="<?= htmlspecialchars(
                    $_POST['contact_name']
                    ?? $supplier['ContactName']
                    ?? ''
                ) ?>"
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                Địa chỉ
            </label>

            <input
                type="text"
                class="form-control"
                name="address"
                value="<?= htmlspecialchars(
                    $_POST['address']
                    ?? $supplier['Address']
                    ?? ''
                ) ?>"
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                Thành phố
            </label>

            <input
                type="text"
                class="form-control"
                name="city"
                value="<?= htmlspecialchars(
                    $_POST['city']
                    ?? $supplier['City']
                    ?? ''
                ) ?>"
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                Mã bưu chính
            </label>

            <input
                type="text"
                class="form-control"
                name="postal_code"
                value="<?= htmlspecialchars(
                    $_POST['postal_code']
                    ?? $supplier['PostalCode']
                    ?? ''
                ) ?>"
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                Quốc gia
            </label>

            <input
                type="text"
                class="form-control"
                name="country"
                value="<?= htmlspecialchars(
                    $_POST['country']
                    ?? $supplier['Country']
                    ?? 'Việt Nam'
                ) ?>"
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                Số điện thoại
            </label>

            <input
                type="text"
                class="form-control"
                name="phone"
                value="<?= htmlspecialchars(
                    $_POST['phone']
                    ?? $supplier['Phone']
                    ?? ''
                ) ?>"
            >

        </div>

        <button
            type="submit"
            class="btn btn-warning"
        >
            Cập nhật
        </button>

        <a
            href="/suppliers/"
            class="btn btn-secondary"
        >
            Hủy
        </a>

    </form>

</div>

<?php

require_once '/var/www/src/includes/footer.php';

$conn->close();

?>