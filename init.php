<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

if (!file_exists(__DIR__ . '/config/config.php')) {
    exit('Файл конфигурации отсутствует.');
}
$config = require __DIR__ . '/config/config.php';

if (!isset($config['db'])) {
    exit('Ошибка конфигурации db: отсутствует необходимый ключ.');
}
