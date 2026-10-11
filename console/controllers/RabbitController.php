<?php
namespace console\controllers;

use common\services\RabbitMQService;
use yii\console\Controller;

class RabbitController  extends Controller{
    public function actionTest(){
        $rabbit = new RabbitMQService();

        echo "RabbitMQ connection: OK" . PHP_EOL;

        $rabbit->close();
    }

    public function actionSend()
    {
        $rabbit = new RabbitMQService();

        $rabbit->onReturnedMessage();

        $exchange = 'app.events';

        $rabbit->declareExchange($exchange);

        $rabbit->declareQueue(
            'emails',
            $exchange,
            'email'
        );

        $rabbit->declareQueue(
            'orders',
            $exchange,
            'order'
        );

        $rabbit->publish(
            $exchange,
            'email',
            'Hello Email!'
        );

        $rabbit->publish(
            $exchange,
            'order',
            'Order #123 created'
        );

        $rabbit->publish(
            $exchange,
            'unknown',
            'What happens to me?'
        );

        $rabbit->getChannel()->wait(null, false, 1);

        $rabbit->close();

        echo "Messages sent!" . PHP_EOL;
    }

    public function actionConsume()
    {
        $rabbit = new RabbitMQService();

        echo "Waiting for messages..." . PHP_EOL;

        $rabbit->consume('emails', function ($message) {
            echo "Received: " . $message->getBody() . PHP_EOL;

            $message->ack();

            echo "ACK sent" . PHP_EOL;
        });
    }

    public function actionConsumeEmails()
    {
        $rabbit = new RabbitMQService();

        echo "Waiting for email messages..." . PHP_EOL;

        $rabbit->consume('emails', function ($message) {
            echo "Email worker received: " . $message->getBody() . PHP_EOL;

            $message->ack();

            echo "ACK sent" . PHP_EOL;
        });
    }

    public function actionConsumeOrders()
    {
        $rabbit = new RabbitMQService();

        echo "Waiting for order messages..." . PHP_EOL;

        $rabbit->consume('orders', function ($message) {
            echo "Order worker received: " . $message->getBody() . PHP_EOL;

            $message->ack();

            echo "ACK sent" . PHP_EOL;
        });
    }
}
?>