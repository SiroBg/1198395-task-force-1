<?php

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$connection = new \yii\db\Connection([
    'dsn'      => 'mysql:host=' . $_ENV['DB_HOST'],
    'username' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD'],
]);

try {
    $connection->open();
    $connection->createCommand(
        'CREATE DATABASE IF NOT EXISTS ' . $_ENV['DB_NAME']
        . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
    )->execute();
} catch (\yii\db\Exception $e) {
    echo $e->getMessage();
}

