<?php

namespace Command;

use Services\Publish\Publish;

class Listen extends \System\Command
{
    public function run()
    {
        $queue = self::$container['queueService'];
        $queue->subscribe();
    }
}
