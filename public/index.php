<?php
use Slim\App;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../inc/settings.php';

$app = new App(["settings" => $config]);

require_once __DIR__ . '/../inc/dependencies.php';
require_once __DIR__ . '/../inc/routes.php';

$app->run();
