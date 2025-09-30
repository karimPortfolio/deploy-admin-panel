<?php

namespace Tests\Feature\Http\Controllers\Api\V1\Admin;

use App\Enums\InstanceType;
use App\Enums\OsFamily;
use App\Enums\ServerStatus;
use App\Models\SecurityGroup;
use App\Models\Server;
use App\Models\SshKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }
    
    public function test_can_get_total_users_count()
    {
        User::factory()->count(5)->create();

        $response = $this->getJson(route('api.v1.admin.dashboard.total-users'));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.total', 6)
                    ->etc()
            );
    }


    public function test_can_get_total_servers_count()
    {
        $user = User::factory()->create();

        $user->servers()->createMany(
            Server::factory()->count(3)->make([
                'instance_type' => InstanceType::T2Micro,
                'os_family' => OsFamily::AmazonLinux,
            ])->toArray()
        );

        $response = $this->getJson(route('api.v1.admin.dashboard.total-servers'));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.total', 3)
                    ->etc()
            );
    }

    public function test_can_get_total_security_groups_count()
    {
        SecurityGroup::factory()->count(4)->create();
        $response = $this->getJson(route('api.v1.admin.dashboard.total-security-groups'));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.total', 4)
                    ->etc()
            );
    }

    public function test_can_get_total_ssh_keys_count()
    {
        SshKey::factory()->count(7)->create();
        $response = $this->getJson(route('api.v1.admin.dashboard.total-sshkeys'));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.total', 7)
                    ->etc()
            );
    }


    public function test_can_get_monthly_servers_total()
    {
        $user = User::factory()->create();

        $user->servers()->createMany(
            Server::factory()->count(3)->make([
                'instance_type' => InstanceType::T2Micro,
                'os_family' => OsFamily::AmazonLinux,
                'created_at' => now()->subMonths(2),
            ])->toArray()
        );

        $user->servers()->createMany(
            Server::factory()->count(2)->make([
                'instance_type' => InstanceType::T2Micro,
                'os_family' => OsFamily::AmazonLinux,
                'created_at' => now()->subMonths(1),
            ])->toArray()
        );

        $response = $this->getJson(route('api.v1.admin.dashboard.monthly-servers-total', [
            'filter' => [
                'year' => now()->year,
            ]
        ]));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['month', 'total']
                ]
                ]);
    }

    public function test_can_get_monthly_security_groups_total()
    {
        $user = User::factory()->create();

        $user->securityGroups()->createMany(
            SecurityGroup::factory()->count(3)->make([
                'created_at' => now()->subMonths(2),
            ])->toArray()
        );

        $user->securityGroups()->createMany(
            SecurityGroup::factory()->count(1)->make([
                'created_at' => now()->subMonths(1),
            ])->toArray()
        );

        $response = $this->getJson(route('api.v1.admin.dashboard.monthly-security-groups-total', [
            'filter' => [
                'year' => now()->year,
            ]
        ]));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['month', 'total']
                ]
            ]);
    }


    public function test_can_get_total_servers_by_security_groups()
    {
        $user = User::factory()->create();

        $securityGroup1 = $user->securityGroups()->create(
            SecurityGroup::factory()->make()->toArray()
        );

        $securityGroup2 = $user->securityGroups()->create(
            SecurityGroup::factory()->make()->toArray()
        );

        $user->servers()->createMany(
            Server::factory()->count(2)->make([
                'instance_type' => InstanceType::T2Micro,
                'os_family' => OsFamily::AmazonLinux,
                'security_group_id' => $securityGroup1->id,
            ])->toArray()
        );

        $user->servers()->createMany(
            Server::factory()->count(2)->make([
                'instance_type' => InstanceType::T2Micro,
                'os_family' => OsFamily::AmazonLinux,
                'security_group_id' => $securityGroup2->id,
            ])->toArray()
        );

        $response = $this->getJson(route('api.v1.admin.dashboard.servers-by-security-groups'));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.0.total', 2)
                    ->where('data.1.total', 2)
                    ->etc()
            );
    }


    public function test_can_get_total_servers_by_status()
    {
        $user = User::factory()->create();

        $user->servers()->createMany(
            Server::factory()->count(3)->make([
                'instance_type' => InstanceType::T2Micro,
                'os_family' => OsFamily::AmazonLinux,
                'status' => 'running',
            ])->toArray()
        );

        $user->servers()->createMany(
            Server::factory()->count(1)->make([
                'instance_type' => InstanceType::T2Micro,
                'os_family' => OsFamily::AmazonLinux,
                'status' => 'stopped',
            ])->toArray()
        );

        $response = $this->getJson(route('api.v1.admin.dashboard.servers-by-status'));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.0.status', ServerStatus::RUNNING->label())
                    ->where('data.0.total', 3)
                    ->where('data.1.status', ServerStatus::STOPPED->label())
                    ->where('data.1.total', 1)
                    ->etc()
            );
    }

    public function test_can_get_total_servers_by_status_returns_403_when_user_not_admin()
    {
        $this->actingAsUser();

        $response = $this->getJson(route('api.v1.admin.dashboard.servers-by-status'));

        $response->assertForbidden();
    }

}
