<?php

namespace Database\Seeders;

use App\Models\RdsDatabase;
use App\Models\SecurityGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RdsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RdsDatabase::factory()
            ->count(10)->create();
    }
}
