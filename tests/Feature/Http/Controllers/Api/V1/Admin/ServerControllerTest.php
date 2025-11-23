<?php

namespace Tests\Feature\Http\Controllers\Api\V1\Admin;

use App\Enums\InstanceType;
use App\Enums\OsFamily;
use App\Jobs\CreateEc2InstanceJob;
use App\Models\SecurityGroup;
use App\Models\Server;
use App\Services\Ec2InstanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ServerControllerTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }
    public function test_can_list_servers_with_pagination()
    {
        $server = Server::factory()->create();

        $response = $this->getJson(route('api.v1.admin.servers.index'));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                 ->has('meta')
                 ->has('links')
        );
    }

    public function test_can_filter_by_instance_type()
    {
        $server1 = Server::factory()->create(['instance_type' => InstanceType::T2Micro->value]);
        $server2 = Server::factory()->create(['instance_type' => InstanceType::T3Micro->value]);

        $response = $this->getJson(route('api.v1.admin.servers.index', ['filter[instance_type]' => InstanceType::T2Micro->value]));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                 ->has('meta')
                 ->has('links')
        );
    }

    public function test_can_filter_by_security_group()
    {
        $securityGroup1 = SecurityGroup::factory()->create();
        $securityGroup2 = SecurityGroup::factory()->create();

        $server1 = Server::factory()->create(['security_group_id' => $securityGroup1->id]);
        $server2 = Server::factory()->create(['security_group_id' => $securityGroup2->id]);

        $response = $this->getJson(route('api.v1.admin.servers.index', ['filter[security_group_id]' => $securityGroup1->group_id]));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                 ->has('meta')
                 ->has('links')
        );
    }

    public function test_can_search_servers()
    {
        $server1 = Server::factory()->create(['name' => 'TestServerOne']);
        $server2 = Server::factory()->create(['name' => 'AnotherServer']);

        $response = $this->getJson(route('api.v1.admin.servers.index', ['search' => 'TestServer']));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                 ->has('meta')
                 ->has('links')
        );
    }

    public function test_can_sort_servers()
    {
        $server1 = Server::factory()->create(['name' => 'AlphaServer']);
        $server2 = Server::factory()->create(['name' => 'BetaServer']);

        $response = $this->getJson(route('api.v1.admin.servers.index', ['sort' => 'name']));

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'AlphaServer')
            ->assertJsonPath('data.1.name', 'BetaServer');
    }

    public function test_index_method_returns_403_when_user_not_admin()
    {
        $this->actingAsUser();

        $response = $this->getJson(route('api.v1.admin.servers.index'));

        $response->assertForbidden();
    }

    public function test_can_show_server()
    {
        $server = Server::factory()->create();

        $response = $this->getJson(route('api.v1.admin.servers.show', $server));

        $response->assertOk()
            ->assertJsonStructure(['data' => ['id', 'name', 'instance_type', 'status']]);
    }

    public function test_show_method_returns_404_for_non_existent_server()
    {
        $response = $this->getJson(route('api.v1.admin.servers.show', ['server' => 999]));

        $response->assertNotFound();
    }

    // public function test_can_start_server()
    // {
    //     $server = Server::factory()->create(['instance_id' => 'i-123456']);

    //     $this->mock(Ec2InstanceService::class, function ($mock) {
    //         $mock->shouldReceive('startInstance')
    //             ->once()
    //             ->andReturn(['StartingInstances' => [['CurrentState' => ['Name' => 'pending']]]]);
    //     });

    //     $response = $this->putJson(route('api.v1.admin.servers.start', $server));

    //     $response->assertNoContent();
    //     $this->assertEquals('running', $server->fresh()->status);
    // }

    public function test_start_method_returns_404_for_non_existent_server()
    {
        $response = $this->putJson(route('api.v1.admin.servers.start', ['server' => 999]));

        $response->assertNotFound();
    }

    // public function test_can_stop_server()
    // {
    //     $server = Server::factory()->create(['instance_id' => 'i-123456']);

    //     $this->mock(Ec2InstanceService::class, function ($mock) {
    //         $mock->shouldReceive('stopInstance')
    //             ->once()
    //             ->andReturn(['StoppingInstances' => [['CurrentState' => ['Name' => 'stopping']]]]);
    //     });

    //     $response = $this->putJson(route('api.v1.admin.servers.stop', $server));

    //     $response->assertNoContent();
    //     $this->assertEquals('stopped', $server->fresh()->status);
    // }

    public function test_stop_method_returns_404_for_non_existent_server()
    {
        $response = $this->putJson(route('api.v1.admin.servers.stop', ['server' => 999]));

        $response->assertNotFound();
    }

    // public function test_can_delete_server()
    // {
    //     $server = Server::factory()->create(['instance_id' => 'i-123456']);

    //     $this->mock(Ec2InstanceService::class, function ($mock) {
    //         $mock->shouldReceive('terminateInstance')
    //             ->once()
    //             ->andReturn(['TerminatingInstances' => [['CurrentState' => ['Name' => 'shutting-down']]]]);
    //     });

    //     $response = $this->deleteJson(route('api.v1.admin.servers.destroy', $server));

    //     $response->assertNoContent();
    //     $this->assertDatabaseMissing('servers', ['id' => $server->id]);
    // }

    public function test_delete_method_returns_404_for_non_existent_server()
    {
        $response = $this->deleteJson(route('api.v1.admin.servers.destroy', ['server' => 999]));

        $response->assertNotFound();
    }

    public function test_can_get_instance_types()
    {
        $response = $this->getJson(route('api.v1.admin.servers.instance-types'));

        $response->assertOk()
            ->assertJsonStructure(['data'])
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')
            );
    }

    public function test_can_get_os_families()
    {
        $response = $this->getJson(route('api.v1.admin.servers.os-families'));

        $response->assertOk()
            ->assertJsonStructure(['data'])
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')
            );
    }

    public function test_can_get_server_statuses()
    {
        $response = $this->getJson(route('api.v1.admin.servers.statuses'));

        $response->assertOk()
            ->assertJsonStructure(['data'])
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')
            );
    }

}
