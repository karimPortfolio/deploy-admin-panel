<?php

namespace Database\Seeders;

use App\Models\UserPreference;
use Illuminate\Database\Seeder;

class UserPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersCount = $this->getUsersCount();

        if ($usersCount === 0) {
            \App\Models\User::factory()
                ->count(5)
                ->create();
            
            $usersCount = $this->getUsersCount();
        }

        UserPreference::factory()
            ->count($usersCount)->create();
    }

    private function getUsersCount(): int
    {
        return \App\Models\User::all()->count();
    }
}
