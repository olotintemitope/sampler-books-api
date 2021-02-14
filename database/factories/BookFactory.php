<?php

/** @var Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Model::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
        'isbn' => $faker->isbn10,
        'published_at' => $faker->date(),
        'status' => randomStatus(random_int(0, 1)),
    ];
});

function randomStatus(int $position): string
{
    return ['CHECKED_OUT', 'AVAILABLE'][$position];
}
