<?php

namespace App\Console\Commands;

use App\Enums\DBSnapshotStatus;
use App\Enums\DBStatus;
use App\Models\RdsDatabaseSnapshot;
use App\Services\RdsDatabaseService;
use App\Services\RdsDatabaseSnapshotsService;
use Illuminate\Console\Command;

class SyncRdsDatabaseSnapshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-rds-db-snapshot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check status of creating RDS databases snapshots and update them if ready';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $creatingSnapshots = $this->getTheCreatingSnaphots();
        $this->info('Found '.count($creatingSnapshots).' creating RDS databases to sync.');

        foreach ($creatingSnapshots as $snapshot) {
            try {
                $service = app(RdsDatabaseSnapshotsService::class);
                $snapshotDetails = $service->getSnapshotByIdentifier($snapshot->snapshot_identifier);

                if (!empty($snapshotDetails['Status']) && $snapshotDetails['Status'] === 'available') {
                    $snapshot->update([
                        'status' => DBSnapshotStatus::STARTED->value,
                        'snapshot_create_time' => $snapshotDetails['SnapshotCreateTime'] ?? now(),
                        'percent_progress' => 100,
                    ]);

                    if ($snapshot->rdsDatabase)
                    {
                        $snapshot->rdsDatabase->update([
                            'latest_restorable_time' => $snapshotDetails['SnapshotCreateTime'] ?? now(),
                            'status' => DBStatus::STARTED->value,
                        ]);
                    }
                }

            } catch (\Exception $e) {
                $snapshot->update([
                    'status' => DBStatus::FAILED->value,
                ]);
                \Log::error('Error syncing RDS database snapshot with ID '.$snapshot->id.': '.$e->getMessage());
            }
        }

        $this->info('RDS database snapshots sync completed.');
    }

    private function getTheCreatingSnaphots()
    {
        return RdsDatabaseSnapshot::where('status', DBSnapshotStatus::CREATING->value)
        ->with('rdsDatabase')
        ->get();
    }
}
