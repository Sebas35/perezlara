<?php

use Core\FrontController;

require_once 'vendor/autoload.php';
require_once 'Helpers/routes.php';

$dotenv = Dotenv\Dotenv ::createImmutable(__DIR__);
$dotenv -> load();

(new FrontController($_GET['action'] ?? 'login')) -> run();
