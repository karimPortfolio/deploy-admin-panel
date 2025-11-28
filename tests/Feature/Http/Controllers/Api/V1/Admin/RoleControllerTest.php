<?php

namespace Tests\Feature\Http\Controllers\Api\V1\Admin;

use App\Enums\InstanceType;
use App\Enums\OsFamily;
use App\Models\SshKey;
use App\Models\User;
use App\Services\RoleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->actingAsAdmin();
    }

    private function guard(): string
    {
        return config('auth.defaults.guard') ?? 'web';
    }
    
    public function test_can_list_roles_with_pagination()
    {
        Role::query()->delete();

        for ($i = 1; $i <= 5; $i++) {
            Role::create(['name' => 'Role ' . $i]);
        }

        $response = $this->getJson(route('api.v1.admin.roles.index'));

        $response->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                 ->has('meta')
                 ->has('links')
        );
    }

    public function test_index_return_permissions_count()
    {
        $role = Role::create(['name' => 'Test Role']);
        Permission::create(['name' => 'edit articles']);
        Permission::create(['name' => 'delete articles']);
        $role->givePermissionTo('edit articles', 'delete articles');

        $response = $this->getJson(route('api.v1.admin.roles.index'));

        $response->assertOk()
            ->assertJsonPath('data.0.permissions_count', 2);
    }


    public function test_can_sort_roles_by_name()
    {
        Role::query()->delete();

        for ($i = 1; $i <= 3; $i++) {
            $names = ['Gamma Role', 'Alpha Role', 'Beta Role'];
            Role::create(['name' => $names[$i - 1]]);
        }

        $response = $this->getJson(route('api.v1.admin.roles.index', ['sort' => 'name']));

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Alpha Role')
            ->assertJsonPath('data.1.name', 'Beta Role')
            ->assertJsonPath('data.2.name', 'Gamma Role');
    }


    public function test_can_search_roles()
    {
        Role::query()->delete();

        Role::create(['name' => 'Administrator']);
        Role::create(['name' => 'Editor']);
        Role::create(['name' => 'Viewer']);

        $response = $this->getJson(route('api.v1.admin.roles.index', ['search' => 'Edit']));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Editor');
    }


    public function test_index_method_returns_403_for_non_admin_users()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->getJson(route('api.v1.admin.roles.index'));
        $response->assertForbidden();
    }

   public function test_can_get_role_details()
    {
        $role = Role::create(['name' => 'Test Role']);
        Permission::create(['name' => 'edit articles']);
        $role->givePermissionTo('edit articles');

        $response = $this->getJson(route('api.v1.admin.roles.show', ['role' => $role->id]));

        $response->assertOk()
            ->assertJsonPath('data.name', 'Test Role')
            ->assertJsonPath('data.permissions.0.name', 'edit articles');
    }

    public function test_show_method_returns_403_for_non_admin_users()
    {
        $role = Role::create(['name' => 'Test Role']);
        $this->actingAs(User::factory()->create());
        $response = $this->getJson(route('api.v1.admin.roles.show', ['role' => $role->id]));
        $response->assertForbidden();
    }

    public function test_show_method_returns_404_for_non_existent_role()
    {
        $response = $this->getJson(route('api.v1.admin.roles.show', ['role' => 999]));
        $response->assertNotFound();
    }


    public function test_store_method_validates_input()
    {
        $response = $this->postJson(route('api.v1.admin.roles.store'), []);
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_can_create_role()
    {
        $response = $this->postJson(route('api.v1.admin.roles.store'), [
            'name' => 'New Role',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'New Role');

        $this->assertDatabaseHas('roles', [
            'name' => 'New Role',
        ]);
    }

    public function test_store_method_returns_403_for_non_admin_users()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->postJson(route('api.v1.admin.roles.store'), [
            'name' => 'Another Role',
        ]);
        $response->assertForbidden();
    }

    public function test_update_method_validates_input()
    {
        $role = Role::create(['name' => 'Old Role']);

        $response = $this->putJson(route('api.v1.admin.roles.update', ['role' => $role->id]), [
            'name' => '',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_can_update_role()
    {
        $role = Role::create(['name' => 'Old Role']);

        $response = $this->putJson(route('api.v1.admin.roles.update', ['role' => $role->id]), [
            'name' => 'Updated Role',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Role');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Updated Role',
        ]);
    }

    public function test_update_method_returns_403_for_non_admin_users()
    {
        $role = Role::create(['name' => 'Old Role']);
        $this->actingAs(User::factory()->create());
        $response = $this->putJson(route('api.v1.admin.roles.update', ['role' => $role->id]), [
            'name' => 'Attempted Update',
        ]);
        $response->assertForbidden();
    }

    public function test_can_delete_role()
    {
        $role = Role::create(['name' => 'Role to Delete']);

        $response = $this->deleteJson(route('api.v1.admin.roles.destroy', ['role' => $role->id]));

        $response->assertNoContent();

        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }

    public function test_delete_method_returns_404_for_non_existent_role()
    {
        $response = $this->deleteJson(route('api.v1.admin.roles.destroy', ['role' => 999]));
        $response->assertNotFound();
    }

    public function test_destroy_method_returns_403_for_non_admin_users()
    {
        $role = Role::create(['name' => 'Role to Delete']);
        $this->actingAs(User::factory()->create());
        $response = $this->deleteJson(route('api.v1.admin.roles.destroy', ['role' => $role->id]));
        $response->assertForbidden();
    }


    public function test_get_permissions_groups_permissions_and_assignment_flags()
    {
        $guard = 'sanctum';
        $permission1 = Permission::create(['name' => 'servers.create', 'guard_name' => $guard]);
        $permission2 = Permission::create(['name' => 'servers.delete', 'guard_name' => $guard]);
        $permission3 = Permission::create(['name' => 'rds-databases.view', 'guard_name' => $guard]);
        $role = Role::create(['name' => 'Test Role', 'guard_name' => $guard]);

        $response = $this->getJson(route('api.v1.admin.roles.get-permissions', ['role' => $role->id]));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data.Servers', fn (AssertableJson $json) =>
                    $json->has(0, fn (AssertableJson $json) =>
                        $json
                             ->where('id', $permission1->id)
                             ->where('key', 'servers.create')
                             ->where('name', 'create servers')
                             ->where('assigned', false)
                    )
                    ->has(1, fn (AssertableJson $json) =>
                        $json->where('name', 'delete servers')
                             ->where('key', 'servers.delete')
                             ->where('id', $permission2->id)
                             ->where('assigned', false)
                    )
                )
                ->has('data.RDS Databases', fn (AssertableJson $json) =>
                    $json->has(0, fn (AssertableJson $json) =>
                        $json->where('name', 'view rds databases')
                             ->where('key', 'rds-databases.view')
                             ->where('id', $permission3->id)
                             ->where('assigned', false)
                    )
                )
            );
    }

    public function test_get_permissions_method_returns_403_for_non_admin_users()
    {
        $role = Role::create(['name' => 'Test Role']);
        $this->actingAs(User::factory()->create());
        $response = $this->getJson(route('api.v1.admin.roles.get-permissions', ['role' => $role->id]));
        $response->assertForbidden();
    }


    public function test_assign_permissions_method_validates_input()
    {
        $role = Role::create(['name' => 'Permission Role']);

        $response = $this->postJson(route('api.v1.admin.roles.assign-permissions', ['role' => $role->id]), [
            'permissions' => 'not-an-array',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['permissions']);
    }


    public function test_can_assign_permissions_to_role()
    {
        $guard = 'sanctum';
        $permission1 = Permission::create(['name' => 'servers.*', 'guard_name' => $guard]);
        $permission2 = Permission::create(['name' => 'rds_databases.*', 'guard_name' => $guard]);
        $role = Role::create(['name' => 'admin', 'guard_name' => $guard]);

        $response = $this->postJson(route('api.v1.admin.roles.assign-permissions', ['role' => $role->id]), [
            'permissions' => [$permission1->name, $permission2->name],
        ]);

        $response->assertOk();   
    }


    public function test_assign_permissions_method_returns_403_for_non_admin_users()
    {
        $role = Role::create(['name' => 'Permission Role']);
        $this->actingAs(User::factory()->create());
        $response = $this->postJson(route('api.v1.admin.roles.assign-permissions', ['role' => $role->id]), [
            'permissions' => ['some.permission'],
        ]);
        $response->assertForbidden();
    }
}
