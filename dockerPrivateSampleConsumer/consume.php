<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$public_sample_producer_host = getenv('PUBLIC_SAMPLE_PRODUCER_HOST');

$rabbit_host = getenv('RABBITMQ_APP_HOST');
$rabbit_port = getenv('RABBITMQ_APP_PORT');
$rabbit_user = getenv('RABBITMQ_APP_USER');
$rabbit_password = getenv('RABBITMQ_APP_PASS');
$rabbit_queue = getenv('RABBITMQ_APP_QUEUE');
$rabbit_exchange = getenv('RABBITMQ_APP_EXCHANGE');
$rabbit_routing_key = getenv('RABBITMQ_APP_ROUTING_KEY');

try {
    $connection = new AMQPStreamConnection($rabbit_host, $rabbit_port, $rabbit_user, $rabbit_password);
    $channel = $connection->channel();
    $channel->exchange_declare($rabbit_exchange, 'topic', false, false, false);//this line could be done by producer only but it is idempotent so can be done by consumer too
    $channel->queue_declare($rabbit_queue, false, false, false, false);
    $channel->queue_bind($rabbit_queue, $rabbit_exchange, $rabbit_routing_key);
} catch (\Throwable $e) {
    error_log($e->getMessage());
    throw new \Exception('Could not create rabbitmq connection');
}
echo ' [*] Hello world this is docker public sample consumer waiting for messages!' . PHP_EOL;

$callback = function($msg) use($public_sample_producer_host) {
    sleep(10);
    echo ' [x] Received ' . $msg->body . 'with routing key' . $msg->delivery_info['routing_key'] . PHP_EOL;
    $body = json_decode($msg->body, true);
    if (!empty($body['payload'])) {
        $uri = $public_sample_producer_host . '/add';
        $options = [
            CURLOPT_URL => $uri,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => ['content' => $body['payload']]
        ];
        $conn = curl_init();
        curl_setopt_array($conn, $options);
        $response = curl_exec($conn);
        $info = curl_getinfo($conn);
        curl_close($conn);
        $body = substr($response, $info['header_size']);
        $body = json_decode($body, true);
        if (!empty($body['success']) && true === $body['success']) {
            echo ' [x] Consumed' . PHP_EOL;
        } else {
            echo ' [x] Failed to consume' . PHP_EOL;
        }
    } else {
        echo ' [x] Ignoring' . PHP_EOL;
    }
};
$channel->basic_consume($rabbit_queue, '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
