<?php
use Faker\Generator as Faker;
use App\Models\Synonym;

$factory->define(Synonym::class, function (Faker $faker) {
    return [
        'keyword' => $faker->lastName,
        'synonym' => $faker->lastName,
    ];
});
