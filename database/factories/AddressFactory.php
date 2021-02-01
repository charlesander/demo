<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Address;
use Faker\Generator as Faker;

$factory->define(Address::class, function (Faker $faker) {
    return [
        'name_on' => $faker->name,
        'first_name_on' => $faker->firstName,
        'last_name_on' => $faker->lastName,
        'address_line1' => $faker->streetAddress,
        'address_line2' => $faker->streetAddress,
        'address_line3' => $faker->streetAddress,
        'town' => $faker->city,
        'county' => $faker->country,
        'postcode' => $faker->postcode,
        'phone' => $faker->phoneNumber,
        'country_id' => 'GB',
        'province_code' => $faker->country,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
    ];
});
