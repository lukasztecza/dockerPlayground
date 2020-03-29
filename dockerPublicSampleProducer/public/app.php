<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
//use PhpAmqpLib\Wire\AMQPTable;

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
    //producer should be responsble for declaration of exchange only but we could declare queue and bind it too
    $channel->exchange_declare($rabbit_exchange, 'topic', false, false, false);
    //$channel->queue_declare($rabbit_queue, false, false, false, false, false, ['x-dead-letter-exchange' => $rabbit_exchange . '_fail']);
    //$channel->queue_bind($rabbit_queue, $rabbit_exchange, $rabbit_routing_key);
} catch (\Throwable $e) {
    error_log($e->getMessage());
    throw new \Exception('Could not create rabbitmq connection');
}
$killRabbit = function() use($connection, $channel) {
    $channel->close();
    $connection->close();
};

$file_name = __DIR__ . '/../stored.json';

if ('POST' === $_SERVER['REQUEST_METHOD']) {
    if ('/add' === $_SERVER['REQUEST_URI'] && !empty($_POST['content']) && is_string($_POST['content'])) {
        $response = '';
        $file = file_get_contents($file_name);
        $file = json_decode($file, true);
        if (!empty($file) && is_array($file)) {
            $file[] = $_POST['content'];
            file_put_contents($file_name, json_encode($file));
            $response = '{"success":true,"message":"added payload"}';
            header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
        } else {
            file_put_contents($file_name, '["' . $_POST['content'] . '"]');
            $response = '{"success":true,"message":"recreated content with payload"}';
            header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
        }
        echo $response;
    } elseif ('/produce' === $_SERVER['REQUEST_URI']) {
        $msg = new AMQPMessage('{"payload":"somepayload' . rand(100,999) . '"}');
        $channel->basic_publish($msg, $rabbit_exchange, $rabbit_routing_key);
        header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
        echo 'Message published <a href=".">Go to form</a>';
    } else {
        header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request");
        echo '{"success":false,"message":"Invalid payload"}';
    }
    $killRabbit();
    exit;
}

//get reqests
echo 'Hello world this is docker public sample producer!<br />';
$file = file_get_contents($file_name);
$file = json_decode($file, true);
if (!empty($file)) {
    echo 'File contents:<br />';
    echo var_export($file);
} else {
    echo 'No content - recreating it';
    file_put_contents($file_name, '["recreated"]');
}
echo '<form action="/produce" method="post">Publish random message<input type="submit" name="" /></form><br />';
echo '<a href=".">Refresh</a>';
$killRabbit();
exit;
