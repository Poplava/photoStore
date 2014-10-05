<?php

namespace Services\Publish;

use Services\QueueManager\QueueManager;

class Publish
{
    private $queueManager;
    private $queueName;

    public function __construct(
        QueueManager $queueManager,
        $queryName
    ) {
        $this->queueManager = $queueManager;
        $this->queueName = $queryName;
    }

    public function publish(array $files)
    {
        foreach ($files as $fileInfo) {
            $fileDomain = new Domain\File($fileInfo);
            $this->queueManager->publish($fileDomain, $this->queueName);
            print "[x] Published file " . $fileDomain->realPath . "\n";
        }
    }
}