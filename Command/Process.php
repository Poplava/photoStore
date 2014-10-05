<?php

namespace Command;

use Services\Publish\Publish;

class Process extends \System\Command
{
    public function run()
    {
        $processService = self::$container['processService'];
        $processService->run();
    }
}
