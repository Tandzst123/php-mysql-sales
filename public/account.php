<?php

require_once '/var/www/src/config/session.php';
require_once '/var/www/src/config/database.php';

if (!isset($_SESSION['customer_id'])) {
    header('Location: /login.php');
    exit;
}

$customerID = (int)$_SESSION['customer_id'];

$stmt = $conn->prepare(
    "SELECT
        CustomerID,
        CustomerName,
        ContactName,
        Address,
        City,
        PostalCode,
        Country,
        Phone,
        Email
     FROM customers
     WHERE CustomerID = ?
     LIMIT 1"
);

$stmt->bind_param('i', $customerID);
$stmt->execute();

$result = $stmt->get_result();
$customer = $result->fetch_assoc();

$stmt->close();

if (!$customer) {
    $_SESSION = [];
    session_destroy();

    header('Location: /login.php');
    exit;
}

$pageTitle = 'Tài khoản';

require_once '/var/www/src/includes/frontend/header.php';
require_once '/var/www/src/includes/frontend/navbar.php';

?>

<main class="container py-5">

    <h1 class="mb-4">
        Tài khoản khách hàng
    </h1>

    <div class="card">

        <div class="card-body">

            <div class="mb-3">
                <strong>Họ và tên:</strong>
                <?= htmlspecialchars($customer['CustomerName']) ?>
            </div>

            <div class="mb-3">
                <strong>Email:</strong>
                <?= htmlspecialchars($customer['Email']) ?>
            </div>

            <div class="mb-3">
                <strong>Số điện thoại:</strong>
                <?= htmlspecialchars($customer['Phone'] ?? '') ?>
            </div>

            <div class="mb-3">
                <strong>Địa chỉ:</strong>
                <?= htmlspecialchars($customer['Address'] ?? '') ?>
            </div>

            <div class="mb-3">
                <strong>Thành phố:</strong>
                <?= htmlspecialchars($customer['City'] ?? '') ?>
            </div>

            <div class="mb-3">
                <strong>Quốc gia:</strong>
                <?= htmlspecialchars($customer['Country'] ?? '') ?>
            </div>

        </div>

    </div>

</main>

<?php

require_once '/var/www/src/includes/frontend/footer.php';

$conn->close();