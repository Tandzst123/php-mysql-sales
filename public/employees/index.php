<?php

$pageTitle = 'Quản lý nhân viên';

require_once '/var/www/src/config/database.php';

$sql = "
    SELECT
        EmployeeID,
        LastName,
        FirstName,
        BirthDate,
        Photo,
        Notes
    FROM employees
    ORDER BY EmployeeID
";

$result = $conn->query($sql);

require_once '/var/www/src/includes/header.php';
require_once '/var/www/src/includes/navbar.php';

?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h2>Quản lý nhân viên</h2>

        <a href="/employees/create.php" class="btn btn-primary">
            Thêm nhân viên
        </a>

    </div>

    <div class="table-responsive">

        <table class="table table-bordered table-striped">

            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Họ và tên</th>
                    <th>Ngày sinh</th>
                    <th>Hình ảnh</th>
                    <th>Ghi chú</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>

            <?php while ($employee = $result->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?= (int)$employee['EmployeeID'] ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            trim(
                                $employee['LastName'] . ' ' .
                                $employee['FirstName']
                            )
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $employee['BirthDate'] ?? ''
                        ) ?>
                    </td>

                    <td>

                        <?php if (!empty($employee['Photo'])): ?>

                            <img
                                src="/uploads/employees/<?= htmlspecialchars($employee['Photo']) ?>"
                                alt="Ảnh nhân viên"
                                style="
                                    width: 70px;
                                    height: 70px;
                                    object-fit: cover;
                                    border-radius: 5px;
                                "
                            >

                        <?php else: ?>

                            Chưa có ảnh

                        <?php endif; ?>

                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $employee['Notes'] ?? ''
                        ) ?>
                    </td>

                    <td>

                        <a
                            href="/employees/edit.php?id=<?= (int)$employee['EmployeeID'] ?>"
                            class="btn btn-sm btn-warning"
                        >
                            Sửa
                        </a>

                        <form
                            action="/employees/delete.php"
                            method="post"
                            class="d-inline"
                            onsubmit="return confirm('Bạn có chắc muốn xóa nhân viên này?');"
                        >

                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int)$employee['EmployeeID'] ?>"
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

require_once '/var/www/src/includes/footer.php';

$conn->close();

?>