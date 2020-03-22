<?php
echo 'Hello world this is docker public sample app!';
echo '<br />';
echo 'Host for public sample app';
echo '<br />';
echo $_SERVER['HOSTNAME'];
echo '<br />';
echo 'Grabbing private app data';
echo '<br />';

$private_sample_app_host = getenv('PRIVATE_SAMPLE_APP_HOST');
$uri = $private_sample_app_host . '/someroute?some_query_param=' . rand(1,100);
$options = [
    CURLOPT_URL => $uri,
    CURLOPT_HEADER => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true
];
$conn = curl_init();
curl_setopt_array($conn, $options);
$response = curl_exec($conn);
$info = curl_getinfo($conn);
curl_close($conn);
$body = substr($response, $info['header_size']);
$decoded_body = json_decode($body, true);
echo 'Host for private sample app';
echo '<br />';
echo $decoded_body['private_app_hostname'];
echo '<br />';
echo 'Uri for private sample app';
echo '<br />';
echo $decoded_body['private_app_uri'];
echo '<br />';
echo 'get from private sample app';
echo '<br />';
var_dump($decoded_body['private_app_get']);
