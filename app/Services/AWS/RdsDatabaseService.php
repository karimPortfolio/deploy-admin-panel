<?php

namespace App\Services\AWS;

use App\Models\SecurityGroup;

class RdsDatabaseService
{
    public function __construct(
        private readonly VpcService $awsVpcService,
        private readonly \Aws\Rds\RdsClient $rdsClient
    ) {}

    /**
     * Create a new RDS database.
     *
     * @throws \Aws\Exception\AwsException
     */
    public function createRdsDatabase(array $params): array
    {
        $subnetGroup = self::getSubnetGroup($params['vpc_security_group']);

        try {
            $result = $this->rdsClient->createDBInstance([
                'DBInstanceIdentifier' => $params['db_instance_identifier'],
                'DBInstanceClass' => $params['db_instance_class'],
                'Engine' => $params['engine'],
                'MasterUsername' => $params['master_username'],
                'MasterUserPassword' => $params['master_user_password'],
                'AllocatedStorage' => 20,
                'StorageType' => $params['storage_type'],
                'DBName' => $params['db_name'],
                'BackupRetentionPeriod' => $params['backup_retention_period'],
                'MultiAZ' => false,
                'PubliclyAccessible' => $params['publicly_accessible'],
                'StorageEncrypted' => $params['storage_encrypted'],
                'VpcSecurityGroupIds' => [$params['vpc_security_group']],
                'DBSubnetGroupName' => $subnetGroup[0]['DBSubnetGroupName'] ?? null,
            ]);

            $result->toArray();

            if (empty($result['DBInstance'])) {
                throw new \Exception('Failed to create RDS database instance.');
            }

            $result['DBInstance']['DBInstanceStatus'] = self::getRdsDatabaseStatus($result['DBInstance']['DBInstanceStatus']);

            return $result['DBInstance'] ?? [];

        } catch (\Aws\Exception\AwsException $e) {
            \Log::error('Error creating RDS database: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Describe an RDS database by its instance identifier.
     *
     * @throws \Aws\Exception\AwsException
     */
    public function describeRdsDatabaseByInstanceId(string $dbInstanceIdentifier): array
    {
        try {
            $result = $this->rdsClient->describeDBInstances([
                'DBInstanceIdentifier' => $dbInstanceIdentifier,
            ]);

            if (! empty($result['DBInstances']) && count($result['DBInstances']) > 0) {
                $result['DBInstances'][0]['DBInstanceStatus'] = self::getRdsDatabaseStatus($result['DBInstances'][0]['DBInstanceStatus']);

                return $result['DBInstances'][0];
            }

            return [];
        } catch (\Aws\Exception\AwsException $e) {
            \Log::error('Error describing RDS database: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete an RDS database by its instance identifier.
     *
     * @throws \Aws\Exception\AwsException
     */
    public function deleteRdsDatabaseByInstanceId(string $dbInstanceIdentifier): \AWS\Result
    {
        try {
            $result = $this->rdsClient->deleteDBInstance([
                'DBInstanceIdentifier' => $dbInstanceIdentifier,
                'SkipFinalSnapshot' => true,
            ]);

            return $result;
        } catch (\Aws\Exception\AwsException $e) {
            \Log::error('Error deleting RDS database: '.$e->getMessage());
            throw $e;
        }

    }

    /**
     * Backup an RDS database by its instance identifier.
     *
     * @throws \Aws\Exception\AwsException
     */
    public function backupRdsDatabaseByInstanceId(string $dbInstanceIdentifier): \AWS\Result
    {
        try {
            $result = $this->rdsClient->createDBSnapshot([
                'DBInstanceIdentifier' => $dbInstanceIdentifier,
                'DBSnapshotIdentifier' => $dbInstanceIdentifier.'-snapshot-'.time(),
            ]);

            return $result;
        } catch (\Aws\Exception\AwsException $e) {
            \Log::error('Error backing up RDS database: '.$e->getMessage());
            throw $e;
        }

    }

    /**
     * Get or create RDS database subnet group.
     *
     * @throws \Aws\Exception\AwsException
     */
    public function getOrCreateRdsDatabaseSubnetGroup(array $params): array
    {
        try {

            if (! empty($params['existing_subnet_group_name'])) {
                try {
                    $result = $this->rdsClient->describeDBSubnetGroups([
                        'DBSubnetGroupName' => $params['existing_subnet_group_name'],
                    ]);

                    if (! empty($result['DBSubnetGroups'])) {
                        // info($result->toArray());
                        return $result->toArray() ?? [];
                    }
                } catch (\Aws\Exception\AwsException $e) {
                    if ($e->getAwsErrorCode() !== 'DBSubnetGroupNotFoundFault') {
                        \Log::error('Error describing RDS subnet group: '.$e->getMessage());
                        throw $e;
                    }
                }
            }

            $result = $this->rdsClient->createDBSubnetGroup([
                'DBSubnetGroupName' => $params['db_subnet_group_name'],
                'DBSubnetGroupDescription' => $params['db_subnet_group_description'],
                'SubnetIds' => $params['subnet_ids'],
            ]);

            return $result->toArray() ?? [];

        } catch (\Aws\Exception\AwsException $e) {
            \Log::error('Error creating RDS database subnet group: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Map AWS RDS status to application status.
     */
    private function getRdsDatabaseStatus(string $awsStatus): string
    {
        return match ($awsStatus) {
            'available' => 'started',

            'backing-up' => 'backing_up',

            'stopping', 'stopped' => 'stopped',

            'failed',
            'inaccessible-encryption-credentials',
            'restore-error',
            'storage-full' => 'failed',

            default => 'pending',
        };
    }

    
    /**
     * Get subnet group for RDS database based on security group ID.
     */
    private function getSubnetGroup(string $securityGroupId): array
    {
        $securityGroup = SecurityGroup::where('group_id', $securityGroupId)->first();
        $vpcSubnets = $this->awsVpcService->getSubnetsByVpcId($securityGroup->vpc_id);

        $maxAttempts = 5;
        $attempts = 0;

        while (count($vpcSubnets) < 2 && $attempts < $maxAttempts) {
            $attempts++;
            $newSubnet = $this->awsVpcService->createSubnet($securityGroup->vpc_id);

            if (! empty($newSubnet)) {
                $vpcSubnets[] = $newSubnet;
            }

            $vpcSubnets = $this->awsVpcService->getSubnetsByVpcId($securityGroup->vpc_id);
        }

        if (count($vpcSubnets) < 2) {
            throw new \Exception('Unable to create sufficient subnets for RDS database. At least 2 subnets are required.');
        }

        $params = [
            'db_subnet_group_name' => 'default-db-subnet-group',
            'db_subnet_group_description' => 'Subnet group for RDS database',
            'subnet_ids' => array_map(fn ($subnet) => $subnet['SubnetId'], $vpcSubnets),
            'existing_subnet_group_name' => 'default-db-subnet-group',
        ];

        $vpcSubnetGroup = $this->getOrCreateRdsDatabaseSubnetGroup($params);

        if (! empty($vpcSubnetGroup['DBSubnetGroups'])) {
            return $vpcSubnetGroup['DBSubnetGroups'];
        }

        return [];
    }
}
