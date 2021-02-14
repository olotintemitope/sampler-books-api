<?php

/** @var Factory $factory */

use App\Models\{Book, User, UserActionLog};
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(UserActionLog::class, function (Faker $faker) {
    $bookBatch1 = factory(Book::class, 5)->create();
    $bookBatch2 = factory(Book::class, 5)->create();

    $user1 = factory(User::class)->create();
    $user2 = factory(User::class)->create();

    $user1->books()->attach($bookBatch1, [
        'action' => randomAction(random_int(0, 1))
    ]);
    $user2->books()->attach($bookBatch2, [
        'action' => randomAction(random_int(0, 1))
    ]);
});

//'action' => randomAction(random_int(0, 1)),

/**
 * @param int $position
 * @return string
 */
function randomAction(int $position): string
{
    return ['CHECKIN', 'CHECKOUT'][$position];
}
