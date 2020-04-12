<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Expense;
use Faker\Generator as Faker;

$factory->define(Expense::class, function (Faker $faker) {
    $expenseOrIncome = $faker->numberBetween(0, 100);
    return [
        'name' => $faker->words(4, true),
        'amount' => $faker->randomFloat(2, 1, 100000),
        'type' => $expenseOrIncome < 20 ? 1 : 0,
        'date' => $faker->dateTimeBetween('-2 years - 3 months', 'now'),
    ];
});
