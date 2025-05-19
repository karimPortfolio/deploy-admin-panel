<?php

namespace App\Services;

use App\Models\SshKey;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Process;

class SshKeyService
{
    /**
     * Create a new SSH public and private keys and returns array contains the private and public keys.
     *
     * @param string $name
     * @return array
     */
    public static function createSshKey(string $name): array
    {
        $name = preg_replace('/[^a-zA-Z0-9_-]/', '', $name);
        $path = storage_path("app/keys/{$name}");

        if (file_exists($path)) {
            throw new \RuntimeException('SSH key already exists.');
        }

        Process::run("ssh-keygen -t rsa -b 2048 -f $path -q -N ''");

        $publicKey = file_get_contents("{$path}.pub");
        $privateKey = file_get_contents($path);

        if (!$publicKey || !$privateKey) {
            throw new \RuntimeException('Failed to read SSH key files.');
        }

        $encryptedPrivateKey = Crypt::encryptString($privateKey);

        unlink("{$path}.pub");
        unlink($path);

        return [
            'public_key' => $publicKey,
            'private_key' => $encryptedPrivateKey,
        ];
    } 

}