<?php

use app\models\Files;
use app\models\Tasks;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

$tasksCount = Tasks::find()->count();
$filesCount = Files::find()->count();

return [
    'task_id' => rand(1, $taskCount),
    'file_id' => rand(1, $filesCount),
];
