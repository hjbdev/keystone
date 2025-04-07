<?php

namespace Database\Factories;

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
            'ipv4' => $this->faker->ipv4(),
            'ipv6' => $this->faker->ipv6(),
            'private_ip' => $this->faker->ipv4(),
            'provider_status' => '',
            'status' => $this->faker->randomElement(ServerStatus::toArray()),
            'region' => '28',
            'os' => 'ubuntu',
            'plan' => '26',
            'user' => 'keystone',
        ];
    }

    public function forNetwork(string $networkId): static
    {
        return $this->state(function (array $attributes) use ($networkId) {
            return [
                'external_network_id' => $networkId,
            ];
        });
    }

    public function forOrganisation(int $organisationId): static
    {
        return $this->state(function (array $attributes) use ($organisationId) {
            return [
                'organisation_id' => $organisationId,
            ];
        });
    }

    public function forProvider(string $providerId): static
    {
        return $this->state(function (array $attributes) use ($providerId) {
            return [
                'provider_id' => $providerId,
            ];
        });
    }
}
