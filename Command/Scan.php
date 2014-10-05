<?php
namespace Command;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Scan extends \System\Command
{
    public function run()
    {
        $scanService = self::$container['scanService'];
        $publishService = self::$container['publishService'];
        $files = $scanService->getFiles($this->args[0]);

        $publishService->publish($files);

        print "\nPublished " . count($files) . " file(s).\n";
    }
}
