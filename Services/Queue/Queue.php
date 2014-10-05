<?php

namespace Services\Queue;

use PhpAmqpLib\Message\AMQPMessage;
use Services\PictureStore\PictureStore;
use Services\QueueManager\QueueManager;

class Queue
{
    private $queueManager;
    private $queueName;
    private $pictureStore;

    public function __construct(
        QueueManager $queueManager,
        $queryName,
        PictureStore $pictureStore
    ) {
        $this->queueManager = $queueManager;
        $this->queueName = $queryName;
        $this->pictureStore = $pictureStore;
    }

    public function publish(array $files)
    {
        foreach ($files as $fileInfo) {
            $fileDomain = new Domain\File($fileInfo);
            $this->queueManager->publish($fileDomain, $this->queueName);
            print "[x] Published file " . $fileDomain->realPath . "\n";
        }
    }

    public function subscribe()
    {
        $this->queueManager->subscribe($this->queueName, [$this, 'callback']);
    }

    public function callback(AMQPMessage $message)
    {
        $data = null;
        $body = null;
        $ack = false;

        if ($message->get('content_type') === 'application/json') {
            $data = (array) json_decode($message->body);
        } else {
            $body = $message->body;
        }

        if (!empty($data)) {
            if ($data['type'] === 'file') {
                $ack = $this->pictureStore->process($data['realPath']);
            }
        }

        if ($ack) {
            if (isset($ack->file)) {
                print "[x] Stored " . $ack->sourcefile . " => " . $ack->file . "\n";
            } else {
                print "[x] Skipped\n";
            }

            QueueManager::ackMessage($message);
        }
    }
}
