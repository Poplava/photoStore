<?php

namespace Services\Process;

use Services\QueueManager\QueueManager;
use PhpAmqpLib\Message\AMQPMessage;

class Process
{
    const QUEUE_NAME = 'photoStoreScanFiles';
    private $queueManager;

    public function __construct(
        QueueManager $queueManager
    )
    {
        $this->queueManager = $queueManager;
    }

    public function run()
    {
        $callback = function (AMQPMessage $msg) {
            QueueManager::ackMessage($msg);
            $this->foo($msg->body);
        };
        $this->queueManager->subscribe(self::QUEUE_NAME, $callback);
    }

    public function foo($text)
    {
        print $text;
    }
}
