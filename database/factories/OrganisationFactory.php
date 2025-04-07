<?php

namespace Database\Factories;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organisation>
 */
class OrganisationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company();
        $owner = User::inRandomOrder()->first() ?: User::factory()->create();

        return [
            'name' => $this->faker->company(),
            'slug' => Organisation::createUniqueSlug($name),
            'owner_id' => $owner->id,
        ];
    }
}
