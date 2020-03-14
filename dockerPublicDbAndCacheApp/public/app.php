<?php
$db_engine = getenv('DB_APP_ENGINE');
$db_host = getenv('DB_APP_HOST');
$db_port = getenv('DB_APP_PORT');
$db_database = getenv('DB_APP_DATABASE');
$db_user = getenv('DB_APP_USER');
$db_password = getenv('DB_APP_PASSWORD');

try {
    $connection = new \PDO(
        $db_engine . ':host=' . $db_host . ';port=' . $db_port . ';dbname=' . $db_database . ';charset=utf8',
        $db_user,
        $db_password
    );
    $connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (\Throwable $e) {
    error_log($e->getMessage());
    throw new \Exception('Could not create pdo connection');
}

$redis_host = getenv('REDIS_APP_HOST');
$redis_port = getenv('REDIS_APP_PORT');
$redis_password = getenv('REDIS_APP_PASSWORD');
$redis_items_cache_key = 'cached_items';

try {
    $redis = new Redis();
    $redis->connect($redis_host, $redis_port);
    $redis->auth($redis_password);
} catch (\Throwable $e) {
    error_log($e->getMessage());
    throw new \Exception('Could not create redis connection');
}


$message = '';
if ('POST' === $_SERVER['REQUEST_METHOD']) {
    if ('/add' === $_SERVER['REQUEST_URI']) {
        $statement = $connection->prepare('INSERT INTO `sample_items` (`name`) VALUES (:name)');
        $statement->execute(['name' => $_POST['add_item']]);
        $message = 'item has been added';
    } elseif ('/delete' === $_SERVER['REQUEST_URI']) {
        $statement = $connection->prepare('DELETE FROM `sample_items` WHERE id = :id');
        $statement->execute(['id' => $_POST['item_id']]);
        $message = 'item has been deleted';
    }
    $redis->del($redis_items_cache_key);
    header('Location: ' . $_SERVER['PHP_SELF']);exit();
}

$items = $redis->get($redis_items_cache_key);
$cached_version = false;
if (!empty($items)) {
    $items = json_decode($items, true);
}
if (empty($items)) {
    $statement = $connection->prepare('SELECT `id`, `name` FROM `sample_items`');
    $statement->execute([]);
    $items = [];
    while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
        $items[] = ['item_name' => $row['name'], 'item_id' => $row['id']];
    }
    if (count($items)) {
        $redis->set($redis_items_cache_key, json_encode($items), 10);
    }
} else {
    $cached_version = true;
}

echo 'Hello world this is docker public db and cache app!<br />';
echo 'Items so far:';
if ($cached_version) {
    echo '<div style="color:coral;">cached version</div>';
}
echo '<ul>';
foreach ($items as $item) {
    echo '<li>' . htmlentities($item['item_name']);
    echo '<form action="/delete" method="post"><input type="hidden" name="item_id" value="' . (int)$item['item_id'] . '" /><input type="submit" value="delete" /></form>';
    echo '</li>';
}
echo '</ul>';
echo '<form action="/add" method="post"><input type="text" name="add_item" /><input type="submit" value="add" /></form>';

if (!empty($message)) {
    echo '<a href="/" style="color:black;text-decoration:none;text-align:center;"><div style="background-color:lightgreen;max-width:200px;">' . $message . '</div></a>';
}
