<?php

/** @var Factory $factory */

use App\Models\Book;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Book::class, function (Faker $faker) {
    return [
        'title' => $faker->jobTitle,
        'isbn' => $faker->isbn10,
        'published_at' => $faker->date(),
        'status' => randomStatus(random_int(0, 1)),
    ];
});

/**
 * @param int $position
 * @return string
 */
function randomStatus(int $position): string
{
    return ['CHECKED_OUT', 'AVAILABLE'][$position];
}
