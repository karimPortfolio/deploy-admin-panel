<?php

namespace App\Services\AWS;

use Livewire\Attributes\Reactive;

class VpcService
{
    public function __construct(
        private readonly \Aws\Ec2\Ec2Client $ec2Client
    ) {}

    /**
     * get a VPC by Tags or create a new one if it doesn't exist.
     */
    public function getOrCreateVpc(): array
    {
        try {
            $defaultVpc = $this->getDefaultVpc();

            if ($defaultVpc) {
                return $defaultVpc;
            }

            $response = $this->ec2Client->describeVpcs([
                'Filters' => [
                    ['Name' => 'tag:DefaultReplacement', 'Values' => ['true']],
                ],
            ]);

            if (! empty($response['Vpcs'])) {
                return $response['Vpcs'][0];
            }

            $result = $this->ec2Client->createVpc([
                'CidrBlock' => '10.0.0.0/16',
                'TagSpecifications' => [
                    [
                        'ResourceType' => 'vpc',
                        'Tags' => [
                            ['Key' => 'DefaultReplacement', 'Value' => 'true'],
                        ],
                    ],
                ],
            ]);

            $vpcId = $result['Vpc']['VpcId'];

            // waiting for the VPC until be available
            $this->ec2Client->waitUntil('VpcAvailable', [
                'VpcIds' => [$vpcId],
            ]);

            return [
                'VpcId' => $vpcId,
            ];
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to create VPC: '.$e->getMessage());
        }
    }

    /**
     * Delete an AWS VPC.
     */
    public function deleteVpc(string $vpcId): void
    {
        try {
            $this->ec2Client->deleteVpc([
                'VpcId' => $vpcId,
            ]);
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to delete VPC: '.$e->getMessage());
        }
    }

    /**
     * Get the details of an AWS VPC.
     */
    public function getVpcDetails(string $vpcId): array
    {
        try {
            $result = $this->ec2Client->describeVpcs([
                'VpcIds' => [$vpcId],
            ]);

            return $result['Vpcs'][0];
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to get VPC details: '.$e->getMessage());
        }
    }

    /**
     * List all AWS VPCs.
     */
    public function listVpcs(): array
    {
        try {
            $result = $this->ec2Client->describeVpcs();

            return $result['Vpcs'];
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to list VPCs: '.$e->getMessage());
        }
    }

    /**
     * Get the default VPC.
     */
    public function getDefaultVpc(): array
    {
        try {
            $result = $this->ec2Client->describeVpcs([
                'Filters' => [
                    [
                        'Name' => 'isDefault',
                        'Values' => ['true'],
                    ],
                    [
                        'Name' => 'tag:DefaultReplacement',
                        'Values' => ['true'],
                    ],
                ],
            ]);

            return $result['Vpcs'][0] ?? [];
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to get default VPC: '.$e->getMessage());
        }
    }

    /**
     * Get or create a subnet by VPC ID.
     */
    public function getOrCreateSubnetByVpcId(string $vpcId): array
    {
        try {
            $existing = $this->ec2Client->describeSubnets([
                'Filters' => [
                    [
                        'Name' => 'vpc-id',
                        'Values' => [$vpcId],
                    ],
                ],
            ]);

            $subnets = $existing->get('Subnets');

            if (! empty($subnets)) {
                return $subnets[0];
            }

            $result = $this->createSubnet($vpcId);

            $subnetId = $result['SubnetId'];

            $this->ec2Client->modifySubnetAttribute([
                'SubnetId' => $subnetId,
                'MapPublicIpOnLaunch' => ['Value' => true],
            ]);

            return $result;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to initialize EC2 client: '.$e->getMessage());
        }
    }

    /**
     * Get all subnets by VPC ID.
     *
     * @throws \RuntimeException
     */
    public function getSubnetsByVpcId(string $vpcId): array
    {
        try {
            $existing = $this->ec2Client->describeSubnets([
                'Filters' => [
                    [
                        'Name' => 'vpc-id',
                        'Values' => [$vpcId],
                    ],
                ],
            ]);

            return $existing->get('Subnets') ?? [];
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to initialize EC2 client: '.$e->getMessage());
        }
    }

    /**
     * Create a subnet in a specified VPC.
     *
     * @throws \RuntimeException
     */
    public function createSubnet(string $vpcId, ?string $cidrBlock = null): array
    {
        try {
            $result = $this->ec2Client->createSubnet([
                'VpcId' => $vpcId,
                'CidrBlock' => $cidrBlock ?? '10.0.1.0/24',
            ]);

            return $result->get('Subnet');
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to create subnet: '.$e->getMessage());
        }
    }
}
