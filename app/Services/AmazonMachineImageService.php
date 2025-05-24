<?php

namespace App\Services;

use App\Enums\OsFamily;
use Aws\Ssm\SsmClient;


class AmazonMachineImageService
{
    /**
     * Get the list of available AMIs by operation system family.
     *
     * @return array
     */
    public static function getAmiByOsFamily(OsFamily $osFamily)
    {
        $ssmClient = app(SsmClient::class);

        try {
            $result = $ssmClient->getParameter([
                'Name' => $osFamily->ssmParameter(),
                'WithDecryption' => false,
            ]);

            return $result['Parameter']['Value'];
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to retrieve AMI list: ' . $e->getMessage());
        }
    }
}