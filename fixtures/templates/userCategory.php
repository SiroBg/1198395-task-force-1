<?php

use app\models\Categories;
use app\models\Users;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

$usersCount = Users::find()->count();
$categoriesCount = Categories::find()->count();

return [
    'user_id' => rand(1, $usersCount),
    'category_id' => rand(1, $categoriesCount),
];
