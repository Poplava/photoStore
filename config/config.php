<?php

return [
    'rabbitmq' => [
        'host' => 'localhost',
        'port' => 5672,
        'user' => 'guest',
        'password' => 'guest',
        'queues' => [
            'scan' => 'photoStoreScan',
        ],
    ],
    'fileExt' => [
        'jpg',
        'jpeg',
        'JPG',
        'JPEG',
        'png',
    ],
    'photoDir' => '/home/poplava/Test/Photos',
    'trunkDir' => '/home/poplava/Test/Trunk'
];