<?php
namespace Command;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Scan extends \System\Command
{
    public function run()
    {
        $connection = self::$container['rabbit'];
        $channel = $connection->channel();

        $channel->queue_declare('scanPhotoStore', false, false, false, false);

        $msg = new AMQPMessage($this->args[0]);
        $channel->basic_publish($msg, '', 'scanPhotoStore');

        echo " [x] Sent '".$this->args[0]."'\n";
        $channel->close();
        $connection->close();
    }
}
