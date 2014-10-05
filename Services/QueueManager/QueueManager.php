<?php

namespace Services\QueueManager;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueManager
{
    /**
     * @var AMQPConnection
     */
    private static $client = null;

    private $host;
    private $port;
    private $user;
    private $password;
    private $vhost = '/';

    private $exchangeType = '';

    public function __construct($host, $port, $user, $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
    }

    private function init()
    {
        if (self::$client) {
            return;
        }

        try {
            self::$client = new AMQPConnection(
                $this->host,
                $this->port,
                $this->user,
                $this->password,
                $this->vhost
            );

            if (!self::$client) {
                trigger_error(__METHOD__ . ': can\'t connect to RabbitMQ');
            }
        } catch (\Exception $e) {
            trigger_error($e->getMessage());
        }
    }

    private function createQueue($queueName = '')
    {
        $this->init();
        $channel = self::$client->channel();

        $channel->queue_declare($queueName, false, true, false, false);
        return $channel;
    }

    public function publish($message, $queueName = '')
    {
        $options = [];
        $channel = $this->createQueue($queueName);

        if (!is_scalar($message)) {
            $options['content_type'] = 'application/json';
            $message = json_encode($message);
        }

        $msg = new AMQPMessage($message, $options);

        $channel->basic_publish($msg, $this->exchangeType, $queueName);
    }

    /**
     * @param $queueName
     * @param $callback
     */
    public function subscribe($queueName, $callback)
    {
        $channel = $this->createQueue($queueName);

        $channel->basic_consume($queueName, '', false, false, false, false, $callback);
        while(count($channel->callbacks)) {
            $channel->wait();
        }
    }

    /**
     * @param AMQPMessage $msg
     */
    public static function ackMessage(AMQPMessage $msg)
    {
        try {
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        } catch (\Exception $e) {
            trigger_error($e->getMessage());
        }
    }
}
