<?php
namespace Command;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Scan extends \System\Command
{
    public function run()
    {
        $scanService = self::$container['scanService'];
        $queue = self::$container['queueService'];
        $files = $scanService->getFiles($this->args[0]);

        $queue->publish($files);

        print "\n[x] Published " . count($files) . " file(s).\n";
    }
}
