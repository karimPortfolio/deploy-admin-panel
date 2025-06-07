<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SecurityGroup>
 */
class SecurityGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group_id' => 'sg-'.$this->faker->uuid(),
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'vpc_id' => 'vpc-'.$this->faker->uuid(),
        ];
    }
}
