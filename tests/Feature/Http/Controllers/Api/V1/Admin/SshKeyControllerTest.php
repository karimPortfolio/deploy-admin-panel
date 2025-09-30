<?php

namespace Tests\Feature\Http\Controllers\Api\V1\Admin;

use App\Enums\InstanceType;
use App\Enums\OsFamily;
use App\Models\SshKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class SshKeyControllerTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }
    
    public function test_can_list_ssh_keys_with_pagination()
    {
        SshKey::factory()->count(5)->create();
        $response = $this->getJson(route('api.v1.admin.ssh-keys.index'));

        $response->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                 ->has('meta')
                 ->has('links')
        );
    }


    public function test_can_filter_ssh_keys_by_name()
    {
        SshKey::factory()->create(['name' => 'My SSH Key']);
        SshKey::factory()->create(['name' => 'Another Key']);
        $response = $this->getJson(route('api.v1.admin.ssh-keys.index', ['filter[name]' => 'My SSH Key']));
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('data.0.name', 'My SSH Key')
                 ->etc()
        );
    }


    public function test_can_sort_ssh_keys_by_name()
    {
        SshKey::factory()->create(['name' => 'B Key']);
        SshKey::factory()->create(['name' => 'A Key']);
        $response = $this->getJson(route('api.v1.admin.ssh-keys.index', ['sort' => 'name']));
        $response->assertOk()
            ->assertJsonPath('data.0.name', 'A Key')
            ->assertJsonPath('data.1.name', 'B Key');
    }


    public function test_can_search_ssh_keys()
    {
        SshKey::factory()->create(['name' => 'Searchable Key']);
        SshKey::factory()->create(['name' => 'Non-matching Key']);
        $response = $this->getJson(route('api.v1.admin.ssh-keys.index', ['search' => 'Searchable']));
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('data.0.name', 'Searchable Key')
                 ->etc()
        );
    }


    public function test_index_method_returns_403_for_non_admin_users()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->getJson(route('api.v1.admin.ssh-keys.index'));
        $response->assertForbidden();
    }

    public function test_can_view_single_ssh_key()
    {
        $sshKey = SshKey::factory()->create();

        $response = $this->getJson(route('api.v1.admin.ssh-keys.show', $sshKey));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('data.id', $sshKey->id)
                 ->where('data.name', $sshKey->name)
                 ->where('data.public_key', substr($sshKey->public_key, 0, 40))
                 ->etc()
        );
    }


    public function test_show_method_returns_403_for_non_admin_users()
    {
        $this->actingAs(User::factory()->create());
        $sshKey = SshKey::factory()->create();
        $response = $this->getJson(route('api.v1.admin.ssh-keys.show', $sshKey));
        $response->assertForbidden();
    }


    public function test_show_method_handles_non_existent_ssh_key()
    {
        $response = $this->getJson(route('api.v1.admin.ssh-keys.show', 9999));
        $response->assertNotFound();
    }


    public function test_can_delete_ssh_key_and_notify_creator()
    {
        \Notification::fake();

        $creatorUser = User::factory()->create();
        $sshKey = SshKey::factory()->create(['created_by' => $creatorUser->id]);

        $response = $this->deleteJson(route('api.v1.admin.ssh-keys.destroy', $sshKey));
      
        $response->assertNoContent();
        $this->assertDatabaseMissing('ssh_keys', ['id' => $sshKey->id]);
        \Notification::assertSentTo(
            [$creatorUser], \App\Notifications\ResourceDeletedNotification::class
        );
    }


    public function test_destroy_method_returns_403_for_non_admin_users()
    {
        $this->actingAs(User::factory()->create());
        $sshKey = SshKey::factory()->create();
        $response = $this->deleteJson(route('api.v1.admin.ssh-keys.destroy', $sshKey));
        $response->assertForbidden();
    }


    public function test_destroy_method_handles_non_existent_ssh_key()
    {
        $response = $this->deleteJson(route('api.v1.admin.ssh-keys.destroy', 9999));
        $response->assertNotFound();
    }

}
