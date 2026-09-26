<?php

require_once '/var/www/src/config/session.php';
require_once '/var/www/src/config/database.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* Xóa toàn bộ giỏ hàng */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    header('Location: /cart.php');
    exit;
}

/* Xóa sản phẩm */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_product'])) {
    $productID = (int)($_POST['ProductID'] ?? 0);

    if ($productID > 0) {
        unset($_SESSION['cart'][$productID]);
    }

    header('Location: /cart.php');
    exit;
}

/* Cập nhật giỏ hàng */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {

    $quantities = $_POST['quantities'] ?? [];

    foreach ($quantities as $productID => $quantity) {

        $productID = (int)$productID;
        $quantity = (int)$quantity;

        if ($productID <= 0) {
            continue;
        }

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productID]);
            continue;
        }

        $stmt = $conn->prepare(
            "SELECT StockQuantity
             FROM products
             WHERE ProductID = ?
             AND IsActive = 1"
        );

        $stmt->bind_param('i', $productID);
        $stmt->execute();

        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        $stmt->close();

        if (!$product) {
            unset($_SESSION['cart'][$productID]);
            continue;
        }

        $stockQuantity = (int)$product['StockQuantity'];

        if ($stockQuantity <= 0) {
            unset($_SESSION['cart'][$productID]);
            continue;
        }

        if ($quantity > $stockQuantity) {
            $quantity = $stockQuantity;
        }

        $_SESSION['cart'][$productID] = $quantity;
    }

    header('Location: /cart.php');
    exit;
}

/* Lấy giỏ hàng */
$cart = $_SESSION['cart'];

$products = [];

if (!empty($cart)) {

    $productIDs = array_map(
        'intval',
        array_keys($cart)
    );

    $placeholders = implode(
        ',',
        array_fill(
            0,
            count($productIDs),
            '?'
        )
    );

    $types = str_repeat(
        'i',
        count($productIDs)
    );

    $sql = "
        SELECT
            ProductID,
            ProductCode,
            ProductName,
            Price,
            StockQuantity
        FROM products
        WHERE ProductID IN ($placeholders)
        AND IsActive = 1
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        $types,
        ...$productIDs
    );

    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    $stmt->close();
}

/* Tính tổng */
$total = 0;

foreach ($products as $product) {

    $productID = (int)$product['ProductID'];

    $quantity = (int)(
        $cart[$productID] ?? 0
    );

    $total +=
        (float)$product['Price']
        * $quantity;
}

$pageTitle = 'Giỏ hàng';

require_once '/var/www/src/includes/frontend/header.php';
require_once '/var/www/src/includes/frontend/navbar.php';
?>

<main class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1>Giỏ hàng</h1>

        <?php if (!empty($cart)): ?>

            <form method="POST" action="/cart.php">

                <button
                    type="submit"
                    name="clear_cart"
                    value="1"
                    class="btn btn-outline-danger"
                >
                    Xóa giỏ hàng
                </button>

            </form>

        <?php endif; ?>

    </div>


    <?php if (empty($products)): ?>

        <div class="alert alert-info">
            Giỏ hàng đang trống.
        </div>

        <a
            href="/products.php"
            class="btn btn-primary"
        >
            Tiếp tục mua hàng
        </a>

    <?php else: ?>

        <form method="POST" action="/cart.php">

            <div class="table-responsive">

                <table class="table table-bordered align-middle">

                    <thead class="table-light">

                        <tr>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Xóa</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($products as $product): ?>

                        <?php
                        $productID =
                            (int)$product['ProductID'];

                        $quantity =
                            (int)(
                                $cart[$productID] ?? 0
                            );

                        $subtotal =
                            (float)$product['Price']
                            * $quantity;
                        ?>

                        <tr>

                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $product['ProductName']
                                    ) ?>
                                </strong>

                                <div class="text-muted small">

                                    Mã:
                                    <?= htmlspecialchars(
                                        $product['ProductCode']
                                    ) ?>

                                </div>

                            </td>

                            <td>

                                <?= number_format(
                                    (float)$product['Price'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                                đ

                            </td>

                            <td>

                                <input
                                    type="number"
                                    name="quantities[<?= $productID ?>]"
                                    value="<?= $quantity ?>"
                                    min="1"
                                    max="<?= (int)$product['StockQuantity'] ?>"
                                    class="form-control"
                                    style="width:100px;"
                                >

                            </td>

                            <td>

                                <?= number_format(
                                    $subtotal,
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                                đ

                            </td>

                            <td>

                                <button
                                    type="submit"
                                    name="remove_product"
                                    value="1"
                                    class="btn btn-danger btn-sm"
                                    onclick="
                                        this.form.ProductID.value =
                                        '<?= $productID ?>';
                                    "
                                >
                                    Xóa
                                </button>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                    <tfoot>

                        <tr>

                            <th
                                colspan="3"
                                class="text-end"
                            >
                                Tổng cộng:
                            </th>

                            <th>

                                <?= number_format(
                                    $total,
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                                đ

                            </th>

                            <th></th>

                        </tr>

                    </tfoot>

                </table>

            </div>


            <input
                type="hidden"
                name="ProductID"
                value=""
            >


            <div class="d-flex justify-content-between">

                <a
                    href="/products.php"
                    class="btn btn-secondary"
                >
                    Tiếp tục mua hàng
                </a>

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        name="update_cart"
                        value="1"
                        class="btn btn-primary"
                    >
                        Cập nhật giỏ hàng
                    </button>

                    <a
                        href="/checkout.php"
                        class="btn btn-success"
                    >
                        Thanh toán
                    </a>

                </div>

            </div>

        </form>

    <?php endif; ?>

</main>

<?php
require_once '/var/www/src/includes/frontend/footer.php';