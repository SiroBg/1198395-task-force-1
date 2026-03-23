<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'url' => $faker->url,
    'file_path' => $faker->file(__DIR__ . '/../../web/img/', __DIR__ . '/../../uploads/', false),
];
