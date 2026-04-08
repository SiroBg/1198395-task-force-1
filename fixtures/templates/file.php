<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'url' => '/uploads/' . $faker->file(__DIR__ . '/../../web/img', __DIR__ . '/../../web/uploads', false),
    'file_path' => $faker->url,
    'name' => $faker->word . '.' . $faker->fileExtension,
];
