<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Client;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;
use Nnjeim\World\Models\Country;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $country = Country::inRandomOrder()->first();
        $client = Client::inRandomOrder()->first() ?? Client::factory()->create();

        // Generate 0-3 additional phones and emails
        $additionalPhones = [];
        $additionalEmails = [];

        $phoneCount = fake()->numberBetween(0, 3);
        for ($i = 0; $i < $phoneCount; $i++) {
            $additionalPhones[] = fake()->phoneNumber();
        }

        $emailCount = fake()->numberBetween(0, 3);
        for ($i = 0; $i < $emailCount; $i++) {
            $additionalEmails[] = fake()->safeEmail();
        }

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'client_id' => $client->id,
            'address' => fake()->optional()->address(),
            'city' => fake()->optional()->city(),
            'state' => fake()->optional()->state(),
            'country_id' => $country?->id,
            'primary_phone' => fake()->optional()->phoneNumber(),
            'primary_email' => fake()->unique()->safeEmail(),
            'additional_phones' => $additionalPhones,
            'additional_emails' => $additionalEmails,
        ];
    }
}
