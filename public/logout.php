<?php

require_once '/var/www/src/config/session.php';

unset($_SESSION['customer_id']);

header('Location: /');
exit;