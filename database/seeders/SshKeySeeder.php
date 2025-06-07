<?php

namespace Database\Seeders;

use App\Models\SshKey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SshKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SshKey::factory()
            ->count(10)->create();
    }
}
