<?php

namespace Database\Factories;

use App\Enums\LeadLabel;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'email' => fake()->optional()->companyEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'notes' => fake()->optional()->sentence(),
            'label' => fake()->randomElement(LeadLabel::cases())->value,
            'sales_id' => User::factory(),
        ];
    }

    public function forSalesPerson(User $user): static
    {
        return $this->state([
            'sales_id' => $user->id,
        ]);
    }

    public function withLabel(LeadLabel $label): static
    {
        return $this->state([
            'label' => $label->value,
        ]);
    }
}
