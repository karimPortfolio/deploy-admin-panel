<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SshKey>
 */
class SshKeyFactory extends Factory
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
            'public_key' => $this->faker->text(100),
            'private_key' => $this->faker->text(100),
            'created_by' => \App\Models\User::find(1)->first()
        ];
    }
}
