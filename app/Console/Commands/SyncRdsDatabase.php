<?php

namespace App\Console\Commands;

use App\Enums\DBStatus;
use App\Models\RdsDatabase;
use App\Services\RdsDatabaseService;
use Illuminate\Console\Command;

class SyncRdsDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-rds-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check status of creating RDS databases and update them if ready';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendingRdsDatabases = $this->getThePendingRdsDatabases();
        $this->info('Found '.count($pendingRdsDatabases).' pending RDS databases to sync.');

        foreach ($pendingRdsDatabases as $rdsDatabase) {
            try {
                $rdsDatabaseDetails = RdsDatabaseService::describeRdsDatabaseByInstanceId($rdsDatabase->db_instance_identifier);

                if (!empty($rdsDatabaseDetails['DBInstanceStatus'])) {
                    $rdsDatabase->update([
                        'status' => DBStatus::STARTED->value,
                        'endpoint_address' => $rdsDatabaseDetails['Endpoint']['Address'] ?? null,
                        'endpoint_port' => $rdsDatabaseDetails['Endpoint']['Port'] ?? null,
                        'endpoint_hosted_zone_id' => $rdsDatabaseDetails['Endpoint']['HostedZoneId'] ?? null,
                        'availability_zone' => $rdsDatabaseDetails['AvailabilityZone'] ?? null,
                        'db_instance_arn' => $rdsDatabaseDetails['DBInstanceArn'] ?? null,
                        'engine_version' => $rdsDatabaseDetails['EngineVersion'] ?? null,
                        'instance_create_time' => $this->getInstanceCreatedTime($rdsDatabaseDetails),
                        'latest_restorable_time' => $this->getLatestRestorableTime($rdsDatabaseDetails),
                    ]);
                }

            } catch (\Exception $e) {
                $rdsDatabase->update([
                    'status' => DBStatus::FAILED->value,
                ]);
                \Log::error('Error syncing RDS database ID '.$rdsDatabase->id.': '.$e->getMessage());
            }
        }

        $this->info('RDS database sync completed.');
    }

    private function getThePendingRdsDatabases()
    {
        return RdsDatabase::where('status', DBStatus::PENDING)
        ->get();
    }

      private function getInstanceCreatedTime(array $syncedRdsDatabase): ?string
    {
        if (isset($syncedRdsDatabase['InstanceCreateTime'])) {
            return date('Y-m-d H:i:s', strtotime($syncedRdsDatabase['InstanceCreateTime']));
        }

        return null;
    }

    private function getLatestRestorableTime(array $syncedRdsDatabase): ?string
    {
        if (isset($syncedRdsDatabase['LatestRestorableTime'])) {
            return date('Y-m-d H:i:s', strtotime($syncedRdsDatabase['LatestRestorableTime']));
        }

        return null;
    }
}
