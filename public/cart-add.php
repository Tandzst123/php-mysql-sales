<?php

require_once '/var/www/src/config/session.php';
require_once '/var/www/src/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /products.php');
    exit;
}

$productID = isset($_POST['ProductID'])
    ? (int) $_POST['ProductID']
    : 0;

$quantity = isset($_POST['quantity'])
    ? (int) $_POST['quantity']
    : 1;


if ($productID <= 0) {
    header('Location: /products.php');
    exit;
}

if ($quantity < 1) {
    $quantity = 1;
}

$sql = "
    SELECT
        ProductID,
        ProductCode,
        ProductName,
        Price,
        StockQuantity
    FROM products
    WHERE ProductID = ?
      AND IsActive = 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $productID);
$stmt->execute();

$result = $stmt->get_result();
$product = $result->fetch_assoc();

$stmt->close();

if (!$product) {
    header('Location: /products.php');
    exit;
}

$stockQuantity = (int) $product['StockQuantity'];

if ($stockQuantity <= 0) {
    header('Location: /product-detail.php?id=' . $productID);
    exit;
}

if ($quantity > $stockQuantity) {
    $quantity = $stockQuantity;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$productID])) {

    $_SESSION['cart'][$productID]['quantity'] += $quantity;

    if (
        $_SESSION['cart'][$productID]['quantity']
        > $stockQuantity
    ) {
        $_SESSION['cart'][$productID]['quantity']
            = $stockQuantity;
    }

} else {

    $_SESSION['cart'][$productID] = [
        'ProductID' => (int) $product['ProductID'],
        'ProductCode' => $product['ProductCode'],
        'ProductName' => $product['ProductName'],
        'Price' => (float) $product['Price'],
        'quantity' => $quantity
    ];
}

header('Location: /cart.php');
exit;