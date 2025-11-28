<?php

namespace Tests\Feature\Http\Controllers\Api\V1\Admin;

use App\Enums\InstanceType;
use App\Enums\OsFamily;
use App\Models\User;
use Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserControllerTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }
    
    public function test_can_list_users_with_pagination()
    {
        User::factory()->count(5)->create();
        $response = $this->getJson(route('api.v1.admin.users.index'));

        $response->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                 ->has('meta')
                 ->has('links')
        );
    }

    public function test_can_search_users()
    {
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@gmail.com']);
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@gmail.com']);
        User::factory()->create(['name' => 'Alice Johnson', 'email' => 'alice@gmail.com']);

        $response = $this->getJson(route('api.v1.admin.users.index', ['search' => 'Jane']));
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name', 'email', 'role', 'created_at']
                ],
                'meta',
                'links'
            ]);
    }

    public function test_can_filter_users_by_role()
    {
        User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'user']);
        User::factory()->create(['role' => 'user']);

        $response = $this->getJson(route('api.v1.admin.users.index', ['filter[role]' => 'admin']));
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name', 'email', 'role', 'created_at']
                ],
                'meta',
                'links'
            ]);
    }

    public function test_can_sort_users_by_name()
    {
        User::factory()->create(['name' => 'Charlie']);
        User::factory()->create(['name' => 'Alice']);
        User::factory()->create(['name' => 'Bob']);

        $response = $this->getJson(route('api.v1.admin.users.index', ['sort' => 'name']));
        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Alice')
            ->assertJsonPath('data.1.name', 'Bob')
            ->assertJsonPath('data.2.name', 'Charlie');
    }

    public function test_index_return_403_if_not_admin()
    {
        $this->actingAsUser();

        $response = $this->getJson(route('api.v1.admin.users.index'));
        $response->assertStatus(403);
    }

    public function test_store_creates_user_and_sends_notification()
    {
        \Notification::fake();

        $userData = [
            'name' => 'Test User',
            'email' => 'user@gmail.com',
            'role' => [
                'value' => \App\Enums\ProfileType::USER->value,
                'label' => \App\Enums\ProfileType::USER->label()
            ],
            'is_active' => true,
        ];

        $response = $this->postJson(route('api.v1.admin.users.store'), $userData);
        $response->assertNoContent();
        $this->assertDatabaseHas('users', ['email' => 'user@gmail.com']);
        \Notification::assertSentTo(
            [User::where('email', 'user@gmail.com')->first()],
            \App\Notifications\NewUserNotification::class
        );
    }

    public function test_store_validation_errors()
    {
        $response = $this->postJson(route('api.v1.admin.users.store'), []);
        $response->assertStatus(422);
    }


    public function test_store_return_403_if_not_admin()
    {
        $this->actingAsUser();

        $response = $this->postJson(route('api.v1.admin.users.store'), []);
        $response->assertStatus(403);
    }

    public function test_show_returns_user_details()
    {
        $user = User::factory()->create();

        $response = $this->getJson(route('api.v1.admin.users.show', $user->id));
        $response->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonStructure([
                'data' => [
                    'id', 'name', 'email', 'role', 'is_active', 'created_at',
                    'servers_count', 'ssh_keys_count', 'security_groups_count', 'photo'
                ]
            ]);
    }

    public function test_show_return_403_if_not_admin()
    {
        $this->actingAsUser();
        $user = User::factory()->create();

        $response = $this->getJson(route('api.v1.admin.users.show', $user->id));
        $response->assertStatus(403);
    }

    public function test_show_return_404_if_user_not_found()
    {
        $response = $this->getJson(route('api.v1.admin.users.show', 999));
        $response->assertStatus(404);
    }

    
    public function test_it_can_activate_user_account()
    {
        \Notification::fake();

        $user = User::factory()->create(['is_active' => false]);

        $response = $this->putJson(route('api.v1.admin.users.activate', $user->id));
        $response->assertNoContent();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_active' => true]);

        \Notification::assertSentTo(
            $user,
            \App\Notifications\AccountActivationNotification::class
        );
    }

    public function test_activate_user_account_method_return_403_if_not_admin()
    {
        $this->actingAsUser();
        $user = User::factory()->create(['is_active' => false]);

        $response = $this->putJson(route('api.v1.admin.users.activate', $user->id));
        $response->assertStatus(403);
    }


    public function test_it_can_deactivate_user_account()
    {
        \Notification::fake();

        $user = User::factory()->create(['is_active' => true]);

        $response = $this->putJson(route('api.v1.admin.users.deactivate', $user->id));
        $response->assertNoContent();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_active' => false]);

        \Notification::assertSentTo(
            [$user],
            \App\Notifications\AccountDeactivationNotification::class
        );
    }

    public function test_deactivate_user_account_method_return_403_if_not_admin()
    {
        $this->actingAsUser();
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->putJson(route('api.v1.admin.users.deactivate', $user->id));
        $response->assertStatus(403);
    }

    public function test_can_get_profile_types()
    {
        $response = $this->getJson(route('api.v1.admin.users.profile-types'));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['value', 'label']
                ]
            ]);
    }

    public function test_destroy_deletes_user_and_related_data()
    {
        $user = User::factory()->create();
        $userId = $user->id;
        $user->preferences()->create(['user_id' => $userId, 'preferences' => []]);
        $user->servers()->create([
            'name' => 'Test Server',
            'instance_id' => 'i-1234567890abcdef0',
            'instance_type' => InstanceType::T2Micro,
            'os_family' => OsFamily::AmazonLinux,
        ]);
        
        $response = $this->deleteJson(route('api.v1.admin.users.destroy', $userId));
        $response->assertNoContent();
        $this->assertDatabaseMissing('users', ['id' => $userId]);
        $this->assertDatabaseMissing('user_preferences', ['user_id' => $userId]);
        $this->assertDatabaseMissing('servers', ['created_by' => $userId]);
    }

    public function test_destroy_return_403_if_not_admin()
    {
        $this->actingAsUser();
        $user = User::factory()->create();

        $response = $this->deleteJson(route('api.v1.admin.users.destroy', $user->id));
        $response->assertStatus(403);
    }

    public function test_destroy_return_404_if_user_not_found()
    {
        $response = $this->deleteJson(route('api.v1.admin.users.destroy', 999));
        $response->assertStatus(404);
    }

    public function test_validates_assign_permissions_request()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('api.v1.admin.users.assign-permissions', $user->id), [
            'permissions' => 'not-an-array',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['permissions']);
    }

    public function test_can_assign_permissions_to_user()
    {
        $user = User::factory()->create();
        //run create permissions command
        Artisan::call('app:create-permissions');

        $permissions = ['servers.*', 'security-groups.*'];

        $response = $this->postJson(route('api.v1.admin.users.assign-permissions', $user->id), [
            'permissions' => $permissions,
        ]);

        $response->assertNoContent();
        foreach ($permissions as $permission) {
            $this->assertTrue($user->fresh()->hasPermissionTo($permission));
        }
    }

    public function test_assign_permissions_return_403_if_not_admin()
    {
        $this->actingAsUser();
        $user = User::factory()->create();

        $response = $this->postJson(route('api.v1.admin.users.assign-permissions', $user->id), [
            'permissions' => ['servers.*'],
        ]);

        $response->assertStatus(403);
    }

    public function test_assign_permissions_return_404_if_user_not_found()
    {
        $response = $this->postJson(route('api.v1.admin.users.assign-permissions', 999), [
            'permissions' => ['servers.*'],
        ]);

        $response->assertStatus(404);
    }

    public function test_validates_assign_roles_request()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('api.v1.admin.users.assign-roles', $user->id), [
            'roles' => 'not-an-array',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['roles']);
    }

    public function test_can_get_roles()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'editor']);
        Role::create(['name' => 'admin']);
        $response = $this->getJson(route('api.v1.admin.users.roles', $user->id));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name', 'assigned']
                ]
            ]);
    }

    public function test_can_get_roles_assigned_to_user()
    {
        $user = User::factory()->create();
        $role1 = Role::create(['name' => 'manager']);
        $role2 = Role::create(['name' => 'editor']);
        $role3 = Role::create(['name' => 'admin']);

        $user->assignRole('editor');

        $response = $this->getJson(route('api.v1.admin.users.roles', $user->id));

        $response->assertOk()
           ->assertJson(fn (AssertableJson $json) =>
           $json->has('data', 3)
             ->where('data.0.id', $role1->id)
             ->where('data.0.name', 'manager')
             ->where('data.0.assigned', false)
             ->where('data.1.id', $role2->id)
             ->where('data.1.name', 'editor')
             ->where('data.1.assigned', true)
             ->where('data.2.id', $role3->id)
             ->where('data.2.name', 'admin')
             ->where('data.2.assigned', false)
          );
    }

    public function test_get_roles_return_403_if_not_admin()
    {
        $user = User::factory()->create();
        $this->actingAsUser();

        $response = $this->getJson(route('api.v1.admin.users.roles', $user->id));
        $response->assertStatus(403);
    }

    public function test_get_roles_return_404_if_user_not_found()
    {
        $response = $this->getJson(route('api.v1.admin.users.roles', 999));
        $response->assertStatus(404);
    }

    public function test_can_get_permissions()
    {
        $user = User::factory()->create();

        Artisan::call('app:create-permissions');

        $response = $this->getJson(route('api.v1.admin.users.permissions', $user->id));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
           $json->has('data')
             ->has('data.Servers.0.id')
             ->has('data.Servers.0.name')
             ->has('data.Servers.0.assigned')
             ->has('data.Security Groups.0.id')
             ->has('data.Security Groups.0.name')
          );
    }

    public function test_can_get_permissions_assigned_to_user()
    {
        $user = User::factory()->create();

        Artisan::call('app:create-permissions');

        $user->givePermissionTo('servers.show');
        $user->givePermissionTo('security-groups.create');

        $response = $this->getJson(route('api.v1.admin.users.permissions', $user->id));

        $response->assertOk()
           ->assertJson(fn (AssertableJson $json) =>
           $json->has('data')
             ->where('data.Servers.3.key', 'servers.show')
             ->where('data.Servers.3.assigned', true)
             ->where('data.Security Groups.2.key', 'security-groups.create')
             ->where('data.Security Groups.2.assigned', true)
          );
    }

    public function test_get_permissions_return_403_if_not_admin()
    {
        $user = User::factory()->create();
        $this->actingAsUser();

        $response = $this->getJson(route('api.v1.admin.users.permissions', $user->id));
        $response->assertStatus(403);
    }

    public function test_get_permissions_return_404_if_user_not_found()
    {
        $response = $this->getJson(route('api.v1.admin.users.permissions', 999));
        $response->assertStatus(404);
    }

}
