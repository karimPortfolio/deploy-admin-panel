<?php

namespace App\Services;

use Aws\Ec2\Ec2Client;


class SecurityGroupService
{

    /**
     * Create a new AWS security group.
     *
     * @param string $name
     * @param string $description
     * @return array
     */
    public static function createSecurityGroup(string $name, string $description): array
    {
        try {
            $ec2Client = app(Ec2Client::class);

            $vpcId = VpcService::getOrCreateVpc()['VpcId'];

            $result = $ec2Client->createSecurityGroup([
                'GroupName' => $name,
                'Description' => $description,
                'VpcId' => $vpcId,
            ]);

            return [
                'GroupId' => $result['GroupId'],
                'vpc_id' => $vpcId,
            ];
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to create security group: ' . $e->getMessage());
        }
    }

    /**
     * Delete an AWS security group.
     *
     * @param string $groupId
     * @return void
     */
    public static function deleteSecurityGroup(string $groupId): void
    {
        try {
            $ec2Client = app(Ec2Client::class);

            $ec2Client->deleteSecurityGroup([
                'GroupId' => $groupId,
            ]);
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to delete security group: ' . $e->getMessage());
        }
    }
}
