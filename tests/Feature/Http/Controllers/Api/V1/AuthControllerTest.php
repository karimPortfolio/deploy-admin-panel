<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_me_endpoint_returns_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson(route('api.v1.me'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name
                ]
            ]);
    }

    public function test_me_method_returns_unauthorized_for_guest(): void
    {
        $response = $this->getJson(route('api.v1.me'));

        $response->assertUnauthorized();
    }
}
