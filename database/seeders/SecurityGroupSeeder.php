<?php

namespace Database\Seeders;

use App\Models\SecurityGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SecurityGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SecurityGroup::factory()
            ->count(10)->create();
    }
}
