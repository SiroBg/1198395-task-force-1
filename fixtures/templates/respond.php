<?php

use app\models\Tasks;
use app\models\Users;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

$tasksCount = Tasks::find()->count();
$executorsCount = Users::find()->count();

return [
    'task_id' => rand(1, $tasksCount),
    'executor_id' => rand(1, $executorsCount),
    'comment' => $faker->text,
    'price' => $faker->randomDigit(),
];
