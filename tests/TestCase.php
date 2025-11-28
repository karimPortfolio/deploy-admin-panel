<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function actingAsUser()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
    }

    public function actingAsAdmin()
    {
        $user = \App\Models\User::factory()->create([
            'role' => \App\Enums\ProfileType::ADMIN->value,
        ]);
        $this->actingAs($user);
    }

}
