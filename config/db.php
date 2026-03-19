<?php

$config = require_once __DIR__ . '/config.php';

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['dbName'],
    'username' => $config['db']['user'],
    'password' => $config['db']['password'],
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
