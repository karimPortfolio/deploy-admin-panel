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

}
