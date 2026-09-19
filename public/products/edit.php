<?php

$pageTitle = 'Sửa sản phẩm';

require_once '/var/www/src/config/database.php';

$error = '';

$productID = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($productID <= 0) {
    header('Location: /products/');
    exit;
}

/*
 * Lấy sản phẩm hiện tại
 */
$sqlProduct = "
    SELECT
        ProductID,
        ProductCode,
        ProductName,
        Description,
        Unit,
        Price,
        StockQuantity,
        IsActive,
        SupplierID,
        CategoryID
    FROM products
    WHERE ProductID = ?
";

$stmtProduct = $conn->prepare($sqlProduct);
$stmtProduct->bind_param('i', $productID);
$stmtProduct->execute();

$resultProduct = $stmtProduct->get_result();
$product = $resultProduct->fetch_assoc();

$stmtProduct->close();

if (!$product) {
    header('Location: /products/');
    exit;
}

/*
 * Lấy danh sách Category
 */
$sqlCategories = "
    SELECT
        CategoryID,
        CategoryName
    FROM categories
    ORDER BY CategoryName
";

$categories = $conn->query($sqlCategories);

/*
 * Lấy danh sách Supplier
 */
$sqlSuppliers = "
    SELECT
        SupplierID,
        SupplierName
    FROM suppliers
    ORDER BY SupplierName
";

$suppliers = $conn->query($sqlSuppliers);

/*
 * Xử lý cập nhật
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $productCode = trim($_POST['product_code'] ?? '');
    $productName = trim($_POST['product_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $unit = trim($_POST['unit'] ?? '');

    $price = (float) ($_POST['price'] ?? 0);
    $stockQuantity = (int) ($_POST['stock_quantity'] ?? 0);

    $selectedCategoryID = (int) ($_POST['category_id'] ?? 0);
    $selectedSupplierID = (int) ($_POST['supplier_id'] ?? 0);

    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if ($productCode === '') {

        $error = 'Mã sản phẩm không được để trống.';

    } elseif ($productName === '') {

        $error = 'Tên sản phẩm không được để trống.';

    } elseif ($price < 0) {

        $error = 'Giá sản phẩm không hợp lệ.';

    } elseif ($stockQuantity < 0) {

        $error = 'Số lượng tồn kho không hợp lệ.';

    } elseif ($selectedCategoryID <= 0) {

        $error = 'Vui lòng chọn danh mục.';

    } elseif ($selectedSupplierID <= 0) {

        $error = 'Vui lòng chọn nhà cung cấp.';

    } else {

        $sql = "
            UPDATE products
            SET
                ProductCode = ?,
                ProductName = ?,
                Description = ?,
                Unit = ?,
                Price = ?,
                StockQuantity = ?,
                IsActive = ?,
                SupplierID = ?,
                CategoryID = ?
            WHERE ProductID = ?
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            'ssssdiiiii',
            $productCode,
            $productName,
            $description,
            $unit,
            $price,
            $stockQuantity,
            $isActive,
            $selectedSupplierID,
            $selectedCategoryID,
            $productID
        );

        if ($stmt->execute()) {

            $stmt->close();

            header('Location: /products/');
            exit;
        }

        $error = 'Không thể cập nhật sản phẩm.';

        $stmt->close();
    }

    /*
     * Nếu có lỗi thì giữ lại dữ liệu người dùng vừa nhập
     */
    $product['ProductCode'] = $productCode;
    $product['ProductName'] = $productName;
    $product['Description'] = $description;
    $product['Unit'] = $unit;
    $product['Price'] = $price;
    $product['StockQuantity'] = $stockQuantity;
    $product['IsActive'] = $isActive;
    $product['CategoryID'] = $selectedCategoryID;
    $product['SupplierID'] = $selectedSupplierID;
}

require_once '/var/www/src/includes/header.php';
require_once '/var/www/src/includes/navbar.php';

?>

<div class="container mt-4">

    <h2 class="mb-4">Sửa sản phẩm</h2>

    <?php if ($error !== ''): ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form method="post">

        <div class="mb-3">
            <label class="form-label">
                Mã sản phẩm
            </label>

            <input
                type="text"
                name="product_code"
                class="form-control"
                value="<?= htmlspecialchars($product['ProductCode']) ?>"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">
                Tên sản phẩm
            </label>

            <input
                type="text"
                name="product_name"
                class="form-control"
                value="<?= htmlspecialchars($product['ProductName']) ?>"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">
                Mô tả
            </label>

            <textarea
                name="description"
                class="form-control"
                rows="4"
            ><?= htmlspecialchars($product['Description'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">
                Đơn vị
            </label>

            <input
                type="text"
                name="unit"
                class="form-control"
                value="<?= htmlspecialchars($product['Unit'] ?? '') ?>"
            >
        </div>

        <div class="mb-3">
            <label class="form-label">
                Giá
            </label>

            <input
                type="number"
                name="price"
                class="form-control"
                value="<?= htmlspecialchars($product['Price']) ?>"
                min="0"
                step="0.01"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">
                Tồn kho
            </label>

            <input
                type="number"
                name="stock_quantity"
                class="form-control"
                value="<?= htmlspecialchars($product['StockQuantity']) ?>"
                min="0"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">
                Danh mục
            </label>

            <select
                name="category_id"
                class="form-select"
                required
            >

                <option value="">
                    -- Chọn danh mục --
                </option>

                <?php while ($category = $categories->fetch_assoc()): ?>

                    <option
                        value="<?= $category['CategoryID'] ?>"
                        <?= (
                            (int) $category['CategoryID']
                            === (int) $product['CategoryID']
                        ) ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($category['CategoryName']) ?>
                    </option>

                <?php endwhile; ?>

            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">
                Nhà cung cấp
            </label>

            <select
                name="supplier_id"
                class="form-select"
                required
            >

                <option value="">
                    -- Chọn nhà cung cấp --
                </option>

                <?php while ($supplier = $suppliers->fetch_assoc()): ?>

                    <option
                        value="<?= $supplier['SupplierID'] ?>"
                        <?= (
                            (int) $supplier['SupplierID']
                            === (int) $product['SupplierID']
                        ) ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($supplier['SupplierName']) ?>
                    </option>

                <?php endwhile; ?>

            </select>
        </div>

        <div class="form-check mb-3">

            <input
                type="checkbox"
                name="is_active"
                class="form-check-input"
                id="is_active"
                <?= ((int) $product['IsActive'] === 1)
                    ? 'checked'
                    : '' ?>
            >

            <label
                class="form-check-label"
                for="is_active"
            >
                Đang kinh doanh
            </label>

        </div>

        <button
            type="submit"
            class="btn btn-primary"
        >
            Lưu
        </button>

        <a
            href="/products/"
            class="btn btn-secondary"
        >
            Hủy
        </a>

    </form>

</div>

<?php

require_once '/var/www/src/includes/footer.php';

$conn->close();