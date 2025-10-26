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
            'db_instance_class' => [
                'value' => 'db.t3.micro',
            ],
            'engine' => [
                'value' => 'mysql',
            ],
            'db_name' => 'testdatabase',
            'master_username' => 'deployer',
            'master_password' => 'SecurePassword123!',
            'storage_type' => [
                'value' => 'gp2',
            ],
            'allocated_storage' => 20,
            'vpc_security_group' => \App\Models\SecurityGroup::factory()->create(),
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

    public function test_attach_sets_existing_primary_to_false_when_new_primary_is_set()
    {
        $rdsDatabase1 = RdsDatabase::factory()->create();
        $rdsDatabase2 = RdsDatabase::factory()->create();
        $server = \App\Models\Server::factory()->create();
        $user = \App\Models\User::factory()->create();
        
        \DB::table('rds_database_server')->insert([
            'rds_database_id' => $rdsDatabase1->id,
            'server_id' => $server->id,
            'is_primary' => true,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = [
            'rds_database_id' => $rdsDatabase2->id,
            'server' => $server,
            'is_primary' => true,
        ];

        $response = $this->postJson(route('api.v1.rds-databases.attach'), $payload);

        $response->assertNoContent();

        $this->assertDatabaseHas('rds_database_server', [
            'rds_database_id' => $rdsDatabase1->id,
            'server_id' => $server->id,
            'is_primary' => false,
        ]);

        $this->assertDatabaseHas('rds_database_server', [
            'rds_database_id' => $rdsDatabase2->id,
            'server_id' => $server->id,
            'is_primary' => true,
        ]);
    }

    public function test_attach_sets_is_primary_true_when_no_existing_attachments()
    {
        $rdsDatabase = RdsDatabase::factory()->create();
        $server = \App\Models\Server::factory()->create();

        $payload = [
            'rds_database_id' => $rdsDatabase->id,
            'server' => $server,
        ];

        $response = $this->postJson(route('api.v1.rds-databases.attach'), $payload);

        $response->assertNoContent();

        $this->assertDatabaseHas('rds_database_server', [
            'rds_database_id' => $rdsDatabase->id,
            'server_id' => $server->id,
            'is_primary' => true,
        ]);
    }

    public function test_can_attach_rds_database_to_server()
    {
        $rdsDatabase = RdsDatabase::factory()->create();
        $server = \App\Models\Server::factory()->create();

        $payload = [
            'rds_database_id' => $rdsDatabase->id,
            'server' => $server,
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


    public function test_can_get_database_engines()
    {
        $response = $this->getJson(route('api.v1.rds-databases.engines'));

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
        $response = $this->getJson(route('api.v1.rds-databases.instance-classes'));

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
        $response = $this->getJson(route('api.v1.rds-databases.storage-types'));

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
        $response = $this->getJson(route('api.v1.rds-databases.statuses'));

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

        $response = $this->getJson(route('api.v1.rds-databases.servers'));

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