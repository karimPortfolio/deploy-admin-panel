<?php

namespace App\Services\AWS;

use App\Enums\OsFamily;
use Aws\Ssm\SsmClient;


class AmazonMachineImageService
{
    public function __construct(
        private readonly SsmClient $ssmClient
    ) {}
    /**
     * Get the list of available AMIs by operation system family.
     *
     * @return array
     */
    public function getAmiByOsFamily(OsFamily $osFamily)
    {
        try {
            $result = $this->ssmClient->getParameter([
                'Name' => $osFamily->ssmParameter(),
                'WithDecryption' => false,
            ]);

            return $result['Parameter']['Value'];
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to retrieve AMI list: ' . $e->getMessage());
        }
    }
}