<?php

namespace App\Services\AWS;


class RdsDatabaseSnapshotsService
{

    public function __construct(
        private \Aws\Rds\RdsClient $rdsClient
    ) {}

    /**
     * Create a snapshot of an RDS database.
     * 
     * @param array $params
     * @return array
     * @throws \Aws\Exception\AwsException
     */
    public function createSnapshot(array $params): array
    {
        try {
            $result = $this->rdsClient->createDBSnapshot([
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
    public function getSnapshotsByInstanceIdentifier(string $dbInstanceIdentifier): array
    {
        try {
            $result = $this->rdsClient->describeDBSnapshots([
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
    public function getSnapshotByIdentifier(string $dbSnapshotIdentifier): array
    {
        try {
            $result = $this->rdsClient->describeDBSnapshots([
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
    public function deleteSnapshot(string $dbSnapshotIdentifier): array
    {
        try {
            $results = $this->rdsClient->deleteDBSnapshot([
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