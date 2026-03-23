<?php

use app\models\Categories;
use app\models\Cities;
use app\models\Users;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

$usersCount = Users::find()->count();
$categoriesCount = Categories::find()->count();
$citiesCount = Cities::find()->count();

return [
    'author_id' => rand(1, $usersCount),
    'name' => $faker->sentences(2),
    'description' => $faker->text,
    'category_id' => rand(1, $categoriesCount),
    'location' => $faker->streetAddress,
    'lat' => $faker->randomFloat(10, -180, 180),
    'long' => $faker->randomFloat(10, -180, 180),
    'city_id' => rand(1, $citiesCount),
    'budget' => rand(100, 10000),
    'expire_date' => $faker->dateTimeBetween('+1 week', '+4 week'),
    'status' => 1,
];
