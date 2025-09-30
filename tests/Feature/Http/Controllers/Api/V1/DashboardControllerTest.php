<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\SecurityGroup;
use App\Models\Server;
use App\Models\SshKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsUser();
    }

    public function test_it_can_get_total_servers_count()
    {
        Server::factory()->count(5)->create();

        $response = $this->getJson(route('api.v1.dashboard.total-servers'));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.total', 5)
                    ->etc()
            );
    }

    public function test_it_can_get_total_security_groups_count()
    {
        SecurityGroup::factory()->count(3)->create();

        $response = $this->getJson(route('api.v1.dashboard.total-security-groups'));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.total', 3)
                    ->etc()
            );
    }

    public function test_it_can_get_total_ssh_keys_count()
    {
        SshKey::factory()->count(4)->create();

        $response = $this->getJson(route('api.v1.dashboard.total-sshkeys'));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.total', 4)
                    ->etc()
            );
    }

    public function test_it_can_get_monthly_servers_total()
    {
        $year = date('Y');
        Server::factory()->create(['created_at' => "$year-01-15"]);
        Server::factory()->create(['created_at' => "$year-01-20"]);
        Server::factory()->create(['created_at' => "$year-02-10"]);
        Server::factory()->create(['created_at' => "$year-03-05"]);
        Server::factory()->create(['created_at' => "$year-03-25"]);
        Server::factory()->create(['created_at' => "$year-03-30"]);

        $response = $this->getJson(route('api.v1.dashboard.monthly-servers-total', ['filter.year' => $year]));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['month', 'total']
                ]
                ]);
    }

    public function test_it_returns_zero_monthly_servers_total_for_year_with_no_data()
    {
        $year = date('Y') - 1; // Previous year with no data

        $response = $this->getJson(route('api.v1.dashboard.monthly-servers-total', ['filter.year' => $year]));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data', fn ($data) =>
                    count($data) === 12 && collect($data)->every(fn ($monthData) => $monthData['total'] === 0)
                )->etc()
            );
    }

    public function test_it_can_get_monthly_security_groups_total()
    {
        $year = date('Y');
        SecurityGroup::factory()->create(['created_at' => "$year-01-15"]);
        SecurityGroup::factory()->create(['created_at' => "$year-01-20"]);
        SecurityGroup::factory()->create(['created_at' => "$year-02-10"]);
        SecurityGroup::factory()->create(['created_at' => "$year-03-05"]);
        SecurityGroup::factory()->create(['created_at' => "$year-03-25"]);
        SecurityGroup::factory()->create(['created_at' => "$year-03-30"]);

        $response = $this->getJson(route('api.v1.dashboard.monthly-security-groups-total', ['filter.year' => $year]));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['month', 'total']
                ]
            ]);
    }

    public function test_it_returns_zero_monthly_security_groups_total_for_year_with_no_data()
    {
        $year = date('Y') - 1; // Previous year with no data

        $response = $this->getJson(route('api.v1.dashboard.monthly-security-groups-total', ['filter.year' => $year]));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data', fn ($data) =>
                    count($data) === 12 && collect($data)->every(fn ($monthData) => $monthData['total'] === 0)
                )->etc()
            );
    }

    public function test_it_can_get_total_servers_by_security_groups()
    {
        $securityGroup1 = SecurityGroup::factory()->create(['name' => 'Group 1']);
        $securityGroup2 = SecurityGroup::factory()->create(['name' => 'Group 2']);

        Server::factory()->count(3)->create(['security_group_id' => $securityGroup1->id]);
        Server::factory()->count(2)->create(['security_group_id' => $securityGroup2->id]);

        $response = $this->getJson(route('api.v1.dashboard.servers-by-security-groups'));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['securityGroup', 'total']
                ]
            ]);
    }


    public function test_it_can_get_total_servers_by_status()
    {
        Server::factory()->count(4)->create(['status' => 'running']);
        Server::factory()->count(3)->create(['status' => 'stopped']);
        Server::factory()->count(2)->create(['status' => 'terminated']);

        $response = $this->getJson(route('api.v1.dashboard.servers-by-status'));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['status', 'total']
                ]
            ]);
    }
   
}
