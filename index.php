<?php

use Core\FrontController;

require_once 'vendor/autoload.php';
require_once 'Helpers/routes.php';
print_r($_SERVER);
(new FrontController($_GET['action'] ?? 'login')) -> run();
