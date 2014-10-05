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
    ]
];