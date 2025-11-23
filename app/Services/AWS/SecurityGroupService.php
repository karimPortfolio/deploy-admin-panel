<?php

namespace App\Services\AWS;

use Aws\Ec2\Ec2Client;


class SecurityGroupService
{
    public function __construct(
        private Ec2Client $ec2Client,
        private VpcService $vpcService
    ) {}

    /**
     * Create a new AWS security group.
     *
     * @param string $name
     * @param string $description
     * @return array
     */
    public function createSecurityGroup(string $name, string $description): array
    {
        try {
            $ec2Client = $this->ec2Client;

            $vpcId = $this->vpcService->getOrCreateVpc()['VpcId'];

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
    public function deleteSecurityGroup(string $groupId): void
    {
        try {
            $ec2Client = $this->ec2Client;

            $ec2Client->deleteSecurityGroup([
                'GroupId' => $groupId,
            ]);
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to delete security group: ' . $e->getMessage());
        }
    }
}
