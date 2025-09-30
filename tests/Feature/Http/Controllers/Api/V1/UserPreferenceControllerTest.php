<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPreferenceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }


    public function test_update_preferences()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $user->preferences()->create([
            'preferences' => [
                'language' => 'en',
                'theme' => 'light',
                'notification' => [
                    'email' => true,
                    'system' => true,
                ],
            ]
        ]);

        $response = $this->putJson(route('api.v1.user.preferences.update', $user->preferences[0]->id), [
            'language' => 'fr',
            'theme' => 'dark',
            'notification' => [
                'email' => false,
                'system' => true,
            ],
        ]);

        $response->assertNoContent();

        $this->assertDatabaseHas('user_preferences', [
            'user_id' => $user->id,
            'preferences->language' => 'fr',
            'preferences->theme' => 'dark',
            'preferences->notification->email' => false,
            'preferences->notification->system' => true,
        ]);
    }

    public function test_update_preferences_unauthenticated()
    {
        $user = User::factory()->create();

        $user->preferences()->create([
            'preferences' => [
                'language' => 'en',
                'theme' => 'light',
                'notification' => [
                    'email' => true,
                    'system' => true,
                ],
            ]
        ]);

        $response = $this->putJson(route('api.v1.user.preferences.update', $user->preferences[0]->id), [
            'language' => 'fr',
            'theme' => 'dark',
            'notification' => [
                'email' => false,
                'system' => true,
            ],
        ]);

        $response->assertUnauthorized();

    }
   
}
