<?php

use app\models\Categories;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

$categoriesCount = Categories::find()->count();

return [
    'user_id' => $index + 1,
    'category_id' => rand(1, $categoriesCount),
];
