<?php

use app\models\Cities;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

$citiesCount = Cities::find()->count();

return [
    'email' => $faker->email,
    'name' => $faker->firstName,
    'city_id' => rand(1, $citiesCount),
    'password' => Yii::$app->getSecurity()->generatePasswordHash('password_' . $index),
    'is_executor' => rand(0, 1),
    'profile_img_file_id' => $index + 1,
    'birthday' => $faker->date(),
    'phone' => substr($faker->e164PhoneNumber, 1, 11),
    'telegram' => $faker->email,
    'about' => $faker->text,
];
