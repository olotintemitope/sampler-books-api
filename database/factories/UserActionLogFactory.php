<?php

/** @var Factory $factory */

use App\Models\{Book, User, UserActionLog};
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(UserActionLog::class, function (Faker $faker) {
    factory(Book::class, 5)->create()->each(function ($book) {
        $book->users()->save(factory(User::class)->create([
            'action' => randomAction(random_int(0, 1)),
        ]));
    });
});

/**
 * @param int $position
 * @return string
 */
function randomAction(int $position): string
{
    return ['CHECKIN', 'CHECKOUT'][$position];
}
