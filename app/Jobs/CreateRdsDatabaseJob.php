<?php

namespace App\Jobs;

use App\Models\RdsDatabase;
use App\Services\RdsDatabaseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateRdsDatabaseJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected RdsDatabase $rdsDatabase, protected array $params)
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
            $rdsDatabaseResults = RdsDatabaseService::createRdsDatabase($this->params);
            $createdRdsDatabase = RdsDatabaseService::describeRdsDatabaseByInstanceId($rdsDatabaseResults['DBInstanceIdentifier']);

            $this->rdsDatabase->update([
                'instance_id' => $createdRdsDatabase['DBInstanceIdentifier'] ?? null,
                'db_instance_arn' => $createdRdsDatabase['DBInstanceArn'] ?? null,
                'engine_version' => $createdRdsDatabase['EngineVersion'] ?? null,
                'endpoint_address' => $createdRdsDatabase['Endpoint']['Address'] ?? null,
                'endpoint_port' => $createdRdsDatabase['Endpoint']['Port'] ?? null,
                'endpoint_hosted_zone_id' => $createdRdsDatabase['Endpoint']['HostedZoneId'] ?? null,
                'aviability_zone' => $createdRdsDatabase['AvailabilityZone'] ?? null,
                'instance_create_time' => $this->getInstanceCreatedTime($createdRdsDatabase),
                'status' => \App\Enums\DBStatus::tryFrom($createdRdsDatabase['DBInstanceStatus'] ?? 'pending'),
            ]);
        }
        catch (\Exception $e) {
            $this->rdsDatabase->forceDelete();
            \Log::error('RDS Database creation failed', ['error' => $e->getMessage()]);
        }
    }

    private function getInstanceCreatedTime(array $createdRdsDatabase): ?string
    {
        if (isset($createdRdsDatabase['InstanceCreateTime'])) {
            return date('Y-m-d H:i:s', strtotime($createdRdsDatabase['InstanceCreateTime']));
        }

        return null;
    }
}
