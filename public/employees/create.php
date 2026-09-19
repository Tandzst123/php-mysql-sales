<?php

$pageTitle = 'Thêm nhân viên';

require_once '/var/www/src/config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $lastName = trim($_POST['last_name'] ?? '');
    $firstName = trim($_POST['first_name'] ?? '');
    $birthDate = trim($_POST['birth_date'] ?? '');
    $photo = trim($_POST['photo'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if ($lastName === '' || $firstName === '') {

        $error = 'Họ và tên nhân viên không được để trống.';

    } else {

        $sql = "
            INSERT INTO employees
            (
                LastName,
                FirstName,
                BirthDate,
                Photo,
                Notes
            )
            VALUES (?, ?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            'sssss',
            $lastName,
            $firstName,
            $birthDate,
            $photo,
            $notes
        );

        if ($stmt->execute()) {

            $stmt->close();

            header('Location: /employees/');
            exit;

        } else {

            $error = 'Không thể thêm nhân viên.';
        }

        $stmt->close();
    }
}

require_once '/var/www/src/includes/header.php';
require_once '/var/www/src/includes/navbar.php';

?>

<div class="container mt-4">

    <h2 class="mb-4">Thêm nhân viên</h2>

    <?php if ($error !== ''): ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form method="post">

        <div class="mb-3">

            <label class="form-label">
                Họ
            </label>

            <input
                type="text"
                name="last_name"
                class="form-control"
                value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>"
                required
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                Tên
            </label>

            <input
                type="text"
                name="first_name"
                class="form-control"
                value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>"
                required
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                Ngày sinh
            </label>

            <input
                type="date"
                name="birth_date"
                class="form-control"
                value="<?= htmlspecialchars($_POST['birth_date'] ?? '') ?>"
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                Hình ảnh
            </label>

            <input
                type="text"
                name="photo"
                class="form-control"
                value="<?= htmlspecialchars($_POST['photo'] ?? '') ?>"
                placeholder="Tên file hình ảnh"
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                Ghi chú
            </label>

            <textarea
                name="notes"
                class="form-control"
                rows="4"
            ><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>

        </div>

        <button
            type="submit"
            class="btn btn-primary"
        >
            Lưu
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