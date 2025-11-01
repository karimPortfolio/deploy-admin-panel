<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Jobs\CreateRdsDatabaseSnapshotJob;
use App\Models\RdsDatabaseSnapshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class RdsDatabaseSnapshotControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setup(): void
    {
        parent::setUp();
        $this->actingAsUser();
    }

    public function test_index_returns_paginated_snapshots()
    {
        RdsDatabaseSnapshot::factory(5)->create();

        $response = $this->getJson(route('api.v1.rds-database-snapshots.index'));

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'snapshot_identifier',
                        'snapshot_arn',
                        'rds_database',
                        'snapshot_type',
                        'status',
                        'percent_progress',
                        'encrypted',
                        'kms_key_id',
                        'snapshot_create_time',
                        'created_at',
                    ],
                ],
                'links',
                'meta',
            ]);
    }


    public function test_index_applies_filters()
    {
        RdsDatabaseSnapshot::factory(5)->create();
        RdsDatabaseSnapshot::factory()->create([
            'rds_database_id' => 1,
            'created_at' => '2023-01-01 00:00:00',
        ]);

        $response = $this->getJson(route('api.v1.rds-database-snapshots.index', [
            'filter[rds_database_id]' => 1,
            'filter[created_at]' => '2023-01-01',
        ]));

        $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
    }

    public function test_index_applies_sorting()
    {
        RdsDatabaseSnapshot::factory()->create(['created_at' => '2023-01-01 00:00:00', 'snapshot_identifier' => 'B']);
        RdsDatabaseSnapshot::factory()->create(['created_at' => '2023-02-01 00:00:00', 'snapshot_identifier' => 'A']);

        $response = $this->getJson(route('api.v1.rds-database-snapshots.index', [
            'created_at' => 'asc',
        ]));

        $response->assertStatus(200)
            ->assertJsonPath('data.0.snapshot_identifier', 'B')
            ->assertJsonPath('data.1.snapshot_identifier', 'A');

    }

    public function test_index_applies_search()
    {
        RdsDatabaseSnapshot::factory()->create(['snapshot_identifier' => 'test-snapshot-1']);
        RdsDatabaseSnapshot::factory()->create(['snapshot_identifier' => 'another-snapshot']);

        $response = $this->getJson(route('api.v1.rds-database-snapshots.index', [
            'search' => 'test-snapshot',
        ]));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.snapshot_identifier', 'test-snapshot-1');
    }

    public function test_show_returns_snapshot_details()
    {
        $snapshot = RdsDatabaseSnapshot::factory()->create();

        $response = $this->getJson(route('api.v1.rds-database-snapshots.show', $snapshot->id));

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $snapshot->id)
            ->assertJsonPath('data.snapshot_identifier', $snapshot->snapshot_identifier);
    }

    public function test_show_returns_404_for_nonexistent_snapshot()
    {
        $response = $this->getJson(route('api.v1.rds-database-snapshots.show', 9999));

        $response->assertStatus(404);
    }

    public function test_store_validates_request()
    {
        $response = $this->postJson(route('api.v1.rds-database-snapshots.store'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['db_instance_identifier', 'rds_database_id']);
    }

    public function test_store_initiates_snapshot_creation()
    {
        Bus::fake();

        $rdsDatabase = \App\Models\RdsDatabase::factory()->create();

        $response = $this->postJson(route('api.v1.rds-database-snapshots.store'), [
            'db_instance_identifier' => $rdsDatabase->db_instance_identifier,
            'rds_database_id' => $rdsDatabase->id,
        ]);

        $response->assertStatus(202)
            ->assertJsonPath('message', __('messages.rds_databases.snapshots.create_initiated_msg'));

        Bus::assertDispatched(CreateRdsDatabaseSnapshotJob::class);
    }

    public function test_store_returns_validation_error_for_invalid_rds_database()
    {
        $response = $this->postJson(route('api.v1.rds-database-snapshots.store'), [
            'db_instance_identifier' => 'nonexistent-db',
            'rds_database_id' => 9999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['db_instance_identifier', 'rds_database_id']);
            
    }

    public function test_store_updates_rds_database_status()
    {
        $rdsDatabase = \App\Models\RdsDatabase::factory()->create([
            'status' => \App\Enums\DBStatus::STARTED,
        ]);

        $this->postJson(route('api.v1.rds-database-snapshots.store'), [
            'db_instance_identifier' => $rdsDatabase->db_instance_identifier,
            'rds_database_id' => $rdsDatabase->id,
        ]);

        $this->assertDatabaseHas('rds_databases', [
            'id' => $rdsDatabase->id,
            'status' => \App\Enums\DBStatus::BACKING_UP,
        ]);
    }


    public function test_destroy_method_throws_error()
    {
        $snapshot = RdsDatabaseSnapshot::factory()->create();

        $response = $this->deleteJson(route('api.v1.rds-database-snapshots.destroy', $snapshot->id));

        $response->assertStatus(500);
    }

}
