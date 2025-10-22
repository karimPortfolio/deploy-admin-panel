<?php

namespace App\Services;

class VpcService
{
    /**
     * get a VPC by Tags or create a new one if it doesn't exist.
     *
     * @return array
     */
    public static function getOrCreateVpc(): array
    {
        try {
            $ec2Client = app(\Aws\Ec2\Ec2Client::class);

            $defaultVpc = self::getDefaultVpc();

            if ($defaultVpc) {
                return $defaultVpc;
            }

            $response = $ec2Client->describeVpcs([
                'Filters' => [
                    ['Name' => 'tag:DefaultReplacement', 'Values' => ['true']]
                ]
            ]);

            if (!empty($response['Vpcs'])) {
                return $response['Vpcs'][0];
            }

            $result = $ec2Client->createVpc([
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

            //waiting for the VPC until be available
            $ec2Client->waitUntil('VpcAvailable', [
                'VpcIds' => [$vpcId],
            ]);

            return [
                'VpcId' => $vpcId,
            ];
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to create VPC: ' . $e->getMessage());
        }
    }

    /**
     * Delete an AWS VPC.
     *
     * @param string $vpcId
     * @return void
     */
    public static function deleteVpc(string $vpcId): void
    {
        try {
            $ec2Client = app(\Aws\Ec2\Ec2Client::class);

            $ec2Client->deleteVpc([
                'VpcId' => $vpcId,
            ]);
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to delete VPC: ' . $e->getMessage());
        }
    }


    /**
     * Get the details of an AWS VPC.
     *
     * @param string $vpcId
     * @return array
     */
    public static function getVpcDetails(string $vpcId): array
    {
        try {
            $ec2Client = app(\Aws\Ec2\Ec2Client::class);

            $result = $ec2Client->describeVpcs([
                'VpcIds' => [$vpcId],
            ]);

            return $result['Vpcs'][0];
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to get VPC details: ' . $e->getMessage());
        }
    }

    /**
     * List all AWS VPCs.
     *
     * @return array
     */
    public static function listVpcs(): array
    {
        try {
            $ec2Client = app(\Aws\Ec2\Ec2Client::class);

            $result = $ec2Client->describeVpcs();

            return $result['Vpcs'];
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to list VPCs: ' . $e->getMessage());
        }
    }


    /**
     * Get the default VPC.
     *
     * @return array
     */
    public static function getDefaultVpc(): array
    {
        try {
            $ec2Client = app(\Aws\Ec2\Ec2Client::class);

            $result = $ec2Client->describeVpcs([
                'Filters' => [
                    [
                        'Name' => 'isDefault',
                        'Values' => ['true'],
                    ],
                    [
                        'Name' => 'tag:DefaultReplacement',
                        'Values' => ['true'],
                    ]
                ],
            ]);

            return $result['Vpcs'][0] ?? [];
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to get default VPC: ' . $e->getMessage());
        }
    }


    /**
     * Get or create a subnet by VPC ID.
     *
     * @param string $vpcId
     * @return array
     */
    public static function getOrCreateSubnetByVpcId(string $vpcId): array
    {
        $ec2Client = app(\Aws\Ec2\Ec2Client::class);

        try {
            $existing = $ec2Client->describeSubnets([
                'Filters' => [
                    [
                        'Name' => 'vpc-id',
                        'Values' => [$vpcId],
                    ],
                ],
            ]);

            $subnets = $existing->get('Subnets');

            if (!empty($subnets)) {
                return $subnets[0];
            }

            $result = $ec2Client->createSubnet([
                'VpcId' => $vpcId,
                'CidrBlock' => '10.0.1.0/24',
            ]);

            $ec2Client->modifySubnetAttribute([
                'SubnetId' => $result->get('Subnet')['SubnetId'],
                'MapPublicIpOnLaunch' => [
                    'Value' => true,
                ],
            ]);

            return $result->get('Subnet');
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to initialize EC2 client: ' . $e->getMessage());
        }
    }

    /**
     * Get all subnets by VPC ID.
     *
     * @param string $vpcId
     * @return array
     * @throws \RuntimeException
     */
    public static function getSubnetsByVpcId(string $vpcId): array
    {
        $ec2Client = app(\Aws\Ec2\Ec2Client::class);

        try {
            $existing = $ec2Client->describeSubnets([
                'Filters' => [
                    [
                        'Name' => 'vpc-id',
                        'Values' => [$vpcId],
                    ],
                ],
            ]);

            return $existing->get('Subnets') ?? [];
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to initialize EC2 client: ' . $e->getMessage());
        }
    }
}
