<?php

require_once '/var/www/src/config/session.php';
require_once '/var/www/src/config/database.php';

$pageTitle = 'Đăng nhập';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {

        $error = 'Vui lòng nhập đầy đủ email và mật khẩu.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'Email không hợp lệ.';

    } else {

        $stmt = $conn->prepare(
            "SELECT
                CustomerID,
                CustomerName,
                Email,
                PasswordHash
             FROM customers
             WHERE Email = ?
             LIMIT 1"
        );

        $stmt->bind_param('s', $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $customer = $result->fetch_assoc();

        $stmt->close();

        if (!$customer || !password_verify($password, $customer['PasswordHash'])) {

            $error = 'Email hoặc mật khẩu không đúng.';

        } else {

            $_SESSION['customer_id'] = (int)$customer['CustomerID'];
            $_SESSION['customer_name'] = $customer['CustomerName'];
            $_SESSION['customer_email'] = $customer['Email'];

            header('Location: /');
            exit;
        }
    }
}

require_once '/var/www/src/includes/frontend/header.php';
require_once '/var/www/src/includes/frontend/navbar.php';

?>

<main class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <h1 class="mb-4">
                Đăng nhập
            </h1>

            <?php if (isset($_GET['registered'])): ?>

                <div class="alert alert-success">
                    Đăng ký tài khoản thành công. Vui lòng đăng nhập.
                </div>

            <?php endif; ?>

            <?php if ($error !== ''): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>

            <form method="POST" action="/login.php">

                <div class="mb-3">

                    <label class="form-label">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Mật khẩu
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Đăng nhập
                </button>

            </form>

            <div class="mt-3">

                Chưa có tài khoản?

                <a href="/register.php">
                    Đăng ký
                </a>

            </div>

        </div>

    </div>

</main>

<?php

require_once '/var/www/src/includes/frontend/footer.php';

$conn->close();