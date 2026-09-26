<?php

$pageTitle = 'Thêm nhân viên';

require_once '/var/www/src/config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullName = trim($_POST['full_name'] ?? '');
    $birthDate = $_POST['birth_date'] ?? null;
    $notes = trim($_POST['notes'] ?? '');

    if ($fullName === '') {

        $error = 'Họ và tên không được để trống.';

    } else {

        /*
         * Tách họ tên thành Họ và Tên
         * để phù hợp với cấu trúc bảng employees.
         */
        $parts = preg_split('/\s+/', $fullName);

        $firstName = array_pop($parts);
        $lastName = implode(' ', $parts);

        if ($lastName === '') {
            $lastName = $firstName;
            $firstName = '';
        }

        $photo = '';

        /*
         * Xử lý upload hình ảnh
         */
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {

            if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {

                $allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'image/webp'
                ];

                $fileType = mime_content_type($_FILES['photo']['tmp_name']);

                if (!in_array($fileType, $allowedTypes, true)) {

                    $error = 'Chỉ được chọn file hình ảnh JPG, PNG, GIF hoặc WEBP.';

                } else {

                    $extension = pathinfo(
                        $_FILES['photo']['name'],
                        PATHINFO_EXTENSION
                    );

                    $photo = uniqid('employee_', true) . '.' . $extension;

                    $uploadDir = '/var/www/html/uploads/employees/';
                    $uploadPath = $uploadDir . $photo;

                    if (!move_uploaded_file(
                        $_FILES['photo']['tmp_name'],
                        $uploadPath
                    )) {
                        $error = 'Không thể tải hình ảnh lên.';
                    }
                }

            } else {

                $error = 'Có lỗi khi tải hình ảnh.';
            }
        }

        if ($error === '') {

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

            if ($birthDate === '') {
    $birthDate = null;
}

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

    <form
        method="post"
        enctype="multipart/form-data"
    >

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
                value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
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
                value="<?= htmlspecialchars($_POST['birth_date'] ?? '') ?>"
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
                rows="3"
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