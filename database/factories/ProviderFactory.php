<?php

namespace Database\Factories;

use App\Enums\ProviderType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Provider>
 */
class ProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'keystone',
            'type' => $this->faker->randomElement([
                ProviderType::HETZNER,
            ]),
            'token' => $this->faker->uuid(),
        ];
    }

    public function forOrganisation($organisationId): static
    {
        return $this->state(fn (array $attributes) => [
            'organisation_id' => $organisationId,
        ]);
    }
}
