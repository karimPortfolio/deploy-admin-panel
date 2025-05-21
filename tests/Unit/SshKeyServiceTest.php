<?php

namespace Tests\Unit;

use App\Services\SshKeyService;
use Tests\TestCase;


class SshKeyServiceTest extends TestCase
{
    public function test_create_ssh_key()
    {
        $storagePath = base_path('app/keys');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $name = 'test_key';
        $result = SshKeyService::createSshKey($name);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('public_key', $result);
        $this->assertArrayHasKey('private_key', $result);

        $this->assertStringContainsString('ssh-rsa', $result['public_key']);
        $this->assertNotEmpty($result['private_key']);

        $this->assertFileDoesNotExist(storage_path("app/keys/{$name}"));
        $this->assertFileDoesNotExist(storage_path("app/keys/{$name}.pub"));

    }
}
