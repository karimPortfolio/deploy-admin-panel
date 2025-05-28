<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Enums\InstanceType;
use App\Enums\OsFamily;
use App\Jobs\CreateEc2InstanceJob;
use App\Models\SecurityGroup;
use App\Models\Server;
use App\Services\Ec2InstanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ServerControllerTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsUser();
    }
    public function test_can_list_servers()
    {
        $server = Server::factory()->create();

        $response = $this->getJson(route('api.v1.servers.index'));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'instance_type', 'status']]]);
    }

    public function test_can_create_server()
    {
        Bus::fake();

        $securityGroup = SecurityGroup::factory()->create();

        $data = [
            'name' => 'test-server',
            'instance_type' => InstanceType::T2Micro->value,
            'os_family' => OsFamily::Ubuntu->value,
            'vpc_id' => 'vpc-123456',
            'security_group_id' => $securityGroup->id
        ];

        $response = $this->postJson(route('api.v1.servers.store'), $data);

        $response->assertCreated()
            ->assertJsonStructure(['data', 'message']);

        $this->assertDatabaseHas('servers', [
            'name' => 'test-server',
            'instance_type' => InstanceType::T2Micro->value,
            'security_group_id' => $securityGroup->id
        ]);

        Bus::assertDispatched(CreateEc2InstanceJob::class);
    }

    public function test_can_show_server()
    {
        $server = Server::factory()->create();

        $response = $this->getJson(route('api.v1.servers.show', $server));

        $response->assertOk()
            ->assertJsonStructure(['data' => ['id', 'name', 'instance_type', 'status']]);
    }

    public function test_show_method_returns_404_for_non_existent_server()
    {
        $response = $this->getJson(route('api.v1.servers.show', ['server' => 999]));

        $response->assertNotFound();
    }

    public function test_can_start_server()
    {
        $server = Server::factory()->create(['instance_id' => 'i-123456']);

        $this->mock(Ec2InstanceService::class, function ($mock) {
            $mock->shouldReceive('startInstance')
                ->once()
                ->andReturn(['StartingInstances' => [['CurrentState' => ['Name' => 'pending']]]]);
        });

        $response = $this->putJson(route('api.v1.servers.start', $server));

        $response->assertNoContent();
        $this->assertEquals('running', $server->fresh()->status);
    }

    public function test_start_method_returns_404_for_non_existent_server()
    {
        $response = $this->putJson(route('api.v1.servers.start', ['server' => 999]));

        $response->assertNotFound();
    }

    public function test_can_stop_server()
    {
        $server = Server::factory()->create(['instance_id' => 'i-123456']);

        $this->mock(Ec2InstanceService::class, function ($mock) {
            $mock->shouldReceive('stopInstance')
                ->once()
                ->andReturn(['StoppingInstances' => [['CurrentState' => ['Name' => 'stopping']]]]);
        });

        $response = $this->putJson(route('api.v1.servers.stop', $server));

        $response->assertNoContent();
        $this->assertEquals('stopped', $server->fresh()->status);
    }

    public function test_stop_method_returns_404_for_non_existent_server()
    {
        $response = $this->putJson(route('api.v1.servers.stop', ['server' => 999]));

        $response->assertNotFound();
    }

    public function test_can_delete_server()
    {
        $server = Server::factory()->create(['instance_id' => 'i-123456']);

        $this->mock(Ec2InstanceService::class, function ($mock) {
            $mock->shouldReceive('terminateInstance')
                ->once()
                ->andReturn(['TerminatingInstances' => [['CurrentState' => ['Name' => 'shutting-down']]]]);
        });

        $response = $this->deleteJson(route('api.v1.servers.destroy', $server));

        $response->assertNoContent();
        $this->assertDatabaseMissing('servers', ['id' => $server->id]);
    }

    public function test_delete_method_returns_404_for_non_existent_server()
    {
        $response = $this->deleteJson(route('api.v1.servers.destroy', ['server' => 999]));

        $response->assertNotFound();
    }

}
