<?php

namespace Tests\Feature\Http\Controllers\Api\V1\Admin;

use App\Enums\InstanceType;
use App\Enums\OsFamily;
use App\Models\SshKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class SecurityGroupControllerTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }
    
   
    public function test_can_list_security_groups_with_pagination()
    {
        $response = $this->getJson(route('api.v1.admin.security-groups.index'));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                 ->has('meta')
                 ->has('links')
        );
    }

    public function test_can_list_security_groups_by_vpc_id()
    {
        $group = \App\Models\SecurityGroup::factory()->create(['vpc_id' => 'vpc-123']);

        $response = $this->getJson(route('api.v1.admin.security-groups.index', ['filter[vpc_id]' => 'vpc-123']));
    
        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data', 1)
                     ->where('data.0.vpc_id', 'vpc-123')
                     ->etc()
            );
    }

    public function test_can_search_security_groups()
    {
       \App\Models\SecurityGroup::factory()->create([
        'name' => 'Searchable Group',
        'description' => 'Special description',
        'group_id' => 'sg-search',
        ]);

        $response = $this->getJson(route('api.v1.admin.security-groups.index', ['search' => 'Searchable']));
    
        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data', 1)
                     ->where('data.0.name', 'Searchable Group')
                     ->etc()
            );
    }
  
    public function test_index_method_returns_403_for_non_admin_users()
    {
        $this->actingAsUser();

        $response = $this->getJson(route('api.v1.admin.security-groups.index'));

        $response->assertStatus(403);
    }


    public function test_can_view_security_group_details()
    {
        $group = \App\Models\SecurityGroup::factory()->create();

        $response = $this->getJson(route('api.v1.admin.security-groups.show', $group->id));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.id', $group->id)
                     ->where('data.name', $group->name)
                     ->has('data.servers')
                     ->has('data.created_by')
                     ->etc()
            );
    }


    public function test_show_method_returns_403_for_non_admin_users()
    {
        $group = \App\Models\SecurityGroup::factory()->create();

        $this->actingAsUser();

        $response = $this->getJson(route('api.v1.admin.security-groups.show', $group->id));

        $response->assertStatus(403);
    }

    public function test_cannot_delete_security_group_associated_with_servers()
    {
        $group = \App\Models\SecurityGroup::factory()->create();
        $server = \App\Models\Server::factory()->create(['security_group_id' => $group->id]);

        $response = $this->deleteJson(route('api.v1.admin.security-groups.destroy', $group->id));

        $response->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'This Security group is associated with servers and cannot be deleted.',
            ]);
    }

    public function test_destroy_method_returns_403_for_non_admin_users()
    {
        $group = \App\Models\SecurityGroup::factory()->create();

        $this->actingAsUser();

        $response = $this->deleteJson(route('api.v1.admin.security-groups.destroy', $group->id));

        $response->assertStatus(403);
    }

}
