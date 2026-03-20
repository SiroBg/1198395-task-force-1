<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'email' => $faker->email,
    'name' => $faker->name,
    'url' => $faker->url,
    'address' => $faker->address,
    'phone' => substr($faker->e164PhoneNumber, 1, 11),
];
