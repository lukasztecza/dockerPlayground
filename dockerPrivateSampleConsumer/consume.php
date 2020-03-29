<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;

$public_sample_producer_host = getenv('PUBLIC_SAMPLE_PRODUCER_HOST');

$rabbit_host = getenv('RABBITMQ_APP_HOST');
$rabbit_port = getenv('RABBITMQ_APP_PORT');
$rabbit_user = getenv('RABBITMQ_APP_USER');
$rabbit_password = getenv('RABBITMQ_APP_PASS');
$rabbit_queue = getenv('RABBITMQ_APP_QUEUE');
$rabbit_exchange = getenv('RABBITMQ_APP_EXCHANGE');
$rabbit_routing_key = getenv('RABBITMQ_APP_ROUTING_KEY');
$rabbit_exchange_fail = $rabbit_exchange . '_fail';
$rabbit_queue_fail = $rabbit_queue . '_fail';
$rabbit_message_ttl = 15000;
$rabbit_message_max_fail_count = 3;
$rabbit_queue_unprocessable = $rabbit_queue . '_unprocessable';
$rabbit_routing_key_unprocessable = $rabbit_routing_key . '_unprocessable';

try {
    $connection = new AMQPStreamConnection($rabbit_host, $rabbit_port, $rabbit_user, $rabbit_password);
    $channel = $connection->channel();

    //consumer ensures that all exchanges and queues and bindings exist
    $channel->exchange_declare($rabbit_exchange, 'topic', false, false, false);
    $channel->exchange_declare($rabbit_exchange . '_fail', 'topic', false, false, false);
    $channel->queue_declare($rabbit_queue, false, false, false, false, false, new AMQPTable(['x-dead-letter-exchange' => $rabbit_exchange_fail]));
    $channel->queue_bind($rabbit_queue, $rabbit_exchange, $rabbit_routing_key);
    $channel->queue_declare($rabbit_queue_fail, false, false, false, false, false, new AMQPTable(['x-dead-letter-exchange' => $rabbit_exchange, 'x-message-ttl' => $rabbit_message_ttl]));
    $channel->queue_bind($rabbit_queue_fail, $rabbit_exchange_fail, $rabbit_routing_key);
    $channel->queue_declare($rabbit_queue_unprocessable, false, false, false, false, false);
    $channel->queue_bind($rabbit_queue_unprocessable, $rabbit_exchange_fail, $rabbit_routing_key_unprocessable);
} catch (\Throwable $e) {
    error_log($e->getMessage());
    throw new \Exception('Could not create rabbitmq connection');
}
echo ' [*] Hello world this is docker public sample consumer waiting for messages!' . PHP_EOL;

$callback = function($msg) use($public_sample_producer_host, $rabbit_exchange_fail, $rabbit_routing_key_unprocessable, $rabbit_message_max_fail_count) {
    sleep(5);//pretend that process takes time
    echo ' [x] Received ' . $msg->body . ' with routing key ' . $msg->delivery_info['routing_key'] . PHP_EOL;
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
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            echo ' [x] Consumed' . PHP_EOL;
            return;
        } else {
            echo ' [x] Failed to consume' . PHP_EOL;
        }
    } else {
        echo ' [x] Wrong payload' . PHP_EOL;
    }
    $count = 0;
    try {
        $count = $msg->get('application_headers')->getNativeData('x-death')['x-death'][0]['count'];
        echo ' [x] Attempt ' . (int)$count . PHP_EOL;
    } catch(\Throwable $e) {
        error_log($e->getMessage());
    }
    if ($count >= $rabbit_message_max_fail_count) {
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        echo ' [x] Publishing to unprocessable' . PHP_EOL;
        $msg->delivery_info['channel']->basic_publish($msg, $rabbit_exchange_fail, $rabbit_routing_key_unprocessable);
    } else {
        $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
    }
};
$channel->basic_consume($rabbit_queue, '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
