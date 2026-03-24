<?php

use app\models\Categories;
use app\models\Cities;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

$categoriesCount = Categories::find()->count();
$citiesCount = Cities::find()->count();

return [
    'author_id' => $index + 1,
    'name' => $faker->word,
    'description' => $faker->text,
    'category_id' => rand(1, $categoriesCount),
    'location' => $faker->streetAddress,
    'lat' => $faker->randomFloat(10, 0, 180),
    'long' => $faker->randomFloat(10, 0, 180),
    'city_id' => rand(1, $citiesCount),
    'budget' => rand(100, 10000),
    'expire_date' => $faker->date,
    'status' => 1,
];
