<?php

use Pimple\Container;

$config = require_once('config.php');

$container = new Container();

$container['config'] = function () use ($config) {
    return $config;
};

$container['queueManager'] = function ($c) {
    return new \Services\QueueManager\QueueManager(
        $c['config']['rabbitmq']['host'],
        $c['config']['rabbitmq']['port'],
        $c['config']['rabbitmq']['user'],
        $c['config']['rabbitmq']['password']
    );
};

$container['scanService'] = function ($c) {
    return new \Services\Scan\Scan(
        $c['config']
    );
};

$container['publishService'] = function ($c) {
    return new \Services\Publish\Publish(
        $c['queueManager'],
        $c['config']['rabbitmq']['queues']['scan']
    );
};

$container['processService'] = function ($c) {
    return new \Services\Process\Process(
        $c['queueManager']
    );
};

return $container;
