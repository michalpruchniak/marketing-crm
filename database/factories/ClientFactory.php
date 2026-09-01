<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'notes' => fake()->optional()->sentence(),
            'coordinator_id' => User::factory(),
        ];
    }

    /**
     * @param  User  $coordinator
     * @return static
     */
    public function forCoordinator(User $coordinator): static
    {
        return $this->state(fn (): array => [
            'coordinator_id' => $coordinator->id,
        ]);
    }
}
