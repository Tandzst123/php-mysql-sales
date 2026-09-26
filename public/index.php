<?php

require_once '/var/www/src/config/session.php';

$appName = "Hệ thống quản lý bán hàng";

$pageTitle = $appName;

require_once '/var/www/src/includes/frontend/header.php';
require_once '/var/www/src/includes/frontend/navbar.php';

?>

<main class="container py-5">

    <h1><?= htmlspecialchars($appName) ?></h1>

    <p>Ứng dụng PHP đang hoạt động.</p>

</main>

<?php

require_once '/var/www/src/includes/frontend/footer.php';