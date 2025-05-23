<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\SecurityGroup;
use App\Services\SecurityGroupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SecurityGroupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->actingAsUser();
    }


    public function test_can_list_security_groups()
    {
        $securityGroup = SecurityGroup::factory(5)->create();

        $response = $this->getJson(route('api.v1.security-groups.index'));

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'description', 'vpc_id', 'group_id', 'servers_count']
                ]
            ]);
    }

    public function test_can_create_security_group()
    {
        $data = [
            'name' => \Str::random(10), 
            'description' => 'Test security group'
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
                ]
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
                    'servers'
                ]
            ]);
    }


    public function test_can_delete_security_group()
    {
        $securityGroup = SecurityGroup::factory()->create();

        $response = $this->deleteJson(route('api.v1.security-groups.destroy', $securityGroup));

        $response->assertNoContent();
        $this->assertDatabaseMissing('security_groups', ['id' => $securityGroup->id]);
    }



    public function test_cannot_delete_security_group_with_associated_servers()
    {
        $securityGroup = SecurityGroup::factory()
            ->hasServers(1)
            ->create();

        $response = $this->deleteJson("/api/v1/security-groups/{$securityGroup->id}");

        $response->assertUnprocessable()
            ->assertJson([
                'message' => 'Security group is associated with servers and cannot be deleted.'
            ]);
    }

}
