<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\SecurityGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsUser();
    }

    public function test_index_returns_paginated_notifications_and_marks_them_as_received()
    {
        $user = User::factory()->create();
    

    }
   
}
