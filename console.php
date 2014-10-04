<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/System/Application.php';

$container = require_once __DIR__ . '/config/container.php';

$app = new System\Application();

System\Application::setContainer($container);
System\Command::setContainer($container);

$app->execute($argv);