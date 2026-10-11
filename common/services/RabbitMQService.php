<?php

namespace common\services;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function __construct()
    {
        $config = \Yii::$app->params['rabbitmq'];

        $this->connection = new AMQPStreamConnection(
            $config['host'],
            $config['port'],
            $config['user'],
            $config['password'],
            $config['vhost']
        );

        $this->channel = $this->connection->channel();
    }

    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }

    public function sendMessage(string $queue, string $message): void
    {
        $this->channel->queue_declare(
            $queue,
            false,
            true,
            false,
            false
        );

        $msg = new AMQPMessage($message);

        $this->channel->basic_publish(
            $msg,
            '',
            $queue
        );
    }

    public function close(): void
    {
        $this->channel->close();
        $this->connection->close();
    }

    public function consume(string $queue, callable $callback): void
    {
        $this->channel->queue_declare(
            $queue,
            false,
            true,
            false,
            false
        );

        $this->channel->basic_consume(
            $queue,
            '',
            false,
            false,
            false,
            false,
            $callback
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function declareExchange(string $exchange): void
    {
        $this->channel->exchange_declare(
            $exchange,
            'direct',
            false,
            true,
            false
        );
    }

    public function declareQueue(
        string $queue,
        string $exchange,
        string $routingKey
    ): void {
        $this->channel->queue_declare(
            $queue,
            false,
            true,
            false,
            false
        );

        $this->channel->queue_bind(
            $queue,
            $exchange,
            $routingKey
        );
    }

    public function publish(
        string $exchange,
        string $routingKey,
        string $message
    ): void {
        $msg = new AMQPMessage($message);

        $this->channel->basic_publish(
            $msg,
            $exchange,
            $routingKey,
            true
        );
    }

    public function onReturnedMessage(): void
    {
        $this->channel->set_return_listener(
            function (
                int $replyCode,
                string $replyText,
                string $exchange,
                string $routingKey,
                AMQPMessage $message
            ) {
                echo "Message returned!" . PHP_EOL;
                echo "Reason: {$replyCode} {$replyText}" . PHP_EOL;
                echo "Exchange: {$exchange}" . PHP_EOL;
                echo "Routing key: {$routingKey}" . PHP_EOL;
                echo "Message: {$message->getBody()}" . PHP_EOL;
            }
        );
    }
}