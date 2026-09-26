<?php

require_once '/var/www/src/config/session.php';

$_SESSION['cart'] = [];

header('Location: /cart.php');
exit;