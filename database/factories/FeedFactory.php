<?php

use Faker\Generator as Faker;

$factory->define(App\Feed::class, function (Faker $faker) {
    return [
        'title' => $faker->text(15),
        'content' => $faker->randomHtml(),
        'imageUrl' => $faker->imageUrl()
    ];
});
