<?php

namespace Tests\Feature\Http\Controllers\Api\V1\Admin;

use App\Enums\DBStatus;
use App\Enums\ServerStatus;
use App\Models\RdsDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;


class RdsDatabaseControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    public function test_index_returns_403_for_users_without_proper_permissions()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson(route('api.v1.admin.rds-databases.index'));

        $response->assertForbidden();
    }

    public function test_can_list_rds_databases_with_pagination()
    {
        $server = RdsDatabase::factory()->create();

        $response = $this->getJson(route('api.v1.admin.rds-databases.index'));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                 ->has('meta')
                 ->has('links')
        );
    }

    public function test_can_list_rds_databases_with_filters()
    {
        $server1 = RdsDatabase::factory()->create(['engine' => 'mysql']);
        $server2 = RdsDatabase::factory()->create(['engine' => 'postgres']);

        $response = $this->getJson(route('api.v1.admin.rds-databases.index', ['filter[engine]' => 'mysql']));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson(fn (AssertableJson $json) =>
            $json->has('data', 1)
                 ->where('data.0.id', $server1->id)
                 ->etc()
        );
    }

    public function test_can_search_rds_databases()
    {
        $server1 = RdsDatabase::factory()->create(['db_name' => 'testdatabase']);
        $server2 = RdsDatabase::factory()->create(['db_name' => 'anotherdb']);

        $response = $this->getJson(route('api.v1.admin.rds-databases.index', ['search' => 'test']));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson(fn (AssertableJson $json) =>
            $json->has('data', 1)
                 ->where('data.0.id', $server1->id)
                 ->etc()
        );
    }

    public function test_can_sort_rds_databases()
    {
        $server1 = RdsDatabase::factory()->create(['allocated_storage' => 20]);
        $server2 = RdsDatabase::factory()->create(['allocated_storage' => 40]);

        $response = $this->getJson(route('api.v1.admin.rds-databases.index', ['sort' => 'allocated_storage']));

        $response->assertOk()
            ->assertJsonPath('data.0.id', $server1->id)
            ->assertJsonPath('data.1.id', $server2->id);
    }


    public function test_show_returns_403_for_users_without_proper_permissions()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $rdsDatabase = RdsDatabase::factory()->create();

        $response = $this->getJson(route('api.v1.admin.rds-databases.show', $rdsDatabase->id));

        $response->assertForbidden();
    }

    public function test_can_show_rds_database_details()
    {
        $server = RdsDatabase::factory()->create();

        $response = $this->getJson(route('api.v1.admin.rds-databases.show', $server->id));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'db_instance_identifier',
                    'engine',
                    'db_name',
                    'allocated_storage',
                    'storage_type',
                    'storage_encrypted',
                    'publicly_accessible',
                    'availability_zone',
                    'multi_az',
                    'backup_retention_period',
                    'instance_create_time',
                    'status',
                    'engine_version',
                    'endpoint_address',
                    'endpoint_port',
                    'latest_restorable_time',
                    'security_group',
                    'servers',
                    'created_at',
                ],
            ]);
    }

    public function test_show_returns_404_for_non_existent_rds_database()
    {
        $response = $this->getJson(route('api.v1.admin.rds-databases.show', 9999));

        $response->assertNotFound();
    }

    public function test_destroy_returns_404_for_non_existent_rds_database()
    {
        $response = $this->deleteJson(route('api.v1.admin.rds-databases.destroy', 9999));

        $response->assertNotFound();
    }

    public function test_destroy_prevents_deletion_if_associated_servers_exist()
    {
        $user = \App\Models\User::factory()->create();
        $rdsDatabase = RdsDatabase::factory()->create();
        $server = \App\Models\Server::factory()->create([
            'status' => ServerStatus::RUNNING,
        ]);

        \DB::table('rds_database_server')->insert([
            'rds_database_id' => $rdsDatabase->id,
            'server_id' => $server->id,
            'is_primary' => true,
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson(route('api.v1.admin.rds-databases.destroy', $rdsDatabase->id));

        $response->assertUnprocessable()
            ->assertJson([
                'message' => __('messages.rds_databases.associated_servers_msg'),
            ]);
    }

    public function test_destroy_returns_403_for_users_without_proper_permissions()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $rdsDatabase = RdsDatabase::factory()->create();

        $response = $this->deleteJson(route('api.v1.admin.rds-databases.destroy', $rdsDatabase->id));

        $response->assertForbidden();
    }


    public function test_can_get_database_engines()
    {
        $response = $this->getJson(route('api.v1.admin.rds-databases.engines'));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'value',
                        'label',
                        'icon',
                        'description',
                    ],
                ],
            ]);
    }


    public function test_can_get_database_instance_classes()
    {
        $response = $this->getJson(route('api.v1.admin.rds-databases.instance-classes'));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'value',
                        'description',
                    ],
                ],
            ]);
    }

    public function test_can_get_database_storage_types()
    {
        $response = $this->getJson(route('api.v1.admin.rds-databases.storage-types'));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'value',
                        'description',
                    ],
                ],
            ]);
    }

    public function test_can_get_database_statuses()
    {
        $response = $this->getJson(route('api.v1.admin.rds-databases.statuses'));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'value',
                        'label',
                        'color'
                    ],
                ],
            ]);
    }

    public function test_can_get_servers()
    {
        $server = \App\Models\Server::factory()->create();

        $response = $this->getJson(route('api.v1.admin.rds-databases.servers'));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'instance_id',
                        'name',
                    ],
                ],
            ]);
    }

}