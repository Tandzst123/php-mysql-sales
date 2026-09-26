<?php

$pageTitle = 'Trang quản trị';

require_once '/var/www/src/includes/admin/header.php';
require_once '/var/www/src/includes/admin/navbar.php';

?>

<div class="container mt-4">

    <div class="mb-4">
        <h2>Trang quản trị</h2>
        <p class="text-muted">
            Quản lý hệ thống bán hàng phụ kiện điện thoại.
        </p>
    </div>

    <div class="row g-4">

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Danh mục</h5>
                    <p class="card-text">
                        Quản lý các danh mục sản phẩm.
                    </p>
                    <a
                        href="/admin/categories/"
                        class="btn btn-primary"
                    >
                        Quản lý
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Sản phẩm</h5>
                    <p class="card-text">
                        Quản lý sản phẩm và hình ảnh sản phẩm.
                    </p>
                    <a
                        href="/admin/products/"
                        class="btn btn-primary"
                    >
                        Quản lý
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Khách hàng</h5>
                    <p class="card-text">
                        Quản lý thông tin khách hàng.
                    </p>
                    <a
                        href="/admin/customers/"
                        class="btn btn-primary"
                    >
                        Quản lý
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Nhà cung cấp</h5>
                    <p class="card-text">
                        Quản lý thông tin nhà cung cấp.
                    </p>
                    <a
                        href="/admin/suppliers/"
                        class="btn btn-primary"
                    >
                        Quản lý
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Nhân viên</h5>
                    <p class="card-text">
                        Quản lý thông tin nhân viên.
                    </p>
                    <a
                        href="/admin/employees/"
                        class="btn btn-primary"
                    >
                        Quản lý
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Shipper</h5>
                    <p class="card-text">
                        Quản lý đơn vị vận chuyển.
                    </p>
                    <a
                        href="/admin/shippers/"
                        class="btn btn-primary"
                    >
                        Quản lý
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>

<?php

require_once '/var/www/src/includes/admin/footer.php';

?>