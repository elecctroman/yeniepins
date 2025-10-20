<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Bootstrap.php';

use Core\Bootstrap;
use Core\Session;

Session::start();

$bootstrap = new Bootstrap();
$bootstrap->run();
