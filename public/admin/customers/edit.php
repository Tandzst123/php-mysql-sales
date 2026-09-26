<?php

$pageTitle = 'Sửa khách hàng';

require_once '/var/www/src/config/database.php';

$error = '';

$customerID = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($customerID <= 0) {
    die('Mã khách hàng không hợp lệ.');
}

/* Lấy dữ liệu hiện tại */
$sql = "
    SELECT
        CustomerID,
        CustomerName,
        ContactName,
        Address,
        City,
        PostalCode,
        Country
    FROM customers
    WHERE CustomerID = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $customerID);
$stmt->execute();

$result = $stmt->get_result();
$customer = $result->fetch_assoc();

$stmt->close();

if (!$customer) {
    die('Không tìm thấy khách hàng.');
}

/* Xử lý cập nhật */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $customerName = trim($_POST['customer_name'] ?? '');
    $contactName  = trim($_POST['contact_name'] ?? '');
    $address      = trim($_POST['address'] ?? '');
    $city         = trim($_POST['city'] ?? '');
    $postalCode   = trim($_POST['postal_code'] ?? '');
    $country      = trim($_POST['country'] ?? '');

    if ($customerName === '') {

        $error = 'Tên khách hàng không được để trống.';

    } else {

        $sql = "
            UPDATE customers
            SET
                CustomerName = ?,
                ContactName = ?,
                Address = ?,
                City = ?,
                PostalCode = ?,
                Country = ?
            WHERE CustomerID = ?
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            'ssssssi',
            $customerName,
            $contactName,
            $address,
            $city,
            $postalCode,
            $country,
            $customerID
        );

        if ($stmt->execute()) {

            $stmt->close();

            header('Location: /admin/customers/');
            exit;

        } else {

            $error = 'Không thể cập nhật khách hàng.';
        }

        $stmt->close();
    }
}

require_once '/var/www/src/includes/admin/header.php';
require_once '/var/www/src/includes/admin/navbar.php';
?>

<div class="container mt-4">

    <h2 class="mb-4">Sửa khách hàng</h2>

    <?php if ($error !== ''): ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form method="post">

        <div class="mb-3">

            <label class="form-label">
                Mã khách hàng
            </label>

            <input
                type="text"
                class="form-control"
                value="<?= (int)$customer['CustomerID'] ?>"
                disabled
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                Tên khách hàng
            </label>

            <input
                type="text"
                class="form-control"
                name="customer_name"
                value="<?= htmlspecialchars(
                    $_POST['customer_name']
                    ?? $customer['CustomerName']
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
                    ?? $customer['ContactName']
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
                    ?? $customer['Address']
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
                    ?? $customer['City']
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
                    ?? $customer['PostalCode']
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
                    ?? $customer['Country']
                    ?? 'Việt Nam'
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
            href="/admin/customers/"
            class="btn btn-secondary"
        >
            Hủy
        </a>

    </form>

</div>

<?php

require_once '/var/www/src/includes/admin/footer.php';

$conn->close();

?>