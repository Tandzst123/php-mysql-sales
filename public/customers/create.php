<?php

$pageTitle = 'Thêm khách hàng';

require_once '/var/www/src/config/database.php';

$error = '';

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
            INSERT INTO customers
            (
                CustomerName,
                ContactName,
                Address,
                City,
                PostalCode,
                Country
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            'ssssss',
            $customerName,
            $contactName,
            $address,
            $city,
            $postalCode,
            $country
        );

        if ($stmt->execute()) {

            $stmt->close();

            header('Location: /customers/');
            exit;

        } else {

            $error = 'Không thể thêm khách hàng.';
        }

        $stmt->close();
    }
}

require_once '/var/www/src/includes/header.php';
require_once '/var/www/src/includes/navbar.php';

?>

<div class="container mt-4">

    <h2 class="mb-4">Thêm khách hàng</h2>

    <?php if ($error !== ''): ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form method="post">

        <div class="mb-3">

            <label
                for="customerName"
                class="form-label"
            >
                Tên khách hàng
            </label>

            <input
                type="text"
                class="form-control"
                id="customerName"
                name="customer_name"
                value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>"
                required
            >

        </div>

        <div class="mb-3">

            <label
                for="contactName"
                class="form-label"
            >
                Người liên hệ
            </label>

            <input
                type="text"
                class="form-control"
                id="contactName"
                name="contact_name"
                value="<?= htmlspecialchars($_POST['contact_name'] ?? '') ?>"
            >

        </div>

        <div class="mb-3">

            <label
                for="address"
                class="form-label"
            >
                Địa chỉ
            </label>

            <input
                type="text"
                class="form-control"
                id="address"
                name="address"
                value="<?= htmlspecialchars($_POST['address'] ?? '') ?>"
            >

        </div>

        <div class="mb-3">

            <label
                for="city"
                class="form-label"
            >
                Thành phố
            </label>

            <input
                type="text"
                class="form-control"
                id="city"
                name="city"
                value="<?= htmlspecialchars($_POST['city'] ?? '') ?>"
            >

        </div>

        <div class="mb-3">

            <label
                for="postalCode"
                class="form-label"
            >
                Mã bưu chính
            </label>

            <input
                type="text"
                class="form-control"
                id="postalCode"
                name="postal_code"
                value="<?= htmlspecialchars($_POST['postal_code'] ?? '') ?>"
            >

        </div>

        <div class="mb-3">

            <label
                for="country"
                class="form-label"
            >
                Quốc gia
            </label>

            <input
                type="text"
                class="form-control"
                id="country"
                name="country"
                value="<?= htmlspecialchars($_POST['country'] ?? 'Việt Nam') ?>"
            >

        </div>

        <button
            type="submit"
            class="btn btn-primary"
        >
            Lưu
        </button>

        <a
            href="/customers/"
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