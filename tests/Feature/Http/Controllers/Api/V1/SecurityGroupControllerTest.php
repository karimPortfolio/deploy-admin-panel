<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\SecurityGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class SecurityGroupControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsUser();
    }

    public function test_can_list_security_groups_with_pagination()
    {
        SecurityGroup::factory(5)->create();

        $response = $this->getJson(route('api.v1.security-groups.index'));
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJson(fn (AssertableJson $json) => $json->has('data')
                ->has('meta')
                ->has('links')
            );
    }

    public function test_can_list_security_groups_by_vpc_id()
    {
        SecurityGroup::factory()->create(['vpc_id' => 'vpc-123']);

        $response = $this->getJson(route('api.v1.security-groups.index', ['filter[vpc_id]' => 'vpc-123']));
    
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'vpc_id' => 'vpc-123',
            ]);
    }

    public function test_can_search_security_groups()
    {
       $group = SecurityGroup::factory()->create([
        'name' => 'Searchable Group',
        'description' => 'Special description',
        'group_id' => 'sg-search',
        ]);

        $response = $this->getJson(route('api.v1.security-groups.index', ['search' => 'Searchable']));
    
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'name' => 'Searchable Group',
            ]);
    }

    public function test_can_create_security_group()
    {
        $data = [
            'name' => \Str::random(10),
            'description' => 'Test security group',
        ];

        $response = $this->postJson(route('api.v1.security-groups.store'), $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'vpc_id',
                    'group_id',
                ],
            ]);

        $this->assertDatabaseHas('security_groups', [
            'name' => $data['name'],
            'description' => $data['description'],
        ]);
    }

    public function test_can_show_security_group()
    {
        $securityGroup = SecurityGroup::factory()->create();

        $response = $this->getJson("/api/v1/security-groups/{$securityGroup->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'vpc_id',
                    'group_id',
                    'servers',
                ],
            ]);
    }


    public function test_cannot_delete_security_group_with_associated_servers()
    {
        $securityGroup = SecurityGroup::factory()
            ->hasServers(1)
            ->create();

        $response = $this->deleteJson("/api/v1/security-groups/{$securityGroup->id}");

        $response->assertUnprocessable()
            ->assertJson([
                'message' => 'This Security group is associated with servers and cannot be deleted.',
            ]);
    }
}
