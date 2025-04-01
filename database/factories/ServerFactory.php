<?php

namespace Database\Factories;

use App\Enums\ServerProvider;
use App\Enums\ServerStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Server>
 */
class ServerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'provider' => ServerProvider::HETZNER,
            'provider_id' => $this->faker->uuid(),
            'ipv4' => $this->faker->ipv4(),
            'ipv6' => $this->faker->ipv6(),
            'provider_status' => '',
            'status' => $this->faker->randomElement(ServerStatus::toArray()),
            'region' => '28',
            'os' => 'ubuntu',
            'plan' => '26',
            'user' => 'keystone',
        ];
    }

    public function forOrganisation(int $organisationId): static
    {
        return $this->state(function (array $attributes) use ($organisationId) {
            return [
                'organisation_id' => $organisationId,
            ];
        });
    }
}
