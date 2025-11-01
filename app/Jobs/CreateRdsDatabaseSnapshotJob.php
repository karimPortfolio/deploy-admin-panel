<?php

namespace App\Jobs;

use App\Enums\DBSnapshotStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateRdsDatabaseSnapshotJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected array $params)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try
        {
            $rdsSnapshotsService = app(\App\Services\RdsDatabaseSnapshotsService::class);
            $results = $rdsSnapshotsService->createSnapshot($this->params);

            \App\Models\RdsDatabaseSnapshot::create([
                'snapshot_identifier' => $results['DBSnapshotIdentifier'] ?? '',
                'snapshot_arn' => $results['DBSnapshotArn'] ?? null,
                'rds_database_id' => $this->params['rds_database_id'],
                'snapshot_type' => 'manual',
                'status' => DBSnapshotStatus::CREATING,
                'percent_progress' => $results['PercentProgress'] ?? 0,
                'encrypted' => $results['Encrypted'] ?? false,
                'kms_key_id' => $results['KmsKeyId'] ?? null,
                'created_by' => $this->params['created_by'] ?? null,
                'snapshot_create_time' => $this->getSnapshotCreateTime($results),
            ]);
        }
        catch (\Exception $e)
        {
            \Log::error('Error in CreateRdsDatabaseSnapshotJob: '.$e->getMessage());
        }
    }

    private function getSnapshotCreateTime(array $results): ?\Carbon\Carbon
    {
        if (isset($results['SnapshotCreateTime'])) {
            return \Carbon\Carbon::parse($results['SnapshotCreateTime']);
        }

        return null;
    }
}
