<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Bootstrap.php';
require_once __DIR__ . '/../core/Session.php';

use Core\Bootstrap;
use Core\Session;

Session::start();

$bootstrap = new Bootstrap();
$bootstrap->run();
