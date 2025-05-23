<?php

namespace Database\Factories;

use App\Models\SecurityGroup;
use App\Models\SshKey;
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
            'instance_id' => $this->faker->uuid(),
            'image_id' => $this->faker->uuid(),
            'name' => $this->faker->word(),
            'instance_type' => $this->faker->word(),
            'status' => $this->faker->word(),
            'private_ip_address' => $this->faker->ipv4(),
            'public_ip_address' => $this->faker->ipv4(),
            'ssh_key_id' => SshKey::factory()->create()->id,
            'security_group_id' => SecurityGroup::factory()->create()->id,
            'vpc_id' => $this->faker->uuid(),
        ];
    }
}
