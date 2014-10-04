<?php

use Pimple\Container;

$config = require_once('config.php');

$container = new Container();

$container['config'] = function () use ($config) {
    return $config;
};

$container['rabbit'] = function ($c) {
    return new \PhpAmqpLib\Connection\AMQPConnection(
        $c['config']['rabbitmq']['host'],
        $c['config']['rabbitmq']['port'],
        $c['config']['rabbitmq']['user'],
        $c['config']['rabbitmq']['password']
    );
};

return $container;
