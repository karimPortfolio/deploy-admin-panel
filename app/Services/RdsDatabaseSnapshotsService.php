<?php

namespace App\Services;


class RdsDatabaseSnapshotsService
{

    /**
     * Create a snapshot of an RDS database.
     * 
     * @param array $params
     * @return array
     * @throws \Aws\Exception\AwsException
     */
    public static function createSnapshot(array $params): array
    {
        $rdsClient = app(\Aws\Rds\RdsClient::class);

        try {
            $result = $rdsClient->createDBSnapshot([
                'DBInstanceIdentifier' => $params['DBInstanceIdentifier'],
                'DBSnapshotIdentifier' => $params['DBSnapshotIdentifier'],
            ]);

            $result->toArray();

            if (empty($result['DBSnapshot'])) {
                throw new \Exception('Failed to create RDS database snapshot.');
            }

            return $result['DBSnapshot'] ?? [];

        } catch (\Aws\Exception\AwsException $e) {
            \Log::error('Error creating RDS database snapshot: '.$e->getMessage());
            throw $e;
        }
    }


    /**
     * Retrieve snapshots for a given RDS database instance identifier.
     * 
     * @param string $dbInstanceIdentifier
     * @return array
     * @throws \Aws\Exception\AwsException
     */
    public static function getSnapshotsByInstanceIdentifier(string $dbInstanceIdentifier): array
    {
        $rdsClient = app(\Aws\Rds\RdsClient::class);

        try {
            $result = $rdsClient->describeDBSnapshots([
                'DBInstanceIdentifier' => $dbInstanceIdentifier,
            ]);

            return $result['DBSnapshots'] ?? [];

        } catch (\Aws\Exception\AwsException $e) {
            \Log::error('Error retrieving RDS database snapshots: '.$e->getMessage());
            throw $e;
        }
    }


    /**
     * Retrieve a snapshot by its identifier.
     * 
     * @param string $dbSnapshotIdentifier
     * @return array
     * @throws \Aws\Exception\AwsException
     */
    public static function getSnapshotByIdentifier(string $dbSnapshotIdentifier): array
    {
        $rdsClient = app(\Aws\Rds\RdsClient::class);

        try {
            $result = $rdsClient->describeDBSnapshots([
                'DBSnapshotIdentifier' => $dbSnapshotIdentifier,
            ]);

            if (empty($result['DBSnapshots'])) {
                throw new \Exception('RDS database snapshot not found: ' . $dbSnapshotIdentifier);
            }

            return $result['DBSnapshots'][0] ?? [];

        } catch (\Aws\Exception\AwsException $e) {
            \Log::error('Error retrieving RDS database snapshot: '.$e->getMessage());
            throw $e;
        }
    }


    /**
     * Delete a snapshot by its identifier.
     * 
     * @param string $dbSnapshotIdentifier
     * @return array
     * @throws \Aws\Exception\AwsException
     */
    public static function deleteSnapshot(string $dbSnapshotIdentifier): array
    {
        $rdsClient = app(\Aws\Rds\RdsClient::class);

        try {
            $results = $rdsClient->deleteDBSnapshot([
                'DBSnapshotIdentifier' => $dbSnapshotIdentifier,
            ]);

            if (!isset($results['DBSnapshot'])) {
                throw new \Exception('Failed to delete RDS database snapshot: ' . $dbSnapshotIdentifier);
            }

            return $results['DBSnapshot'] ?? [];
        } catch (\Aws\Exception\AwsException $e) {
            \Log::error('Error deleting RDS database snapshot: '.$e->getMessage());
            throw $e;
        }
    }
}