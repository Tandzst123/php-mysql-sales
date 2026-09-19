<?php

$pageTitle = 'Sửa nhân viên';

require_once '/var/www/src/config/database.php';

$error = '';

$employeeID = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($employeeID <= 0) {
    die('Mã nhân viên không hợp lệ.');
}

/* Lấy dữ liệu hiện tại */
$sql = "
    SELECT
        EmployeeID,
        LastName,
        FirstName,
        BirthDate,
        Photo,
        Notes
    FROM employees
    WHERE EmployeeID = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $employeeID);
$stmt->execute();

$result = $stmt->get_result();
$employee = $result->fetch_assoc();

$stmt->close();

if (!$employee) {
    die('Không tìm thấy nhân viên.');
}

/* Xử lý cập nhật */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $lastName = trim($_POST['last_name'] ?? '');
    $firstName = trim($_POST['first_name'] ?? '');
    $birthDate = $_POST['birth_date'] ?? '';
    $photo = trim($_POST['photo'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if ($lastName === '' || $firstName === '') {

        $error = 'Họ và tên không được để trống.';

    } else {

        $sql = "
            UPDATE employees
            SET
                LastName = ?,
                FirstName = ?,
                BirthDate = NULLIF(?, ''),
                Photo = ?,
                Notes = ?
            WHERE EmployeeID = ?
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            'sssssi',
            $lastName,
            $firstName,
            $birthDate,
            $photo,
            $notes,
            $employeeID
        );

        if ($stmt->execute()) {

            $stmt->close();

            header('Location: /employees/');
            exit;

        } else {

            $error = 'Không thể cập nhật nhân viên.';
            $stmt->close();
        }
    }
}

require_once '/var/www/src/includes/header.php';
require_once '/var/www/src/includes/navbar.php';

?>

<div class="container mt-4">

    <h2 class="mb-4">Sửa nhân viên</h2>

    <?php if ($error !== ''): ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form method="post">

        <div class="mb-3">

            <label class="form-label">
                Mã nhân viên
            </label>

            <input
                type="text"
                class="form-control"
                value="<?= (int)$employee['EmployeeID'] ?>"
                disabled
            >

        </div>

        <div class="mb-3">

            <label
                for="lastName"
                class="form-label"
            >
                Họ
            </label>

            <input
                type="text"
                class="form-control"
                id="lastName"
                name="last_name"
                value="<?= htmlspecialchars(
                    $_POST['last_name']
                    ?? $employee['LastName']
                ) ?>"
                required
            >

        </div>

        <div class="mb-3">

            <label
                for="firstName"
                class="form-label"
            >
                Tên
            </label>

            <input
                type="text"
                class="form-control"
                id="firstName"
                name="first_name"
                value="<?= htmlspecialchars(
                    $_POST['first_name']
                    ?? $employee['FirstName']
                ) ?>"
                required
            >

        </div>

        <div class="mb-3">

            <label
                for="birthDate"
                class="form-label"
            >
                Ngày sinh
            </label>

            <input
                type="date"
                class="form-control"
                id="birthDate"
                name="birth_date"
                value="<?= htmlspecialchars(
                    $_POST['birth_date']
                    ?? $employee['BirthDate']
                    ?? ''
                ) ?>"
            >

        </div>

        <div class="mb-3">

            <label
                for="photo"
                class="form-label"
            >
                Hình ảnh
            </label>

            <input
                type="text"
                class="form-control"
                id="photo"
                name="photo"
                placeholder="Tên file hình ảnh"
                value="<?= htmlspecialchars(
                    $_POST['photo']
                    ?? $employee['Photo']
                    ?? ''
                ) ?>"
            >

        </div>

        <div class="mb-3">

            <label
                for="notes"
                class="form-label"
            >
                Ghi chú
            </label>

            <textarea
                class="form-control"
                id="notes"
                name="notes"
                rows="4"
            ><?= htmlspecialchars(
                $_POST['notes']
                ?? $employee['Notes']
                ?? ''
            ) ?></textarea>

        </div>

        <button
            type="submit"
            class="btn btn-warning"
        >
            Cập nhật
        </button>

        <a
            href="/employees/"
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