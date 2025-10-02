<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\SshKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class SshKeyControllerTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->actingAsUser();
    }

    public function test_it_can_get_all_ssh_keys()
    {
        SshKey::factory()->count(5)->create();

        $response = $this->getJson(route("api.v1.ssh-keys.index"));

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'public_key',
                        'created_at',
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function test_it_can_search_ssh_keys()
    {
        SshKey::factory()->create(['name' => 'unique-key-name']);
        SshKey::factory()->count(5)->create();

        $response = $this->getJson(route("api.v1.ssh-keys.index", ['search' => 'unique-key-name']));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'public_key',
                        'created_at',
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function test_it_can_filter_ssh_keys_by_name()
    {
        SshKey::factory()->create(['name' => 'filter-key-name']);
        SshKey::factory()->count(5)->create();

        $response = $this->getJson(route("api.v1.ssh-keys.index", ['filter[name]' => 'filter-key-name']));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'public_key',
                        'created_at',
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function test_index_method_returns_empty_array_when_no_ssh_keys()
    {
        $response = $this->getJson(route("api.v1.ssh-keys.index"));

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_it_can_create_ssh_key()
    {
    
        $sshKeyData = ['name' => 'test-key-name'];
    
        $response = $this->postJson(route("api.v1.ssh-keys.store"), $sshKeyData);
    
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'public_key', 'created_at']
            ]);
    
        $this->assertDatabaseHas('ssh_keys', [
            'name' => $sshKeyData['name'],
        ]);

        $this->assertFileDoesNotExist(storage_path("app/keys/test-key-name"));
        $this->assertFileDoesNotExist(storage_path("app/keys/test-key-name.pub"));
    }


    public function test_it_can_show_ssh_key()
    {
        $sshKey = SshKey::factory()->create();

        $response = $this->getJson(route("api.v1.ssh-keys.show", $sshKey));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'public_key',
                    'created_at',
                ]
            ]);
    }


    public function test_show_method_returns_404_when_ssh_key_not_found()
    {
        $response = $this->getJson(route("api.v1.ssh-keys.show", 999));

        $response->assertStatus(404);
    }


    public function test_it_can_update_ssh_key()
    {
        $sshKey = SshKey::factory()->create();

        $sshKeyData = [
            'name' => 'updated-key-name'
        ];

        $response = $this->putJson(route("api.v1.ssh-keys.update", $sshKey), $sshKeyData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'public_key',
                    'created_at',
                ]
            ]);

        $this->assertDatabaseHas('ssh_keys', [
            'id' => $sshKey->id,
            'name' => $sshKeyData['name'],
        ]);
    }

    public function test_update_method_returns_404_when_ssh_key_not_found()
    {
        $response = $this->putJson(route("api.v1.ssh-keys.update", 999));

        $response->assertStatus(404);
    }

    public function test_it_can_delete_ssh_key()
    {
        $sshKey = SshKey::factory()->create();

        $response = $this->deleteJson(route("api.v1.ssh-keys.destroy", $sshKey));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('ssh_keys', [
            'id' => $sshKey->id,
        ]);
    }

    public function test_delete_method_returns_404_when_ssh_key_not_found()
    {
        $response = $this->deleteJson(route("api.v1.ssh-keys.destroy", 999));

        $response->assertStatus(404);
    }
    
}
