<?php

require_once '/var/www/src/config/database.php';

$sql = "
    SELECT
        ShipperID,
        ShipperName,
        Phone
    FROM shippers
    ORDER BY ShipperID
";

$shippers = $conn->query($sql);

require_once '/var/www/src/includes/admin/header.php';
require_once '/var/www/src/includes/admin/navbar.php';

?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Shipper</h2>

        <a
            href="/admin/shippers/create.php"
            class="btn btn-primary"
        >
            Thêm shipper
        </a>
    </div>

    <?php if (
        isset($_GET['success'])
        && $_GET['success'] === 'created'
    ): ?>

        <div class="alert alert-success">
            Thêm shipper thành công.
        </div>

    <?php endif; ?>

    <?php if (
        isset($_GET['success'])
        && $_GET['success'] === 'updated'
    ): ?>

        <div class="alert alert-success">
            Cập nhật shipper thành công.
        </div>

    <?php endif; ?>

    <?php if (
        isset($_GET['success'])
        && $_GET['success'] === 'deleted'
    ): ?>

        <div class="alert alert-success">
            Xóa shipper thành công.
        </div>

    <?php endif; ?>

    <div class="table-responsive">

        <table class="table table-bordered table-striped">

            <thead class="table-dark">

                <tr>
                    <th>ID</th>
                    <th>Tên shipper</th>
                    <th>Số điện thoại</th>
                    <th>Thao tác</th>
                </tr>

            </thead>

            <tbody>

                <?php while (
                    $shipper = $shippers->fetch_assoc()
                ): ?>

                    <tr>

                        <td>
                            <?= (int) $shipper['ShipperID'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $shipper['ShipperName']
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $shipper['Phone'] ?? ''
                            ) ?>
                        </td>

                        <td>

                            <a
                                href="/admin/shippers/edit.php?id=<?= (int) $shipper['ShipperID'] ?>"
                                class="btn btn-warning btn-sm"
                            >
                                Sửa
                            </a>

                            <form
    action="/admin/shippers/delete.php"
    method="post"
    class="d-inline"
    onsubmit="return confirm('Bạn có chắc muốn xóa shipper này?');"
>
    <input
        type="hidden"
        name="id"
        value="<?= (int)$shipper['ShipperID'] ?>"
    >

    <button
        type="submit"
        class="btn btn-sm btn-danger"
    >
        Xóa
    </button>
</form>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>

<?php

require_once '/var/www/src/includes/admin/footer.php';