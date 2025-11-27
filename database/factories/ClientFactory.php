<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Nnjeim\World\Models\City;
use Nnjeim\World\Models\Country;
use Nnjeim\World\Models\Currency;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $country = Country::inRandomOrder()->first();
        $city = City::where('country_id', $country?->id)->inRandomOrder()->first();
        $currency = Currency::where('country_id', $country?->id)->first()
            ?? Currency::inRandomOrder()->first();

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'country_id' => $country?->id ?? 1,
            'city_id' => $city?->id,
            'currency_id' => $currency?->id ?? 1,
            'address' => fake()->optional()->address(),
            'phone' => fake()->optional()->phoneNumber(),
            'company' => fake()->optional()->company(),
            'tax_id' => fake()->optional()->numerify('TAX-########'),
            'website' => fake()->optional()->url(),
            'notes' => fake()->optional()->paragraph(),
        ];
    }
}
