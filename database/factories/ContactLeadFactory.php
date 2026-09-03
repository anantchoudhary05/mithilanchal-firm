<?php

namespace Database\Factories;

use App\Models\ContactLead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactLead>
 */
class ContactLeadFactory extends Factory
{
    protected $model = ContactLead::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'company' => fake()->optional()->company(),
            'email' => fake()->boolean(80) ? fake()->unique()->safeEmail() : null,
            'phone' => fake()->numerify('9#########'),
            'requirement' => fake()->randomElement(ContactLead::REQUIREMENTS),
            'quantity' => fake()->optional()->randomElement(['200 KG', '500 KG', '1 Ton']),
            'message' => fake()->optional()->paragraph(),
            'status' => ContactLead::STATUS_NEW,
            'admin_notes' => null,
            'ip_address' => fake()->ipv4(),
        ];
    }

    public function contacted(): static
    {
        return $this->state(fn (): array => [
            'status' => ContactLead::STATUS_CONTACTED,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (): array => [
            'status' => ContactLead::STATUS_CLOSED,
        ]);
    }
}
