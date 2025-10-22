<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SecurityGroup>
 */
class RdsDatabaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'db_instance_identifier' => 'rds-'.$this->faker->uuid(),
            'db_name' => $this->faker->unique()->word(),
            'engine' => $this->faker->randomElement(['mysql', 'postgres']),
            'engine_version' => $this->faker->randomElement(['5.7', '8.0', '12.3', '19c', '2017']),
            'db_instance_class' => $this->faker->randomElement(['db.t3.micro', 'db.t3.small', 'db.m5.large']),
            'allocated_storage' => $this->faker->numberBetween(20, 1000),
            'master_username' => $this->faker->userName(),
            'master_password_encrypted' => bcrypt('password'),
            'vpc_security_group' => \App\Models\SecurityGroup::factory()->create()->group_id,
            'created_by' => \App\Models\User::find(1)->first()
        ];
    }
}
