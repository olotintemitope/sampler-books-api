<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(Model::class, function (Faker $faker) {
    return [
        'book_id' => function () {
            return factory(App\Models\Book::class)->create()->id;
        },
        'user_id' => function () {
            factory(App\Models\User::class)->create()->id;
        },
        'action' => randomAction(random_int(0, 1)),
    ];
});

function randomAction(int $position): string
{
    return ['CHECKIN', 'CHECKOUT'][$position];
}
