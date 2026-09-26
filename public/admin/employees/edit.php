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

    $fullName = trim($_POST['full_name'] ?? '');
    $birthDate = $_POST['birth_date'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    if ($fullName === '') {

        $error = 'Họ và tên không được để trống.';

    } else {

        /*
         * Database hiện tại có 2 cột LastName và FirstName.
         * Ta lưu toàn bộ họ tên vào LastName,
         * còn FirstName để trống.
         */
        $lastName = $fullName;
        $firstName = '';

        /* Giữ lại ảnh cũ */
        $photo = $employee['Photo'];

        /* Nếu chọn ảnh mới */
        if (
            isset($_FILES['photo']) &&
            $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {

                $error = 'Không thể tải hình ảnh lên.';

            } else {

                $allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'image/webp'
                ];

                $fileType = mime_content_type(
                    $_FILES['photo']['tmp_name']
                );

                if (!in_array($fileType, $allowedTypes)) {

                    $error = 'Chỉ được chọn ảnh JPG, PNG, GIF hoặc WEBP.';

                } else {

                    $uploadDir = '/var/www/html/uploads/employees/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $extension = strtolower(
                        pathinfo(
                            $_FILES['photo']['name'],
                            PATHINFO_EXTENSION
                        )
                    );

                    $fileName = 'employee_' .
                        $employeeID . '_' .
                        time() . '.' .
                        $extension;

                    $uploadPath = $uploadDir . $fileName;

                    if (
                        move_uploaded_file(
                            $_FILES['photo']['tmp_name'],
                            $uploadPath
                        )
                    ) {
                        $photo = $fileName;
                    } else {
                        $error = 'Không thể lưu hình ảnh.';
                    }
                }
            }
        }

        /* Cập nhật database */
        if ($error === '') {

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

                header('Location: /admin/employees/');
                exit;

            } else {

                $error = 'Không thể cập nhật nhân viên: ' . $stmt->error;

                $stmt->close();
            }
        }
    }
}
require_once '/var/www/src/includes/admin/header.php';
require_once '/var/www/src/includes/admin/navbar.php';

?>

<div class="container mt-4">

    <h2 class="mb-4">Sửa nhân viên</h2>

    <?php if ($error !== ''): ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">

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
        for="fullName"
        class="form-label"
    >
        Họ và tên
    </label>

    <input
        type="text"
        class="form-control"
        id="fullName"
        name="full_name"
        value="<?= htmlspecialchars(
            $_POST['full_name']
            ?? trim(
                ($employee['LastName'] ?? '') . ' ' .
                ($employee['FirstName'] ?? '')
            )
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
                type="file"
                class="form-control"
                id="photo"
                name="photo"
                accept="image/jpeg,image/png,image/gif,image/webp"
            >

            <?php if (!empty($employee['Photo'])): ?>

                <div class="mt-2">

                    <small class="text-muted">
                        Ảnh hiện tại:
                    </small>

                    <br>

                    <img
                        src="/uploads/employees/<?= htmlspecialchars($employee['Photo']) ?>"
                        alt="Ảnh nhân viên"
                        style="
                            width: 100px;
                            height: 100px;
                            object-fit: cover;
                            border-radius: 8px;
                            margin-top: 5px;
                        "
                    >

                </div>

            <?php endif; ?>

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
            href="/admin/employees/"
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