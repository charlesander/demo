<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Ambassador;
use App\Models\AmbassadorLegalStatus;
use App\Models\Customer;
use Faker\Generator as Faker;

$factory->define(Ambassador::class, function (Faker $faker) {

    return [
        'title' => $faker->title,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'active' => 1,
        'email' => $faker->email,
        'DOB' => $faker->dateTimeBetween('-50 years', '-30 years'),
        'mobile' => '07720844356',
        'telephone' => '02086841551',
        'legal_status_id' => AmbassadorLegalStatus::all()->first(),
        'vat_registered' => 1,
        'accepted_terms_and_conditions' => 1,
        'exigo_id' => 999
    ];
});
