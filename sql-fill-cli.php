<?php

use TaskForce\Files\CsvToSqlFilesConverter;

require_once __DIR__ . '/init.php';

$converter = new CsvToSqlFilesConverter(
    __DIR__ . '/data/',
    __DIR__ . '/db/',
);

try {
    $converter->convert();
} catch (Exception $exception) {
    error_log($exception->getMessage());
}
