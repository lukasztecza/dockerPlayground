<?php
echo json_encode(['private_app_hostname' => $_SERVER['HOSTNAME'], 'private_app_uri' => $_SERVER['REQUEST_URI'], 'private_app_get' => $_GET]);
