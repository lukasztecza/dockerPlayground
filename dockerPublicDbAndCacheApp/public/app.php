<?php
echo 'Hello world this is docker public db and cache app!';
$engine = getenv('DB_APP_ENGINE');
$host = getenv('DB_APP_HOST');
$port = getenv('DB_APP_PORT');
$database = getenv('DB_APP_DATABASE');
$user = getenv('DB_APP_USER');
$password = getenv('DB_APP_PASSWORD');

try {
    $connection = new \PDO(
        $engine . ':host=' . $host . ';port=' . $port . ';dbname=' . $database . ';charset=utf8',
        $user,
        $password
    );
    $connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (\Throwable $e) {
    error_log($e->getMessage());
    throw new \Exception('Could not create pdo connection');
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
}

$statement = $connection->prepare('SELECT `id`, `name` FROM `sample_items`');
$statement->execute([]);
$items = [];
while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
    $items[] = ['item_name' => $row['name'], 'item_id' => $row['id']];
}

echo 'Items so far:';
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
