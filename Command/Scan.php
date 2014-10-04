<?php
namespace Command;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Scan extends \System\Command
{
    public function run()
    {
        $scanService = self::$container['scanService'];
        $files = $scanService->getFiles($this->args[0]);
    }
}
