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

$container['queueService'] = function ($c) {
    return new \Services\Queue\Queue(
        $c['queueManager'],
        $c['config']['rabbitmq']['queues']['scan'],
        $c['pictureStoreService']
    );
};

$container['pictureStoreService'] = function ($c) {
    return new \Services\PictureStore\PictureStore(
        $c['config']['photoDir'],
        $c['config']['trunkDir']
    );
};

ActiveRecord\Config::initialize(function ($cfg) {
    $cfg->set_connections([
        'development' => 'mysql://root@localhost/photoStore'
    ]);
});
return $container;
