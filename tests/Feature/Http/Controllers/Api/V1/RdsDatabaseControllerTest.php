<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

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
        $this->actingAsUser();
    }

    public function test_can_list_rds_databases_with_pagination()
    {
        $server = RdsDatabase::factory()->create();

        $response = $this->getJson(route('api.v1.rds-databases.index'));

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

        $response = $this->getJson(route('api.v1.rds-databases.index', ['filter[engine]' => 'mysql']));

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

        $response = $this->getJson(route('api.v1.rds-databases.index', ['search' => 'test']));

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
        $server1 = RdsDatabase::factory()->create(['db_name' => 'aaa']);
        $server2 = RdsDatabase::factory()->create(['db_name' => 'bbb']);

        $response = $this->getJson(route('api.v1.rds-databases.index', ['sort' => 'db_name']));

        $response->assertOk()
            ->assertJsonPath('data.0.id', $server1->id)
            ->assertJsonPath('data.1.id', $server2->id);
    }

    public function test_can_show_rds_database_details()
    {
        $server = RdsDatabase::factory()->create();

        $response = $this->getJson(route('api.v1.rds-databases.show', $server->id));

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
        $response = $this->getJson(route('api.v1.rds-databases.show', 9999));

        $response->assertNotFound();
    }

    public function test_store_handles_validation_errors()
    {
        $response = $this->postJson(route('api.v1.rds-databases.store'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'db_instance_identifier',
                'engine',
                'db_instance_class',
                'db_name',
                'master_username',
                'master_password',
                'allocated_storage',
                'storage_type',
                'vpc_security_group',
            ]);
    }


    public function test_store_rds_database_successfully()
    {
        $payload = [
            'db_instance_identifier' => 'test-db-instance',
            'db_instance_class' => 'db.t3.micro',
            'engine' => 'mysql',
            'db_name' => 'testdatabase',
            'master_username' => 'admin',
            'master_password' => 'SecurePassword123!',
            'storage_type' => 'gp2',
            'allocated_storage' => 20,
            'vpc_security_group' => \App\Models\SecurityGroup::factory()->create()->group_id,
            'backup_retention_period' => 7,
            'publicly_accessible' => false,
            'storage_encrypted' => true,
            'multi_az' => false,
        ];

        \Queue::fake();

        $response = $this->postJson(route('api.v1.rds-databases.store'), $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'db_instance_identifier',
                    'engine',
                    'db_name',
                ],
            ]);

        $this->assertDatabaseHas('rds_databases', [
            'db_instance_identifier' => 'test-db-instance',
            'db_name' => 'testdatabase',
            'engine' => 'mysql',
            'status' => 'pending',
        ]);

        \Queue::assertPushed(\App\Jobs\CreateRdsDatabaseJob::class);
    }

    public function test_attach_handles_validation_errors()
    {
        $response = $this->postJson(route('api.v1.rds-databases.attach'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'rds_database_id',
                'server_id',
            ]);
    }

    public function test_attach_handles_primary_validation()
    {
        $rdsDatabase = RdsDatabase::factory()->create();
        $server = \App\Models\Server::factory()->create();

        $payload = [
            'rds_database_id' => $rdsDatabase->id,
            'server_id' => $server->id,
        ];

        $response = $this->postJson(route('api.v1.rds-databases.attach'), $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['is_primary']);

        $payload['is_primary'] = true;
        $response = $this->postJson(route('api.v1.rds-databases.attach'), $payload);
        $response->assertNoContent();

        $rdsDatabase2 = RdsDatabase::factory()->create();
        $payload2 = [
            'rds_database_id' => $rdsDatabase2->id,
            'server_id' => $server->id,
        ];

        $response2 = $this->postJson(route('api.v1.rds-databases.attach'), $payload2);
        $response2->assertNoContent();
    }

    public function test_attach_fails_when_rds_database_already_attached_to_the_same_server()
    {
        $rdsDatabase = RdsDatabase::factory()->create();
        $server = \App\Models\Server::factory()->create();
        $user = \App\Models\User::factory()->create();
        
        \DB::table('rds_database_server')->insert([
            'rds_database_id' => $rdsDatabase->id,
            'server_id' => $server->id,
            'is_primary' => true,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = [
            'rds_database_id' => $rdsDatabase->id,
            'server_id' => $server->id,
            'is_primary' => false,
        ];

        $response = $this->postJson(route('api.v1.rds-databases.attach'), $payload);

        $response->assertStatus(422);
    }

    public function test_can_attach_rds_database_to_server()
    {
        $rdsDatabase = RdsDatabase::factory()->create();
        $server = \App\Models\Server::factory()->create();

        $payload = [
            'rds_database_id' => $rdsDatabase->id,
            'server_id' => $server->id,
            'is_primary' => true,
        ];

        $response = $this->postJson(route('api.v1.rds-databases.attach'), $payload);

        $response->assertNoContent();

        $this->assertDatabaseHas('rds_database_server', [
            'rds_database_id' => $rdsDatabase->id,
            'server_id' => $server->id,
            'is_primary' => true,
        ]);
    }

}