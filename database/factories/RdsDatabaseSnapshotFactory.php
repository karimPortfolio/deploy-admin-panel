<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RdsDatabaseSnapshot>
 */
class RdsDatabaseSnapshotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'snapshot_identifier' => $this->faker->uuid,
            'snapshot_arn' => 'arn:aws:rds:' . $this->faker->word . ':' . $this->faker->randomNumber(5) . ':snapshot:' . $this->faker->uuid,
            'rds_database_id' => \App\Models\RdsDatabase::factory(),
            'snapshot_type' => $this->faker->randomElement(['automated', 'manual']),
            'status' => \App\Enums\DBSnapshotStatus::STARTED,
            'percent_progress' => $this->faker->numberBetween(0, 100),
            'encrypted' => $this->faker->boolean,
            'kms_key_id' => $this->faker->uuid,
            'snapshot_create_time' => $this->faker->dateTimeBetween('-1 years', 'now'),
        ];
    }
}
