<?php

namespace App\Services;

class RdsDatabaseService
{
    /**
     * Create a new RDS database.
     * 
     * @param array $params
     * @return array
     * @throws \Aws\Exception\AwsException
     */
    public static function createRdsDatabase(array $params): array
    {
        $rdcClient = app(\Aws\Rds\RdsClient::class);

        try {
            $result = $rdcClient->createDBInstance([
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
                'EnableCloudwatchLogsExports' => ['error', 'general', 'slowquery'],
                'VpcSecurityGroupIds' => [$params['vpc_security_group']],
            ]);

            return $result->toArray() ?? [];
        } catch (\Aws\Exception\AwsException $e) {
            \Log::error('Error creating RDS database: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Describe an RDS database by its instance identifier.
     * 
     * @param string $dbInstanceIdentifier
     * @return array
     * @throws \Aws\Exception\AwsException
     */
    public static function describeRdsDatabaseByInstanceId(string $dbInstanceIdentifier): array
    {
        $rdcClient = app(\Aws\Rds\RdsClient::class);

        try {
            $result = $rdcClient->describeDBInstances([
                'DBInstanceIdentifier' => $dbInstanceIdentifier,
            ]);

            if (! empty($result['DBInstances']) && count($result['DBInstances']) > 0) {
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
     * @param string $dbInstanceIdentifier
     * @return \AWS\Result
     * @throws \Aws\Exception\AwsException
     */
    public static function deleteRdsDatabaseByInstanceId(string $dbInstanceIdentifier): \AWS\Result
    {
        $rdcClient = app(\Aws\Rds\RdsClient::class);

        try
        {
            $result = $rdcClient->deleteDBInstance([
                'DBInstanceIdentifier' => $dbInstanceIdentifier,
                'SkipFinalSnapshot' => true,
            ]);

            return $result;
        }
        catch (\Aws\Exception\AwsException $e)
        {
            \Log::error('Error deleting RDS database: '.$e->getMessage());
            throw $e;
        }

    }

    /**
     * Backup an RDS database by its instance identifier.
     * 
     * @param string $dbInstanceIdentifier
     * @return \AWS\Result
     * @throws \Aws\Exception\AwsException
     */
    public static function backupRdsDatabaseByInstanceId(string $dbInstanceIdentifier): \AWS\Result
    {
        $rdcClient = app(\Aws\Rds\RdsClient::class);

        try
        {
            $result = $rdcClient->createDBSnapshot([
                'DBInstanceIdentifier' => $dbInstanceIdentifier,
                'DBSnapshotIdentifier' => $dbInstanceIdentifier . '-snapshot-' . time(),
            ]);

            return $result;
        }
        catch (\Aws\Exception\AwsException $e)
        {
            \Log::error('Error backing up RDS database: '.$e->getMessage());
            throw $e;
        }

    }

    /**
     * Map AWS RDS status to application status.
     * 
     * @param string $awsStatus
     * @return string
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
}
