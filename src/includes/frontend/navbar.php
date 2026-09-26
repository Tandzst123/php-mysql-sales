<?php

$cartCount = array_sum(
    $_SESSION['cart'] ?? []
);

$isLoggedIn = isset($_SESSION['customer_id']);

?>

<nav class="navbar navbar-expand-lg bg-dark navbar-dark">

    <div class="container">

        <a
            class="navbar-brand"
            href="/"
        >
            Sales Management
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div
            class="collapse navbar-collapse"
            id="mainNavbar"
        >

            <ul class="navbar-nav">

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="/"
                    >
                        Trang chủ
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="/products.php"
                    >
                        Sản phẩm
                    </a>
                </li>

            </ul>

            <div class="ms-auto d-flex gap-2 align-items-center">

                <?php if ($isLoggedIn): ?>

                    <span class="text-white">
                        Xin chào,
                        <?= htmlspecialchars($_SESSION['customer_name']) ?>
                    </span>

                    <a
                        class="btn btn-outline-light btn-sm"
                        href="/logout.php"
                    >
                        Đăng xuất
                    </a>

                <?php else: ?>

                    <a
                        class="btn btn-outline-light btn-sm"
                        href="/login.php"
                    >
                        Đăng nhập
                    </a>

                    <a
                        class="btn btn-outline-light btn-sm"
                        href="/register.php"
                    >
                        Đăng ký
                    </a>

                <?php endif; ?>

                <a
                    class="btn btn-outline-light btn-sm"
                    href="/cart.php"
                >
                    Giỏ hàng (<?= (int) $cartCount ?>)
                </a>

                <a
                    class="btn btn-outline-light btn-sm"
                    href="/admin/"
                >
                    Quản trị
                </a>

            </div>

        </div>

    </div>

</nav>